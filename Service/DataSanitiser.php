<?php

namespace RybakDigital\Bundle\ApiFrameworkBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Ucc\Data\Types\Pseudo\DisplayType;
use Symfony\Component\HttpFoundation\Request;

/**
 * RybakDigital\Bundle\ApiFrameworkBundle\Service\DataSanitiser
 *
 * @author Kris Rybak <kris.rybak@rybakdigital.com>
 */
class DataSanitiser
{
    /**
     * Request stack
     *
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * Data Requirements
     *
     * @var array
     */
    protected $requirements;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->requirements = array('fields' => array());
    }

    public function getRequestStack()
    {
        return $this->requestStack;
    }

    public function getRequirements()
    {
        return $this->requirements;
    }

    public function setRequirements($requirements)
    {
        return $this->requirements = $requirements;
    }

    /**
     * Finds display based on request parameters
     *
     * @return mixed
     */
    public function getRequestedDisplay()
    {
        $request = $this->requestStack->getMasterRequest();

        // Skip sanitiser for OPTIONS
        if ($request->getMethod() === Request::METHOD_OPTIONS) {
            return false;
        }

        $display = $request->query->get('display', false);

        return $display;
    }

    public function sanitise($data)
    {
        if ($this->getRequestedDisplay()) {
            $this->requirements['fields'] = $this->exploreDataFields($data);

            $display = DisplayType::check($this->getRequestedDisplay(), $this->requirements);

            if (is_array($data)) {
                $ret = array();

                foreach ($data as $item) {
                    $ret[] = $this->sanitiseDisplayData($display, $item);
                }
            } else {
                $ret = $this->sanitiseDisplayData($display, $data);
            }

            $data = $ret;
        }

        return $data;
    }

    public function sanitiseDisplayData($displays, $item)
    {
        $ret = new \StdClass;

        foreach ($displays as $display) {
            $property       = $display->getField();
            $alias          = $display->getAlias();

            if (isset($item->$property)) {
                if ($alias) {
                    $ret->$alias = $item->$property;
                } else {
                    $ret->$property = $item->$property;
                }
            }
        }

        return $ret;
    }

    public function exploreDataFields($data)
    {
        $fields = array();

        if (is_array($data)) {
            $fields = $this->exploreDataFields($data[0]);
        } else {
            $fields = array_keys( (array) $data);
        }

        return $fields;
    }
}
