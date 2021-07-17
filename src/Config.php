<?php
declare(strict_types=1);

namespace Promopult\YandexBusinessApi;

final class Config implements \Promopult\YandexBusinessApi\ConfigInterface
{
    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $clientLogin;

    /**
     * @var string
     */
    private $apiHost;

    public function __construct(
        string $accessToken,
        string $clientLogin = '',
        string $apiHost = 'https://geoadv-api.yandex.ru'
    ) {
        $this->accessToken = $accessToken;
        $this->clientLogin = $clientLogin;
        $this->apiHost = $apiHost;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @return string
     */
    public function getClientLogin(): string
    {
        return $this->clientLogin;
    }

    public function setClientLogin(string $clientLogin): void
    {
        $this->clientLogin = $clientLogin;
    }

    /**
     * @return string
     */
    public function getApiHost(): string
    {
        return $this->apiHost;
    }
}
