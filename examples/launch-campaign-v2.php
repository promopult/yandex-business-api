<?php

require '../vendor/autoload.php';

$client = new \Promopult\YandexBusinessApi\Client(
    new \Promopult\YandexBusinessApi\Config(getenv('__ACCESS_TOKEN__')),
    new \GuzzleHttp\Client()
);

$client->launchCampaignV2(
    getenv('__COMPANY_ID__'),
    'geoproduct',
    'DEFAULT',
    90
);
