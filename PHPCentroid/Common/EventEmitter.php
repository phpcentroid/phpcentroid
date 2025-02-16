<?php

namespace PHPCentroid\Common;
use Closure;

/**
 * @template T
 */
class EventEmitter implements iEventEmitter
{
    private array $subscriptions = [];

    /**
     * Subscribes a callback to the event emitter.
     *
     * @param Closure $callback The callback to subscribe.
     * @return iEventSubscription The subscription object.
     */
    public function subscribe(Closure $callback): iEventSubscription {
        $subscription = new EventSubscription($this, $callback);
        $this->subscriptions[] = $subscription;
        return $subscription;
    }

    /**
     * Emits an event to all subscribers.
     *
     * @param T $event The event to emit.
     */
    public function emit($event): void {
        foreach ($this->subscriptions as $subscription) {
            $subscription->callback($event);
        }
    }

    /**
     * Unsubscribes a callback from the event emitter.
     *
     * @param Closure $callback The callback to unsubscribe.
     */
    public function unsubscribe(Closure $callback): void
    {
        foreach ($this->subscriptions as $key => $subscription) {
            if ($subscription->callback === $callback) {
                unset($this->subscriptions[$key]);
            }
        }
    }
}