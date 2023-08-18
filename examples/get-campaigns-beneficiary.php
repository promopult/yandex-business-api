<?php

require '../vendor/autoload.php';

$client = new \Promopult\YandexBusinessApi\Client(
    new \Promopult\YandexBusinessApi\Config(getenv('__ACCESS_TOKEN__')),
    new \GuzzleHttp\Client()
);

$response = $client
    ->getCampaignBeneficiaryV5(getenv('__CAMPAIGN_ID__'));

var_dump($response);
