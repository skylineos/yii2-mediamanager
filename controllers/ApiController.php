<?php

namespace skylineos\yii\mediamanager\controllers;

use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use skylineos\yii\mediamanager\components\Directory;
use skylineos\yii\mediamanager\components\File;

class ApiController extends \yii\rest\ActiveController
{
    public $modelClass = '';

    private array $accessRoles;

    private \League\Flysystem\Filesystem $fileSystem;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        $module = \Yii::$app->getModule('mediamanager');
        $this->accessRoles = $module->accessRoles;
        $this->fileSystem = $module->fileSystem;
        unset($module);
    }

    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => 'yii\filters\ContentNegotiator',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => $this->accessRoles,
                    ]
                ],
                'denyCallback' => function ($rule, $action) {
                    throw new \yii\web\ForbiddenHttpException('You are not allowed to access this page');
                }
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'list-directories' => ['GET', 'HEAD'],
                    'list-contents' => ['GET', 'HEAD'],
                    'create-directory' => ['POST'],
                    'delete-directory' => ['POST'],
                    'delete-file' => ['POST'],
                ],
            ],
        ];
    }

    public function actionListDirectories(): array
    {
        $directory = new Directory($this->fileSystem);
        return $directory->getDirectories();
    }

    public function actionListContents($path): array
    {
        $directory = new Directory($this->fileSystem);
        return $directory->listContents();
    }

    public function actionCreateDirectory(): void
    {
        $post = \Yii::$app->request->post();

        if (!$post['name'] || !$post['parent']) {
            throw new \yii\web\BadRequestHttpException('"name" and "parent" properties are required for this method');
        }

        $directory = new Directory($this->fileSystem);
        $directory->create($post['parent'] . '/' . $post['name']);
    }

    public function actionDeleteDirectory(): array
    {
        $post = \Yii::$app->request->post();

        if (!$post['key']) {
            throw new \yii\web\BadRequestHttpException('"key" property is required for this method');
        }

        $directory = new Directory($this->fileSystem);

        // Cannot delete a directory with contents
        if (\count($directory->listContents($post['key'])) > 0) {
            return [
                'message' => 'You cannot delete a directory that is not empty.',
                'code' => 405
            ];
        }

        $directory->delete($post['key']);

        return [
            'code' => 200
        ];
    }

    public function actionDeleteFile(): bool
    {
        $post = \Yii::$app->request->post();

        if (!$post['path']) {
            throw new \yii\web\BadRequestHttpException('We cannot delete an imaginary file. Please provide a path');
        }

        $file = new File($this->fileSystem);
        return $file->delete($post['path']);
    }
}
