<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\RssFeeds\Model;

use Gm\Panel\Data\Model\FormModel;

/**
 * Модель данных профиля RSS-канала.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\RssFeeds\Model
 * @since 1.0
 */
class GridRow extends FormModel
{
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
                ['id'],
                ['enabled'],
                ['title']
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this
            ->on(self::EVENT_AFTER_SAVE, function ($isInsert, $columns, $result, $message) {
                if ($message['success']) {
                    if (isset($columns['enabled'])) {
                        $publish = (int) $columns['enabled'];
                        $message['message'] = $this->module->t('The RSS feed "{0}" has been ' . ($publish > 0 ? 'enabled' : 'disabled'), [$this->title]);
                        $message['title']   = $this->t($publish > 0 ? 'Show' : 'Hide');
                    }
                }
                // всплывающие сообщение
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']);
            });
    }
}
