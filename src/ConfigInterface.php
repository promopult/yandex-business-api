<?php

declare(strict_types=1);

namespace Promopult\YandexBusinessApi;

interface ConfigInterface
{
    /**
     * Токен доступа рекламного агентства.
     */
    public function getAccessToken(): string;

    /**
     * Логин клиента в агентства.
     */
    public function getClientLogin(): string;

    /**
     * @param string $clientLogin
     */
    public function setClientLogin(string $clientLogin): void;

    /**
     * Хост API-сервера.
     */
    public function getApiHost(): string;
}
