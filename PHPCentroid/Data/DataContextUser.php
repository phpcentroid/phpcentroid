<?php

namespace PHPCentroid\Data;

use PHPCentroid\Serializer\Attributes\JsonProperty;

class DataContextUser
{
    #[JsonProperty('name')]
    protected string $name;

    #[JsonProperty('authenticationMode')]
    protected string $authenticationMode;

    protected string $authenticationToken;
    protected mixed $authenticationProviderKey = null;
    protected string $authenticationScope;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAuthenticationMode(): string
    {
        return $this->authenticationMode;
    }

    public function setAuthenticationMode(string $authenticationMode): void
    {
        $this->authenticationMode = $authenticationMode;
    }

    public function getAuthenticationToken(): string
    {
        return $this->authenticationToken;
    }

    public function setAuthenticationToken(string $authenticationToken): void
    {
        $this->authenticationToken = $authenticationToken;
    }

    public function getAuthenticationProviderKey(): mixed
    {
        return $this->authenticationProviderKey;
    }

    public function setAuthenticationProviderKey(mixed $authenticationProviderKey): void
    {
        $this->authenticationProviderKey = $authenticationProviderKey;
    }

    public function getAuthenticationScope(): string
    {
        return $this->authenticationScope;
    }

    public function setAuthenticationScope(string $authenticationScope): void
    {
        $this->authenticationScope = $authenticationScope;
    }

}