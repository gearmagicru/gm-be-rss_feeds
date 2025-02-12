<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\RssFeeds\Model;

use Gm;
use Gm\Panel\Data\Model\GridModel;

/**
 * Модель данных списка RSS-каналов.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\RssFeeds\Model
 * @since 1.0
 */
class Grid extends GridModel
{
    /**
     * Языки.
     * 
     * @var array
     */
    protected array $languages = [];

    /**
     * {@inheritdoc}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'useAudit'   => true,
            'tableName'  => '{{rss}}',
            'primaryKey' => 'id',
            'fields'     => [
                ['published'], // дата публикации
                ['channel'], // название канала
                ['title'], // заголовок 
                ['description'], // описание
                ['enabled'], // доступен
                ['caching'], // кэширование
                ['format'], // формат
                ['languageName', 'direct' => 'language_id'], // язык
            ],
            'order' => ['title' => 'ASC'],
            'resetIncrements' => ['{{rss}}']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        // все доступные языки
        $this->languages = Gm::$app->language->available->getAllBy('code');
        $this
            ->on(self::EVENT_AFTER_DELETE, function ($someRecords, $result, $message) {
                // всплывающие сообщение
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']);
                /** @var \Gm\Panel\Controller\GridController $controller */
                $controller = $this->controller();
                // обновить список
                $controller->cmdReloadGrid();
            })
            ->on(self::EVENT_AFTER_SET_FILTER, function ($filter) {
                /** @var \Gm\Panel\Controller\GridController $controller */
                $controller = $this->controller();
                // обновить список
                $controller->cmdReloadGrid();
            });
    }

    /**
     * {@inheritdoc}
     */
    public function prepareRow(array &$row): void
    {
        // заголовок контекстного меню записи
        $row['popupMenuTitle'] = $row['title'];

        if ($row['format'] === 'ATOM') {
            $row['channel'] = $row['channel'] . '.atom';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function fetchRow(array $row): array
    {
        // Язык
        if ($row['language_id']) {
            $language = $this->languages[(int) $row['language_id']] ?? null;
            if ($language) {
                $row['languageName'] = $language['shortName'] . ' (' . $language['slug'] . ')';
            }
        }

        // Дата публикации
        $row['published'] = $this->fetchDateTimeField($row['published']);
        return $row;
    }
}
