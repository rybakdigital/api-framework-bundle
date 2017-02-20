<?php

namespace RybakDigital\Bundle\ApiFrameworkBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Bdcc\Json\Parser;
use Bdcc\Exception as Bdcc_Exception;
use RybakDigital\Bundle\ApiFrameworkBundle\Exception\ErrorCode;

/**
 * RybakDigital\Bundle\ApiFrameworkBundle\Service\RequestHandler
 *
 * @author Kris Rybak <kris.rybak@rybakdigital.com>
 */
class RequestHandler
{
    const   FORMAT_BODY_FORM_DATA       = 'multipart/form-data',
            FORMAT_BODY_URLENCODED      = 'application/x-www-form-urlencoded',
            FORMAT_BODY_RAW_JSON        = 'application/json',
            FORMAT_BODY_RAW_TEXT        = 'text/plain';

    /**
     * Array of supported request body formats
     *
     * @static array
     */
    public static $supportedFormats = array(
        self::FORMAT_BODY_FORM_DATA,
        self::FORMAT_BODY_URLENCODED,
        self::FORMAT_BODY_RAW_JSON,
        self::FORMAT_BODY_RAW_TEXT,
    );

    /**
     * Array of mapped mime types associated with given request formats
     *
     * @static array
     */
    public static $mimeTypeMapper = array(
        'multipart/form-data'               => self::FORMAT_BODY_FORM_DATA,
        'application/x-www-form-urlencoded' => self::FORMAT_BODY_URLENCODED,
        'application/json'                  => self::FORMAT_BODY_RAW_JSON,
        'text/plain'                        => self::FORMAT_BODY_RAW_TEXT,
    );

    /**
     * Request stack
     *
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Gets POST data from the request and delivers the payload as StdClass object
     *
     * @throws Exception    When fails to parse JSON
     * @return \StdClass | null
     */
    public function getPostData()
    {
        $format     = $this->getRequestedFormat();
        $request    = $this->requestStack->getMasterRequest();

        switch ($format) {
            case self::FORMAT_BODY_FORM_DATA:
            case self::FORMAT_BODY_URLENCODED:
                return (object) $request->request->all();
                break;
            case self::FORMAT_BODY_RAW_JSON:
            case self::FORMAT_BODY_RAW_TEXT:
                try {
                    return Parser::parse($request->getContent());
                } catch (Bdcc_Exception $e) {
                    throw new \Exception($e->getMessage(), ErrorCode::ERROR_CLIENT_HTTP_BAD_REQUEST);
                }
                break;
        }

        return null;
    }

    /**
     * Determines request format based on request stack
     *
     * @return string
     */
    public function getRequestedFormat()
    {
        $request    = $this->requestStack->getMasterRequest();
        $type       = $request->headers->get('content_type', null);
        $format     = self::getFormat($type);

        return $format;
    }

    /**
     * Gets format based on known mime type
     *
     * @param   string  $mimeType
     * @return  string|null
     */
    public static function getFormat($mimeType)
    {
        $canonicalMimeType = $mimeType;

        if (false !== $pos = strpos($mimeType, ';')) {
            $canonicalMimeType = substr($mimeType, 0, $pos);
        }

        if (array_key_exists($canonicalMimeType, self::$mimeTypeMapper)) {
            return $canonicalMimeType;
        }

        return null;
    }
}
