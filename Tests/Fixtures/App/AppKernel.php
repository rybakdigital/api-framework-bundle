<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new RybakDigital\Api\FrameworkBundle\RybakDigitalApiFrameworkBundle(),
        );
    }

    /** 
     * @return string
     */
    public function getCacheDir()
    {   
        return sys_get_temp_dir().'/RybakDigitalApiFrameworkBundle/cache';
    }   
    /** 
     * @return string
     */
    public function getLogDir()
    {   
        return sys_get_temp_dir().'/RybakDigitalApiFrameworkBundle/logs';
    } 

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
