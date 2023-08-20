<?php

use Promopult\YandexBusinessApi\Dto\CampaignClient;
use Promopult\YandexBusinessApi\Dto\CampaignContract;
use Promopult\YandexBusinessApi\Dto\CampaignContractor;
use Promopult\YandexBusinessApi\Enum\CampaignClientTypeEnum;
use Promopult\YandexBusinessApi\Enum\CampaignContractorTypeEnum;
use Promopult\YandexBusinessApi\Enum\CampaignContractSubjectTypeEnum;

require '../vendor/autoload.php';

$client = new \Promopult\YandexBusinessApi\Client(
    new \Promopult\YandexBusinessApi\Config(getenv('__ACCESS_TOKEN__')),
    new \GuzzleHttp\Client()
);

$campaign_client = new CampaignClient(
    type: CampaignClientTypeEnum::LEGAL,
    name: 'Зимина Виктория Романовна',
    okveds: ['47.91.2'],
    inn: '7727563778'
);
$campaign_contractor = new CampaignContractor(
    type: CampaignContractorTypeEnum::PHYSICAL,
    name: 'Данилова Василиса Викторовна',
    inn: '588093656296',
    phoneNum: '+7 (911) 222 22 21'
);

$contract = new CampaignContract(
    type: \Promopult\YandexBusinessApi\Enum\CampaignContractTypeEnum::CONTRACT,
    number: '123456789',
    date: '2023-07-17',
    amount: '200000',
    isVat: true,
    subjectType: CampaignContractSubjectTypeEnum::DISTRIBUTION,
);

$response = $client
    ->setCampaignBeneficiaryV5(getenv('__CAMPAIGN_ID__'), $campaign_client, $campaign_contractor, $contract);

var_dump($response);
