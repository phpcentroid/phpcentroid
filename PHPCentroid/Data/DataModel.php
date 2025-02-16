<?php
namespace PHPCentroid\Data;
use PHPCentroid\Common\DynamicObject;
use PHPCentroid\Common\EventEmitter;

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

    public DataModelEventEmitter $before;
    public DataModelEventEmitter $after;

    public function __construct() {
        parent::__construct();
        $this->before = new DataModelEventEmitter();
        $this->after = new DataModelEventEmitter();
    }

    public function get_source(): string {
        return $this->source ?? $this->name . 'Base';
    }

    public function get_view(): string {
        return $this->view ?? $this->source . 'Data';
    }
}