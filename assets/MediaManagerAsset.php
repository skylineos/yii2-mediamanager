<?php

namespace skylineos\yii\mediamanager\assets;

use yii\web\AssetBundle;

class MediaManagerAsset extends AssetBundle
{
    public $sourcePath = '@vendor/skylineos/yii-mediamanager/assets';

    public $css = [
        '//use.fontawesome.com/releases/v5.15.3/css/all.css',
        '//cdnjs.cloudflare.com/ajax/libs/jstree/3.3.11/themes/default/style.min.css',
        '//cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.2/basic.min.css',
        'css/main.css',
    ];

    public $js = [
        '//cdn.jsdelivr.net/npm/sweetalert2@10',
        '//cdnjs.cloudflare.com/ajax/libs/jstree/3.3.8/jstree.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.2/min/dropzone.min.js',
        'js/tree.js',
        'js/dropzone.js',
        'js/main.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
