<?php

require '../vendor/autoload.php';

$client = new \Promopult\YandexBusinessApi\Client(
    new \Promopult\YandexBusinessApi\Config(getenv('__ACCESS_TOKEN__')),
    new \GuzzleHttp\Client()
);

$response = $client
    ->useClientLogin(getenv('__CLIENT_LOGIN__'))
    ->getCampaignV4(getenv('__CAMPAIGN_ID__'));

var_dump($response);
