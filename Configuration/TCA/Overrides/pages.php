<?php

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', array(
    'tx_abtest2_b_id' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:abtest2/Resources/Private/Language/locallang_db.xlf:pages.tx_abtest2_b_id',
        'config' => array(
            'type' => 'group',
            'internal_type' => 'db',
            'allowed' => 'pages',
            'maxitems' => 1,
            'size' => 1
        )
    ),
    'tx_abtest2_cookie_time' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:abtest2/Resources/Private/Language/locallang_db.xlf:pages.tx_abtest2_cookie_time',
        'config' => array(
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => array(
                ['LLL:EXT:abtest2/Resources/Private/Language/locallang_db.xlf:cookie_1_month', 2419200],
                ['LLL:EXT:abtest2/Resources/Private/Language/locallang_db.xlf:cookie_1_week', 604800],
                ['LLL:EXT:abtest2/Resources/Private/Language/locallang_db.xlf:cookie_1_day', 86400],
                ['LLL:EXT:abtest2/Resources/Private/Language/locallang_db.xlf:cookie_12_day', 43200],
                ['LLL:EXT:abtest2/Resources/Private/Language/locallang_db.xlf:cookie_1_hour', 3600],
                ['LLL:EXT:abtest2/Resources/Private/Language/locallang_db.xlf:cookie_1_minute', 60]
            )
        )
    ),
    'tx_abtest2_header' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:abtest2/Resources/Private/Language/locallang_db.xlf:pages.tx_abtest2_header',
        'config' => array(
            'type' => 'text'
        )
    ),
    'tx_abtest2_counter' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:abtest2/Resources/Private/Language/locallang_db.xlf:pages.tx_abtest2_counter',
        'config' => array(
            'type' => 'text'
        )
    )
));

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('pages',
    '--palette--;LLL:EXT:abtest2/Resources/Private/Language/locallang_db.xlf:palette_title;tx_abtest2', '',
    'after:subtitle');

$GLOBALS['TCA']['pages']['palettes']['tx_abtest2'] = array(
    'showitem' => 'tx_abtest2_b_id,--linebreak--,tx_abtest2_header,tx_abtest2_cookie_time,tx_abtest2_counter'
);

