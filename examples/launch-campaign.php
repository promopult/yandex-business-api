<?php

require '../vendor/autoload.php';

$client = new \Promopult\YandexBusinessApi\Client(
    new \Promopult\YandexBusinessApi\Config(getenv('__ACCESS_TOKEN__')),
    new \GuzzleHttp\Client()
);

$client->launchCampaignV4(
    getenv('__CAMPAIGN_ID__'),
    90,
    100
);
