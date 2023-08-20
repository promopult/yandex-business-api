<?php

declare(strict_types=1);

namespace Promopult\YandexBusinessApi;

use GuzzleHttp\Psr7\Request;
use Promopult\YandexBusinessApi\Dto\CampaignBeneficiary;
use Promopult\YandexBusinessApi\Dto\CampaignClient;
use Promopult\YandexBusinessApi\Dto\CampaignContract;
use Promopult\YandexBusinessApi\Dto\CampaignContractor;
use Promopult\YandexBusinessApi\Exception\BusinessErrorException;
use Promopult\YandexBusinessApi\Exception\ServerErrorException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class Client
{
    private ClientInterface $httpClient;
    private ConfigInterface $config;

    public function __construct(
        ConfigInterface $config,
        ClientInterface $httpClient
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
     * @throws ClientExceptionInterface
     *
     * @see https://yandex.ru/dev/business-api/doc/ref/Campaign_management/GetCampaingGet.html
     *
     * @deprecated
     */
    public function getCampaign(int $campaignId): array
    {
        $response = $this->requestApi(
            'GET',
            '/priority/v1/get-campaign',
            [
                'campaignId' => $campaignId,
            ]
        );

        return json_decode($response->getBody()->__toString(), true);
    }

    /**
     * Информация о рекламной кампании
     *
     * @param int $campaignId   ID рекламной кампании
     * @return array
     *
     * @throws ClientExceptionInterface
     *
     * @see https://yandex.ru/dev/business-api/doc/ref/Campaign_management/GetCampaingGetV4.html
     */
    public function getCampaignV4(int $campaignId): array
    {
        $response = $this->requestApi(
            'GET',
            '/priority/v4/get-campaign',
            [
                'campaignId' => $campaignId,
            ]
        );

        return json_decode($response->getBody()->__toString(), true);
    }

    /**
     * Пинг API
     *
     * @return array
     *
     * @throws ClientExceptionInterface
     */
    public function pingping(): array
    {
        $request = $this->createRequest('POST', '/priority/v1/pingping');
        $response = $this->httpClient->sendRequest($request);

        $this->handleErrors($request, $response);

        return json_decode($response->getBody()->__toString(), true);
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
     * @throws ClientExceptionInterface
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
     * @throws ClientExceptionInterface
     *
     * @deprecated
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
     * Запуск рекламной кампании
     *
     * @param int $campaignId       ID рекламной кампании
     * @param int $duration         Продолжительность рекламной кампании (90 | 180 | 360)
     * @param float $monthAmount    Сумма месячного бюджета рекламной кампании. Должна быть не меньше значения
     *                              в MINIMAL бюджете за 30 дней
     * @return void
     * @throws ClientExceptionInterface
     *
     * @see https://yandex.ru/dev/business-api/doc/ref/Campaign_management/LaunchCampaignPostV4.html
     */
    public function launchCampaignV4(
        int $campaignId,
        int $duration,
        float $monthAmount
    ): void {
        $this->requestApi(
            'POST',
            '/priority/v3/launch-campaign',
            [
                'campaignId' => $campaignId,
                'monthAmount' => $monthAmount,
                'duration' => $duration,
            ]
        );
    }

    /**
     * Указание рекламодателя
     *
     * @param int $campaignId   ID рекламной кампании
     * @param string $email     Email рекламодателя
     * @param string $inn       ИНН рекламодателя. Обязателен для типов рекламодателей:
     *                            - LEGAL - Юридическое лицо (12 символов)
     *                            - FOREIGN_LEGAL - Иностранное юридическое лицо (от 1 до 50 символов)
     *                            - INDIVIDUAL - ИП (12 символов)
     * @param string $phone     Номер телефона рекламодателя. Обязательное поле для типов рекламодателей:
     *                            - FOREIGN_PHYSICAL - Иностранное физическое лицо
     *                            - PHYSICAL - Физическое лицо
     * @param string $type      Тип рекламодателя. Возможные значения:
     *                              - FOREIGN_LEGAL - Иностранное юридическое лицо
     *                              - FOREIGN_PHYSICAL - Иностранное физическое лицо
     *                              - INDIVIDUAL - ИП
     *                              - LEGAL - Юридическое лицо
     *                              - PHYSICAL - Физическое лицо
     *
     * @return array
     *
     * @throws ClientExceptionInterface
     *
     * @see https://yandex.ru/dev/business-api/doc/ref/Campaign_management/CampaignBeneficiaryV4.html
     */
    public function setCampaignBeneficiaryV4(
        int $campaignId,
        string $email,
        string $inn,
        string $phone,
        string $type
    ): array {
        $response = $this->requestApi(
            'POST',
            '/priority/v4/campaign-beneficiary',
            [
                'campaignId' => $campaignId,
                'beneficiary' => [
                    'email' => $email,
                    'inn' => $inn,
                    'phone' => $phone,
                    'type' => $type,
                ]
            ]
        );

        return json_decode($response->getBody()->__toString(), true);
    }

    /**
     * Заполнение данных о рекламодателе
     *
     * @param int                $campaignId Id рекламной кампании
     * @param CampaignClient     $campaign_client Данные конечного рекламодателя
     * @param CampaignContractor $campaign_contractor Посредник
     * @param CampaignContract   $campaign_contract Договор между клиентом и посредником, обязательно если
     *
     * @return array
     */
    public function setCampaignBeneficiaryV5(
        int $campaignId,
        CampaignClient $campaign_client,
        CampaignContractor $campaign_contractor,
        CampaignContract $campaign_contract
    ): array {
        $body =[
            'campaignId' => $campaignId,
            'beneficiary' => [
                'client'=>$campaign_client->toArray(),
                'contractor'=>$campaign_contractor->toArray(),
                'contract'=>$campaign_contract->toArray()
            ]
        ];

        $response = $this->requestApi(
            'POST',
            '/priority/v5/campaign-beneficiary',
            $body
        );

        return json_decode($response->getBody()->__toString(), true);
    }

    /**
     * Получение данных о рекламодателе
     *
     * @param int $campaign_id Id рекламной кампании
     *
     * @return CampaignBeneficiary|null
     */
    public function getCampaignBeneficiaryV5(int $campaign_id): ?CampaignBeneficiary
    {
        $response = $this->requestApi(
            'GET',
            '/priority/v5/get-campaign-beneficiary?campaignId='.$campaign_id
        );

        $content =  json_decode($response->getBody()->__toString(), true);
        if (empty($content['data'])){
            return null;
        }

        return CampaignBeneficiary::fromArray($content['data']);
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
     * @throws ClientExceptionInterface
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

        return json_decode($response->getBody()->__toString(), true);
    }


    /**
     * Получение цен
     *
     * Показывает доступные бюджеты для конкретной рекламной кампании.
     *
     * @param int $countryGeoId
     * @param ?int $campaignId
     * @param ?int $companyId
     * @param ?int $chainId
     * @param bool $branding
     *
     * @return array
     *
     * @throws ClientExceptionInterface
     *
     * @deprecated
     */
    public function getPricesV3(
        int $countryGeoId,
        ?int $campaignId,
        ?int $companyId,
        ?int $chainId,
        bool $branding
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

        return json_decode($response->getBody()->__toString(), true);
    }

    /**
     * Получение цен для кампании
     *
     * @param int $campaignId   ID рекламной кампании
     * @return array
     *
     * @throws ClientExceptionInterface
     *
     * @see https://yandex.ru/dev/business-api/doc/ref/Campaign_management/CampaignPriceGetV4.html
     */
    public function getPricesV4(
        int $campaignId
    ): array {
        $response = $this->requestApi(
            'GET',
            '/priority/v4/campaign-price',
            [
                'campaignId' => $campaignId,
            ]
        );

        return json_decode($response->getBody()->__toString(), true);
    }

    /**
     * Баланс кошелька (нужно для перевода средств).
     *
     * @return array
     * @throws ClientExceptionInterface
     */
    public function getBalance(): array
    {
        $request = $this->createRequest('GET', '/priority/v1/get-balance-client');
        $response = $this->httpClient->sendRequest($request);

        $this->handleErrors($request, $response);

        return json_decode($response->getBody()->__toString(), true);
    }

    /**
     * Депозит
     *
     * @param int $walletId
     * @param string $contract
     * @param string $currency # Варианты 'byn' | 'kzt' | 'rub' | 'uah'
     * @param float $amount
     * @throws ClientExceptionInterface
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
     * @throws ClientExceptionInterface
     *
     * @deprecated
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
     * @throws ClientExceptionInterface
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
     * @throws ClientExceptionInterface
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

        return json_decode($response->getBody()->__toString(), true);
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
     * @throws ClientExceptionInterface
     *
     * @deprecated
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

        return json_decode($response->getBody()->__toString(), true);
    }

    /**
     * @param bool $mapsOnly            Создание кампании с рекламой только на Яндекс Картах
     * @param int|null $companyId       Идентификатор организации, у которой только одна физическая точка
     * @param int|null $chainId         ID организации, у которых несколько физических точек (сетевые или франшизы)
     * @param int|null $countryGeoId    Географический идентификатор страны. Обязательный при указании chainId
     * @param string|null $url          Рекламируемый сайт. Не учитывается при указании chainId или mapsOnly
     *
     * @return array
     *
     * @throws ClientExceptionInterface
     *
     * @see https://yandex.ru/dev/business-api/doc/ref/Campaign_management/CreateCampaignPostV4.html
     */
    public function createCampaignV4(
        bool $mapsOnly,
        ?int $companyId = null,
        ?int $chainId = null,
        ?int $countryGeoId = null,
        ?string $url = null
    ): array {
        $request = $this->createRequest(
            'POST',
            '/priority/v4/create-campaign',
            [
                'companyId' => $companyId,
                'chainId' => $chainId,
                'countryGeoId' => $countryGeoId,
                'mapsOnly' => $mapsOnly,
                'url' => $url
            ]
        );

        $response = $this->httpClient->sendRequest($request);

        $this->handleErrors($request, $response);

        return json_decode($response->getBody()->__toString(), true);
    }

    /**
     * @param int|null $companyId
     * @param int|null $chainId
     * @param int|null $countryGeoId
     * @return array
     * @throws ClientExceptionInterface
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

        return json_decode($response->getBody()->__toString(), true);
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
     * @throws ClientExceptionInterface
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

        return json_decode($response->getBody()->__toString(), true);
    }

    /**
     *
     * @param int $limit
     * @param int $offset
     *
     * @return array
     *
     * @throws ClientExceptionInterface
     *
     * @deprecated
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

        return json_decode($response->getBody()->__toString(), true);
    }

    public function getCampaignsV4(
        int $limit,
        int $offset = 0
    ): array {
        $response = $this->requestApi(
            'GET',
            '/priority/v4/get-campaigns',
            [
                'limit' => $limit,
                'offset' => $offset,
            ]
        );

        return json_decode($response->getBody()->__toString(), true);
    }

    /**
     * @param int $countryGeoId // countryGeoId страны из поиска
     * @param int|null $companyId // id орги из поиска, если одноорг
     * @param int|null $chainId // chainId сети из поиска, если сеть
     * @param bool $branding
     * @param array $arbitrateRates
     * @return array
     *
     * @throws ClientExceptionInterface
     *
     * @deprecated
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

        return json_decode($response->getBody()->__toString(), true);
    }

    /**
     * Применение промокода к кампании
     *
     * @param int $campaignId
     * @param string $promocode
     * @return array
     *
     * @throws ClientExceptionInterface
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

        return json_decode($response->getBody()->__toString(), true);
    }

    /**
     * Сброс промокода
     *
     * @param int $campaignId
     *
     * @return array
     *
     * @throws ClientExceptionInterface
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

        return json_decode($response->getBody()->__toString(), true);
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
     * @throws ClientExceptionInterface
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

        return json_decode($response->getBody()->__toString(), true);
    }

    /**
     * Получение статуса задачи и результата
     * Получает статус выполнения задачи по её ID полученной в /priority/v1/generate-commercial-offer
     *
     * @param int $taskId
     * @return array
     * @throws ClientExceptionInterface
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

        return json_decode($response->getBody()->__toString(), true);
    }

    public function requestApi(
        string $httpMethod,
        string $endpoint,
        ?array $params = null
    ): ResponseInterface {
        $request = $this->createRequest(
            $httpMethod,
            $endpoint,
            $params
        );

        $response = $this->httpClient->sendRequest($request);

        $this->handleErrors($request, $response);

        return $response;
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
    ): RequestInterface {
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

        return new Request($method, $uri, $headers, $body);
    }

    private function handleErrors(
        RequestInterface $request,
        ResponseInterface $response
    ): void {
        $decodedJsonBody = json_decode($response->getBody()->__toString(), true);

        if (isset($decodedJsonBody['error'])
            && isset($decodedJsonBody['error']['businessCode'])
        ) {
            throw new BusinessErrorException(
                $request,
                $response,
                $decodedJsonBody['error']['businessCode'],
                $decodedJsonBody['error']['businessData'] ?? null,
                $decodedJsonBody['error']['message'],
                $decodedJsonBody['error']['code']
            );
        }

        if ($response->getStatusCode() !== 200) {
            throw new ServerErrorException(
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
