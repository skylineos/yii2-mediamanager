<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use skylineos\yii\mediamanager\components\Directory;
use skylineos\yii\mediamanager\assets\MediaManagerAsset;

MediaManagerAsset::register($this);

$this->registerJs("var path = '/'", \yii\web\View::POS_HEAD);
?>


<div class="row">
    <div class="col-md-2">
        <div class="card">
            <div class="card-body">
    
                <div id="folderTree"></div>

            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-body">
    
                <?= Html::beginForm(['/mediamanager/default/upload'], 'post', [
                    'enctype' => 'multipart/form-data',
                    'id' => 'mm-file-upload-form'
                    ]) ?>
                
                <div class="dz-message border-success rounded" id="mm-dropzone" data-dz-message>
                    <span class="text-success">
                        <i class="fas fa-cloud-upload-alt fa-2x"></i> 
                        Click or drag files here to upload (Max Filesize: <?= ini_get('upload_max_filesize') ?>)
                    </span>
                </div>

                <?= html::hiddenInput('mm-upload-path', '/', ['id' => 'mm-upload-path']) ?>

                <?= Html::endForm() ?>

                <?php

                Pjax::begin([
                    'id' => 'folderContents',
                ]);

                /**
                 * @todo none of this logic should be in the view, but I have not yet figured out
                 * how to get the jstree directory to talk directly to the pjax
                 */
                $directory = new Directory($fileSystem);
                $fileContents = $directory->listContents(isset($_COOKIE['mmpath']) ? $_COOKIE['mmpath'] : '/');

                $files = new \skylineos\yii\mediamanager\models\File($fileContents);
                $fileProvider = $files->search(\Yii::$app->request->get());

                echo GridView::widget([
                    'dataProvider' => $fileProvider,
                    'filterModel' => $files,
                    'columns' => [
                        [
                            'class' => \yii\grid\ActionColumn::class,
                            'template' => '{insert} {open} {delete}',
                            'options' => [
                                'class' => 'mm-actionColumn',
                            ],
                            'buttons' => [
                                'insert' => function ($url, $model, $key) {
                                    if (Url::to() !== '/mediamanager/default/index') {
                                        $path = Url::to('@workingDirectory' . '/' . $model['path'], true);

                                        return Html::button(
                                            '<i class="fas fa-angle-double-down"></i>',
                                            [
                                                'class' => 'btn btn-success btn-sm btn-icon insert-file',
                                                'title' => 'Insert',
                                                'data-toggle' => 'tooltip',
                                                'data-path' => $path,
                                            ]
                                        );
                                    }

                                    return null;
                                },
                                'open' => function ($url, $model, $key) {
                                    return Html::a(
                                        '<i class="fas fa-external-link-square-alt"></i>',
                                        \yii\helpers\Url::to('@workingDirectory' . '/' . $model['path'], true),
                                        [
                                            'class' => 'btn btn-info btn-sm btn-icon',
                                            'target' => '_blank',
                                            'title' => 'Open',
                                            'data-pjax' => '0',
                                            'data-toggle' => 'tooltip',
                                        ]
                                    );
                                },
                                'delete' => function ($url, $model, $key) {
                                    return Html::button(
                                        '<i class="fas fa-times-circle"></i>',
                                        [
                                            'class' => 'btn btn-danger btn-sm btn-icon delete-file',
                                            'title' => 'Delete',
                                            'data-toggle' => 'tooltip',
                                            'data-path' => $model['path'],
                                        ]
                                    );
                                },
                            ],
                        ],
                        [
                            'attribute' => 'path',
                            'value' => function ($model) {
                                $parts = \explode('/', $model['path']);
                                $filename = \end($parts);
                                return $filename;
                            },
                        ],
                        'visibility',
                        [
                            'attribute' => 'fileSize',
                            'format' => 'shortSize',
                        ],
                        [
                            'attribute' => 'lastModified',
                            'format' => 'datetime',
                            'filter' => false,
                        ],
                    ]
                    ]);

                Pjax::end();

                ?>
            </div>
        </div>
    </div>
</div>
