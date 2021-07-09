<?php

namespace skylineos\yii\mediamanager;

class Module extends \yii\base\Module
{
    /**
     * Management adapters we're currently accepting
     */
    private const VALID_ADAPTERS = [
        's3',
        'local',
    ];

    /**
     * An array of $this->adapter => [ 'expected configuration params' ];
     */
    private const VALID_ADAPTER_CONFIGURATION = [
        's3' => [
            'bucket',
            'region',
            'prefix',
        ],
        'local' => [
            'directory',
        ],
    ];

    /**
     * @inheritDoc
     */
    public $controllerNamespace = 'skylineos\yii\mediamanager\controllers';

    /**
     * string - Adapater to use for media management
     * supported: s3 & local
     */
    public string $adapter = 's3';

    /**
     * The configuration for the adapter. Should be provided in config.
     */
    public array $configuration = [];

    /**
     * Role(s) that can access the module's actions
     */
    public array $accessRoles = ['@'];

    /**
     * string the layout to use when rendering the media manager
     * Type of skylineos\yii\mediamanager\Module::$layout must not be defined (as in class yii\base\Module)
     */
    public $layout = '@vendor/skylineos/yii-mediamanager/views/layouts/main.php';

    /**
     * The filesystem
     */
    public \League\Flysystem\Filesystem $fileSystem;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        if (!\in_array($this->adapter, self::VALID_ADAPTERS)) {
            throw new \yii\base\InvalidConfigException('Specified adapter is not valid');
        }

        if ($this->validateConfiguration() === false) {
            throw new \yii\base\InvalidConfigException('Adapter configuration is not valid');
        }

        \Yii::setAlias('@workingDirectory', $this->getWorkingDirectory());

        $driver = '\skylineos\yii\mediamanager\adapters\\' . \ucfirst(\strtolower($this->adapter)) . 'Adapter';
        $driverAdapter = new $driver($this->configuration);
        $this->fileSystem = $driverAdapter->run();
    }

    private function validateConfiguration()
    {
        foreach (\array_keys($this->configuration) as $configParam) {
            if (!\in_array($configParam, self::VALID_ADAPTER_CONFIGURATION[$this->adapter])) {
                return false;
            }
        }

        return true;
    }

    private function getWorkingDirectory()
    {
        // Set our working directory alias for us in the views
        $workingDirectory = $this->adapter === 's3'
            ? $this->configuration['prefix']
            : $this->configuration['directory'];

        $workingDirectory = \explode('/web', $workingDirectory);

        return $workingDirectory[1];
    }
}
