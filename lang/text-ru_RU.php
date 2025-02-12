<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * Пакет русской локализации.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    '{name}'        => 'Управление RSS-каналами',
    '{description}' => 'Управление RSS-каналами вашего сайта',
    '{permissions}' => [
        'any'    => ['Полный доступ', 'Просмотр и внесение изменений в RSS-каналы'],
        'view'   => ['Просмотр', 'Просмотр RSS-каналов'],
        'read'   => ['Чтение', 'Чтение каналов RSS-каналов'],
        'add'    => ['Добавление', 'Добавление RSS-канала'],
        'edit'   => ['Изменение', 'Изменение RSS-канала'],
        'delete' => ['Удаление', 'Удаление RSS-канала'],
        'clear'  => ['Очистка', 'Удаление всех RSS-каналов']
    ],

    // Grid: панель инструментов
    'Adding a new RSS feed' => 'Добавление нового RSS-канала',
    'Delete selected RSS feeds' => 'Удаление выделенных RSS-каналов',
    'Are you sure you want to RSS feeds - {0}?' => 'Вы уверены, что хотите удалить RSS-канал(ы) - <b>{0}</b> ?',
    'You need to select RSS feeds' => 'Вам необходимо выбрать RSS-каналы.',
    'Are you sure you want to delete all RSS feeds?' => 'Вы действительно хотите удалить все RSS-каналы?',
    'Deleting all RSS feeds' => 'Удаление всех RSS-каналов',
    // Grid: контекстное меню записи
    'Edit RSS' => 'Редактировать',
    // Grid: столбцы
    'Date of publication' => 'Дата публикации',
    'Date of publication of the RSS feed' => 'Дата публикации RSS-канала',
    'Title' => 'Заголовок',
    'RSS feed name' => 'Название RSS-канала',
    'Description' => 'Описание',
    'Channel' => 'Канал',
    'RSS feed name in URL' => 'Имя RSS-канала в URL-адресе',
    'View' => 'Просмотреть',
    'View RSS' => 'Просмотреть RSS-канал',
    'Enabled' => 'Доступен',
    'Caching' => 'Кэширование',
    'RSS feed caching' => 'Кэширование RSS-канала',
    'Format' => 'Формат',
    'Web resource format' => 'Формат веб-ресурса',
    // GridRow: сообщения
    'The RSS feed "{0}" has been enabled' => 'RSS-канал "<b>{0}</b>" включён.',
    'The RSS feed "{0}" has been disabled' => 'RSS-канал "<b>{0}</b>" отключён.',
    // GridRow: сообщения / заголовки
    'Show' => 'Показать',
    'Hide' => 'Скрыть',

    // Form
    '{form.title}' => 'Добавление RSS-канала',
    '{form.titleTpl}' => 'Изменение RSS-канала "{title}"',
    // Form: поля
    'Channel options' => 'Параметры канала',
    'Language' => 'Язык',
    'Language of RSS feed material' => 'Язык материала RSS-канала',
    'Image' => 'Изображение',
    'GIF, JPEG or PNG image, RSS feed' => 'Изображение GIF, JPEG или PNG',
    'Copyright' => 'Авторские права',
    'Copyright notice for channel content' => 'Уведомление об авторских правах на материал канала',
    'E-mail editor' => 'E-mail редактора',
    'Email address of the person responsible for editorial content' => 'Адрес электронной почты лица, ответственного за редакционный контент',
    'E-mail webmaster' => 'E-mail веб-мастера',
    'Email address of the person responsible for technical issues related to the channel' 
        => 'Адрес электронной почты лица, ответственного за технические проблемы, связанные с каналом',
    'Publication date' => 'Дата публикации',
    'Date of publication of content on the channel' => 'Дата публикации материала на канале',
    'Last modified date' => 'Последняя дата изменения',
    'Last date of channel content modification' => 'Последняя дата изменения контента канала',
    'Category' => 'Категория',
    'One or more categories to which the channel belongs' => 'Одна или несколько категорий, к которым принадлежит канал',
    'Documentation URL' => 'URL-адрес документации',
    'URL pointing to documentation on the format used in the RSS file' => 'URL-адрес, указывающий на документацию по формату, используемому в файле RSS',
    'Channel lifetime, min.' => 'Время жизни канала, мин.',
    'A number of minutes that indicates how long a feed can be cached before being updated from the source' 
        => 'Количество минут, которое указывает, как долго канал может быть кэширован перед обновлением из источника',
    'PICS channel rating' => 'PICS-рейтинг канала',
    'Skip hours' => 'Пропускать часы',
    'Hint for aggregators telling them which hours (GMT) they can skip' 
        => 'Подсказка для агрегаторов, сообщающая им, какие часы (время по Гринвичу) можно пропустить',
    'Skip days' => 'Пропускать дни',
    '[None]' => '[ без выбора ]',
    'Monday' => 'Понедельник', 
    'Tuesday' => 'Вторник', 
    'Wednesday' => 'Среда', 
    'Thursday' => 'Четверг', 
    'Friday' => 'Пятница', 
    'Saturday' => 'Суббота', 
    'Sunday' => 'Воскресенье',
    'Hint for aggregators telling them which days can be skipped' 
        => 'Подсказка для агрегаторов, сообщающая им, какие дни можно пропустить',
    'Channel content output options' => 'Параметры вывода материала канала',
    'Cloud registration:' => 'Регистрация RSS-канала в облаке',
    'Domain' => 'Домен',
    'Port' => 'Порт',
    'Path' => 'Путь',
    'Procedure' => 'Процедура',
    'Protocol' => 'Протокол',
    'Icon' => 'Значок (пиктограмма)',
    'Author of the RSS feed:' => 'Автор RSS-канала',
    'Author\'s name' => 'Имя автора',
    'URL address' => 'URL-адрес',
    'Contributor name' => 'Имя соавтора',
    'Contributor of the RSS feed:' => 'Соавтор RSS-канала',
    'Category of material' => 'Категория материала',
    'Category of material, section in which the material is located'  => 'Категория материала, раздел в котором находиться материал',
    'Number of records' => 'Количество записей',
    'Number of entries displayed in the RSS feed' => 'Количество записей выводимых в RSS-канале. Если "0" - без ограничений.',
    'Date of publication of the RSS feed:' => 'Дата публикации RSS-канала:',
    // Form: сообщения
    'RSS feed successfully added' => 'RSS-канал успешно добавлен.',
    'Unable to add RSS feed' => 'Невозможно выполнить добавление RSS-канала.',
    'RSS feed successfully update' => 'RSS-канал успешно изменён.',
    'Unable to update RSS feed' => 'Невозможно выполнить изменение RSS-канала.',
    'RSS feed successfully deleted' => 'RSS-канал успешно удалён.',
    'Unable to delete RSS feed' => 'Невозможно выполнить удаление RSS-канала.',
    'Unable to delete cache ("{0}") of RSS feed' => 'Невозможно удалить кэш ("{0}") RSS-канала.',
    'Date' => 'Дата',
    'Time' => 'Время',
    // Form: сообщения / заголовки
    'Update RSS feed' => 'Изменение RSS-канала',
    'Adding RSS feed' => 'Добавление RSS-канала'
];
