<?php
declare(strict_types=1);

namespace Promopult\YandexBusinessApi;

interface ConfigInterface
{
    /**
     * Токен доступа рекламного агентства.
     *
     * @return string
     */
    public function getAccessToken(): string;

    /**
     * Логин клиента в агентства.
     *
     * @return string
     */
    public function getClientLogin(): string;

    /**
     * @param string $clientLogin
     */
    public function setClientLogin(string $clientLogin): void;

    /**
     * Хост сервера API.
     *
     * @return string
     */
    public function getApiHost(): string;
}
