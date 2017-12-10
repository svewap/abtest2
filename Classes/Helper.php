<?php

namespace WapplerSystems\ABTest2;

/**
 * This file is part of the "abtest2" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * This class detects which page version (either by cookie or by random) and sets the page content ID accordingly.
 *
 * @package WapplerSystems\ABTest2
 * @author Sven Wapler <typo3YYYYY@wappler.systems>
 */
class Helper {

	/** @var int|null */
	protected $currentPageId = null;

	/** @var int|null */
	protected $rootpage_id = null;	

	/** @var int|null */
	protected $realurlConfig = null;

	/** @var int|null */
	protected $selectBSite = null;

	/** @var int|null */
	protected $cookieLifeTime = null;

	/** @var int|null */
	protected $randomAbPageId = null;

	/** @var string */
	protected $additionalHeaderData;

    /**
     *
     * @param array $params
     * @param $pObj
     * @return void
     * @throws \InvalidArgumentException
     */
	public function determineContentId(array $params, &$pObj) {

		// only try to change the page if it's not the googlebot.  
		if(false === stripos($_SERVER['HTTP_USER_AGENT'], 'googlebot')) {

			$this->currentPageId = $params['pObj']->id;

			// Get the rootpage_id from realurl config.
			$this->realurlConfig = $params['pObj']->TYPO3_CONF_VARS['EXTCONF']['realurl'];
			if(array_key_exists($_SERVER['SERVER_NAME'], $this->realurlConfig)) {
				$this->rootpage_id = $this->realurlConfig[$_SERVER['SERVER_NAME']]['pagePath']['rootpage_id'];
			} else {
				$this->rootpage_id = $this->realurlConfig['_DEFAULT']['pagePath']['rootpage_id'];
			}
			
			// If the ID is NULL, then we set this value to the rootpage_id. NULL is the "Home"page, ID is a specific sub-page, e.g. www.domain.de (NULL) - www.domain.de/page.html (ID)
			if(!$this->currentPageId) {
				if($this->rootpage_id) {
					$this->currentPageId = $this->rootpage_id;
				} else {
					// Leave the function because we can not determine the ID.
					return;
				}
			}

			$pageRepository = GeneralUtility::makeInstance(PageRepository::class);
			$currentPagePropertiesArray = $pageRepository->getPage($this->currentPageId);

			$this->selectBSite = $currentPagePropertiesArray['tx_abtest2_b_id'];
			$this->cookieLifeTime = $currentPagePropertiesArray['tx_abtest2_cookie_time'];

			if($this->selectBSite) {
				if((int)$_COOKIE['abtest2-'.$this->currentPageId] > 0) {
					$this->randomAbPageId = (int)$_COOKIE['abtest2-'.$this->currentPageId];
				} else {
					$randomPage = rand(0,1); // 0 = original ID; 1 = "B" site.
					if($randomPage) {
						$this->randomAbPageId = $this->selectBSite;
					} else {
						$this->randomAbPageId = $this->currentPageId;
					}
					setcookie('abtest2-'.$this->currentPageId,$this->randomAbPageId,time()+$this->cookieLifeTime);
				}

				// If current page ID is different from the random page ID we set the correct page ID. 
				if($this->currentPageId != $this->randomAbPageId) {
                    $pObj->contentPid = $this->randomAbPageId;
                    $GLOBALS['TSFE']->page['content_from_pid'] = $this->randomAbPageId;
                    $GLOBALS['TSFE']->page['no_cache'] = true;
				}
			}

			// If additional headerdata is present then we specify additionalHeaderData. 
			$randomPagePropertiesArray = $pageRepository->getPage($this->randomAbPageId);
			$this->additionalHeaderData = $randomPagePropertiesArray['tx_abtest2_header'];
			if($this->additionalHeaderData) {
				$GLOBALS['TSFE']->additionalHeaderData['abtest2'] = $this->additionalHeaderData;
			} 

		}

	}
}


