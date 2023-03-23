<?php

namespace App\Api;

use App\Api\ApiPagination;

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
     * @var ApiPagination|null
     */
    private ?ApiPagination $pagination = null;

    /**
     * @var string|null
     */
    private ?string $errors = null;

    /**
     * @var int
     */
    private int $httpStatus = self::HTTP_OK;

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

        // Now the pagination
        if ($this->pagination !== null) {
            $result->pagination = $this->pagination;
        }

        return $result;
    }

    /**
     * @param int $page
     * @param int $perPage
     * @param int $totalOfElementsToPaginate
     * @return void
     */
    public function setPagination(int $page, int $perPage, int $totalOfElementsToPaginate = 0): void
    {
        $this->pagination = new ApiPagination($page, $perPage, $totalOfElementsToPaginate);
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
}