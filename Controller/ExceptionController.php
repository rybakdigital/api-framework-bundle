<?php

namespace RybakDigital\Bundle\ApiFrameworkBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use RybakDigital\Bundle\ApiFrameworkBundle\Service\RequestFormatter;
use RybakDigital\Bundle\ApiFrameworkBundle\Exception\ExceptionHandler;

/**
 * RybakDigital\Bundle\ApiFrameworkBundle\Controller\ExceptionController
 *
 * @author Kris Rybak <kris.rybak@krisrybak.com>
 */
class ExceptionController extends Controller
{
    /**
     * @Method({"GET", "POST"})
     */
    public function showExceptionAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        $data = ExceptionHandler::handle($exception);

        $requestFormatter = $this->container->get('rybakdigital.apiframework.request_formatter');

        // Add additional debug information for dev mode
        if ($this->get('kernel')->getEnvironment() == 'dev') {
            $data->info = ExceptionHandler::getInfo($exception);
        }

        return $requestFormatter->render($data);
    }
}
