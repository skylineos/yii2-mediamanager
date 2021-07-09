<?php

namespace skylineos\yii\mediamanager\components;

use yii\helpers\ArrayHelper;
use League\Flysystem\StorageAttributes;
use skylineos\yii\mediamanager\components\Icon;

class Directory
{
    /**
     * What we use as the root file node
     */
    public const ROOT_DELIMITER = '/';

    private \League\Flysystem\Filesystem $fileSystem;

    /**
     * Array of directories - in memeory storage while the hierarchy is built
     * Must always have root
     */
    private array $directories = [
        [
            'text' => '/',
            'parent' => '#',
            'id' => '/',
            'icon' => '',
            'state' => [
                'opened' => false,
                'selected' => false,
            ],
        ]
    ];

    public function __construct(\League\Flysystem\Filesystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
        $this->directories[0]['icon'] = Icon::FOLDER;
    }

    /**
     * Returns a nested, jstree ready array of all folders in the module's configured path
     *
     * @return array
     */
    public function getDirectories(): array
    {
        $directories = $this->fileSystem->listContents('/', true)
            ->filter(fn (StorageAttributes $attributes) => $attributes->isDir());

        foreach ($directories as $directory) {
            $this->addDirectory($directory->path());
        }

        // Alphabatize the folders so they're easier to navigate
        \sort($this->directories);

        return $this->directories;
    }

    /**
     * list contents of a directory
     *
     * @param string $path
     * @param boolean $recursive
     * @return array
     */
    public function listContents(string $path = '.', bool $recursive = false): array
    {
        $pathContents = [];

        try {
            $contents = $this->fileSystem->listContents($path, $recursive)
                ->filter(fn (StorageAttributes $attributes) => $attributes->isFile());

            /** @var \League\Flysystem\StorageAttributes $item */
            foreach ($contents as $item) {
                $pathContents[] = [
                    'path' => $item->path(),
                    'fileSize' => $item->fileSize(),
                    'visibility' => $item->visibility(),
                    'lastModified' => $item->lastModified(),
                ];
            }
        } catch (FilesystemException $exception) {
            \Yii::error($exception);
            return false;
        }

        return $pathContents;
    }

    /**
     * create a directory
     *
     * @param string $path
     * @param array $config
     * @return boolean
     */
    public function create(string $path, array $config = []): bool
    {
        try {
            $this->fileSystem->createDirectory($path, $config);
        } catch (FilesystemException | UnableToCreateDirectory $exception) {
            throw new yii\web\ServerErrorHttpException($exception);
        }

        return true;
    }

    /**
     * delete a directory
     *
     * @param string $path
     * @return boolean
     */
    public function delete(string $path): bool
    {
        try {
            $this->fileSystem->deleteDirectory($path);
        } catch (FilesystemException | UnableToDeleteDirectory $exception) {
            throw new yii\web\ServerErrorHttpException($exception);
        }

        return true;
    }

    /**
     * Adds a directory to our in-memory filesystem
     *
     * @param string $path the full path of the directory (eg folder1/folder2/folder3)
     * @return integer @see https://php.net/array_push
     */
    private function addDirectory(string $path): int
    {
        $opened = $selected = false;

        if (isset($_COOKIE['mmpath']) && $_COOKIE['mmpath'] === $path) {
            $opened = $selected = true;
        }

        $pathParts = $this->getPathParts($path);

        $existingFolders = ArrayHelper::getColumn($this->directories, 'id');
        if (in_array($pathParts['id'], $existingFolders) || $pathParts['id'] === '') {
            return -1;
        }

        return array_push($this->directories, [
            'text' => $pathParts['name'],
            'parent' => $pathParts['parent'],
            'id' => $pathParts['id'],
            'icon' => Icon::FOLDER,
            'state' => [
                'opened' => $opened,
                'selected' => $selected,
            ],
        ]);
    }

    /**
     * Gets the name and parent of a given path. If given
     * folder1/folder2/object, the name becomes object and the parent folder1/folder2
     *
     * @param string $path
     * @return array
     */
    private function getPathParts(?string $path): array
    {
        $parts = explode('/', $path);

        if (count($parts) === 1) {
            return [
                'name' => $parts[0],
                'id' => $parts[0],
                'parent' => self::ROOT_DELIMITER
            ];
        }

        $directoryName = $parts[(\count($parts) - 1)];
        unset($parts[(\count($parts) - 1)]);
        $parent = \implode('/', $parts);

        return [
            'name' => $directoryName,
            'id' => "$parent/$directoryName",
            'parent' => $parent,
        ];
    }
}
