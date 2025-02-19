<?php

namespace PHPCentroid\Data;

class DataField extends \stdClass
{
    /**
     * @var string $name A string which represents the name of this attribute e.g. title, description, dateCreated etc
     */
    public string $name;
    /**
     * @var string $type A string which represents the type of this attribute e.g. Text, Number, Boolean etc
     */
    public string $type;
    /**
     * @var ?int $size An integer which represents the size of this attribute
     */
    public ?int $size;
    /**
     * @var ?int $scale A number which represents the number of digits of a decimal number
     */
    public ?int $scale;
    /**
     * @var ?bool $nullable A boolean which represents whether this attribute is nullable or not
     */
    public ?bool $nullable = FALSE;
    /**
     * @var string $title A string which represents the title of this attribute e.g. Title, Description, Date Created etc
     */
    public string $title;
    /**
     * @var string $description A string which represents the description of this attribute e.g. The title of the article, The description of the article, The date the article was created etc
     */
    public string $description;
    /**
     * @var ?bool $readonly A boolean which represents whether this attribute is readonly or not
     */
    public ?bool $readonly = FALSE;
    /**
     * @var ?bool $editable A boolean which represents whether this attribute is editable or not
     */
    public ?bool $editable = TRUE;
    /**
     * @var ?bool $indexed A boolean which represents whether this attribute is indexed or not
     */
    public ?bool $indexed = FALSE;
    /**
     * @var ?bool $primary A boolean which represents whether this attribute is the primary key or not
     */
    public ?bool $primary = FALSE;
    /**
     * @var ?bool $many A boolean which represents whether this attribute is a collection or not
     */
    public ?bool $many = TRUE;
    /**
     * @var string $multiplicity A string which represents the multiplicity of this attribute e.g. OneToOne, OneToMany, ManyToOne, ManyToMany or ZeroOrOne etc
     */
    public string $multiplicity;
    /**
     * @var ?bool $expandable A boolean which represents whether this attribute is expandable or not
     * An expandable attribute which describes a relationship between models will be represented as an object or an array of objects
     */
    public ?bool $expandable = FALSE;
    /**
     * @var bool|null $nested A boolean which represents whether this attribute is embedded or not
     * An embedded attribute which describes a relationship between models will be represented as a nested object
     * which is a part of the parent object and is being saved together with the parent object
     */
    public ?bool $nested = FALSE;
    /**
     * @var mixed $value An object which represents the default value of this attribute
     */
    public mixed $value;
    /**
     * @var mixed $calculation An object which represents the calculated value of this attribute
     */
    public mixed $calculation;
    /**
     * @var ?DataAssociationMapping $mapping A DataAssociationMapping object which represents the mapping of this attribute
     * A mapping is used to define the relationship between two models e.g. User has many Articles, Article belongs to User etc
     */
    public ?DataAssociationMapping $mapping;
    /**
     * @var ?DataFieldValidation $validation A DataFieldValidation object which represents the validation of this attribute
     * A validation is used to define the rules for validating the value of this attribute e.g. minValue, maxValue etc
     */
    public ?DataFieldValidation $validation;
}