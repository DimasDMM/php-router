<?php
namespace Utils;

class Files
{
    public function __construct()
    {
        if (!file_exists(PATH_TEMP)) {
            mkdir(PATH_TEMP, 0755);
        }
    }

    /**
     * @param string $file Path or filename
     * @return null|string
     */
    public function getFileExtension($file)
    {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        return empty($ext) ? null : strtolower($ext);
    }

    /**
     * Read file and returns its content
     *
     * @param string $file
     * @return string
     */
    public function readFile($file)
    {
        return file_get_contents($file);
    }

    /**
     * Move file to the temporal files folder. Returns the name of the temporal
     * file.
     *
     * @param string $filepathOrig
     * @return string|null
     */
    public function moveToTemp($filepathOrig)
    {
        $ext = $this->getFileExtension($filepathOrig);
        $filenameTemp = hash('md5', rand()) . '.' . $ext;
        $filepathTemp = PATH_TEMP . $filenameTemp;

        if (copy($filepathOrig, $filepathTemp)) {
            $this->delete($filepathOrig);
            return $filenameTemp;
        }

        return null;
    }

    /**
     * Returns the list of files that a directory contains
     *
     * @param string $dir Path ended in "/"
     * @param bool $recursive Optional True to check subdirectories
     * @return array
     */
    public function listDir($dir, $recursive = true)
    {
        $list = array();

        foreach (scandir($dir) as $filename) {
            if ($filename == '.' || $filename == '..') {
                continue;
            }

            $path = $dir . $filename;

            if (is_file($path)) {
                $list[] = $path;
            } elseif (is_dir($path) && $recursive) {
                $path .= '/';
                $tmp = $this->listDir($path);
                $list = array_merge($list, $tmp);
            }
        }

        return $list;
    }

    /**
     * @param string $filepath
     * @return boolean
     */
    public function delete($filepath)
    {
        return unlink($filepath);
    }

    /**
     * @param $dir
     * @return boolean
     */
    public function deleteDir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object)) {
                        $this->deleteDir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }

            rmdir($dir);
        }
    }
}
