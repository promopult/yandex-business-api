<?php

namespace Promopult\YandexBusinessApi\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Promopult\YandexBusinessApi\Client;
use Promopult\YandexBusinessApi\Config;
use Promopult\YandexBusinessApi\Exception\BusinessErrorException;
use Promopult\YandexBusinessApi\Exception\ServerErrorException;

class ClientTest extends TestCase
{
    public function testBusinessErrorResponse()
    {
        $mock = new MockHandler([
            new Response(422, [], json_encode([
                'error' => [
                    'code' => 422,
                    'message' => 'Message',
                    'businessCode' => 'RESOURCE_ACCESS_FORBIDDEN'
                ]
            ]))
        ]);

        $client = new Client(
            new Config('', ''),
            new \GuzzleHttp\Client(['handler' => HandlerStack::create($mock)])
        );

        $this->expectException(BusinessErrorException::class);

        $client->pingping();
    }

    public function testServerErrorResponse()
    {
        $mock = new MockHandler([
            new Response(400, [], json_encode([
                'error' => [
                    'code' => 400,
                    'message' => 'Message',
                ]
            ]))
        ]);

        $client = new Client(
            new Config('', ''),
            new \GuzzleHttp\Client(['handler' => HandlerStack::create($mock)])
        );

        $this->expectException(ServerErrorException::class);

        $client->pingping();
    }

    public function testSuccessResponse()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'data' => 'pongpong'
            ]))
        ]);

        $client = new Client(
            new Config('', ''),
            new \GuzzleHttp\Client(['handler' => HandlerStack::create($mock)])
        );

        $response = $client->pingping();

        $this->assertEquals(['data' => 'pongpong'], $response);
    }
}
