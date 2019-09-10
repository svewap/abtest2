<?php

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', [
    'tx_abtest2_b_id' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:abtest2/Resources/Private/Language/locallang_db.xlf:pages.tx_abtest2_b_id',
        'config' => [
            'type' => 'group',
            'internal_type' => 'db',
            'allowed' => 'pages',
            'maxitems' => 1,
            'size' => 1,
            'default' => 0
        ]
    ],
    'tx_abtest2_cookie_time' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:abtest2/Resources/Private/Language/locallang_db.xlf:pages.tx_abtest2_cookie_time',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                ['LLL:EXT:abtest2/Resources/Private/Language/locallang_db.xlf:cookie_1_month', 2419200],
                ['LLL:EXT:abtest2/Resources/Private/Language/locallang_db.xlf:cookie_1_week', 604800],
                ['LLL:EXT:abtest2/Resources/Private/Language/locallang_db.xlf:cookie_1_day', 86400],
                ['LLL:EXT:abtest2/Resources/Private/Language/locallang_db.xlf:cookie_12_day', 43200],
                ['LLL:EXT:abtest2/Resources/Private/Language/locallang_db.xlf:cookie_1_hour', 3600],
                ['LLL:EXT:abtest2/Resources/Private/Language/locallang_db.xlf:cookie_1_minute', 60]
            ]
        ]
    ],
    'tx_abtest2_header' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:abtest2/Resources/Private/Language/locallang_db.xlf:pages.tx_abtest2_header',
        'config' => [
            'type' => 'text'
        ]
    ],
    'tx_abtest2_footer' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:abtest2/Resources/Private/Language/locallang_db.xlf:pages.tx_abtest2_footer',
        'config' => [
            'type' => 'text'
        ]
    ],
    'tx_abtest2_counter' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:abtest2/Resources/Private/Language/locallang_db.xlf:pages.tx_abtest2_counter',
        'config' => [
            'type' => 'input'
        ]
    ]
]);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('pages',
    '--palette--;LLL:EXT:abtest2/Resources/Private/Language/locallang_db.xlf:palette_title;tx_abtest2', '',
    'after:subtitle');

$GLOBALS['TCA']['pages']['palettes']['tx_abtest2'] = array(
    'showitem' => 'tx_abtest2_b_id,--linebreak--,tx_abtest2_header,tx_abtest2_footer,tx_abtest2_cookie_time,tx_abtest2_counter'
);

