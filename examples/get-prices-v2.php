<?php

require '../vendor/autoload.php';


$client = new \Promopult\YandexBusinessApi\Client(
    new \Promopult\YandexBusinessApi\Config(getenv('__ACCESS_TOKEN__')),
    new \GuzzleHttp\Client()
);

$response = $client->getPricesV2(
    225,
    getenv('__COMPANY_ID__'),
    null
);

var_dump($response);
