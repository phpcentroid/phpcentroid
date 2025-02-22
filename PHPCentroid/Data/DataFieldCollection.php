<?php

namespace PHPCentroid\Data;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use PHPCentroid\Serializer\Attributes\JsonArray;
use PHPCentroid\Serializer\Attributes\JsonArrayItem;

/**
 * Class DataFieldCollection
 * @package PHPCentroid\Data
 */
#[JsonArray]
#[JsonArrayItem(DataField::class)]
class DataFieldCollection implements IteratorAggregate, Countable, ArrayAccess {
    /**
     * @var DataField[]
     */
    protected array $items = [];

    function __construct(DataField ...$items) {
        $this->items = $items;
    }

    public function getIterator(): ArrayIterator {
        return new ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function add(DataField $field): void {
        $this->items[] = $field;
    }

    public function get(string $name): ?DataField {
        foreach ($this->items as $field) {
            if ($field->name === $name) {
                return $field;
            }
        }
        return null;
    }

    public function remove(DataField $field): void {
        $index = array_search($field, $this->items);
        if ($index >= 0) {
            array_splice($this->items, $index, 1);
        }
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet(mixed $offset): DataField
    {
        return $this->items[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->items[] = $value;
            return;
        }
        $this->items[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }
}