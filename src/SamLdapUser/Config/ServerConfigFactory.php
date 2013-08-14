<?php
namespace SamLdapUser\Config;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ServerConfigFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     * @throws ServerConfigNotFoundException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('SamLdapUser\Config');

        if (!isset($config['server_config']) || 0 === count($config['server_config'])) {
            throw new ServerConfigNotFoundException('Ldap-Server-Configuration could not be found. Did you move /config/ldap.local.php.dist to your /autoload directory?');
        }

        return $config['server_config'];
    }
}