<?php

namespace PHPCentroid\Common;

class EventSubscription implements iEventSubscription
{
    private iEventEmitter $emitter;
    private \Closure $callback;

    public function __construct(iEventEmitter $emitter, \Closure $callback)
    {
        $this->emitter = $emitter;
        $this->callback = $callback;
    }

    public function unsubscribe(): void {
        $this->emitter->unsubscribe($this->callback);
    }

}