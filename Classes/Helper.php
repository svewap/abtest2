<?php

namespace WapplerSystems\ABTest2;

/**
 * This file is part of the "abtest2" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\PageRepository;

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
     * @param $pObj TypoScriptFrontendController
     * @return void
     * @throws \InvalidArgumentException
     */
    public function determineContentId(array $params, &$pObj)
    {

        // only try to change the page if it's not the googlebot.
        if (false === stripos($_SERVER['HTTP_USER_AGENT'], 'googlebot')) {

            $currentPageId = $randomPageId = $pObj->id;

            // Get the rootpage_id from realurl config.
            $realurlConfig = $pObj->TYPO3_CONF_VARS['EXTCONF']['realurl'];
            if (array_key_exists($_SERVER['SERVER_NAME'], $realurlConfig)) {
                $rootpage_id = $realurlConfig[$_SERVER['SERVER_NAME']]['pagePath']['rootpage_id'];
            } else {
                $rootpage_id = $realurlConfig['_DEFAULT']['pagePath']['rootpage_id'];
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

            $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
            $currentPagePropertiesArray = $pageRepository->getPage($currentPageId);

            $pageBPageId = $currentPagePropertiesArray['tx_abtest2_b_id'];
            $cookieLifeTime = $currentPagePropertiesArray['tx_abtest2_cookie_time'];

            if ($pageBPageId) {
                /* page b id exists */
                $cookiePageId = (int)$_COOKIE['abtest2-' . $currentPageId];

                if ($cookiePageId > 0 && ($cookiePageId === $pageBPageId || $cookiePageId === $currentPageId)) {
                    /* valid cookie page id -> select cookie page id */
                    $randomPageId = $cookiePageId;
                } else {
                    /* select least used page */
                    $pageBPropertiesArray = $pageRepository->getPage($pageBPageId);
                    if ((int)$currentPagePropertiesArray['tx_abtest2_counter'] > (int)$pageBPropertiesArray['tx_abtest2_counter']) {
                        $randomPageId = $pageBPageId;
                        $currentPagePropertiesArray = $pageBPropertiesArray;

                    } elseif ((int)$currentPagePropertiesArray['tx_abtest2_counter'] < (int)$pageBPropertiesArray['tx_abtest2_counter']) {

                    } else {
                        /* random */
                        $randomPage = rand(0, 1); // 0 = original ID; 1 = "B" site.
                        if ($randomPage) {
                            $randomPageId = $pageBPageId;
                            $currentPagePropertiesArray = $pageBPropertiesArray;
                        }
                    }

                    /* rise counter */
                    $GLOBALS['TYPO3_DB']->exec_UPDATEquery('pages', 'uid='. (int)$randomPageId, array('tx_abtest2_counter' => $currentPagePropertiesArray['tx_abtest2_counter'] + 1));

                    setcookie('abtest2-' . $currentPageId, $randomPageId, time() + $cookieLifeTime);
                }

                // If current page ID is different from the random page ID we set the correct page ID.
                if ($currentPageId !== $randomPageId) {
                    $pObj->contentPid = $randomPageId;
                    $pObj->page['content_from_pid'] = $randomPageId;
                }

                $pObj->page['no_cache'] = true;


                if ($currentPagePropertiesArray) {
                    $additionalHeaderData = $currentPagePropertiesArray['tx_abtest2_header'];
                    $additionalFooterData = $currentPagePropertiesArray['tx_abtest2_footer'];
                    if ($additionalHeaderData) {
                        $pObj->additionalHeaderData['abtest2'] = $additionalHeaderData;
                    }
                    if ($additionalFooterData) {
                        $pObj->additionalFooterData['abtest2'] = $additionalFooterData;
                    }
                }

            }

        }

    }
}


