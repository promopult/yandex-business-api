<?php

require '../vendor/autoload.php';

$client = new \Promopult\YandexBusinessApi\Client(
    new \Promopult\YandexBusinessApi\Config(getenv('__ACCESS_TOKEN__')),
    new \GuzzleHttp\Client()
);

$client->launchCampaignV3(
    getenv('__CAMPAIGN_ID__'),
    'DEFAULT',
    90
);
