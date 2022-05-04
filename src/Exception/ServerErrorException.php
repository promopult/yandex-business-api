<?php

declare(strict_types=1);

namespace Promopult\YandexBusinessApi\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ServerErrorException extends \RuntimeException
{
    use HttpExceptionTrait;

    protected string $requestId;

    public function __construct(
        RequestInterface $request,
        ResponseInterface $response,
        string $requestId = "",
        string $message = "",
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->request = $request;
        $this->response = $response;
        $this->requestId = $requestId;
    }

    public function getRequestId(): string
    {
        return $this->requestId;
    }
}
