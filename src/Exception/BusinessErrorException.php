<?php
declare(strict_types=1);

namespace Promopult\YandexBusinessApi\Exception;

class BusinessErrorException extends \RuntimeException
{
    use HttpExceptionTrait;

    /**
     * @var string
     */
    protected $businessCode;

    /**
     * @var array|null
     */
    private $businessData;

    public function __construct(
        \Psr\Http\Message\RequestInterface $request,
        \Psr\Http\Message\ResponseInterface $response,
        string $businessCode,
        ?array $businessData = null,
        string $message = '',
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->businessCode = $businessCode;
        $this->request = $request;
        $this->response = $response;
        $this->businessData = $businessData;
    }

    /**
     * @return string
     */
    public function getBusinessCode(): string
    {
        return $this->businessCode;
    }

    /**
     * @return array|null
     */
    public function getBusinessData(): ?array
    {
        return $this->businessData;
    }
}
