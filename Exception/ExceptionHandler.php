<?php

namespace RybakDigital\Bundle\ApiFrameworkBundle\Exception;

use RybakDigital\Bundle\ApiFrameworkBundle\Exception\ErrorCode;
use Symfony\Component\Debug\Exception\FlattenException;
use RybakDigital\Bundle\ApiFrameworkBundle\Exception\ApiException;

/**
 * RybakDigital\Bundle\ApiFrameworkBundle\Exception\ExceptionHandler
 *
 * @author Kris Rybak <kris.rybak@krisrybak.com>
 */
class ExceptionHandler
{
    /**
     * @static  array
     */
    public static $validExceptionClasses = array(
        'Symfony\Component\Debug\Exception\FlattenException',
        'Exception',
        'RybakDigital\Bundle\ApiFrameworkBundle\Exception\ApiException',
        'Ucc\Exception\Data\InvalidDataValueException'
    );

    /**
     * Handles exceptions
     *
     * @param   $exception
     * @return  StdClass
     */
    public static function handle($exception)
    {
        return self::parse($exception);
    }

    /**
     * Parses exceptions
     *
     * @param   $exception
     * @return  StdClass
     */
    public static function parse($exception)
    {
        // Reserve space
        $data = new \StdClass;

        if (!self::isValidException($exception)) {
            return self::getUnsupportedException();
        }

        // Set defaults for error, this can be overridden by specific cases
        $data->message      = $exception->getMessage();
        $data->code         = $exception->getCode();
        $data->errorCode    = ErrorCode::ERROR_SERVER_UNDEFINED;
        $data->message      = ErrorCode::$errorMessage[$data->errorCode];

        if ($exception instanceof FlattenException) {
            // Set ststus code
            $data->errorCode    = self::getErrorCode($exception);
            $data->message      = ErrorCode::$errorMessage[$data->errorCode];
            $data->code         = $exception->getStatusCode();
        }

        return $data;
    }

    /**
     * Gets additional information about exceptions
     *
     * @param   $exception
     * @return  StdClass
     */
    public static function getInfo($exception)
    {
        $info           = new \StdClass;
        $info->class    = get_class($exception);

        if (self::isValidException($exception)) {
            // Get info details about exception
            $info->message  = $exception->getMessage();

            if ($exception instanceof FlattenException) {
                $info->class = $exception->getClass();
            }

            return $info;
        }

        $info->message = 'Unsupported type \'' . $info->class . '\' had been passed to ExceptionHandler';

        return $info;
    }

    /**
     * Checks if passed exception is supported by the ExceptionHandler
     *
     * @param   mixed   $exception
     * @return  boolean
     */
    protected static function isValidException($exception)
    {
        if (in_array(get_class($exception), self::$validExceptionClasses)) {
            return true;
        }

        return false;
    }

    /**
     * Returns undefined server exception data
     *
     * @return  StdClass
     */
    protected static function getUnsupportedException()
    {
        $data = new \StdClass;
        $data->message      = ErrorCode::$errorMessage[ErrorCode::ERROR_SERVER_UNSUPPORTED_EXCEPTION];
        $data->code         = 500;
        $data->errorCode    = ErrorCode::ERROR_SERVER_UNSUPPORTED_EXCEPTION;

        return $data;
    }

    /**
     * Gets errorCode from passed exception
     *
     * @param   mixed   $exception
     * @return  boolean
     */
    protected static function getErrorCode($exception)
    {
        $errorCode = ErrorCode::ERROR_SERVER_UNDEFINED;

        if ($exception instanceof FlattenException) {
            switch ($exception->getClass()) {
                case 'Symfony\Component\HttpKernel\Exception\NotFoundHttpException':
                    $errorCode    = ErrorCode::ERROR_CLIENT_HTTP_NOT_FOUND;
                    break;

                case 'Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException':
                    $errorCode    = ErrorCode::ERROR_CLIENT_HTTP_METHOD_NOT_ALLOWED;
                    break;

                case 'Symfony\Component\HttpKernel\Exception\BadRequestHttpException':
                    $errorCode    = ErrorCode::ERROR_CLIENT_HTTP_BAD_REQUEST;
                    break;

                case 'Ucc\Exception\Data\InvalidDataValueException':
                    $errorCode    = ErrorCode::ERROR_CLIENT_DATA_FILTER_UNAVAILABLE;
                    break;

                case 'RybakDigital\Bundle\ApiFrameworkBundle\Exception\ApiException':
                    $errorCode    = ErrorCode::ERROR_CLIENT_HTTP_BAD_REQUEST;

                    if (array_key_exists($exception->getCode(), ErrorCode::$errorMessage)) {
                        $errorCode = $exception->getCode();
                    }
                    break;
            }
        }

        return $errorCode;
    }
}
