<?php

declare(strict_types=1);

namespace Promopult\YandexBusinessApi;

final class Config implements ConfigInterface
{
    private string $accessToken;
    private string $clientLogin;
    private string $apiHost;

    public function __construct(
        string $accessToken,
        string $clientLogin = '',
        string $apiHost = 'https://geoadv-api.yandex.ru'
    ) {
        $this->accessToken = $accessToken;
        $this->clientLogin = $clientLogin;
        $this->apiHost = $apiHost;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getClientLogin(): string
    {
        return $this->clientLogin;
    }

    public function setClientLogin(string $clientLogin): void
    {
        $this->clientLogin = $clientLogin;
    }

    public function getApiHost(): string
    {
        return $this->apiHost;
    }
}
