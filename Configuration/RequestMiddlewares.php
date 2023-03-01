<?php

return [
    'frontend' => [
        'abtest-cookie' => [
            'target' => \WerkraumMedia\ABTest\Middleware\SetCookie::class,
            'after' => [
                'typo3/cms-frontend/prepare-tsfe-rendering',
            ],
            'before' => [
                'typo3/cms-frontend/output-compression',
            ],
        ],
    ],
];
