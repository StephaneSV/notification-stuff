<?php

namespace App\Api;

class ApiResponse implements \JsonSerializable
{
    public const HTTP_OK = 200;
    public const HTTP_CREATED = 201;
    public const HTTP_NO_CONTENT = 204;
    public const HTTP_BAD_REQUEST = 400;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_NOT_FOUND = 404;

    /**
     * @var string|null
     */
    private ?string $message = null;

    /**
     * @var string|null
     */
    private ?string $errors = null;

    /**
     * @var int|null
     */
    private ?int $httpStatus = null;

    /**
     * @var \stdClass
     */
    private \stdClass $jsonData;

    /**
     * @param $arrayOrObjectForData
     */
    public function __construct($arrayOrObjectForData)
    {
        $this->jsonData = new \stdClass();
        foreach ($arrayOrObjectForData as $indexOrProperty => $value) {
            if (!is_numeric($indexOrProperty)) {
                // We ignore any values that is not assigned via a property or an associative index
                $this->jsonData->$indexOrProperty = $value;
            }
        }

        // Now we get the caller of the instanciation
        $caller = 'unknown';
        $trace = debug_backtrace();
        foreach ($trace as $traceIndex => $traceData) {
            if ($traceIndex < 1) {
                // We don't care about the level 0 (this class)
                continue;
            }
            $caller = $traceData['function'] ?? null;
            if ($caller === null) {
                continue;
            }
        }
        $this->jsonData->endpointTitle = $caller;
    }

    /**
     * Cheap header setter for the sake of the exercise
     * @param int $httpStatus
     * @return void
     */
    public function setHeaders(int $httpStatus = self::HTTP_OK): void
    {
        $this->httpStatus = $httpStatus;
        header('Content-type: application/json; charset=utf-8');
        http_response_code($httpStatus);
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return \stdClass
     */
    public function jsonSerialize(): \stdClass
    {
        $result = new \stdClass();

        // We transmit every data property
        foreach ($this->jsonData as $property => $value) {
            if ($value !== null) {
                $result->$property = $value;
            }
        }

        if (!isset($result->message)) {
            $result->message = 'Undefined message';
        }
        if ($this->httpStatus === null) {
            $this->setHeaders();
        }


        // These properties must be kept as-is in jsonData instead of being overwritten
        foreach (['message', 'errors'] as $resultProperty) {
            if (isset($this->jsonData->$resultProperty)) {
                $result->$resultProperty = $this->jsonData->$resultProperty;
            } else {
                if ($this->$resultProperty !== null) {
                    $result->$resultProperty = $this->$resultProperty;
                }
            }
        }

        return $result;
    }
}