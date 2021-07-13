<?php

namespace skylineos\yii\mediamanager\controllers;

use yii\web\Controller;
use yii\filters\AccessControl;
use skylineos\yii\mediamanager\components\File;

class DefaultController extends Controller
{
    public $layout;

    public $viewPath = '@vendor/skylineos/yii-mediamanager/views';

    private array $accessRoles;

    private \League\Flysystem\Filesystem $fileSystem;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        $module = \Yii::$app->getModule('mediamanager');
        $this->layout = $module->layout;
        $this->accessRoles = $module->accessRoles;
        $this->fileSystem = $module->fileSystem;

        unset($module);
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => $this->accessRoles,
                    ]
                ],
            ],
        ];
    }

    public function actionIndex(string $path = '.'): string
    {
        return $this->render('index', [
            'fileSystem' => $this->fileSystem,
        ]);
    }

    public function actionUpload(): void
    {
        if (!isset($_FILES['file'])) {
            throw new yii\web\BadRequestHttpException('You cannot hope to upload anything without providing a file.');
        }

        set_time_limit(0);
        $uploaded = \yii\web\UploadedFile::getInstanceByName('file');
        $path = strlen(\Yii::$app->request->post('mm-upload-path')) > 1
            ? ltrim(\Yii::$app->request->post('mm-upload-path'), '/')
            : '/';

        $fileLocation = "$path/$uploaded->name";

        $file = new File($this->fileSystem);
        $upload = $file->write($fileLocation, file_get_contents($uploaded->tempName));
    }
}
