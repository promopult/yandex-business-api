<?php
declare(strict_types=1);

namespace Promopult\YandexBusinessApi\Exception;

class ServerErrorException extends \RuntimeException
{
    use HttpExceptionTrait;

    /**
     * @var string
     */
    protected $requestId;

    public function __construct(
        \Psr\Http\Message\RequestInterface $request,
        \Psr\Http\Message\ResponseInterface $response,
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

    /**
     * @return string
     */
    public function getRequestId(): string
    {
        return $this->requestId;
    }
}
