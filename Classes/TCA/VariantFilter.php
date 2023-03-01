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

namespace WerkraumMedia\ABTest\TCA;

use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;

class VariantFilter
{
    private PageRepository $pageRepository;

    public function __construct(
        PageRepository $pageRepository
    ) {
        $this->pageRepository = $pageRepository;
    }

    public function doFilter(array $parameters, DataHandler $dataHandler): array
    {
        return array_filter($parameters['values'], [$this, 'filterPage']);
    }

    private function filterPage(string $pageIdentifier): bool
    {
        $uid = (int) str_replace('pages_', '', $pageIdentifier);
        $page = $this->pageRepository->getPage($uid);
        $doktype = (int) ($page['doktype'] ?? 0);
        return $doktype === PageRepository::DOKTYPE_DEFAULT;
    }
}
