<?php

namespace RybakDigital\Bundle\ApiFrameworkBundle\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * RybakDigital\Bundle\ApiFrameworkBundle\Exception\ErrorCode
 *
 * @author Kris Rybak <kris.rybak@krisrybak.com>
 */
class ErrorCode
{
    # Client errors 4xxxx;
    const ERROR_CLIENT_UNDEFINED = 40001;
    const ERROR_CLIENT_DATA_VALIDATOR_FAIL = 40002;
    const ERROR_CLIENT_DUPLICATE_ENTRY = 40003;
    const ERROR_CLIENT_DATA_FILTER_UNAVAILABLE = 40004;

    # Client HTTP errors 41xxx;
    const ERROR_CLIENT_HTTP_BAD_REQUEST = 40000;
    const ERROR_CLIENT_HTTP_NOT_FOUND = 40400;
    const ERROR_CLIENT_HTTP_FORBIDDEN = 40300;
    const ERROR_CLIENT_HTTP_FORBIDDEN_LIMIT_EXCEEDED = 40301;
    const ERROR_CLIENT_HTTP_METHOD_NOT_ALLOWED = 40500;

    # Server errors 5xxxx;
    const ERROR_SERVER_UNDEFINED = 50000;
    const ERROR_SERVER_GENERIC_DB_ERROR  = 50001;

    # Server Process errors 51xxx;
    const ERROR_SERVER_UNSUPPORTED_EXCEPTION = 51001;

    public static $errorMessage = array(
        40001 => 'Unexpected client error',
        40002 => 'Bad request. Data validation failed',
        40003 => 'Bad request. Duplicate entry',
        40004 => 'Requsted filter is not available for given data set',
        40000 => 'Bad request',
        40300 => 'Forbidden',
        40301 => 'Forbidden. API rate limit exceeded',
        40400 => 'Resource not found',
        40500 => 'Method Not Allowed',
        50000 => 'Unexpected system error',
        50001 => 'Unexpected system error. Database error',
        51001 => 'Unexpected system error: Unsupported Exception Type',
    );

    public static $errorCodeToHttpStatusMap = array(
        self::ERROR_CLIENT_UNDEFINED                    => Response::HTTP_BAD_REQUEST,
        self::ERROR_CLIENT_DATA_VALIDATOR_FAIL          => Response::HTTP_BAD_REQUEST,
        self::ERROR_CLIENT_DUPLICATE_ENTRY              => Response::HTTP_BAD_REQUEST,
        self::ERROR_CLIENT_HTTP_BAD_REQUEST             => Response::HTTP_BAD_REQUEST,
        self::ERROR_CLIENT_DATA_FILTER_UNAVAILABLE      => Response::HTTP_BAD_REQUEST,
        self::ERROR_CLIENT_HTTP_FORBIDDEN               => Response::HTTP_FORBIDDEN,
        self::ERROR_CLIENT_HTTP_FORBIDDEN_LIMIT_EXCEEDED    => Response::HTTP_FORBIDDEN,
        self::ERROR_CLIENT_HTTP_NOT_FOUND               => Response::HTTP_NOT_FOUND,
        self::ERROR_CLIENT_HTTP_METHOD_NOT_ALLOWED      => Response::HTTP_METHOD_NOT_ALLOWED,
        self::ERROR_SERVER_GENERIC_DB_ERROR             => Response::HTTP_INTERNAL_SERVER_ERROR,
    );
}
