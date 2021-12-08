<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        if (TYPO3_MODE === 'BE') {

            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'Mohsin.MqkLocallangtools',
                'web', // Make module a submodule of 'web'
                'tolocallang', // Submodule key
                '', // Position
                [
                    \Mohsin\MqkLocallangtools\Controller\MainController::class => 'index, list, replace',

                ],
                [
                    'access' => 'user,group',
                    'icon'   => 'EXT:mqk_locallangtools/Resources/Public/Icons/user_mod_tolocallang.svg',
                    'labels' => 'LLL:EXT:mqk_locallangtools/Resources/Private/Language/locallang_tolocallang.xlf',
                ]
            );

        }
    }
);
