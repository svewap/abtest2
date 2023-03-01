<?php

(static function (
    string $extensionName = 'abtest',
    string $tableName = 'pages'
) {
    $languagePath = 'LLL:EXT:' . $extensionName . '/Resources/Private/Language/locallang_db.xlf:' . $tableName . '.';

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns($tableName, [
        'tx_abtest_variant' => [
            'exclude' => 1,
            'label' => $languagePath . 'tx_abtest_variant',
            'config' => [
                'type' => 'group',
                'allowed' => 'pages',
                'maxitems' => 1,
                'minitems' => 0,
                'size' => 1,
                'suggestOptions' => [
                    'default' => [
                        'addWhere' => 'AND pages.doktype = ' . \TYPO3\CMS\Core\Domain\Repository\PageRepository::DOKTYPE_DEFAULT,
                    ],
                ],
                'filter' => [
                       [
                           'userFunc' => \WerkraumMedia\ABTest\TCA\VariantFilter::class . '->doFilter',
                       ],
                ],
            ],
        ],
        'tx_abtest_cookie_time' => [
            'exclude' => 1,
            'label' => $languagePath . 'tx_abtest_cookie_time',
            'config' => [
                'type' => 'input',
                'eval' => 'int',
                'size' => 10,
                'valuePicker' => [
                    'items' => [
                        [$languagePath . 'tx_abtest_cookie_time.cookie_1_month', 2419200],
                        [$languagePath . 'tx_abtest_cookie_time.cookie_1_week', 604800],
                        [$languagePath . 'tx_abtest_cookie_time.cookie_1_day', 86400],
                        [$languagePath . 'tx_abtest_cookie_time.cookie_12_days', 43200],
                        [$languagePath . 'tx_abtest_cookie_time.cookie_1_hour', 3600],
                        [$languagePath . 'tx_abtest_cookie_time.cookie_1_minute', 60],
                    ],
                ],
            ],
        ],
        'tx_abtest_counter' => [
            'exclude' => 1,
            'label' => $languagePath . 'tx_abtest_counter',
            'config' => [
                'type' => 'input',
                'eval' => 'int',
                'size' => 10,
            ],
        ],
    ]);

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        $tableName,
        '--div--;' . $languagePath . 'div_title,tx_abtest_variant,tx_abtest_cookie_time,tx_abtest_counter',
        '',
        'after:content_from_pid'
    );
})();
