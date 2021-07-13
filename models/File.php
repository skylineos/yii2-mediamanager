<?php

namespace skylineos\yii\mediamanager\models;

use yii\base\Model;
use yii\data\ArrayDataProvider;

class File extends Model
{
    public $path;

    public $visibility;

    public $fileSize;

    public $lastModified;


    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [
                [
                    'path',
                    'visibility'
                ],
                'string',
            ],
            [
                [
                    'fileSize',
                    'lastModified'
                ],
                'integer'
            ],
        ];
    }

    public function search(array $params): \yii\data\ArrayDataProvider
    {
        return new ArrayDataProvider([
            'allModels' => $this->getData($this->load($params) && $this->validate()),
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'path',
                    'visibility',
                    'fileSize',
                    'lastModified'
                ],
            ],
        ]);
    }

    public function getData(bool $filtered): array
    {
        if ($filtered === true) {
            $this->data = \array_filter($this->data, function ($value) {
                $conditions = [true];

                foreach (['path', 'visibility', 'fileSize', 'lastModified'] as $property) {
                    if (!empty($this->$property)) {
                        $conditions[] = strpos($value[$property], $this->$property) !== false;
                    }
                }

                return \array_product($conditions);
            });
        }

        return $this->data;
    }
}
