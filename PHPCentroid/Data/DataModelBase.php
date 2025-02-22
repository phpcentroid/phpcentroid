<?php

namespace PHPCentroid\Data;

interface DataModelBase
{
    public function getName(): string;
    public function getAttributes(): DataFieldCollection;
    public function getContext(): DataContextBase;
    public function setContext(DataContextBase $context);
    public function getSchema(): DataModelProperties;

}