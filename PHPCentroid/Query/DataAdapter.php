<?php

namespace PHPCentroid\Query;

abstract class DataAdapter extends DataAdapterBase
{
    public abstract function table(string $table): DataTable;
}