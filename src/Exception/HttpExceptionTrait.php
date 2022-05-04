<?php
declare(strict_types=1);

namespace Promopult\YandexBusinessApi\Exception;

use GuzzleHttp\Psr7\Message;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait HttpExceptionTrait
{
    protected RequestInterface $request;
    protected ResponseInterface $response;

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function getResponseAsString(): string
    {
        return Message::toString($this->response);
    }

    public function getRequestAsString(): string
    {
        return Message::toString($this->request);
    }

    public function __toString(): string
    {
        return parent::__toString() . PHP_EOL .
            '>>>' . $this->getRequestAsString() . PHP_EOL .
            '<<<' . $this->getResponseAsString() . PHP_EOL;
    }
}
