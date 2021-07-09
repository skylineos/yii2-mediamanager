<?php

namespace skylineos\yii\mediamanager\widgets;

use yii\base\Widget;
use skylineos\yii\mediamanager\Module as skyS3Module;
use skylineos\yii\mediamanager\assets\MediaManagerAsset;

class MediaManagerModal extends Widget
{
    /**
     * This should be set here, in params, or in the module config
     *
     * @var string $s3bucket The s3 bucket to use.
     */
    public $s3bucket;

    /**
     * This should be set here, in params, or in the module config
     *
     * @var string $s3region The region in which the $s3bucket exists, example 'us-east-1'
     */
    public $s3region;

    /**
     * @var string $s3prefix The s3 prefix to use. Can be any base folder
     */
    public $s3prefix = null;


    private \League\Flysystem\Filesystem $fileSystem;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        $module = \Yii::$app->getModule('mediamanager');
        $this->fileSystem = $module->fileSystem;
        unset($module);
    }

    /**
     * Renders the media manager wrapped in a modal
     * @return [type] [description]
     */
    public function run()
    {
        MediaManagerAsset::register($this->view);

        return $this->renderFile('@vendor/skylineos/yii-mediamanager/views/default/modal.php', [
            'fileSystem' => $this->fileSystem,
        ]);
    }
}
