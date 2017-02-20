<?php

namespace RybakDigital\Bundle\ApiFrameworkBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use RybakDigital\Bundle\ApiFrameworkBundle\Service\RequestHandler;

class RequestHandlerTest extends WebTestCase
{
    private $container;

    public function setUp()
    {
        self::bootKernel();

        $this->container = self::$kernel->getContainer();
    }

    public function getPostDataProvider()
    {
        $data = array();

        $object = new \StdClass();
        $object->username = "Jane";
        $object->password = "AbCdEf";

        $parameters = get_object_vars($object);

        $request = new Request;
        $request->headers->set('content_type', RequestHandler::FORMAT_BODY_FORM_DATA);

        foreach ($parameters as $key => $value) {
            $request->request->set($key, $value);
        }

        $requestStack = new RequestStack;
        $requestStack->push($request);

        $requestHandler = new RequestHandler($requestStack);

        $data[] = array($requestHandler, $object);

        // Second round
        $object = new \StdClass();
        $object->name   = "Jane";
        $object->email  = "jane@example.com";

        $request = Request::create(
            '/',
            'GET',
            array(),
            array(),
            array(),
            array(),
            json_encode($object)
        );

        $request->headers->set('content_type', RequestHandler::FORMAT_BODY_RAW_JSON);

        $requestStack = new RequestStack;
        $requestStack->push($request);

        $requestHandler = new RequestHandler($requestStack);

        $data[] = array($requestHandler, $object);

        $request = new Request;
        $requestStack = new RequestStack;
        $requestStack->push($request);

        $requestHandler = new RequestHandler($requestStack);

        $data[] = array($requestHandler, null);

        return $data;
    }

    /**
     * @dataProvider getPostDataProvider
     */
    public function testGetPostData($requestHandler, $expectedData)
    {
        $this->assertEquals($requestHandler->getPostData(), $expectedData);
        if ($expectedData !== null) {
            $this->assertTrue(is_object($requestHandler->getPostData()));
        }
    }

    public function getPostDataFailProvider()
    {
        return array(
            array("{invalikd:jSOn}"),
            array('{"Json": [{"invalid"}]}'),
        );
    }

    /**
     * @dataProvider getPostDataFailProvider
     * @expectedException   Exception
     */
    public function testGetPostDataFail($data)
    {
        $request = Request::create(
            '/',
            'GET',
            array(),
            array(),
            array(),
            array(),
            $data
        );

        $request->headers->set('content_type', RequestHandler::FORMAT_BODY_RAW_JSON);

        $requestStack = new RequestStack;
        $requestStack->push($request);

        $requestHandler = new RequestHandler($requestStack);

        $this->assertEquals($requestHandler->getPostData(), $expectedData);
    }

    public function getRequestFormatProvider()
    {
        $data = array();

        foreach (RequestHandler::$supportedFormats as $format) {
            // Pre-configure class
            $request        = new Request;
            $request->headers->set('content_type', RequestHandler::FORMAT_BODY_FORM_DATA);

            $requestStack   = new RequestStack;
            $requestStack->push($request);

            $requestHandler = new RequestHandler($requestStack);

            $data[] = array($requestHandler, RequestHandler::FORMAT_BODY_FORM_DATA);
        }

        return $data;
    }

    /**
     * @dataProvider getRequestFormatProvider
     */
    public function testGetRequestFormat($requestHandler, $expectedFormat)
    {
        $this->assertSame($requestHandler->getRequestedFormat(), $expectedFormat);
    }

    public function getFormatProvider()
    {
        return array(
            array('multipart/form-data', RequestHandler::FORMAT_BODY_FORM_DATA),
            array('application/x-www-form-urlencoded', RequestHandler::FORMAT_BODY_URLENCODED),
            array('application/json', RequestHandler::FORMAT_BODY_RAW_JSON),
            array('text/plain', RequestHandler::FORMAT_BODY_RAW_TEXT),
            array('text/plain;UTF8', RequestHandler::FORMAT_BODY_RAW_TEXT),
            array('application/javascript', null),
        );
    }

    /**
     * @dataProvider getFormatProvider
     */
    public function testGetFormat($mimeType, $expectedFormat)
    {
        $this->assertSame(RequestHandler::getFormat($mimeType), $expectedFormat);
    }
}
