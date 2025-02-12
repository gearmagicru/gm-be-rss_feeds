<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * Файл конфигурации Карты SQL-запросов.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    'drop'   => ['{{rss}}'],
    'create' => [
        '{{rss}}' => function () {
            return "CREATE TABLE `{{rss}}` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `language_id` int(11) unsigned DEFAULT NULL,
                `published` datetime DEFAULT NULL,
                `format` varchar(10) DEFAULT NULL,
                `channel` varchar(100) DEFAULT NULL,
                `title` varchar(255) DEFAULT NULL,
                `description` varchar(255) DEFAULT NULL,
                `enabled` tinyint(1) unsigned DEFAULT '0',
                `caching` tinyint(1) unsigned DEFAULT '0',
                `options` text,
                `selector` text,
                `_updated_date` datetime DEFAULT NULL,
                `_updated_user` int(11) unsigned DEFAULT NULL,
                `_created_date` datetime DEFAULT NULL,
                `_created_user` int(11) unsigned DEFAULT NULL,
                `_lock` tinyint(1) unsigned DEFAULT '0',
                PRIMARY KEY (`id`)
            ) ENGINE={engine} 
            DEFAULT CHARSET={charset} COLLATE {collate}";
        }
    ],

    'run' => [
        'install'   => ['drop', 'create'],
        'uninstall' => ['drop']
    ]
];