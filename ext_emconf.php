<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "mqk_locallangtools"
 *
 * Auto generated by Extension Builder 2021-09-02
 *
 * Manual updates:
 * Only the data in the array - anything else is removed by next write.
 * "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'Hardcoded text to Locallang',
    'description' => 'Finds hardcoded text in Fluid HTML templates and transfer them to locallang.xlf file automatically.',
    'category' => 'module',
    'author' => 'Mohsin Khan',
    'author_email' => 'mohsinqayyumkhan@gmail.com',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.9',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-10.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
