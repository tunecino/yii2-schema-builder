<?php

namespace tunecino\builder;

use yii\web\AssetBundle;

class BuilderAsset extends AssetBundle
{
    public $sourcePath = '@tunecino/builder/assets';
    public $css = [
        'site.css',
        'main.css',
    ];
    public $js = [
        'main.js',
        'console.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'tunecino\builder\FlatUIAsset',
    ];
}
