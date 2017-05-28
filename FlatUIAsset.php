<?php

namespace tunecino\builder;

use yii\web\AssetBundle;

class FlatUIAsset extends AssetBundle
{
    public $sourcePath = '@bower/flat-ui/dist';
    public $css = [
        'css/flat-ui.css',
    ];
    public $js = [
        'js/flat-ui.js',
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
}
