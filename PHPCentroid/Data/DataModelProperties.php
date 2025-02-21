<?php

namespace PHPCentroid\Data;

use PHPCentroid\Serializer\JsonArray;
use PHPCentroid\Serializer\JsonArrayItem;
use stdClass;

class DataModelProperties extends stdClass
{
    public function __construct()
    {
        $this->fields = new DataFieldCollection();
    }

    /**
     * @var string $name A string which represents the name of this model e.g. Article, User, Comment etc
     */
    public string $name;
    /**
     * @var string $version A string which represents the version of this model e.g. 1.0.0, 1.0.1, 1.1.0 etc
     */
    public string $version;
    /**
     * @var ?bool $abstract A boolean which represents whether this model is abstract or not
     */
    public ?bool $abstract = FALSE;
    /**
     * @var ?bool $hidden A boolean which represents whether this model is hidden or not.
     * A hidden model is a model that may be used internally by the system but is not exposed to the end users
     */
    public ?bool $hidden = FALSE;
    /**
     * @var string $inherits A string which represents the name of the model this model inherits from e.g. ActionBase etc
     */
    public string $inherits;
    /**
     * @var string $title A string which represents the title of this model e.g. Article, User, Comment etc
     */
    public string $title;
    /**
     * @var string $description A string which represents the description of this model e.g. Represents an article, Represents a user, Represents a comment etc
     */
    public string $description;
    /**
     * @var string $source A string which represents the source of this model e.g. ArticleBase, UserBase, CommentBase etc
     */
    public string $source;
    /**
     * @var string $view A string which represents the view of this model e.g. ArticleData, UserData, CommentData etc
     */
    public string $view;


    /**
     * @var DataModelConstraint[] An array of strings which represents the constraints of this model
     */
    #[JsonArray()]
    #[JsonArrayItem(DataModelConstraint::class)]
    public array $constraints = [];
    /**
     * @var DataObjectPrivilege[]
     * An array of DataObjectPrivilege objects which represents the privileges of this model
     */
    #[JsonArray()]
    #[JsonArrayItem(DataObjectPrivilege::class)]
    public array $privileges = [];
    public array $views = [];

    /**
     * @var DataFieldCollection
     */
    #[JsonArray()]
    #[JsonArrayItem(DataField::class)]
    public DataFieldCollection $fields;

}