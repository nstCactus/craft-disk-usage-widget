<?php

namespace nstcactus\craftcms\diskUsageWidget;

use Craft;
use craft\base\Plugin as BasePlugin;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Dashboard;
use nstcactus\craftcms\diskUsageWidget\services\ShellCommandsService;
use nstcactus\craftcms\diskUsageWidget\widgets\DiskUsageWidget;
use yii\base\Event;

/**
 * @property-read ShellCommandsService $shellCommands
 */
class Plugin extends BasePlugin
{
    public function init(): void
    {
        parent::init();

        if (!Craft::$app->getRequest()->getIsCpRequest()) {
            return;
        }

        $this->_registerComponents();
        $this->_registerWidgets();
    }

    protected function _registerWidgets(): void
    {
        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            static function (RegisterComponentTypesEvent $event) {
                $event->types[] = DiskUsageWidget::class;
            }
        );
    }

    protected function _registerComponents(): void
    {
        $this->setComponents([
            'shellCommands' => ShellCommandsService::class
        ]);
    }
}
