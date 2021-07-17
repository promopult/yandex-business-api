<?php

require '../vendor/autoload.php';

$client = new \Promopult\YandexBusinessApi\Client(
    new \Promopult\YandexBusinessApi\Config(getenv('__ACCESS_TOKEN__')),
    new \GuzzleHttp\Client()
);

$response = $client
    ->useClientLogin(getenv('__CLIENT_LOGIN__'))
    ->isOwner(null, getenv('__COMPANY_ID__'), 225);

var_dump($response);
