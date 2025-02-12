<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\RssFeeds\Controller;

use Gm;
use Gm\Mvc\Module\BaseModule;
use Gm\Panel\Widget\TabGrid;
use Gm\Panel\Helper\ExtGrid;
use Gm\Panel\Helper\HtmlGrid;
use Gm\Panel\Helper\HtmlNavigator as HtmlNav;
use Gm\Panel\Controller\GridController;

/**
 * Контроллер списка RSS-каналов сайта.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\RssFeeds\Controller
 * @since 1.0
 */
class Grid extends GridController
{
    /**
     * {@inheritdoc}
     * 
     * @var BaseModule|\Gm\Backend\RssFeeds\Extension
     */
    public BaseModule $module;

    /**
     * {@inheritdoc}
     */
    public function createWidget(): TabGrid
    {
        /** @var TabGrid $tab Сетка данных (Gm.view.grid.Grid GmJS) */
        $tab = parent::createWidget();

        $formats = Gm::$app->formatter->formatsWithoutPrefix();
        // столбцы (Gm.view.grid.Grid.columns GmJS)
        $tab->grid->columns = [
            ExtGrid::columnNumberer(),
            ExtGrid::columnAction(),
            [
                'xtype'     => 'templatecolumn',
                'text'      => '#Date of publication',
                'align'     => 'center',
                'tooltip'   => '#Date of publication of the RSS feed',
                'dataIndex' => 'published',
                'tpl'       => '{published:date("' . $formats['dateTimeFormat'] . '")}',
                'filter'    => ['type' => 'date', 'dateFormat' => 'Y-m-d'],
                'width'     => 140
            ],
            [
                'text'    => ExtGrid::columnInfoIcon($this->t('Title')),
                'cellTip' => HtmlGrid::tags([
                    HtmlGrid::header('{title:ellipsis(50)}'),
                    HtmlGrid::fieldLabel($this->t('Description'), '{description}'),
                    HtmlGrid::fieldLabel($this->t('Channel'), '{channel}'),
                    HtmlGrid::fieldLabel($this->t('Format'), '{format}'),
                    HtmlGrid::fieldLabel($this->t('Language'), '{languageName}'),
                    HtmlGrid::fieldLabel($this->t('Date of publication'), '{published:date("' . $formats['dateTimeFormat'] . '")}'),
                    HtmlGrid::fieldLabel($this->t('Enabled'), HtmlGrid::tplChecked('enabled==1')),
                    HtmlGrid::fieldLabel($this->t('RSS feed caching'), HtmlNav::tplChecked('caching==1'))
                ]),
                'dataIndex' => 'title',
                'filter'    => ['type' => 'string'],
                'width'     => 280
            ],
            [
                'text'      => '#Description',
                'dataIndex' => 'description',
                'filter'    => ['type' => 'string'],
                'width'     => 200
            ],
            [
                'text'      => '#Channel',
                'tooltip'   => '#RSS feed name in URL',
                'dataIndex' => 'channel',
                'filter'    => ['type' => 'string'],
                'width'     => 150
            ],
            [
                'text'      => '#Format',
                'tooltip'   => '#Web resource formatL',
                'dataIndex' => 'format',
                'filter'    => ['type' => 'string'],
                'width'     => 120
            ],
            [
                'text'      => '#Language',
                'dataIndex' => 'languageName',
                'width'     => 120
            ],
            [
                'text'        => ExtGrid::columnIcon('g-icon-m_visible', 'svg'),
                'xtype'       => 'g-gridcolumn-switch',
                'tooltip'     => '#Enabled',
                'selector'    => 'grid',
                'collectData' => ['title'],
                'dataIndex'   => 'enabled'
            ],
            [
                'xtype'     => 'g-gridcolumn-checker',
                'text'      => ExtGrid::columnIcon('gm-rssfeeds__icon-cache', 'svg'),
                'tooltip'   => '#RSS feed caching',
                'dataIndex' => 'caching',
                'filter'    => ['type' => 'boolean']
            ],
            [
                'xtype'    => 'templatecolumn',
                'dataIndex' => 'url',
                'sortable' => false,
                'width'    => 45,
                'align'    => 'center',
                'tpl'      => HtmlGrid::a(
                    '', 
                    $this->module->getRssUrl('/{channel}'),
                    [
                        'title' => $this->t('View RSS'),
                        'class' => 'g-icon g-icon-svg g-icon_size_14 g-icon-m_link g-icon-m_color_default g-icon-m_is-hover',
                        'target' => '_blank'
                    ]
                )
            ]
        ];

        // если не установлен модуль "Публикация RSS-ленты", тогда убираем столбец ссылок
        if (!Gm::$app->modules->getRegistry()->has('gm.fe.rss')) {
            array_pop($tab->grid->columns);            
        }

        // панель инструментов (Gm.view.grid.Grid.tbar GmJS)
        $tab->grid->tbar = [
            'padding' => 1,
            'items'   => ExtGrid::buttonGroups([
                'edit' => [
                    'items' => [
                        // инструмент "Добавить"
                        'add' => [
                            'iconCls' => 'g-icon-svg gm-rssfeeds__icon-add',
                            'tooltip' => '#Adding a new RSS feed',
                            'caching' => false
                        ],
                        // инструмент "Удалить"
                        'delete' => [
                            'iconCls'       => 'g-icon-svg gm-rssfeeds__icon-delete',
                            'tooltip'       => '#Delete selected RSS feeds',
                            'msgConfirm'    => '#Are you sure you want to RSS feeds - {0}?',
                            'msgMustSelect' => '#You need to select RSS feeds'
                        ],
                        'cleanup' => [
                            'msgConfirm' => '#Are you sure you want to delete all RSS feeds?',
                            'tooltip'    => '#Deleting all RSS feeds',
                        ],
                        '-',
                        'edit',
                        'select',
                        '-',
                        'refresh'
    
                    ]
                ],
                'columns',
                'search'
            ])
        ];

        // контекстное меню записи (Gm.view.grid.Grid.popupMenu GmJS)
        $tab->grid->popupMenu = [
            'cls'        => 'g-gridcolumn-popupmenu',
            'titleAlign' => 'center',
            'width'      => 150,
            'items'      => [
                [
                    'text'        => '#Edit RSS',
                    'iconCls'     => 'g-icon-svg g-icon-m_edit g-icon-m_color_default',
                    'handlerArgs' => [
                          'route'   => Gm::alias('@match', '/form/view/{id}'),
                          'pattern' => 'grid.popupMenu.activeRecord'
                      ],
                      'handler' => 'loadWidget'
                ]
            ]
        ];

        // 2-й клик на строке сетки
        $tab->grid->rowDblClickConfig = [
            'allow' => true,
            'route' => Gm::alias('@match', '/form/view/{id}')
        ];
        // сортировка сетки по умолчанию
        $tab->grid->sorters = [
           ['property' => 'title', 'direction' => 'ASC']
        ];
        // количество строк в сетке
        $tab->grid->store->pageSize = 50;
        // поле аудита записи
        $tab->grid->logField = 'title';
        // плагины сетки
        $tab->grid->plugins = 'gridfilters';
        // класс CSS применяемый к элементу body сетки
        $tab->grid->bodyCls = 'g-grid_background';

        // панель навигации (Gm.view.navigator.Info GmJS)
        $tab->navigator->info['tpl'] = HtmlNav::tags([
            HtmlNav::header('{title}'),
            HtmlNav::fieldLabel($this->t('Description'), '{description}'),
            HtmlNav::fieldLabel($this->t('Channel'), '{channel}'),
            HtmlNav::fieldLabel($this->t('Format'), '{format}'),
            HtmlNav::fieldLabel($this->t('Language'), '{languageName}'),
            HtmlNav::fieldLabel($this->t('Date of publication'), '{published:date("' . $formats['dateTimeFormat'] . '")}'),
            HtmlNav::fieldLabel(
                ExtGrid::columnIcon('g-icon-m_visible', 'svg') . ' ' . $this->t('Enabled'), 
                HtmlNav::tplChecked('enabled==1')
            ),
            HtmlNav::fieldLabel(
                ExtGrid::columnIcon('gm-rssfeeds__icon-cache', 'svg') . ' ' . $this->t('RSS feed caching'), 
                HtmlNav::tplChecked('caching==1')
            ),
            HtmlNav::widgetButton(
                $this->t('Edit RSS'),
                ['route' => Gm::alias('@match', '/form/view/{id}'), 'long' => true],
                ['title' => $this->t('Edit RSS')]
            ),
            HtmlNav::linkButton(
                $this->t('View'),
                ['long' => true],
                ['title' => $this->t('View RSS feed'), 'href' => $this->module->getRssUrl('/{channel}'), 'target' => '_blank']
            )
        ]);

        $tab
            ->addCss('/grid.css')
            ->addRequire('Gm.view.grid.column.Switch');
        return $tab;
    }
}
