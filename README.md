## Installation

`composer require skylineos/yii-mediamanager:~1.0`

## Yii2 Configuration

Add the module (per the configuration below) and you should be able to access the Media Manager at /mediamanager

```php
'modules' => [
    ...
    'mediamanager' => [
        'class' => 'skylineos\yii\mediamanager\Module',

        // To control authorization
        'accessRoles' => ['@'],
        
        // Layout if you wish to specify
        'layout' => '@vendor/skylineos/yii/mediamanager/views/layouts/main.php',
        
        // Adapter definitions below - only pick one at a time.
        
        // For AWS S3
        'adapter' => 's3',
        'configuration' => [
            'bucket' => 'my-bucket',
            'region' => 'my-region',
            'prefix' => 'my-prefix',
        ],

        // For local filesystem
        'adapter' => 'local',
        'configuration' => [
            'directory' => 'path/to/your/files',
        ],
    ],
]
```

## Fileinput Widget

To use the media manager as a file input (eg. in an active form):

```php
<?php

use skylineos\yii\mediamanager\widgets\FileInput;
use skylineos\yii\mediamanager\widgets\MediaManagerModal;

?>

<?= FileInput::widget([
    'model' => $model,
    'attribute' => 'image',
    'label' => 'Image',
    ]) ?>

<?= MediaManagerModal::widget([]) ?>    
```

## TinyMce Integration

Integration w/ Tinymce is very similar to the FileInput. TinyMce setup is consistant with the TinyMce official 
configuration and matches previous previous Yii2 integrations. The example below shows this setup in some detail 
however, the only point that concerns this integration is `'plugins' => ['media', 'image']`. You can 
configure them on the toolbar however you like.

```php
<?php 

use skylineos\yii\mediamanager\widgets\TinyMce;
use skylineos\yii\mediamanager\widgets\MediaManagerModal;

?>

<?= $form->field($model, 'content')->widget(TinyMce::className(), [
    'options' => ['rows' => 15],
    'clientOptions' => [
        'plugins' => [
            "advlist autolink lists link charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table contextmenu paste image"
        ],
        'menubar' => 'edit insert view format table tools help',
        'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
    ]
]); ?>

<?= MediaManagerModal::widget([]) ?>    
```