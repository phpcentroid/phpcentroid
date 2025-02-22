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
class DataModel implements DataModelBase
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

    private DataFieldCollection $attributes;
    private DataContext $context;

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
     * @return DataFieldCollection
     */
    public function getAttributes(): DataFieldCollection {
        if (!isset($this->attributes)) {
            // create attributes collection
            $this->attributes = new DataFieldCollection();
            // get inherited fields, if any
            if ($this->properties->inherits) {
                // get inherited fields
                $inherits = $this->context->getModel($this->properties->inherits);
                foreach ($inherits->getAttributes() as $field) {
                    // set inherited from
                    $field->setInheritedFrom($this->properties->inherits);
                    // add field to attributes
                    $this->attributes->add($field);
                };
            }
            // add fields
            foreach ($this->properties->fields as $field) {
                $this->attributes[] = $field;
            }
        }
        return $this->attributes;
    }

    public function getName(): string
    {
        return $this->properties->name;
    }

    public function getContext(): DataContextBase
    {
        return $this->context;
    }

    public function setContext(DataContextBase $context): void
    {
        $this->context = $context;
    }
}