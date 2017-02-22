<?php

namespace RybakDigital\Bundle\ApiFrameworkBundle\Tests\Exception;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use RybakDigital\Bundle\ApiFrameworkBundle\Exception\ErrorCode;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use RybakDigital\Bundle\ApiFrameworkBundle\Exception\ApiException;

class ApiExceptionTest extends WebTestCase
{
    public function testSetStatusCode()
    {
        $exception = new ApiException();
        $this->assertTrue(is_a($exception->setStatusCode(400), ApiException::class));
    }

    public function testErrorCodeProvider()
    {
        $data = array();

        foreach (ErrorCode::$errorMessage as $code => $message) {
            $data[] = array($message, $code, (isset(ErrorCode::$errorCodeToHttpStatusMap[$code])) ? ErrorCode::$errorCodeToHttpStatusMap[$code] : null);
        }

        return $data;
    }

    /**
     * @dataProvider testErrorCodeProvider
     */
    public function testErrorCode($message, $errorCode, $statusCode = null)
    {
        $exception = new ApiException($message, $errorCode, $statusCode);
        $this->assertTrue(is_a($exception, ApiException::class));
        $this->assertEquals($exception->getMessage(), $message);
        $this->assertEquals($exception->getCode(), $errorCode);
        if (!is_null($statusCode)) {
            $this->assertEquals($exception->getStatusCode(), $statusCode);
        }
    }

    public function testGetHeaders()
    {
        $exception = new ApiException();
        $this->assertTrue(is_array($exception->getHeaders()));
    }
}
