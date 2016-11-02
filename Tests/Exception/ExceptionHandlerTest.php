<?php

namespace RybakDigital\Api\FrameworkBundle\Tests\Exception;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use RybakDigital\Api\FrameworkBundle\Exception\ExceptionHandler;
use Symfony\Component\Debug\Exception\FlattenException;
use RybakDigital\Api\FrameworkBundle\Exception\ErrorCode;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RequestFormatterTest extends WebTestCase
{
    public function ordinaryExceptionDataProvider()
    {
        $data = array();
        
        $stdClass = new \StdClass;
        $stdClass->message      = ErrorCode::$errorMessage[ErrorCode::ERROR_SERVER_UNSUPPORTED_EXCEPTION];
        $stdClass->code         = 500;
        $stdClass->errorCode    = ErrorCode::ERROR_SERVER_UNSUPPORTED_EXCEPTION;

        $stdClass = array(
            new \StdClass,
            $stdClass,
        );

        $data[] = $stdClass;

        $expectedOrdinary = new \StdClass;
        $expectedOrdinary->message      = ErrorCode::$errorMessage[ErrorCode::ERROR_SERVER_UNDEFINED];
        $expectedOrdinary->code         = 0;
        $expectedOrdinary->errorCode    = ErrorCode::ERROR_SERVER_UNDEFINED;

        $ordinaryException = array(
            new \Exception,
            $expectedOrdinary,
        );

        $data[] = $ordinaryException;

        // Test Flatent Exception
        $message = 'Route not found';
        $code = 404;
        $exception = new HttpException($code, $message);
        $flattenException = new FlattenException($exception);

        $expectedFlatten = new \StdClass;
        $expectedFlatten->message      = ErrorCode::$errorMessage[ErrorCode::ERROR_CLIENT_HTTP_NOT_FOUND];
        $expectedFlatten->code         = 404;
        $expectedFlatten->errorCode    = ErrorCode::ERROR_CLIENT_HTTP_NOT_FOUND;

        $data[] = array(
            $flattenException,
            $expectedFlatten,
        );

        return $data;
    }

    /**
     * @dataProvider ordinaryExceptionDataProvider
     */
    public function testHandleOrdinaryException($exception, $expected)
    {
        $this->assertEquals($expected, ExceptionHandler::handle($exception));
    }
}
