<?php

namespace PHPCentroid\Query;

abstract class DataAdapterBase
{
    public function __construct()
    {
    }

    public abstract function execute(QueryExpression $query): array;
    public abstract function execute_in_transaction(\Closure $closure): void;
    public abstract function open(): void;
    public abstract function close(): void;
    public abstract function select_identity(): int;
    public abstract function last_identity(): int;
}