<?php

namespace PHPCentroid\Data;

use PHPCentroid\Common\Application;

abstract class DataContextBase
{
    protected ?DataContextUser $user;
    protected DataContextUser $interactiveUser;

    public function __construct()
    {

    }

    public abstract function getApplication(): Application;
    public abstract function setApplication(Application $application): void;
    public abstract function getModel(string $name): ?DataModelBase;

    public function getUser(): ?DataContextUser {
        return $this->user;
    }

    public function setUser(DataContextUser $user): void {
        $this->user = $user;
    }

    public function getInteractiveUser(): ?DataContextUser {
        return $this->interactiveUser;
    }

    public function setInteractiveUser(DataContextUser $user): void {
        $this->interactiveUser = $user;
    }

}