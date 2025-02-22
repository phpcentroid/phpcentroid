<?php

namespace PHPCentroid\Data;

use PHPCentroid\Serializer\Attributes\JsonArray;
use PHPCentroid\Serializer\Attributes\JsonArrayItem;
use stdClass;

class DataAssociationMapping extends stdClass
{
    /**
     * @var string $associationType A string which represents the type of association e.g. association or junction
     */
    public string $associationType;
    /**
     * @var string $associationAdapter A string which represents the adapter of the association e.g. GroupMembers ArticleTags etc
     */
    public string $associationAdapter;
    /**
     * @var string $associationObjectField A string which represents the object field of the association e.g. group_id article_id etc
     */
    public string $associationObjectField;
    /**
     * @var string $associationValueField A string which represents the value field of the association e.g. user_id tag_id etc
     */
    public string $associationValueField;
    /**
     * @var string $parentModel A string which represents the parent model of the association e.g. Group Article etc
     */
    public string $parentModel;
    /**
     * @var string $parentField A string which represents the parent field of the association e.g. group_id article_id etc
     */
    public string $parentField;
    /**
     * @var string $childModel A string which represents the child model of the association e.g. User Tag etc
     */
    public string $childModel;
    /**
     * @var string $childField A string which represents the child field of the association e.g. user_id tag_id etc
     */
    public string $childField;
    /**
     * @var string $cascade A string which represents the cascade of the association e.g. none, delete etc
     */
    public string $cascade;
    /**
     * @var DataObjectPrivilege[] $privileges A collection of DataObjectPrivilege objects which represents the privileges of this association
     */
    #[JsonArray]
    #[JsonArrayItem(DataObjectPrivilege::class)]
    public array $privileges = [];
    /**
     * @var ?array $options An object which represents the options of this association
     */
    public ?array $options;
}