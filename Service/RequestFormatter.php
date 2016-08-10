<?php

namespace RybakDigital\Api\FrameworkBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * RybakDigital\Api\FrameworkBundle\Service\RequestFormatter
 *
 * @author Kris Rybak <kris.rybak@rybakdigital.com>
 */
class RequestFormatter
{
    const   FORMAT_JSON       = 'json',
            FORMAT_DEBUG      = 'debug';

    /**
     * Array of supported request formats
     *
     * @static array
     */
    public static $supportedFormats = array(
        self::FORMAT_JSON,
        self::FORMAT_DEBUG,
    );

    /**
     * Array of mapped mime types associated with given request formats
     *
     * @static array
     */
    public static $mimeTypeMapper = array(
        'application/vnd+debug' => self::FORMAT_DEBUG,
        'application/json'      => self::FORMAT_JSON,
    );

    /**
     * Request stack
     *
     * @var RequestStack
     */
    private $requestStack;


    public function __construct(RequestStack $requestStack, EngineInterface $templating = null)
    {
        $this->requestStack = $requestStack;
        $this->templating   = $templating;
    }

    public function render($data, $status = 200, $headers = array(), $template = null)
    {
        switch ($this->getRequestedFormat()) {
            case self::FORMAT_DEBUG:
                if (!$template) {
                    // Use default template
                    $template = 'RybakDigitalApiFrameworkBundle:V1:debug.html.twig';
                }

                if ($this->templating) {
                    $response = $this->templating->renderResponse($template, array('data' => $data));

                    // Set custom header to inform user of debug mode
                    $headers = array_merge($headers, array('X-Response-Format' => self::FORMAT_DEBUG));
                    $response->headers->add($headers);

                    // Set status
                    $response->setStatusCode($status);

                    return $response;
                }

                throw new \LogicException('You can not use the "render" method if the Templating Component or the Twig Bundle are not available.');
                break;
            
            default:
                $headers = array_merge($headers, array('X-Response-Format' => self::FORMAT_JSON));

                return new JsonResponse($data, $status, $headers);
                break;
        }
    }

    /**
     * Determines request format based on request stack
     *
     * @return string
     */
    public function getRequestedFormat()
    {
        $request            = $this->requestStack->getMasterRequest();
        $requestedFormat    = self::FORMAT_JSON;

        // First we always look at format parameters to determine type of the request
        // The following matching order applies:
        // 1. 'format' parameter
        // 2. 'content-type' header
        $format = $request->query->get('format', false);

        if (!$format) {
            $format = $request->request->get('format', false);
        }

        if (!$format) {
            $format = $request->headers->get('content_type', null);
        }

        switch ($format) {
            case self::FORMAT_DEBUG:
                $requestedFormat = self::FORMAT_DEBUG;
                break;

            case self::FORMAT_JSON:
                $requestedFormat = self::FORMAT_JSON;
                $request->setRequestFormat($requestedFormat);
                break;
            
            default:
                if (array_key_exists($format, $types = self::$mimeTypeMapper)) {
                    $requestedFormat = $types[$format];
                } else {                
                    $requestedFormat = self::FORMAT_JSON;
                    $request->setRequestFormat($requestedFormat);
                }

                break;
        }

        // Set requested format to Controller
        return $requestedFormat;
    }
}
