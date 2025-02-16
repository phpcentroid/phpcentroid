<?php

namespace PHPCentroid\Common;

class EventEmitter implements iEventEmitter
{
    private array $subscriptions = [];

    public function subscribe($callback): iEventSubscription {
        $subscription = new EventSubscription($this, $callback);
        $this->subscriptions[] = $subscription;
        return $subscription;
    }

    public function emit($event): void {
        foreach ($this->subscriptions as $subscription) {
            $subscription->callback($event);
        }
    }

    public function unsubscribe($callback): void
    {
        foreach ($this->subscriptions as $key => $subscription) {
            if ($subscription->callback === $callback) {
                unset($this->subscriptions[$key]);
            }
        }
    }
}