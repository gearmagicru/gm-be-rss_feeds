<?php
/**
 * Модуль веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\RssFeeds;

use Gm;
use Gm\Helper\Url;

/**
 * Модуль управления RSS-каналами.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\RssFeeds
 */
class Module extends \Gm\Panel\Module\Module
{
    /**
     * {@inheritdoc}
     */
    public string $id = 'gm.be.rss_feeds';

    /**
     * Возвращает URL-адрес опубликованной RSS-ленты.
     * 
     * URL-путь определяется из маршрута модуля "Публикация RSS-ленты".
     * 
     * @param string $pattern Шаблон добавляемый в URL-путь (по умолчанию '').
     * 
     * @return string
     */
    public function getRssUrl(string $pattern = ''): string
    {
        static $route;

        if ($route === null) {
            $route = '';
            /** @var array|null $params Конфигурация установки модуля "Публикация RSS-ленты" */
            $params = Gm::$app->modules->getRegistry()->getConfigInstall('gm.fe.rss');
            if ($params) {
                // из указанных маршрутов, находим маршрут для публикации RSS-ленты
                $routes = $params['routes'] ?? [];
                foreach ($routes as $route) {
                    $use = $route['use'] ?? '';
                    if ($use === FRONTEND) {
                        $route = $route['options']['route'] ?? '';
                        break; // foreach
                    }
                }
            }
        }
        return Url::to($route . $pattern);
    }
}
