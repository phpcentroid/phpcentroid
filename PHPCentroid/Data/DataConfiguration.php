<?php
namespace PHPCentroid\Data;

use PHPCentroid\Common\TextUtils;

class DataConfiguration
{
    protected $cwd;
    public function __construct(?string $cwd = NULL)
    {
        $this->cwd = is_string($cwd) ? $cwd : getcwd();
    }

    /**
     * @param $name
     * @return DataModel|null
     */
    public function get_model($name): ?DataModel {
        $path = TextUtils::join_path( $this->cwd, 'config', 'models', $name.'.json');
        if (file_exists($path)) {
            $string = file_get_contents($path);
            return new DataModel(json_decode($string));
        }
        return NULL;
    }
}