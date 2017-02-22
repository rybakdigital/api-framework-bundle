<?php

namespace RybakDigital\Bundle\ApiFrameworkBundle\Tests\Exception;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use RybakDigital\Bundle\ApiFrameworkBundle\Exception\ApiException;
use RybakDigital\Bundle\ApiFrameworkBundle\Exception\ErrorCode;
use RybakDigital\Bundle\ApiFrameworkBundle\Exception\ExceptionHandler;

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
        $exception          = new NotFoundHttpException($message);
        $flattenException   = FlattenException::create($exception);

        $expectedFlatten = new \StdClass;
        $expectedFlatten->message      = ErrorCode::$errorMessage[ErrorCode::ERROR_CLIENT_HTTP_NOT_FOUND];
        $expectedFlatten->code         = 404;
        $expectedFlatten->errorCode    = ErrorCode::ERROR_CLIENT_HTTP_NOT_FOUND;

        $data[] = array(
            $flattenException,
            $expectedFlatten,
        );

        // Test MethodNotAllowedHttpException Exception
        $message = 'Method Not Allowed';
        $code = 405;
        $exception              = new MethodNotAllowedHttpException(array('GET'), $message);
        $flattenExceptionTwo    = FlattenException::create($exception);

        $expectedFlattenTwo = new \StdClass;
        $expectedFlattenTwo->message      = ErrorCode::$errorMessage[ErrorCode::ERROR_CLIENT_HTTP_METHOD_NOT_ALLOWED];
        $expectedFlattenTwo->code         = 405;
        $expectedFlattenTwo->errorCode    = ErrorCode::ERROR_CLIENT_HTTP_METHOD_NOT_ALLOWED;

        $data[] = array(
            $flattenExceptionTwo,
            $expectedFlattenTwo,
        );

        // Test BadRequestHttpException Exception
        $message = 'Bad Request';
        $code = 400;
        $exception              = new BadRequestHttpException($message);
        $flattenExceptionTwo    = FlattenException::create($exception);

        $expectedFlattenTwo = new \StdClass;
        $expectedFlattenTwo->message      = ErrorCode::$errorMessage[ErrorCode::ERROR_CLIENT_HTTP_BAD_REQUEST];
        $expectedFlattenTwo->code         = 400;
        $expectedFlattenTwo->errorCode    = ErrorCode::ERROR_CLIENT_HTTP_BAD_REQUEST;

        $data[] = array(
            $flattenExceptionTwo,
            $expectedFlattenTwo,
        );

        // Test ApiException Exception
        $message    = ErrorCode::$errorMessage[ErrorCode::ERROR_CLIENT_DATA_VALIDATOR_FAIL];
        $code       = ErrorCode::ERROR_CLIENT_DATA_VALIDATOR_FAIL;
        $exception  = new ApiException($message, $code);
        $flattenExceptionTwo    = FlattenException::create($exception);

        $expectedFlattenTwo = new \StdClass;
        $expectedFlattenTwo->message      = ErrorCode::$errorMessage[ErrorCode::ERROR_CLIENT_DATA_VALIDATOR_FAIL];
        $expectedFlattenTwo->code         = 400;
        $expectedFlattenTwo->errorCode    = ErrorCode::ERROR_CLIENT_DATA_VALIDATOR_FAIL;

        $data[] = array(
            $flattenExceptionTwo,
            $expectedFlattenTwo,
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

    public function getInfoDataProvider()
    {
        $data = array();

        $exception = new \Exception('Message', 404);

        $data[] = array($exception, 'Exception', 'Message');

        // Test Flatent Exception
        $message = 'Route not found';
        $code = 404;
        $exception          = new NotFoundHttpException($message);
        $flattenException   = FlattenException::create($exception);

        $expectedFlatten = new \StdClass;
        $expectedFlatten->message      = ErrorCode::$errorMessage[ErrorCode::ERROR_CLIENT_HTTP_NOT_FOUND];
        $expectedFlatten->code         = 404;
        $expectedFlatten->errorCode    = ErrorCode::ERROR_CLIENT_HTTP_NOT_FOUND;

        $data[] = array(
            $flattenException,
            get_class($exception),
            $message
        );

        $exception = new \ErrorException;
        $data[] = array($exception, get_class($exception), 'Unsupported type \'ErrorException\' had been passed to ExceptionHandler');

        return $data;
    }

    /**
     * @dataProvider getInfoDataProvider
     */
    public function testGetInfo($exception, $expectedClass, $message = null)
    {
        $info = ExceptionHandler::getInfo($exception);
        $this->assertTrue(is_object($info));
        $this->assertSame($info->class, $expectedClass);
        $this->assertSame($info->message, $message);
    }
}
