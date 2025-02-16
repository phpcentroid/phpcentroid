<?php

namespace PHPCentroid\Common;

interface iEventEmitter {
    public function subscribe(\Closure $callback): iEventSubscription;
    public function unsubscribe(\Closure $callback): void;
}