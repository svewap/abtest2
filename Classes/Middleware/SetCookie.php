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

namespace WerkraumMedia\ABTest\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;
use WerkraumMedia\ABTest\Cookie;

class SetCookie implements MiddlewareInterface
{
    /**
     * @var Cookie
     */
    private $cookie;

    public function __construct(
        Cookie $cookie
    ) {
        $this->cookie = $cookie;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $response = $handler->handle($request);

        if ($this->cookie->needsToBeSet()) {
            $cookie = new SymfonyCookie(
                'ab-' . $this->cookie->getRequestedPage(),
                (string) $this->cookie->getActualPage(),
                time() + $this->cookie->getLifetime()
            );

            // Do we need to adjust response header for caching to not cache the cookie within proxies?
            $response = $response->withAddedHeader('Set-Cookie', $cookie->__toString());
        }

        return $response;
    }
}
