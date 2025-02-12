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
use Gm\Helper\Str;
use Gm\Helper\Url;
use Gm\Helper\Json;
use Gm\Db\ActiveRecord;
use Gm\Site\Data\Model\Article;

/**
 * Модель данных новостной ленты (канала).
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\RssFeeds\Model
 * @since 1.0
 */
class Feed extends ActiveRecord
{
    /** @var string Формат ленты (канала) RSS (Rich Site Summary). */
    public const FORMAT_RSS = 'RSS';

    /** @var string Формат ленты (канала) ATOM (формат синдикации Atom). */
    public const FORMAT_ATOM = 'ATOM';

    /** @var string Формат ленты (канала) JSON Feed. */
    public const FORMAT_JSON = 'JSON';

    /**
     * {@inheritdoc}
     */
    public function primaryKey(): string
    {
        return 'id';
    }

    /**
     * {@inheritdoc}
     */
    public function tableName(): string
    {
        return '{{rss}}';
    }

    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            'published'   => 'published', //дата публикации
            'channel'     => 'channel', // название канала
            'title'       => 'title', // заголовок 
            'description' => 'description', // описание
            'enabled'     => 'enabled', // доступен
            'format'      => 'format', // формат
            'languageId'  => 'language_id', // языка
            'caching'     => 'caching', // кэшировать
            'options'     => 'options', // параметры канала
            'selector'    => 'selector' // параметры вывода материала
        ];
    }

    /**
     * Возвращает запись (ленту) по указанному идентификатору.
     * 
     * @see ActiveRecord::selectOne()
     * 
     * @param int|string $idOrChannel Идентификатор или название ленты.
     * 
     * @return Feed|null Активная запись при успешном запросе, иначе `null`.
     */
    public function get(mixed $idOrChannel): ?static
    {
        $column = is_numeric($idOrChannel) ? 'id' : 'channel';
        return $this->selectOne([$column => $idOrChannel]);
    }

    /**
     * Возвращает расширение файла ленты согласно указанному формату.
     *
     * @param string $format Формат канала.
     * 
     * @return string
     */
    public static function getFileExtension(string $format): string
    {
        switch ($format) {
            case self::FORMAT_ATOM: return 'atom';
            case self::FORMAT_JSON:  return 'json';
        }
        return 'xml';
    }

    /**
     * Возвращает имя файла.
     *
     * @param string $channel Название канала.
     * @param string $format Формат канала.
     * @param bool $includePath Имя файла включает полный путь (по умолчанию `true`).
     * 
     * @return string
     */
    public static function getFilename(string $channel, string $format, bool $includePath = true): string
    {
        if ($channel) {
            $filename = $channel . '.' . self::getFileExtension($format);
            return $includePath ? Gm::getAlias("@runtime/$filename") : $filename;
        }
        return '';
    }

    /**
     * Проверяет, имеет ли лента кэш.
     *
     * @param string $channel Название канала.
     * @param string $format Формат канала.
     * 
     * @return bool
     */
    public static function hasCache(string $channel, string $format): bool
    {
        return file_exists(self::getFilename($channel, $format, true));
    }

    /**
     * Возвращает кэш.
     *
     * @param string $channel Название канала.
     * @param string $format Формат канала.
     * 
     * @return false|string Возвращает значение `false`, если была ошибки чтения файла 
     *     кэша или кэш не найден.
     */
    public static function getCache(string $channel, string $format): false|string
    {
        $filename = self::getFilename($channel, $format, true);
        if (file_exists($filename)) {
            return file_get_contents($filename, true);
        }
        return false;
    }

    /**
     * Создаёт кэш файла.
     *
     * @param string $content Содержимое ленты.
     * @param string $channel Название канала. Если значение `null`, то текущее название 
     *     канала (по умолчанию `null`).
     * @param string $format Формат канала. Если значение `null`, то текущий формат 
     *     (по умолчанию `null`).
     * 
     * @return bool Возвращает значение `false`, если ошибка создания кэша.
     */
    public function createCache(string $content, string $channel = null, string $format = null): bool
    {
        if ($channel === null) {
            $channel = $this->channel;
        }
        if ($format === null) {
            $format = $this->format;
        }

        $filename = self::getFilename($channel, $format, true);
        if (empty($filename)) {
            return false;
        }
        return file_put_contents($filename, $content) !== false;
    }

    /**
     * Если лента доступна.
     * 
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool) $this->enabled;
    }

    /**
     * Возвращает формат параметров ленты.
     * 
     * @return array
     */
    protected function getOptionsFormat(): array
    {
        return [];
    }

    /**
     * Возвращает формат вывода материалов ленты.
     * 
     * @return array
     */
    protected function getSelectorFormat(): array
    {
        return [
            'category' => ['type' => 'int'], // категория материала
            'limit'    => ['type' => 'int'] // количество записей
        ];
    }

    /**
     * Устанавливает значение атрибуту "options".
     * 
     * @param mixed $value Если значение `string`, то формат JSON.
     * 
     * @return void
     */
    public function setOptions(mixed $options): void
    {
        if ($options) {
            if (is_string($options)) {
                $options = Json::tryDecode($options);
            }
        } else
            $options = [];

        $result = [];
        $format = $this->getOptionsFormat();
        foreach ($format as $field => $params) {
            $type  = $params['type'];
            $value = $options[$field] ?? null;

            if ($type === 'date')
                $value = $value ? Gm::$app->formatter->toDate($value, 'Y-m-d', false, Gm::$app->dataTimeZone) : null;
            else
                settype($value, $type);
            $result[$field] = $value;
        }
        $this->attributes['options'] = $result;
    }

    /**
     * Возращает значение для сохранения в поле "options".
     * 
     * @return string
     */
    public function unOptions(): string
    {
        return json_encode((array) $this->options);
    }

    /**
     * Устанавливает значение атрибуту "selector".
     * 
     * @param null|array|string $value Если значение `string`, то формат JSON.
     * 
     * @return void
     */
    public function setSelector(array|string|null $selector): void
    {
        if ($selector) {
            if (is_string($selector)) {
                $selector = Json::tryDecode($selector);
            }
        } else
            $selector = [];

        $result = [];
        $format = $this->getSelectorFormat();
        foreach ($format as $field => $params) {
            $type  = $params['type'];
            $value = $selector[$field] ?? null;

            if ($type === 'date')
                $value = $value ? Gm::$app->formatter->toDate($value, 'Y-m-d', false, Gm::$app->dataTimeZone) : null;
            else
                settype($value, $type);
            $result[$field] = $value;
        }
        $this->attributes['selector'] = $result;
    }

    /**
     * Возращает значение для сохранения в поле "selector".
     * 
     * @return string
     */
    public function unSelector(): string
    {
        return json_encode((array) $this->selector);
    }

    /**
     * Возвращает конструкцию для ленты (теги ленты с атрибутами и их значениями).
     * 
     * @return array
     */
    public function getConstructs(): array
    {
        return [];
    }

    /**
     * Возвращает код языка согласно ISO 639.
     * 
     * @return string
     */
    public function getLanguage(): string
    {
        if ($this->languageId) {
            $language = Gm::$app->language->available->getBy($this->languageId, 'code');
            if ($language) {
                return $language['tag'];
            }
        }
        return '';
    }

    /**
     * Подготавливает элемент ленты для вывода.
     * 
     * @param array $row Параметры элемента.
     * 
     * @return array
     */
    protected function prepareItem(array $row): array
    {
        return $row;
    }

    /**
     * Возвращает элементы ленты с параметрами.
     * 
     * @return array
     */
    public function getItems(): array
    {
        /** @var Article $article */
        $article = new Article();
        /** @var array $where Условие запроса */
        $where = [
            'article.feed_enabled' => 1, // если материал хочет быть в ленте
            'article.publish'      => 1 // материал опубликован
        ];

        /** @var int $limit Количество записей в ленте */
        $limit = $this->selector['limit'] ?? 0;
        /** @var int $categoryId Категория материала */
        $categoryId = $this->selector['category'] ?? 0;
        if ($categoryId) {
            $where['category_id'] = $categoryId;
        }

        /** @var int $languageId Язык материала */
        if ($this->languageId) {
            $where['article.language_id'] = $this->languageId;
        }

        // все доступные языки
        $languages = Gm::$app->language->available->getAllBy('code');

        /** @var \Gm\Db\Sql\Select $select */
        $select = $article->selectJoinCategories(
            ['id', 'language_id', 'header', 'image', 'text', 'slug', 'slug_type', 'publish_date', '_updated_date', 'announce_plain'], 
            ['category_slug_path' => 'slug_path'],
            $where,
            ['publish_date' => 'DESC'],
            $limit
        );
        /** @var \Gm\Db\Adapter\Driver\AbstractCommand $command */
        $command = $this->getDb()->createCommand($select);
        $command->query();
        $items = [];
        while ($row = $command->fetch()) {
            // Язык
            if ($row['language_id'])
                $languageSlug = $languages[$row['language_id']]['slug'] ?? null;
            else
                $languageSlug = null;

            $slugType = $row['slug_type'] ?? 0;
            $slug = $row['slug'];
            if ($slugType == Article::SLUG_DYNAMIC && $slug) {
                $slug = Str::idToStr($slug, $row['id']);
            } 
            $row['url'] = Url::to([$row['category_slug_path'], 'basename' => $slug, 'langSlug' => $languageSlug]);

            $items[] = $this->prepareItem($row);
        }
        return $items;
    }
}
