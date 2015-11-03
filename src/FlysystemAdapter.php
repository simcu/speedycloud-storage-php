<?php namespace Speedycloud\Storage;

/**
 * Created by IntelliJ IDEA.
 * User: xRain
 * Date: 15/11/3
 * Time: 00:30
 */

use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;


class FlysystemAdapter implements AdapterInterface
{
    protected $bucket;

    protected $auth;

    public function __construct($accessKey, $secretKey, $bucket)
    {
        $this->bucket = $bucket;

        $this->auth = new Actor();
        $this->auth->init($accessKey, $secretKey, $bucket);
    }

    /**
     * Write a new file.
     *
     * @param string $path
     * @param string $contents *
     * @return array|false false on failure file meta data on success
     */
    public function write($path, $contents, Config $config)
    {
        $data = $this->auth->newObject($path, $contents);
        $this->auth->aclObject($path);
        return $data;
    }

    /**
     * Write a new file using a stream.
     *
     * @param string $path
     * @param resource $resource
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function writeStream($path, $resource, Config $config)
    {
        $data = $this->auth->newObject($path, $resource);
        $this->auth->aclObject($path);
        return $data;
    }

    /**
     * Update a file.
     *
     * @param string $path
     * @param string $contents
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function update($path, $contents, Config $config)
    {
        $data = $this->auth->newObject($path, $contents);
        $this->auth->aclObject($path);
        return $data;
    }

    /**
     * Update a file using a stream.
     *
     * @param string $path
     * @param resource $resource
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function updateStream($path, $resource, Config $config)
    {
        $data = $this->auth->newObject($path, $resource);
        $this->auth->aclObject($path);
        return $data;
    }

    /**
     * Rename a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function rename($path, $newpath)
    {
        $data = $this->auth->getObject($path);
        $this->auth->delObject($path);
        $this->auth->newObject($newpath, $data);
        return $this->auth->aclObject($newpath);
    }

    /**
     * Copy a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function copy($path, $newpath)
    {
        $data = $this->auth->getObject($path);
        return $this->auth->newObject($newpath, $data);
    }

    /**
     * Delete a file.
     *
     * @param string $path
     *
     * @return bool
     */
    public function delete($path)
    {
        return $this->auth->delObject($path);
    }

    /**
     * Delete a directory.
     *
     * @param string $dirname
     *
     * @return bool
     */
    public function deleteDir($dirname)
    {
        return true;
    }

    /**
     * Create a directory.
     *
     * @param string $dirname directory name
     * @param Config $config
     *
     * @return array|false
     */
    public function createDir($dirname, Config $config)
    {
        return true;
    }

    /**
     * Set the visibility for a file.
     *
     * @param string $path
     * @param string $visibility
     *
     * @return array|false file meta data
     */
    public function setVisibility($path, $visibility)
    {
        // TODO 看看如何实现，Qiniu 有 Bucket 基本的共有和私有
        return self::VISIBILITY_PUBLIC;
    }

    /**
     * Check whether a file exists.
     *
     * @param string $path
     *
     * @return array|bool|null
     */
    public function has($path)
    {
        $data = $this->auth->getObject($path);
        return !empty($data);
    }

    /**
     * Read a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function read($path)
    {
        return $this->auth->getObject($path);
    }

    /**
     * Read a file as a stream.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function readStream($path)
    {
        return $this->auth->getObject($path);
    }

    /**
     * List contents of a directory.
     *
     * @param string $directory
     * @param bool $recursive
     *
     * @return array
     */
    public function listContents($directory = '', $recursive = false)
    {
        $list = [];
        $data = $this->auth->getObjects();
        foreach ($data['list'] as $v) {
            $list[] = $this->normalizeFileInfo($v);
        }
        return $list;
    }

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMetadata($path)
    {
        $r = $this->bucketManager->stat($this->bucket, $path);
        $r[0]['key'] = $path;
        return $this->normalizeFileInfo($r[0]);
    }

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getSize($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * Get the mimetype of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMimetype($path)
    {
        $response = $this->bucketManager->stat($this->bucket, $path);
        return ['mimetype' => $response[0]['mimeType']];
    }

    /**
     * Get the timestamp of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getTimestamp($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * Get the visibility of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getVisibility($path)
    {
        // TODO: Implement getVisibility() method.
        return self::VISIBILITY_PUBLIC;
    }

    protected function normalizeFileInfo($filestat)
    {
        return array(
            'type' => 'file',
            'path' => $filestat['Key'],
            'timestamp' => strtotime($filestat['LastModified']),
            'size' => $filestat['Size'],
        );
    }

}
