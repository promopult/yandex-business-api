<?php

declare(strict_types=1);

namespace Promopult\YandexBusinessApi;

/**
 * Class Client
 */
final class Client
{
    /**
     * @var \Psr\Http\Client\ClientInterface
     */
    private $httpClient;

    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(
        \Promopult\YandexBusinessApi\ConfigInterface $config,
        \Psr\Http\Client\ClientInterface $httpClient
    ) {
        $this->httpClient = $httpClient;
        $this->config = $config;
    }

    /* API methods */

    /**
     * Информация о рекламной кампании
     *
     * @param int $campaignId
     * @return array
     * @throws \Psr\Http\Client\ClientExceptionInterface
     *
     * @see https://yandex.ru/dev/business-api/doc/ref/Campaign_management/GetCampaingGet.html
     */
    public function getCampaign(int $campaignId): array
    {
        $request = $this->createRequest(
            'GET',
            '/priority/v1/get-campaign',
            [
                'campaignId' => $campaignId,
            ]
        );

        $response = $this->httpClient->sendRequest($request);

        $this->handleErrors($request, $response);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Пинг API
     *
     * @return array
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function pingping(): array
    {
        $request = $this->createRequest('POST', '/priority/v1/pingping');
        $response = $this->httpClient->sendRequest($request);

        $this->handleErrors($request, $response);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Бюджетный запуск
     *
     * Запуск рк (на кошельке должны быть деньги)
     * Происходит списание денег с кошелька и старт РК
     * Нужно вызывать только для РК в статусе waiting, stopped, finished
     * В противном случае произойдет продление еще не оконченной РК!
     *
     * @param int $campaignId
     * @param string $product
     * @param string $budgetType
     * @param int $duration
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @deprecated
     */
    public function launchCampaignV2(
        int $campaignId,
        string $product,
        string $budgetType,
        int $duration
    ): void {
        $request = $this->createRequest(
            'POST',
            '/priority/v2/launch-campaign',
            [
                'campaignId' => $campaignId,
                'product' => $product,
                'budgetType' => $budgetType,
                'duration' => $duration,
            ]
        );

        $response = $this->httpClient->sendRequest($request);

        $this->handleErrors($request, $response);
    }

    /**
     * Бюджетный запуск
     * Запускает рекламную компанию. На кошельке должны быть деньги. Происходит списание денег с кошелька и старт
     * кампании. Нужно вызывать только для кампании в статусе waiting, stopped, finished. В противном случае произойдет
     * продление еще не оконченной кампании.
     *
     * @param int $campaignId
     * @param string $budgetType
     * @param int $duration
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function launchCampaignV3(
        int $campaignId,
        string $budgetType,
        int $duration
    ): void {
        $request = $this->createRequest(
            'POST',
            '/priority/v3/launch-campaign',
            [
                'campaignId' => $campaignId,
                'budgetType' => $budgetType,
                'duration' => $duration,
            ]
        );

        $response = $this->httpClient->sendRequest($request);

        $this->handleErrors($request, $response);
    }

    /**
     * Бюджетный расчет
     *
     * @param int $countryGeoId
     * @param int|null $companyId
     * @param int|null $chainId
     * @param bool $branding
     * @return array
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     *
     * @deprecated
     */
    public function getPricesV2(
        int $countryGeoId,
        ?int $companyId = null,
        ?int $chainId = null,
        bool $branding = false
    ): array {
        $request = $this->createRequest(
            'POST',
            '/priority/v2/price',
            [
                'countryGeoId' => $countryGeoId,
                'branding' => $branding,
                'companyId' => $companyId,
                'chainId' => $chainId
            ]
        );

        $response = $this->httpClient->sendRequest($request);

        $this->handleErrors($request, $response);

        return json_decode($response->getBody()->getContents(), true);
    }


    /**
     * Получение цен
     *
     * Показывает доступные бюджеты для конкретной рекламной кампании.
     *
     * @param int $countryGeoId
     * @param int $campaignId
     * @param int|null $chainId
     * @param bool $branding
     *
     * @return array
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function getPricesV3(
        int $countryGeoId,
        ?int $campaignId = null,
        ?int $companyId = null,
        ?int $chainId = null,
        bool $branding = false
    ): array {
        $request = $this->createRequest(
            'POST',
            '/priority/v3/price',
            [
                'branding' => $branding,
                'campaignId' => $campaignId,
                'chainId' => $chainId,
                'countryGeoId' => $countryGeoId,
                'companyId' => $companyId,
            ]
        );

        $response = $this->httpClient->sendRequest($request);

        $this->handleErrors($request, $response);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Баланс кошелька (нужно для перевода средств).
     *
     * @return array
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function getBalance(): array
    {
        $request = $this->createRequest('GET', '/priority/v1/get-balance-client');
        $response = $this->httpClient->sendRequest($request);

        $this->handleErrors($request, $response);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Депозит
     *
     * @param int $walletId
     * @param string $contract
     * @param string $currency # Варианты 'byn' | 'kzt' | 'rub' | 'uah'
     * @param float $amount
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function deposit(
        int $walletId,
        string $contract,
        string $currency,
        float $amount
    ): void {
        $request = $this->createRequest(
            'POST',
            '/priority/v1/deposit',
            [
                'walletId' => $walletId,
                'contract' => $contract,
                'currency' => $currency,
                'amount' => $amount
            ]
        );

        $response = $this->httpClient->sendRequest($request);

        $this->handleErrors($request, $response);
    }

    /**
     * Запуск рк (на кошельке должны быть деньги)
     * Происходит списание денег с кошелька и старт РК
     * Нужно вызывать только для РК в статусе waiting, stopped, finished
     * В противном случае произойдет продление еще не оконченной РК
     *
     * @param int $campaignId
     * @param int $duration
     * @param int $arbitrateRate
     * @param bool $priority
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function launchCampaignV1(
        int $campaignId,
        int $duration,
        int $arbitrateRate,
        bool $priority
    ): void {
        $request = $this->createRequest(
            'POST',
            '/priority/v1/launch-campaign',
            [
                'campaignId' => $campaignId,
                'duration' => $duration,
                'arbitrateRate' => $arbitrateRate,
                'priority' => $priority
            ]
        );

        $response = $this->httpClient->sendRequest($request);

        $this->handleErrors($request, $response);
    }

    /**
     * @param int $srcWallet # id кошелька с которого переводить
     * @param int $dstWallet # id кошелька на который нужно перевести
     * @param float $sumOld # текущая сумма на кошельке с которого переводить
     * @param float $sumNew # сумма на кошельке с которого переводить ПОСЛЕ перевода
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function billingTransfer(
        int $srcWallet,
        int $dstWallet,
        float $sumOld,
        float $sumNew
    ): void {
        $request = $this->createRequest(
            'POST',
            '/priority/v1/billing-transfer',
            [
                'srcWallet' => $srcWallet,
                'dstWallet' => $dstWallet,
                'sumOld' => $sumOld,
                'sumNew' => $sumNew
            ]
        );

        $response = $this->httpClient->sendRequest($request);

        $this->handleErrors($request, $response);
    }

    /**
     * Поиск клиентов.
     *
     * @param string $text
     * @param int $limit
     * @param int $offset
     *
     * @return array
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function companySearch(
        string $text,
        int $limit,
        int $offset
    ): array {
        $request = $this->createRequest(
            'GET',
            '/priority/v1/company-search',
            [
                'text' => $text,
                'limit' => $limit,
                'offset' => $offset
            ]
        );

        $response = $this->httpClient->sendRequest($request);

        $this->handleErrors($request, $response);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Создание рекламной кампании
     *
     * Создает рекламную кампанию. Операция асинхронная, чтобы получить ответ может быть нужно повторить запрос
     * несколько раз. Передаем companyId если создаем на организацию с одной физической точкой (или один филиал сети),
     * chainId если на всю сеть (сразу все филиалы). Создание на определенный набор филиалов пока недоступно.
     *
     * @param string $type              # Тип кампании WEB | GEO | SUBSCRIPTION
     * @param int|null $chainId         # ID организации, у которых несколько физических точек (сетевые или франшизы)
     * @param int|null $companyId       # Идентификатор организации, у которой только одна физическая точка.
     * @param int|null $countryGeoId    # Географический идентификатор страны.
     * @param string|null $name         # Имя рекламной кампании.
     * @param string|null $url          # Требуется для типа WEB
     *
     * @return array
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function createCampaignV3(
        string $type,
        ?int $companyId = null,
        ?int $chainId = null,
        ?int $countryGeoId = null,
        ?string $name = null,
        ?string $url = null
    ): array {
        $request = $this->createRequest(
            'POST',
            '/priority/v3/create-campaign',
            [
                'companyId' => $companyId,
                'chainId' => $chainId,
                'countryGeoId' => $countryGeoId,
                'name' => $name,
                'type' => $type,
                'url' => $url
            ]
        );

        $response = $this->httpClient->sendRequest($request);

        $this->handleErrors($request, $response);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param int|null $companyId
     * @param int|null $chainId
     * @param int|null $countryGeoId
     * @return array
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function isOwner(
        ?int $companyId,
        ?int $chainId,
        ?int $countryGeoId
    ): array {
        $request = $this->createRequest(
            'GET',
            '/priority/v1/is-owner',
            [
                'chainId' => $chainId,
                'companyId' => $companyId,
                'countryGeoId' => $countryGeoId
            ]
        );

        $response = $this->httpClient->sendRequest($request);

        $this->handleErrors($request, $response);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Создание РК получилось асинхронным, поэтому эту ручку нужно популить (только задайте кол-во ретраев),
     * чтобы создать РК.
     *
     * @param string|null $name // название РК
     * @param int|null $companyId // id орги из поиска, если создаем на оргу
     * @param int|null $chainId // chainId сети из поиска, если создаем на сеть
     * @param int|null $countryGeoId // countryGeoId страны из поиска
     *
     * @return array
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     *
     * @deprecated
     */
    public function createCampaign(
        ?string $name = null,
        ?int $companyId = null,
        ?int $chainId = null,
        ?int $countryGeoId = null
    ): array {
        $request = $this->createRequest(
            'POST',
            '/priority/v1/create-campaign',
            [
                'name' => $name,
                'companyId' => $companyId,
                'chainId' => $chainId,
                'countryGeoId' => $countryGeoId
            ]
        );

        $response = $this->httpClient->sendRequest($request);

        $this->handleErrors($request, $response);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     *
     * @param int $limit
     * @param int $offset
     *
     * @return array
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function getCampaigns(
        int $limit,
        int $offset = 0
    ): array {
        $request = $this->createRequest(
            'GET',
            '/priority/v1/get-campaigns',
            [
                'limit' => $limit,
                'offset' => $offset,
            ]
        );

        $response = $this->httpClient->sendRequest($request);

        $this->handleErrors($request, $response);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param int $countryGeoId // countryGeoId страны из поиска
     * @param int|null $companyId // id орги из поиска, если одноорг
     * @param int|null $chainId // chainId сети из поиска, если сеть
     * @param bool $branding
     * @param array $arbitrateRates
     * @return array
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function getPricesV1(
        int $countryGeoId,
        ?int $companyId,
        ?int $chainId,
        bool $branding,
        array $arbitrateRates = [0]
    ): array {
        $request = $this->createRequest(
            'POST',
            '/priority/v1/price',
            [
                'companyId' => $companyId,
                'chainId' => $chainId,
                'countryGeoId' => $countryGeoId,
                'options' => [
                    'branding' => $branding,
                    'arbitrate_rates' => $arbitrateRates
                ]
            ]
        );

        $response = $this->httpClient->sendRequest($request);

        $this->handleErrors($request, $response);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Применение промокода к кампании
     *
     * @param int $campaignId
     * @param string $promocode
     * @return array
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     *
     * @see https://yandex.ru/dev/business-api/doc/ref/Campaign_management/ApplyPromocodePost.html
     */
    public function applyPromocode(
        int $campaignId,
        string $promocode
    ): array {
        $request = $this->createRequest(
            'POST',
            '/priority/v1/apply-promocode',
            [
                'campaignId' => $campaignId,
                'promocode' => $promocode
            ]
        );

        $response = $this->httpClient->sendRequest($request);

        $this->handleErrors($request, $response);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Сброс промокода
     *
     * @param int $campaignId
     *
     * @return array
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     *
     * @see https://yandex.ru/dev/business-api/doc/ref/Campaign_management/DiscardPromocodePost.html
     */
    public function discardPromocode(int $campaignId): array
    {
        $request = $this->createRequest(
            'POST',
            '/priority/v1/discard-promocode',
            [
                'campaignId' => $campaignId
            ]
        );

        $response = $this->httpClient->sendRequest($request);

        $this->handleErrors($request, $response);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Создание задачи на генерацию КП
     *
     * Метод доступен по запросу через менеджера. Создает задачу на генерацию нового
     * коммерческого предложения. Ограничения: не более 200 презентаций в день. Не запускайте
     * создание всех КП разом.
     *
     * @param bool $branding
     * @param int $chainId
     * @param int $companyId
     * @param int $countryGeoId
     * @param string $managerName
     * @param string $managerEmail
     * @param string $product
     * @return array
     * @throws \Psr\Http\Client\ClientExceptionInterface
     *
     * @see https://yandex.ru/dev/business-api/doc/ref/Generate_commercial_offer/GenerateCommercialOfferPost.html
     */
    public function generateCommercialOffer(
        bool $branding,
        int $chainId,
        int $companyId,
        int $countryGeoId,
        string $managerName,
        string $managerEmail,
        string $product
    ): array {
        $request = $this->createRequest(
            'POST',
            '/priority/v1/generate-commercial-offer',
            [
                'branding' => $branding,
                'chainID' => $chainId,
                'companyId' => $companyId,
                'countryGeoID' => $countryGeoId,
                'manager' => [
                    'email' => $managerEmail,
                    'name' => $managerName
                ],
                'product' => $product
            ]
        );

        $response = $this->httpClient->sendRequest($request);

        $this->handleErrors($request, $response);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Получение статуса задачи и результата
     * Получает статус выполнения задачи по её ID полученной в /priority/v1/generate-commercial-offer
     *
     * @param int $taskId
     * @return array
     * @throws \Psr\Http\Client\ClientExceptionInterface
     *
     * @see https://yandex.ru/dev/business-api/doc/ref/Generate_commercial_offer/GetCommercialOfferGet.html
     */
    public function getCommercialOfferResult(int $taskId): array
    {
        $request = $this->createRequest(
            'GET',
            '/priority/v1/generate-commercial-offer',
            [
                'taskId' => $taskId,
            ]
        );

        $response = $this->httpClient->sendRequest($request);

        $this->handleErrors($request, $response);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param string $clientLogin
     * @return $this
     */
    public function useClientLogin(string $clientLogin): self
    {
        $this->config->setClientLogin($clientLogin);

        return $this;
    }

    /**
     * @return $this
     */
    public function resetClientLogin(): self
    {
        $this->config->setClientLogin('');

        return $this;
    }

    /* Private */

    private function createRequest(
        string $method,
        string $url,
        ?array $params = null
    ): \Psr\Http\Message\RequestInterface {
        $headers = [
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
            'Authorization' => 'OAuth ' . $this->config->getAccessToken(),
        ];

        if ($this->config->getClientLogin()) {
            $headers['Client-Login'] = $this->config->getClientLogin();
        }

        switch (strtoupper($method)) {
            case 'GET':
                $query = $params
                    ? '?' . http_build_query($this->filterParams($params))
                    : '';
                $uri = $this->config->getApiHost() . $url . $query;
                $body = null;
                break;

            case 'POST':
            default:
                $uri = $this->config->getApiHost() . $url;
                $body = $params
                    ? json_encode($this->filterParams($params))
                    : null;
                break;
        }

        return new \GuzzleHttp\Psr7\Request($method, $uri, $headers, $body);
    }

    private function handleErrors(
        \Psr\Http\Message\RequestInterface $request,
        \Psr\Http\Message\ResponseInterface $response
    ): void {
        $decodedJsonBody = json_decode($response->getBody()->__toString(), true);

        if (isset($decodedJsonBody['error'])
            && isset($decodedJsonBody['error']['businessCode'])
        ) {
            throw new \Promopult\YandexBusinessApi\Exception\BusinessErrorException(
                $request,
                $response,
                $decodedJsonBody['error']['businessCode'],
                $decodedJsonBody['error']['businessData'] ?? null,
                $decodedJsonBody['error']['message'],
                $decodedJsonBody['error']['code']
            );
        }

        if ($response->getStatusCode() !== 200) {
            throw new \Promopult\YandexBusinessApi\Exception\ServerErrorException(
                $request,
                $response,
                $decodedJsonBody['error']['requestId'] ?? '',
                $decodedJsonBody['error']['message'] ?? ($response->getBody()->__toString() ?: $response->getReasonPhrase()),
                $decodedJsonBody['error']['code'] ?? $response->getStatusCode()
            );
        }

        $response->getBody()->rewind();
    }

    private function filterParams(array $params): array
    {
        return array_filter($params, function ($param) {
            return $param !== null;
        });
    }
}
