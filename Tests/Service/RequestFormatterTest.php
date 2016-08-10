<?php

namespace RybakDigital\Api\FrameworkBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use RybakDigital\Api\FrameworkBundle\Service\RequestFormatter;

class RequestFormatterTest extends WebTestCase
{
    private $container;

    public function setUp()
    {
        self::bootKernel();

        $this->container = self::$kernel->getContainer();
    }

    public function requestJSONDataProvider()
    {
        return array(
            array(
                array(
                    'data'    => $data = array(
                        'foo' => 'moo',
                        'loo' => false,
                    ),
                ),
                JsonResponse::class,
                JsonResponse::HTTP_OK,
                json_encode($data),
            ),
            array(
                array(
                    'data'    => $data = array(
                        'foo' => 'loo',
                        'loo' => true,
                    ),
                ),
                JsonResponse::class,
                JsonResponse::HTTP_BAD_REQUEST,
                json_encode($data),
                array(
                    'x-auth-token' => 123,
                ),
            ),
            array(
                array(
                    'data'    => array(),
                ),
                JsonResponse::class,
                JsonResponse::HTTP_OK,
                json_encode(array()),
                array(
                    'x-auth-token'  => '123',
                    'x-auth-somekey'=> 'abc',
                ),
            ),
        );
    }

    /**
     * @dataProvider requestJSONDataProvider
     */
    public function testRenderJson($requestData, $expectedClass, $expectedStatus, $expectedContent, $expectedHeaders = array())
    {
        // Pre-configure class
        $requestStack   = new RequestStack;
        $request        = new Request;
        $requestStack->push($request);
        $requestFormatter = new RequestFormatter($requestStack);

        $response = $requestFormatter->render($requestData['data'], $expectedStatus, $expectedHeaders);

        // Check class
        $this->assertEquals($expectedClass, get_class($response));

        // Check Response code
        $this->assertEquals($expectedStatus, $response->getStatusCode());

        // Check content
        $this->assertEquals($expectedContent, $response->getContent());

        // Check headers
        foreach ($expectedHeaders as $key => $value) {
            $this->assertEquals($expectedHeaders[$key], $response->headers->get($key));
        }
    }

    public function requestDebugDataProvider()
    {
        return array(
            array(
                array(
                    'data'    => $data = array(
                        'foo' => 'moo',
                        'loo' => false,
                    ),
                ),
                Response::HTTP_OK,
                array(
                    'x-auth-token'  => '123',
                    'x-auth-somekey'=> 'abc',
                ),
                'RybakDigitalApiFrameworkBundle:V1:debug.html.twig',
            ),
            array(
                array(
                    'data'    => $data = array(
                        'foo' => 'moo',
                        'loo' => false,
                    ),
                ),
                Response::HTTP_CREATED,
            ),
            array(
                array(
                    'data'    => $data = array(),
                ),
                Response::HTTP_CREATED,
            ),
        );
    }

    /**
     * @dataProvider requestDebugDataProvider
     */
    public function testRenderDebug($requestData, $expectedStatus, $expectedHeaders = array(), $expectedTemplate = null)
    {
        // Pre-configure class
        $request        = new Request;
        $request->query->set('format', 'debug');

        $requestStack   = new RequestStack;
        $requestStack->push($request);

        $templating     = $this->container->get('templating');
        $requestFormatter = new RequestFormatter($requestStack, $templating);

        $response = $requestFormatter->render($requestData, $expectedStatus, $expectedHeaders, $expectedTemplate);

        $this->assertTrue($response->headers->has('X-Response-Format'));
        $this->assertEquals(RequestFormatter::FORMAT_DEBUG, $response->headers->get('X-Response-Format'));

        // Check Response code
        $this->assertEquals($expectedStatus, $response->getStatusCode());

        // Check headers
        foreach ($expectedHeaders as $key => $value) {
            $this->assertEquals($expectedHeaders[$key], $response->headers->get($key));
        }
    }

    /**
     * @expectedException   LogicException
     */
    public function testRenderDebugNoTemplating()
    {
        // Pre-configure class
        $request        = new Request;
        $request->query->set('format', 'debug');

        $requestStack   = new RequestStack;
        $requestStack->push($request);

        $requestFormatter = new RequestFormatter($requestStack);

        $response = $requestFormatter->render(array(), Response::HTTP_OK);
    }

    public function requestTypeProvider()
    {
        return array(
            array(
                array(
                ),
                RequestFormatter::FORMAT_JSON,
            ),
            array(
                array(
                    'query'     => RequestFormatter::FORMAT_JSON,
                ),
                RequestFormatter::FORMAT_JSON,
            ),
            array(
                array(
                    'query'     => RequestFormatter::FORMAT_DEBUG,
                ),
                RequestFormatter::FORMAT_DEBUG,
            ),
            array(
                array(
                    'query'     => RequestFormatter::FORMAT_DEBUG,
                    'request'   => RequestFormatter::FORMAT_JSON,
                ),
                RequestFormatter::FORMAT_DEBUG,
            ),
            array(
                array(
                    'query'     => RequestFormatter::FORMAT_DEBUG,
                    'headers'   => 'application/json',
                ),
                RequestFormatter::FORMAT_DEBUG,
            ),
            array(
                array(
                    'request'   => RequestFormatter::FORMAT_DEBUG,
                ),
                RequestFormatter::FORMAT_DEBUG,
            ),
            array(
                array(
                    'request'     => RequestFormatter::FORMAT_DEBUG,
                    'headers'   => 'application/json',
                ),
                RequestFormatter::FORMAT_DEBUG,
            ),
            array(
                array(
                    'headers'   => RequestFormatter::FORMAT_DEBUG,
                ),
                RequestFormatter::FORMAT_DEBUG,
            ),
            array(
                array(
                    'headers'   => 'application/json',
                ),
                RequestFormatter::FORMAT_JSON,
            ),
        );
    }

    /**
     * @dataProvider requestTypeProvider
     */
    public function testGetRequestedFormat($requestData, $expected)
    {
        // Pre-configure class
        $requestStack   = new RequestStack;
        $request        = new Request;

        if (isset($requestData['query'])) {
            $request->query->set('format', $requestData['query']);
        }

        if (isset($requestData['request'])) {
            $request->request->set('format', $requestData['request']);
        }

        if (isset($requestData['headers'])) {
            $request->headers->set('Content-Type', $requestData['headers']);
        }
    
        $requestStack->push($request);
        $requestFormatter = new RequestFormatter($requestStack);

        // Default request is of JSON format
        $this->assertEquals($expected, $requestFormatter->getRequestedFormat());
    }
}
