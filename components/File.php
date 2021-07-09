<?php

namespace skylineos\yii\mediamanager\components;

class File
{
    private \League\Flysystem\Filesystem $fileSystem;

    public function __construct(\League\Flysystem\Filesystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    public function write(string $path, string $contents, array $config = []): bool
    {
        try {
            $this->fileSystem->write($path, $contents, $config = []);
        } catch (FilesystemException | UnableToWriteFile $exception) {
            throw new \yii\web\ServerErrorHttpException($exception);
        }

        return true;
    }

    public function read(string $path): string
    {
        try {
            return $this->fileSystem->read($path);
        } catch (FilesystemException | UnableToReadFile $exception) {
            throw new \yii\web\ServerErrorHttpException($exception);
        }
    }

    public function delete(string $path): bool
    {
        try {
            $this->fileSystem->delete($path);
        } catch (FilesystemException | UnableToDeleteFile $exception) {
            throw new \yii\web\ServerErrorHttpException($exception);
        }

        return true;
    }

    public function fileExists(string $path): bool
    {
        try {
            return $this->fileSystem->fileExists($path);
        } catch (FilesystemException | UnableToRetrieveMetadata $exception) {
            \Yii::error($exception);
            return false;
        }
    }

    public function lastModified(string $path): int | false
    {
        try {
            return $this->fileSystem->lastModified($path);
        } catch (FilesystemException | UnableToRetrieveMetadata $exception) {
            \Yii::error($exception);
            return false;
        }
    }

    public function mimeType(string $path): string | false
    {
        try {
            return $this->fileSystem->mimeType($path);
        } catch (FilesystemException | UnableToRetrieveMetadata $exception) {
            \Yii::error($exception);
            return false;
        }
    }

    public function fileSize(string $path): int | false
    {
        try {
            return $this->fileSystem->fileSize($path);
        } catch (FilesystemException | UnableToRetrieveMetadata $exception) {
            \Yii::error($exception);
            return false;
        }
    }

    public function visibility(string $path): string | false
    {
        try {
            return $this->fileSystem->visibility($path);
        } catch (FilesystemException | UnableToRetrieveMetadata $exception) {
            \Yii::error($exception);
            return false;
        }
    }
}
