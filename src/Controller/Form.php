<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\RssFeeds\Controller;

use Gm;
use Gm\Mvc\Module\BaseModule;
use Gm\Panel\Widget\EditWindow;
use Gm\Panel\Controller\FormController;

/**
 * Контроллер формы RSS-канала.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\RssFeeds\Controller
 * @since 1.0
 */
class Form extends FormController
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
    public function createWidget(): EditWindow
    {
        /** @var EditWindow $window */
        $window = parent::createWidget();

        // панель формы (Gm.view.form.Panel GmJS)
        $window->form->loadJSONFile('/form', 'items');
        $window->form->bodyPadding = 10;
        $window->form->autoScroll = true;
        $window->form->defaults = [
            'labelWidth' => 130,
            'labelAlign' => 'right',
            'allowBlank' => false, 
            'anchor'     => '100%'
        ];

        // окно компонента (Ext.window.Window Sencha ExtJS)
        $window->width = 500;
        $window->height = 700;
        $window->resizable = false;
        $window->layout = 'fit';
        $window->responsiveConfig = [
            'height < ' . $window->height => ['height' => '100%'],
            'width < ' . $window->width => ['width' => '100%']
        ];
        return $window;
    }
}
