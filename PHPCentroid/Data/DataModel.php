<?php
namespace PHPCentroid\Data;
use PHPCentroid\Common\DynamicObject;

/**
 * Created by PhpStorm.
 * User: kbarbounakis
 * Date: 16/10/2016
 * Time: 9:27 πμ
 */

/**
 * Class DataModel
 * @property string $name
 * @property string $inherits
 * @property string $title
 * @property string $description
 * @property string $source
 * @property string $view
 * @package PHPCentroid\Data
 */
class DataModel extends DynamicObject
{
    public function get_source(): string {
        return $this->source ?? $this->name . 'Base';
    }

    public function get_view(): string {
        return $this->view ?? $this->source . 'Data';
    }
}