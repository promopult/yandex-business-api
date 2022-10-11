<?php

require '../vendor/autoload.php';

$client = new \Promopult\YandexBusinessApi\Client(
    new \Promopult\YandexBusinessApi\Config(getenv('__ACCESS_TOKEN__')),
    new \GuzzleHttp\Client()
);

$response = $client
    ->useClientLogin(getenv('__CLIENT_LOGIN__'))
    ->createCampaignV4(
        true,
        getenv('__COMPANY_ID__'),
        null,
        225
    );

var_dump($response);
