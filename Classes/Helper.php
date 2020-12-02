<?php

namespace WapplerSystems\ABTest2;

/**
 * This file is part of the "abtest2" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\CacheHashCalculator;
use TYPO3\CMS\Frontend\Page\PageRepository;
use WapplerSystems\ABTest2\DeviceDetector\DeviceDetector;
use WapplerSystems\ABTest2\DeviceDetector\Yaml\Parser;

/**
 * This class detects which page version (either by cookie or by random) and sets the page content ID accordingly.
 *
 * @package WapplerSystems\ABTest2
 * @author Sven Wapler <typo3YYYYY@wappler.systems>
 */
class Helper
{

    /**
     *
     * @param array $params
     * @param $tsFeController TypoScriptFrontendController
     * @return void
     * @throws \InvalidArgumentException
     */
    public function determineContentId(array $params, &$tsFeController)
    {
        $deviceDetector = new DeviceDetector();
        $deviceDetector->setUserAgent($_SERVER['HTTP_USER_AGENT']);
        try {
            $deviceDetector->setYamlParser(new Parser());
            $deviceDetector->parse();
            if ($deviceDetector->isBot()) return;
        } catch (\Exception $e) {
        }

        $currentPageId = $targetPageId = $tsFeController->id;
        /** @var PageRepository $pageRepository */
        $pageRepository = GeneralUtility::makeInstance(PageRepository::class);

        // Get the rootpage_id from realurl config.
        if (ExtensionManagementUtility::isLoaded('realurl')) {
            $realurlConfig = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl'];

            if (is_array($realurlConfig) && array_key_exists($_SERVER['SERVER_NAME'], $realurlConfig)) {
                $rootpage_id = $realurlConfig[$_SERVER['SERVER_NAME']]['pagePath']['rootpage_id'];
            } else {
                $rootpage_id = $realurlConfig['_DEFAULT']['pagePath']['rootpage_id'];
            }
        } else {
            $rootline = $pageRepository->getRootLine($GLOBALS['TSFE']->id);

            $rootpage_id = array_pop($rootline);
        }

        // If the ID is NULL, then we set this value to the rootpage_id. NULL is the "Home"page, ID is a specific sub-page, e.g. www.domain.de (NULL) - www.domain.de/page.html (ID)
        if (!$currentPageId) {
            if ($rootpage_id) {
                $currentPageId = $rootpage_id;
            } else {
                // Leave the function because we can not determine the ID.
                return;
            }
        }

        $currentPagePropertiesArray = $pageRepository->getPage($currentPageId);

        $pageBPageId = $currentPagePropertiesArray['tx_abtest2_b_id'];
        /* TODO: check if page b exists */
        $cookieLifeTime = $currentPagePropertiesArray['tx_abtest2_cookie_time'];

        if ($pageBPageId) {

            $pageBPropertiesArray = $pageRepository->getPage($pageBPageId);
            $cookieValue = $_COOKIE['abtest2'];

            if ($cookieValue === 'b') {
                $targetPageId = $pageBPageId;
                $currentPagePropertiesArray = $pageBPropertiesArray;
            } else if ($cookieValue === 'a') {

            } else {
                $cookieValue = 'a';
                /* select least used page */

                if ((int)$currentPagePropertiesArray['tx_abtest2_counter'] > (int)$pageBPropertiesArray['tx_abtest2_counter']) {
                    /* take b */
                    $targetPageId = $pageBPageId;
                    $currentPagePropertiesArray = $pageBPropertiesArray;
                    $cookieValue = 'b';
                }

                /* rise counter */
                $GLOBALS['TYPO3_DB']->exec_UPDATEquery('pages', 'uid='. (int)$targetPageId, array('tx_abtest2_counter' => $currentPagePropertiesArray['tx_abtest2_counter'] + 1));

                setcookie('abtest2', $cookieValue, time() + $cookieLifeTime);
            }

            // If current page ID is different from the random page ID we set the correct page ID.
            if ($currentPageId !== $targetPageId) {
                $tsFeController->contentPid = $targetPageId;
                $tsFeController->page['content_from_pid'] = $targetPageId;
            }

            $_GET['abtest'] = $cookieValue;

            $this->makeCacheHash($tsFeController);


            if ($currentPagePropertiesArray) {
                $additionalHeaderData = $currentPagePropertiesArray['tx_abtest2_header'];
                $additionalFooterData = $currentPagePropertiesArray['tx_abtest2_footer'];
                if ($additionalHeaderData) {
                    $tsFeController->additionalHeaderData['abtest2'] = $additionalHeaderData;
                }
                if ($additionalFooterData) {
                    $tsFeController->additionalFooterData['abtest2'] = $additionalFooterData;
                }
            }

        }



    }


    /**
     *
     * @param $tsFeController TypoScriptFrontendController
     * @return void
     * @throws \InvalidArgumentException
     */
    private function makeCacheHash(&$tsFeController)
    {
        $GET = GeneralUtility::_GET();

        /* Fix for root pages */
        if (!isset($GET['id'])) {
            $GET['id'] = $tsFeController->id;
        }

        /** @var CacheHashCalculator $cacheHash */
        $cacheHash = GeneralUtility::makeInstance(CacheHashCalculator::class);

        $tsFeController->cHash_array = $cacheHash->getRelevantParameters(GeneralUtility::implodeArrayForUrl('', $GET));
        $tsFeController->cHash = $cacheHash->calculateCacheHash($tsFeController->cHash_array);

    }
}


