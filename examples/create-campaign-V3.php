<?php

require '../vendor/autoload.php';

$client = new \Promopult\YandexBusinessApi\Client(
    new \Promopult\YandexBusinessApi\Config(getenv('__ACCESS_TOKEN__')),
    new \GuzzleHttp\Client()
);

$response = $client
    ->useClientLogin(getenv('__CLIENT_LOGIN__'))
    ->createCampaignV3(
        'GEO',
        getenv('__COMPANY_ID__'),
        null,
        225,
        'Тестовая кампания для ' . getenv('__CLIENT_LOGIN__')
    );

var_dump($response);
