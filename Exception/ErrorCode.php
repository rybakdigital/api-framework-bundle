<?php

namespace RybakDigital\Api\FrameworkBundle\Exception;

/**
 * RybakDigital\Api\FrameworkBundle\Exception\ErrorCode
 *
 * @author Kris Rybak <kris.rybak@krisrybak.com>
 */
class ErrorCode
{
    # Client errors 4xxxx;
    const ERROR_CLIENT_UNDEFINED = 40000;

    # Client HTTP errors 41xxx;
    const ERROR_CLIENT_HTTP_NOT_FOUND = 41404;
    const ERROR_CLIENT_HTTP_METHOD_NOT_ALLOWED = 41405;

    # Server errors 5xxxx;
    const ERROR_SERVER_UNDEFINED = 50000;

    # Server Process errors 51xxx;
    const ERROR_SERVER_UNSUPPORTED_EXCEPTION = 51001;

    public static $errorMessage = array(
        40000 => 'Unexpected client error',
        41404 => 'Resource not found',
        41405 => 'Method Not Allowed',
        50000 => 'Unexpected system error',
        51001 => 'Unexpected system error: Unsupported Exception Type',
    );
}
