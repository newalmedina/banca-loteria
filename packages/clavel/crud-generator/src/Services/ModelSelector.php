<?php


namespace Clavel\CrudGenerator\Services;

class ModelSelector
{
    public static function searchModels()
    {
        $directories = [];
        self::recursiveDirectoryIterator(
            base_path().DIRECTORY_SEPARATOR."packages",
            "Models",
            $directories
        );

        self::recursiveDirectoryIterator(
            app_path(),
            "Models",
            $directories
        );

        $models = [];
        foreach ($directories as $directory) {
            // Debemos evitar los directorios de stubs ya que contienen elementos que no queremos traducir
            if (strpos($directory, 'stubs') === false) {
                $models = array_merge($models, self::getModels($directory));
            }
        }

        return $models;
    }

    private static function getModels($path)
    {
        $out = [];
        $results = scandir($path);
        foreach ($results as $result) {
            if ($result === '.' or $result === '..') {
                continue;
            }
            $filename = $path . '/' . $result;
            if (is_dir($filename)) {
                $out = array_merge($out, self::getModels($filename));
            } else {
                if (strpos($filename, '.git') === false) {
                    $modelPath = substr($filename, 0, -4);
                    $modelName = explode(DIRECTORY_SEPARATOR, $modelPath);
                    $out[] = [end($modelName), $modelPath];
                }
            }
        }
        return $out;
    }

    private static function recursiveDirectoryIterator($directory, $directoryName, &$directories)
    {
        $iterator = new \DirectoryIterator($directory);

        foreach ($iterator as $info) {
            if (!$info->isFile() && !$info->isDot()) {
                if ($info->__toString() == $directoryName) {
                    $directories [] = $info->getPathname();
                } else {
                    self::recursiveDirectoryIterator(
                        $directory.DIRECTORY_SEPARATOR.$info->__toString(),
                        $directoryName,
                        $directories
                    );
                }
            }
        }
        return;
    }

    public static function extractNamespace($file)
    {
        $ns = null;
        $handle = fopen($file, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (strpos($line, 'namespace') === 0) {
                    $parts = explode(' ', $line);
                    $ns = rtrim(trim($parts[1]), ';');
                    break;
                }
            }
            fclose($handle);
        }
        return $ns;
    }
}
