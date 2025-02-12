<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\RssFeeds\Model;

/**
 * Модель данных формата ленты (канала) ATOM (формат синдикации Atom).
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\RssFeeds\Model
 * @since 1.0
 */
class AtomFeed extends Feed
{
    /**
     * {@inheritdoc}
     */
    public function get(mixed $idOrChannel): ?static
    {
        $column = is_numeric($idOrChannel) ? 'id' : 'channel';
        return $this->selectOne([$column => $idOrChannel, 'format' => 'ATOM']);
    }

    /**
     * {@inheritdoc}
     */
    protected function getOptionsFormat(): array
    {
        return [
            'image'          => ['type' => 'string'], // изображение
            'icon'           => ['type' => 'string'], // значок
            'copyright'      => ['type' => 'string'], // авторские права
            'published'      => ['type' => 'date'], // дата публикации
            'category'       => ['type' => 'string'], // категория
            'authorName'     => ['type' => 'string'], // имя автора канала
            'authorUri'      => ['type' => 'string'], // адрес страницы автора
            'authorEmail'    => ['type' => 'string'], // e-mail автора
            'contributorName'  => ['type' => 'string'], // имя соавтора канала
            'contributorUri'   => ['type' => 'string'], // адрес страницы соавтора
            'contributorEmail' => ['type' => 'string'], // e-mail соавтора
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getConstructs(): array
    {
        return [
            'title'    => $this->title,
            'subtitle' => $this->description,
            'rights'   => $this->options['copyright'],
            'author'   => [
                'name'  => $this->options['authorName'],
                'uri'   => $this->options['authorUri'],
                'email' => $this->options['authorEmail'],
            ],
            'contributor' => [
                'name'  => $this->options['contributorName'],
                'uri'   => $this->options['contributorUri'],
                'email' => $this->options['contributorEmail'],
            ],
            'published' => $this->options['published'],
            'category'  => $this->options['category'],
            'logo'      => $this->options['image'],
            'icon'      => $this->options['icon'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareItem(array $row): array
    {
        return [
            'title'       => $row['header'],
            'link'        => $row['url'],
            'published'   => $row['publish_date'],
            'updated'     => $row['_updated_date'] ? $row['_updated_date'] : $row['publish_date'],
            'summary'     => $row['announce_plain'],
            'author'      => '',
            'contributor' => '',
            'content'     => $row['text'],
            'image'       => $row['image'],
        ];
    }
}
