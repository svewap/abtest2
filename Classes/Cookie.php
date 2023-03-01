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

use TYPO3\CMS\Core\SingletonInterface;

class Cookie implements SingletonInterface
{
    /**
     * @var int
     */
    private $requestedPageUid = 0;

    /**
     * @var int
     */
    private $actualPageUid = 0;

    /**
     * @var int
     */
    private $lifetime = 604800;

    public function setRequestedPage(int $uid): void
    {
        $this->requestedPageUid = $uid;
    }

    public function setActualPage(int $uid): void
    {
        $this->actualPageUid = $uid;
    }

    public function setLifetime(int $seconds): void
    {
        if ($seconds > 0) {
            $this->lifetime = $seconds;
        }
    }

    public function getRequestedPage(): int
    {
        return $this->requestedPageUid;
    }

    public function getActualPage(): int
    {
        return $this->actualPageUid;
    }

    public function getLifetime(): int
    {
        return $this->lifetime;
    }

    public function needsToBeSet(): bool
    {
        return $this->actualPageUid > 0;
    }
}
