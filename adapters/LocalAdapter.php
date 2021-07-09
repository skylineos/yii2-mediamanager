<?php

namespace skylineos\yii\mediamanager\adapters;

use Yii;
use yii\helpers\StringHelper;

class LocalAdapter
{
    private string $directory;

    public function __construct(array $config = [])
    {
        if (!\array_key_exists('directory', $config)) {
            throw new \yii\base\InvalidConfigException('Local adapter requires the parameter: directory');
        }

        $this->directory = \Yii::getAlias($config['directory'], $throwException = true);
    }

    public function run(): \League\Flysystem\Filesystem
    {
        $adapter = new \League\Flysystem\Local\LocalFilesystemAdapter(
            $this->directory
        );

        return new \League\Flysystem\Filesystem($adapter);
    }
}
