<?php
namespace SamLdapUser\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new AuthService(
            $serviceLocator->get('SamLdapUser\ServerConfig'),
            $serviceLocator->get('SamLdapUser\Authentication\Storage')
        );
    }
}