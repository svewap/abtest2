<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "abtest2".
 *
 * Auto generated 04-12-2017 10:08
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
    'title' => 'AB Test Pages',
    'description' => 'With this extension, administrators can deliver different content for the same URL (AB test), depending on cookies or parameters.',
    'category' => 'misc',
    'author' => 'Sven Wappler',
    'author_email' => 'typo3YYYY@wappler.systems',
    'author_company' => 'WapplerSystems',
    'state' => 'stable',
    'uploadfolder' => false,
    'createDirs' => '',
    'clearCacheOnLoad' => true,
    'version' => '0.1.0',
    'constraints' =>
        array(
            'depends' =>
                array(
                    'typo3' => '7.6.0-8.7.99',
                    'realurl' => '2.0.0-0.0.0',
                ),
            'conflicts' =>
                array(),
            'suggests' =>
                array(),
        ),
);

