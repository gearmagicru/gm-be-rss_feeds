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
 * Модель данных формата ленты (канала) RSS (Rich Site Summary).
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\RssFeeds\Model
 * @since 1.0
 */
class RssFeed extends Feed
{
    /**
     * {@inheritdoc}
     */
    public function get(mixed $idOrChannel): ?static
    {
        $column = is_numeric($idOrChannel) ? 'id' : 'channel';
        return $this->selectOne([$column => $idOrChannel, 'format' => 'RSS']);
    }

    /**
     * Возвращает формат параметров RSS-канала.
     * 
     * @return array
     */
    protected function getOptionsFormat(): array
    {
        return [
            'language'       => ['type' => 'int'], // язык
            'image'          => ['type' => 'string'], // изображение
            'copyright'      => ['type' => 'string'], // авторские права
            'authorName'     => ['type' => 'string'], // имя автора канала
            'authorUri'      => ['type' => 'string'], // адрес страницы автора
            'authorEmail'    => ['type' => 'string'], // e-mail автора
            'webmaster'      => ['type' => 'string'], // e-mail веб-мастера
            'published'      => ['type' => 'date'], // дата публикации
            'category'       => ['type' => 'string'], // категория
            'docs'           => ['type' => 'string'], // URL-адрес документации
            'ttl'            => ['type' => 'int'], // время жизни канала, мин
            'rating'         => ['type' => 'string'], // PICS-рейтинг канала
            'skipHours'      => ['type' => 'string'], // пропускать часы
            'skipDays'       => ['type' => 'string'], // пропускать дни
            'cloudDomain'    => ['type' => 'string'], // название домена в облаке
            'cloudPort'      => ['type' => 'int'], // порт в облаке
            'cloudPath'      => ['type' => 'string'], // путь в облаке
            'cloudProcedure' => ['type' => 'string'], // процедура вызова в облаке
            'cloudProtocol'  => ['type' => 'string'] // протокол в облаке
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getConstructs(): array
    {
        return [
            'title'         => $this->title,
            'description'   => $this->description,
            'language'      => $this->options['language'],
            'image'         => $this->options['image'],
            'copyright'     => $this->options['copyright'],
            'managingEditor' => [
                'name'  => $this->options['authorName'],
                'uri'   => $this->options['authorUri'],
                'email' => $this->options['authorEmail']
            ],
            'webMaster'     => $this->options['webmaster'],
            'pubDate'       => $this->options['published'],
            'category'      => $this->options['category'],
            'docs'          => $this->options['docs'],
            'ttl'           => $this->options['ttl'],
            'rating'        => $this->options['rating'],
            'skipHours'     => $this->options['skipHours'],
            'skipDays'      => $this->options['skipDays'],
            'cloud'         => [
                'domain'    => $this->options['cloudDomain'],
                'port'      => $this->options['cloudPort'],
                'path'      => $this->options['cloudPath'],
                'protocol'  => $this->options['cloudProtocol'],
                'registerProcedure' => $this->options['cloudProcedure']
            ]
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
            'description' => $row['text'],
            'author'      => '',
            'image'       => $row['image'],
            'pubDate'     => $row['publish_date'],
            'category'    => '',
            'comments'    => ''
        ];
    }
}
