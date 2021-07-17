<?php
declare(strict_types=1);

namespace Promopult\YandexBusinessApi\Exception;

trait HttpExceptionTrait
{
    /**
     * @var \Psr\Http\Message\RequestInterface
     */
    protected $request;

    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $response;

    /**
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getRequest(): \Psr\Http\Message\RequestInterface
    {
        return $this->request;
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getResponse(): \Psr\Http\Message\ResponseInterface
    {
        return $this->response;
    }

    /**
     * @return string
     */
    public function getResponseAsString(): string
    {
        return \GuzzleHttp\Psr7\Message::toString($this->response);
    }

    /**
     * @return string
     */
    public function getRequestAsString(): string
    {
        return \GuzzleHttp\Psr7\Message::toString($this->request);
    }

    public function __toString(): string
    {
        return parent::__toString() . PHP_EOL .
            '>>>' . $this->getRequestAsString() . PHP_EOL .
            '<<<' . $this->getResponseAsString() . PHP_EOL;
    }
}
