<?php

declare(strict_types=1);

/*
 * Copyright (C) 2023 Daniel Siepmann <coding@daniel-siepmann.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

namespace WerkraumMedia\ABTest;

use DeviceDetector\DeviceDetector;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\CacheHashCalculator;

/**
 * Will decide whether to switch to another variant.
 */
class Switcher
{
    private PageRepository $pageRepository;

    private Cookie $cookie;

    public function __construct(
        PageRepository $pageRepository,
        Cookie $cookie
    ) {
        $this->pageRepository = $pageRepository;
        $this->cookie = $cookie;
    }

    public function determineContentId(
        array $params,
        TypoScriptFrontendController $frontendController
    ): void {
        if ($this->isRequestByBot()) {
            return;
        }

        $currentPageId = $frontendController->id;
        if (is_numeric($currentPageId) === false) {
            $currentPageId = $this->getRootPageId();
        } else {
            $currentPageId = (int)$currentPageId;
        }

        if ($currentPageId === 0) {
            return;
        }

        $currentPagePropertiesArray = $this->pageRepository->getPage($currentPageId);
        if ((int) $currentPagePropertiesArray['tx_abtest_variant'] === 0) {
            return;
        }

        $requestedViaCookie = (int) ($this->getRequest()->getCookieParams()['ab-' . $currentPageId] ?? '0');
        $targetPage = $this->getTargetPage($currentPagePropertiesArray, $requestedViaCookie);

        if ($frontendController->id !== (int) $targetPage['uid']) {
            $frontendController->id = (int) $targetPage['uid'];
            $frontendController->contentPid = (int) $targetPage['uid'];
            $frontendController->page = $targetPage;
        }

        if (
            $requestedViaCookie === 0
            || (int) $targetPage['uid'] !== $requestedViaCookie
        ) {
            $this->pageRepository->updateCounter((int) $targetPage['uid'], ++$targetPage['tx_abtest_counter']);
        }

        $this->cookie->setRequestedPage($currentPageId);
        $this->cookie->setActualPage($targetPage['uid']);
        $this->cookie->setLifetime($targetPage['tx_abtest_cookie_time']);

        // TODO: Cover caching
    }

    private function isRequestByBot(): bool
    {
        $deviceDetector = new DeviceDetector();
        $deviceDetector->setUserAgent($_SERVER['HTTP_USER_AGENT']);
        try {
            $deviceDetector->parse();
            return $deviceDetector->isBot();
        } catch (\Exception $e) {
        }

        return false;
    }

    /**
     * Returns 0 if no site could be fetched.
     */
    private function getRootPageId(): int
    {
        $site = $this->getRequest()->getAttribute('site');
        if (!$site instanceof Site) {
            return 0;
        }

        return $site->getRootPageId();
    }

    private function getRequest(): ServerRequest
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }

    private function getTargetPage(array $page, int $cookiePageUid): array
    {
        if ($cookiePageUid > 0 && $cookiePageUid === (int) $page['uid']) {
            return $page;
        }

        $variantPage = $this->pageRepository->getPage((int) $page['tx_abtest_variant']);

        if (
            $variantPage !== []
            && (
                ($cookiePageUid > 0 && $cookiePageUid === (int)$variantPage['uid'])
                || ((int)$variantPage['tx_abtest_counter'] < (int)$page['tx_abtest_counter'])
            )
        ) {
            return $variantPage;
        }

        return $page;
    }

    public static function register(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['determineId-PostProc'][self::class] = self::class . '->determineContentId';
    }
}
