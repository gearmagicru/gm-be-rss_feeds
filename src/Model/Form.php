<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\RssFeeds\Model;

use Gm;
use Gm\Helper\Json;
use Gm\Panel\Data\Model\FormModel;

/**
 * Модель данных профиля RSS-канала.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\RssFeeds\Model
 * @since 1.0
 */
class Form extends FormModel
{
    use FeedCache;

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
                [ // дата публикации
                    'published', 
                    'label' => 'Date of publication',
                ], 
                [ // название канала
                    'channel', 
                    'label'  => 'Channel', 
                    'unique' => true
                ], 
                [ // заголовок 
                    'title',
                    'label' => 'Title', 
                ],
                [ // описание
                    'description',
                    'label' => 'Description', 
                ],
                [ // доступен
                    'enabled',
                    'label' => 'Enabled', 
                ],
                [ // кэширование
                    'caching',
                    'label' => 'Caching', 
                ],
                [ // формат
                    'format',
                    'label' => 'Format', 
                ], 
                [ // язык
                    'language_id',
                    'alias' => 'language', 
                    'label' => 'Language'
                ],
                ['options'], // параметры канала
                ['selector'] // параметры вывода материала
            ],
            // правила форматирования полей
            'formatterRules' => [
                [['channel', 'description'], 'safe'],
                [['enabled', 'caching'], 'logic']
            ],
            // правила валидации полей
            'validationRules' => [
                [['channel', 'title', 'description'], 'notEmpty'],
                // название канала
                [
                    'channel',
                    'between',
                    'max' => 255, 'type' => 'string'
                ],
                // заголовок
                [
                    'title',
                    'between',
                    'max' => 255, 'type' => 'string'
                ],
                [
                    'description',
                    'between',
                    'max' => 255, 'type' => 'string'
                ],
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
                /** @var \Gm\Panel\Controller\GridController $controller */
                $controller = $this->controller();
                // если изменение записи
                if (!$this->isInsert) {
                    if (!$this->dropCache()) {
                        // всплывающие сообщение
                        $this->response()
                            ->meta
                                ->cmdPopupMsg(
                                    $this->module->t('Unable to delete cache ("{0}") of RSS feed', [
                                        self::getCacheFile($this->channel, $this->format, false)
                                    ]), 
                                    $message['title'], 'error'
                                );
                    }
                }
                // всплывающие сообщение
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']);
                // обновить список
                $controller->cmdReloadGrid();
            })
            ->on(self::EVENT_BEFORE_DELETE, function (&$canDelete) {
                if (!$this->dropCache()) {
                    $canDelete = false;
                    // всплывающие сообщение
                    $this->response()
                        ->meta
                            ->cmdPopupMsg(
                                $this->module->t('Unable to delete cache ("{0}") of RSS feed', [
                                    self::getCacheFile($this->channel, $this->format, false)
                                ]), 
                                Gm::t(BACKEND, 'Deletion'), 'error'
                            );
                }
            })
            ->on(self::EVENT_AFTER_DELETE, function ($result, $message) {
                /** @var \Gm\Panel\Controller\GridController $controller */
                $controller = $this->controller();
                // всплывающие сообщение
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']);
                // обновить список
                $controller->cmdReloadGrid();
            });
    }

    /**
     * {@inheritdoc}
     */
    public function beforeLoad(array &$data): void
    {
        // дата и время публикации
        if (empty($data['published']) && empty($data['publishedTime'])) {
            $data['published'] = null;
        } else {
            $data['published'] = [
                $data['published'] ?? '', 
                $data['publishedTime'] ?? ''
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionMessages(): array
    {
        return [
            'titleAdd'           => $this->module->t('Adding RSS feed'),
            'titleUpdate'        => $this->module->t('Update RSS feed'),
            'titleDelete'        => Gm::t(BACKEND, 'Deletion'),
            'msgSuccessAdd'      => $this->module->t('RSS feed successfully added'),
            'msgUnsuccessAdd'    => $this->module->t('Unable to add RSS feed'),
            'msgSuccessUpdate'   => $this->module->t('RSS feed successfully update'),
            'msgUnsuccessUpdate' => $this->module->t('Unable to update RSS feed'),
            'msgSuccessDelete'   => $this->module->t('RSS feed successfully deleted'),
            'msgUnsuccessDelete' => $this->module->t('Unable to delete RSS feed'),
        ];
    }

    /**
     * Возвращает формат параметров RSS-канала.
     * 
     * @return array
     */
    protected function getOptionsFormat(): array
    {
        return [
            'image'          => ['type' => 'string'], // изображение
            'icon'           => ['type' => 'string'], // значок
            'copyright'      => ['type' => 'string'], // авторские права
            'webmaster'      => ['type' => 'string'], // e-mail веб-мастера
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
            'cloudProtocol'  => ['type' => 'string'], // протокол в облаке
            'authorName'     => ['type' => 'string'], // имя автора канала
            'authorUri'      => ['type' => 'string'], // адрес страницы автора
            'authorEmail'    => ['type' => 'string'], // e-mail автора
            'contributorName'  => ['type' => 'string'], // имя соавтора канала
            'contributorUri'   => ['type' => 'string'], // адрес страницы соавтора
            'contributorEmail' => ['type' => 'string'], // e-mail соавтора
        ];
    }

    /**
     * Возвращает формат вывода материалов RSS-канала.
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
     * Устанавливает значение атрибуту "language".
     * 
     * @param null|string|int $value
     * 
     * @return void
     */
    public function setLanguage($value)
    {
        $value = (int) $value;
        $this->attributes['language'] = $value === 0 ? null : $value;
    }

    /**
     * Возвращает значение параметра "language" для элемента интерфейса формы.
     * 
     * @param null|string|int $value
     * 
     * @return array
     */
    public function outLanguage($value): array
    {
        $language = $value ? Gm::$app->language->available->getBy($value, 'code') : null;
        if ($language) {
            return [
                'type'  => 'combobox', 
                'value' => $language['code'],
                'text'  => $language['shortName'] . ' (' . $language['tag'] . ')'
            ];
        }
        return [
            'type'  => 'combobox',
            'value' => 0,
            'text'  => Gm::t(BACKEND, '[None]')
        ];       
    }

    /**
     * Возвращает значение атрибутов "options[...]" для элементов интерфейса формы.
     * 
     * @param null|array $value
     * 
     * @return null|array
     */
    public function outOptions($value): ?array
    {
        if ($value) {
            if (isset($value['language'])) {
                $value['language'] = $this->outLanguage($value['language']);
            }

            foreach ($value as $field => $fieldValue) {
                $this->attributes["options[$field]"] = $fieldValue;
            }
        }
        return null;
    }

    /**
     * Устанавливает значение атрибуту "options".
     * 
     * @param null|array|string $value Если значение `string`, то формат JSON.
     * 
     * @return void
     */
    public function setOptions($options)
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
     * Возвращает значение параметра "category" элементу интерфейса формы.
     * 
     * @param null|string|int $value
     * 
     * @return array
     */
    public function outArticleCategory($value): array
    {
        /** @var \Gm\Backend\ArticleCategories\Model\Category $category */
        $category = $value ? Gm::$app->modules->getModel('Category', 'gm.be.article_categories') : null;
        $category = $category ? $category->selectByPk($value) : null;
        if ($category) {
            return [
                'type'  => 'combobox', 
                'value' => $category->id, 
                'text'  => $category->name
            ];
        }
        return [
            'type'  => 'combobox',
            'value' => 0,
            'text'  => Gm::t(BACKEND, '[None]')
        ]; 
    }

    /**
     * Возвращает значение атрибутов "selector[...]" для элементов интерфейса формы.
     * 
     * @param null|array $value
     * 
     * @return null|array
     */
    public function outSelector($value): ?array
    {
        if ($value) {
            if (isset($value['category'])) {
                $value['category'] = $this->outArticleCategory($value['category']);
            }

            foreach ($value as $field => $fieldValue) {
                $this->attributes["selector[$field]"] = $fieldValue;
            }
        }
        return null;
    }

    /**
     * Устанавливает значение атрибуту "selector".
     * 
     * @param null|array|string $value Если значение `string`, то формат JSON.
     * 
     * @return void
     */
    public function setSelector($selector)
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
     * Возвращает значение атрибута "published" элементу интерфейса формы.
     * 
     * @return null|array
     */
    protected function unPublished(): ?string
    {
        $value = $this->published;

        /** @var \Gm\Db\Adapter\Platform\AbstractPlatform $platform */
        $platform = Gm::$app->db->getPlatform();
        /** @var \Gm\I18n\Formatter $formatter Форматер */
        $formatter = Gm::$app->formatter;
        /** @var \DateTimeZone $dataTZ Часовой пояс на сервере хранилища*/
        $dataTZ = Gm::$app->dataTimeZone;
        /** @var \DateTimeZone $userTZ Часовой пояс пользователя */
        $userTZ = Gm::$app->user->getTimeZone();

        $date = $time = '';
        if ($value) {
            if (is_string($value)) {
                $value = explode(' ', $value);
            }
            if (is_array($value)) {
                $date = $value[0] ?? '';
                $time = $value[1] ?? '';
            }
        }

        // если указана дата публикации
        if ($date) {
            if ($time)
                $date = $date . ' ' . $time;
            else
                $date = $date . ' ' . $formatter->toTime('now', $platform->timeFormat, false, $userTZ);
        // если не указана дата публикации
        } else {
            if ($time)
                $date = $formatter->toDate('now', $platform->dateFormat, false, $userTZ) . ' ' . $time;
            else
                $date = $formatter->toDateTime('now', $platform->dateTimeFormat, false, $userTZ);
        }
        // переводим из часового пояса пользователя в часовой пояс сервера
        return $formatter->toDateTimeZone($date, $platform->dateTimeFormat, false, $userTZ, $dataTZ);
    }

    /**
     * Возвращает значение атрибутов: "published", "publishedTime" для элементов 
     * интерфейса формы.
     * 
     * @param null|string $value
     * 
     * @return null|string
     */
    public function outPublished($value): ?string
    {
        if ($value) {
            // преобразование из UTC в текущий часовой пояс
            $datetime = Gm::$app->formatter->toDateTime($value, 'php:Y-m-d_H:i:s', true, Gm::$app->user->getTimeZone());
            $datetime = explode('_', $datetime);
            $value = $datetime[0];
            $this->attributes['publishedTime'] = $datetime[1];
        }
        return $value;
    }
}
