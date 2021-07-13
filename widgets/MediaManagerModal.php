<?php

namespace skylineos\yii\mediamanager\widgets;

use yii\base\Widget;
use skylineos\yii\mediamanager\Module as skyS3Module;
use skylineos\yii\mediamanager\assets\MediaManagerAsset;

class MediaManagerModal extends Widget
{
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
