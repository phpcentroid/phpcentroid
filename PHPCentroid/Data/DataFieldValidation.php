<?php

namespace PHPCentroid\Data;

use stdClass;

class DataFieldValidation extends stdClass
{
    /**
     * @var mixed $minValue The minimum value for the field validation
     */
    public mixed $minValue;
    /**
     * @var mixed $maxValue The maximum value for the field validation
     */
    public mixed $maxValue;
    /**
     * @var ?int $minLength The minimum length for the field validation
     */
    public ?int $minLength;
    /**
     * @var ?int $maxLength The maximum length for the field validation
     */
    public ?int $maxLength;
    /**
     * @var string $pattern The regex pattern for the field validation
     */
    public string $pattern;
    /**
     * @var string $patternMessage The message to display if the pattern validation fails
     */
    public string $patternMessage;
    /**
     * @var string $message The message to display if the validation fails
     */
    public string $message;
    /**
     * @var string $type The type of validation
     */
    public string $type;
    /**
     * @var string $validator The custom validator for the field
     */
    public string $validator;
}