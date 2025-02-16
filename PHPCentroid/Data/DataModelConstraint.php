<?php

namespace PHPCentroid\Data;

class DataModelConstraint extends \stdClass
{
    /**
     * @var DataConstraintTypeEnum $type A string which represents the type of this constraint e.g. unique
     */
    public DataConstraintTypeEnum $type;
    /**
     * @var string $description A short description for this constraint e.g. Unique identifier field must be unique across different records.
     */
    public string $description;
    /**
     * @var string[] $fields An array of strings which represents the fields of this constraint
     */
    public array $fields;
}