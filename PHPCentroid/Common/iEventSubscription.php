<?php

namespace PHPCentroid\Common;

interface iEventSubscription
{
    public function unsubscribe(): void;
}