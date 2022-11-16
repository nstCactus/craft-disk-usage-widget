<?php

namespace nstcactus\craftcms\diskUsageWidget\assets;

use craft\web\AssetBundle as BaseAssetBundle;
use craft\web\assets\cp\CpAsset;

class AssetBundle extends BaseAssetBundle
{
    /**
     * @inheritdoc
     */
    public function init(): void
    {
        $this->sourcePath = __DIR__ . '/dist';

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'disk-usage-widget.js',
        ];

        $this->css = [
            'disk-usage-widget.css',
        ];

        parent::init();

    }
}
