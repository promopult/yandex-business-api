<?php

declare(strict_types=1);

namespace Promopult\YandexBusinessApi\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class BusinessErrorException extends \RuntimeException
{
    use HttpExceptionTrait;

    protected string $businessCode;
    private ?array $businessData;

    public function __construct(
        RequestInterface $request,
        ResponseInterface $response,
        string $businessCode,
        ?array $businessData = null,
        string $message = '',
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->businessCode = $businessCode;
        $this->request = $request;
        $this->response = $response;
        $this->businessData = $businessData;
    }

    public function getBusinessCode(): string
    {
        return $this->businessCode;
    }

    public function getBusinessData(): ?array
    {
        return $this->businessData;
    }
}
