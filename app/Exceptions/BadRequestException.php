<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

/**
 * Excepcion lanzada por errores dentro de requests
 * se puede envolver con el middleware {@link HandleBadRequestExceptionMiddleware}.
 */
class BadRequestException extends \Exception
{
    private $sendToLog = false;

    public function __construct(
        protected array $response_data,
        protected int $response_code = Response::HTTP_BAD_REQUEST,
        $message = '',
        $code = 0,
        \Throwable $previous = null
    ) {
        $this->sendToLog = null != $previous;
        parent::__construct($message, $code, $previous);
    }

    public static function create(array $response_data, int $response_code = Response::HTTP_BAD_REQUEST, \Throwable $origin_exception = null): BadRequestException
    {
        $class = static::class;

        return new $class(response_data: $response_data, response_code: $response_code, message: 'Error al procesar metodo', previous: $origin_exception);
    }

    public function shouldLog()
    {
        return $this->sendToLog;
    }

    public function getResponseData(): array
    {
        return $this->response_data;
    }

    public function getResponseCode(): int
    {
        return $this->response_code;
    }
}
