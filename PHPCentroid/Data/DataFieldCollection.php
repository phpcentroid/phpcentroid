<?php

namespace PHPCentroid\Data;

use ArrayIterator;
use Countable;
use IteratorAggregate;

final class DataFieldCollection implements Countable, IteratorAggregate
{
    /**
     * @var DataField[] $fields An array of DataField object
     */
    private array $fields;

    public function __construct(DataField ...$fields)
    {
        $this->fields = $fields;
    }
    public function count(): int
    {
        return count($this->fields);
    }
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->fields);
    }

}
