<?php

namespace RybakDigital\Bundle\ApiFrameworkBundle\Exception;

use RybakDigital\Bundle\ApiFrameworkBundle\Exception\ErrorCode;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * RybakDigital\Bundle\ApiFrameworkBundle\Exception\ApiException
 *
 * @author Kris Rybak <kris.rybak@krisrybak.com>
 */
class ApiException extends \Exception implements HttpExceptionInterface
{
    protected $statusCode;

    public function __construct($message = null, $errorCode = null, $statusCode = null)
    {
        parent::__construct($message, $errorCode);

        // Assign default http status code

        if (array_key_exists($errorCode, ErrorCode::$errorCodeToHttpStatusMap)) {
            $this->statusCode = ErrorCode::$errorCodeToHttpStatusMap[$errorCode];
        }

        // Force http status code
        if ($statusCode !== null) {
            $this->statusCode = $statusCode;
        }
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Returns the status code.
     *
     * @return int An HTTP response status code
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Returns response headers.
     *
     * @return array Response headers
     */
    public function getHeaders()
    {
        return array();
    }
}
