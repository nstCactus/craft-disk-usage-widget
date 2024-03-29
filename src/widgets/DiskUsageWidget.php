<?php

namespace nstcactus\craftcms\diskUsageWidget\widgets;

use Craft;
use craft\base\Widget;
use craft\helpers\StringHelper;
use nstcactus\craftcms\diskUsageWidget\assets\AssetBundle;
use nstcactus\craftcms\diskUsageWidget\exceptions\ShellCommandException;
use nstcactus\craftcms\diskUsageWidget\helpers\FilesizeHelper;
use nstcactus\craftcms\diskUsageWidget\Plugin;

class DiskUsageWidget extends Widget
{
    public ?string $directory = null;
    public ?string $softLimit = null;
    public bool $areQuotasUsed = false;
    public bool $overrideSoftLimit = false;
    public ?string $partition = null;

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
        if ($this->areQuotasUsed) {
            return Craft::t(
                'disk-usage-widget',
                'Disk usage in the {partition} partition',
                ['partition' => $this->partition]
            );
        }

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
        Craft::$app->getView()->registerAssetBundle(AssetBundle::class);

        $id = StringHelper::randomString();
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);
        $namespace = Craft::$app->getView()->getNamespace();

        Craft::$app->getView()->registerJs("new DiskUsageWidget('$namespacedId', '$namespace');");

        // Get available partitions for quota mode
        $partitions = $this->getAvailablePartitionsForQuotaMode();

        return Craft::$app->view->renderTemplate('disk-usage-widget/settings.twig', [
            'widget' => $this,
            'id' => $id,
            'namespace' => $namespace,
            'partitions' => $partitions ?? [],
        ]);
    }

    public function getBodyHtml(): string
    {
        Craft::$app->getView()->registerAssetBundle(AssetBundle::class);

        if ($this->areQuotasUsed) {
            return $this->renderUsingQuotas();
        }

        return $this->renderUsingPhpBuiltInFunctions();
    }

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = ['directory', 'required'];
        $rules[] = [
            'directory',
            function ($attribute) {
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
            }
        ];

        return $rules;
    }

    protected function renderError(string $error): string
    {
        return Craft::$app->view->renderTemplate('disk-usage-widget/error.twig', [
            'error' => $error
        ]);
    }

    protected function renderUsingQuotas(): string
    {
        try {
            $output = Plugin::getInstance()->shellCommands->executeShellCommand('quota -s -w -p');

            if (!preg_match(
                "|\s*$this->partition\s+(?<used>.+?)\s+(?<quota>.+?)\s+(?<limit>.+?)\s|",
                $output,
                $matches,
            )) {
                return $this->renderError(
                    'An error occurred while parsing the output of the <code>quota -s -w -p</code> command.'
                );
            }

            ['used' => $used, 'quota' => $softLimit, 'limit' => $total] = $matches;

            if ($this->overrideSoftLimit && $this->softLimit) {
                $softLimit = $this->softLimit;
            }

            $total = FilesizeHelper::toMachineReadable($total);
            $used = FilesizeHelper::toMachineReadable($used);
            $softLimit = FilesizeHelper::toMachineReadable($softLimit);

            return Craft::$app->view->renderTemplate('disk-usage-widget/body.twig', [
                'widget' => $this,
                'usedPercentage' => $used / $total,
                'softLimitPercentage' => $softLimit / $total,
                'used' => FilesizeHelper::toHumanReadable($used),
                'total' => FilesizeHelper::toHumanReadable($total),
                'softLimit' => FilesizeHelper::toHumanReadable($softLimit),
                'isOverSoftLimit' => $used > $softLimit,
            ]);
        } catch (ShellCommandException $e) {
            return $this->renderError($e->getMessage());
        }
    }

    protected function renderUsingPhpBuiltInFunctions(): string
    {
        if (!file_exists($this->directory)) {
            return $this->renderError("The <code>$this->directory</code> directory doesn't exist.");
        }

        $free = disk_free_space($this->directory);
        $total = disk_total_space($this->directory);

        if (!$free || !$total) {
            return $this->renderError(
                "Couldn't get the free and/or total space on the partition containing the $this->directory directory."
            );
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

    protected function getAvailablePartitionsForQuotaMode(): array
    {
        try {
            $output = Plugin::getInstance()->shellCommands->executeShellCommand('quota -s -w -p');

            if (preg_match_all(
                "/^\\s*(?<partitions>\\/.+?)\\s+\\d/m",
                $output,
                $matches,
            )) {
                $partitions = $matches['partitions'];

                return array_combine($partitions, $partitions);
            }
        } catch (ShellCommandException $e) {
        }

        return [];
    }
}
