<?php

namespace nstcactus\craftcms\diskUsageWidget\widgets;

use Craft;
use craft\base\Widget;
use nstcactus\craftcms\diskUsageWidget\helpers\FilesizeHelper;

class DiskUsageWidget extends Widget
{
    public ?string $directory = null;
    public ?string $softLimit = null;

    public static function displayName(): string
    {
        return Craft::t('disk-usage-widget', 'Disk usage');
    }

    public function getTitle(): string
    {
        return Craft::t('disk-usage-widget', 'Disk usage');
    }

    public function getSubtitle(): ?string
    {
        return Craft::t(
            'disk-usage-widget',
            'Disk usage in the {directory} directory',
            ['directory' => $this->directory]
        );
    }

    public static function icon(): string
    {
        return '@nstcactus/craftcms/diskUsageWidget/resources/hdd.svg';
    }

    public function getSettingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate('disk-usage-widget/settings.twig', [
            'widget' => $this,
        ]);
    }

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = ['directory', 'required'];
        $rules[] = ['directory', function($attribute) {
            if (realpath($this->directory) === false) {
                $this->addError(
                    $attribute,
                    Craft::t(
                        'disk-usage-widget',
                        'This field must be the path to an existing directory on the server',
                        ['attribute' => $attribute]
                    )
                );
            }
        }];

        return $rules;
    }


    public function getBodyHtml(): string
    {
        if (!file_exists($this->directory)) {
            return $this->renderError("The <code>$this->directory</code> directory doesn't exist.");
        }

        $free = disk_free_space($this->directory);
        $total = disk_total_space($this->directory);

        if (!$free || !$total) {
            return $this->renderError("Couldn't get the free and/or total space on the partition containing the $this->directory directory.");
        }

        $used = $total - $free;
        $softLimit = FilesizeHelper::toMachineReadable($this->softLimit ?? '0') ?: $total;
        $isOverSoftLimit = $used >= $softLimit;

        return Craft::$app->view->renderTemplate('disk-usage-widget/body.twig', [
            'widget' => $this,
            'usedPercentage' => $used / $total,
            'softLimitPercentage' => $softLimit / $total,
            'used' => FilesizeHelper::toHumanReadable($used),
            'total' => FilesizeHelper::toHumanReadable($total),
            'softLimit' => FilesizeHelper::toHumanReadable($softLimit),
            'isOverSoftLimit' => $isOverSoftLimit,
        ]);
    }

    public function renderError(string $error): string
    {
        return Craft::$app->view->renderTemplate('disk-usage-widget/error.twig', [
            'error' => $error
        ]);
    }
}
