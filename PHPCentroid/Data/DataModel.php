<?php
namespace PHPCentroid\Data;
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
class DataModel
{
    public readonly EventEmitter $beforeSave;
    public readonly EventEmitter $beforeRemove;
    public readonly EventEmitter $beforeExecute;
    public readonly EventEmitter $beforeUpgrade;

    public readonly EventEmitter $afterSave;
    public readonly EventEmitter $afterRemove;
    public readonly EventEmitter $afterExecute;
    public readonly EventEmitter $afterUpgrade;

    public readonly object $properties;

    private array $attributes;

    public function __construct(object $schema) {

        $this->properties = $schema;

        $this->beforeSave = new EventEmitter();
        $this->beforeRemove = new EventEmitter();
        $this->beforeExecute = new EventEmitter();
        $this->beforeUpgrade = new EventEmitter();

        $this->afterSave = new EventEmitter();
        $this->afterRemove = new EventEmitter();
        $this->afterExecute = new EventEmitter();
        $this->afterUpgrade = new EventEmitter();
    }

    /**
     * @return DataField[]
     */
    public function getAttributes(): array {
        if (!isset($this->attributes)) {
            $this->attributes = [];
            foreach ($this->properties->fields as $field) {
                $this->attributes[] = $field;
            }
        }
        return $this->attributes;
    }

}