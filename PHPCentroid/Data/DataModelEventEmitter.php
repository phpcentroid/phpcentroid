<?php

namespace PHPCentroid\Data;

use PHPCentroid\Common\EventEmitter;

class DataModelEventEmitter
{
    public EventEmitter $save;
    public EventEmitter $remove;
    public EventEmitter $execute;
    public EventEmitter $upgrade;

    public function __construct() {
        $this->save = new EventEmitter();
        $this->remove = new EventEmitter();
        $this->execute = new EventEmitter();
        $this->upgrade = new EventEmitter();
    }
}