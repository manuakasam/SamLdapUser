<?php
/**
 * @author    Manuel Stosic <manuel.stosic@duit.de>
 * @copyright 2013 DU-IT GmbH
 */
namespace SamLdapUser\Service;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\Ldap as AuthAdapter;
use Zend\Authentication\Storage\Session;
use Zend\Authentication\Storage\StorageInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\ResponseCollection;
use Zend\Ldap\Exception\LdapException;
use Zend\Ldap\Ldap;

class AuthService
{
    const EVENT_LDAP_BIND                   = 'sam-ldap-user.service.auth-service.bind';
    const EVENT_LDAP_BIND_EXCEPTION         = 'sam-ldap-user.service.auth-service.bind.exception';
    const EVENT_LDAP_AUTHENTICATION_INVALID = 'sam-ldap-user.service.auth-service.authenticate.invalid';
    const EVENT_LDAP_AUTHENTICATION_VALID   = 'sam-ldap-user.service.auth-service.authenticate.valid';
    const EVENT_IDENTITY_GET                = 'sam-ldap-user.service-auth-service.identity.get';

    /**
     * @var AuthenticationService
     */
    protected $authService;

    protected $serverConfig;

    protected $eventManager;

    /**
     * @var StorageInterface
     */
    protected $storage;

    public function __construct($serverConfig, StorageInterface $storage)
    {
        $this->setServerConfig($serverConfig);
        $this->setStorage($storage);
        $this->bind();
    }

    /**
     * Test if a bind to the server works. Basically this checks if the connection to the server is working correctly
     *
     * @throws LdapException
     */
    protected function bind()
    {
        $server1 = $this->getServerConfig(0);
        try {
            $ldap = new Ldap($server1);

            $this->getEventManager()->trigger(self::EVENT_LDAP_BIND, $this, array(
                'server_config' => $this->getServerConfig()
            ));

            $ldap->bind();

            $this->getEventManager()->trigger(self::EVENT_LDAP_BIND . '.post', $this, array(
                'server_config' => $this->getServerConfig()
            ));
        } catch (LdapException $e) {
            $this->getEventManager()->trigger(self::EVENT_LDAP_BIND_EXCEPTION, $this, array(
                'server_config' => $this->getServerConfig(),
                'exception'     => $e
            ));
        }
    }

    /**
     * @param $index integer|null
     * @return array
     */
    public function getServerConfig($index = null)
    {
        if (!$index) {
            return $this->serverConfig;
        }

        if (isset($this->serverConfig[$index])) {
            return $this->serverConfig[$index];
        }

        return $this->serverConfig;
    }

    /**
     * @param array $serverConfig
     * @return AuthService
     */
    public function setServerConfig(array $serverConfig)
    {
        $this->serverConfig = $serverConfig;

        return $this;
    }

    /**
     * @param $username
     * @param $password
     * @throws LdapException
     * @return bool
     */
    public function authenticate($username, $password)
    {
        try {
            $adapter = new AuthAdapter(
                $this->getServerConfig(),
                $username,
                $password
            );

            $result = $this->getAuthService()->authenticate($adapter);

            if (!$result->isValid()) {
                $this->getEventManager()->trigger(self::EVENT_LDAP_AUTHENTICATION_INVALID, $this, array(
                    'server_config' => $this->getServerConfig(),
                    'ldap_result'   => $result
                ));

                return false;
            }

            $this->getEventManager()->trigger(self::EVENT_LDAP_AUTHENTICATION_VALID, $this, array(
                'server_config' => $this->getServerConfig(),
                'ldap_result'   => $result
            ));

            return true;
        } catch (LdapException $e) {
            $this->getEventManager()->trigger(self::EVENT_LDAP_AUTHENTICATION_EXCEPTION, $this, array(
                'server_config' => $this->getServerConfig(),
                'exception'     => $e
            ));

            return false;
        }
    }

    public function clearIdentity()
    {
        $this->getAuthService()->clearIdentity();
    }

    public function hasIdentity()
    {
        return $this->getAuthService()->hasIdentity();
    }

    public function getIdentity()
    {
        $identity = $this->getAuthService()->getIdentity();

        $trigger = $this->getEventManager()->trigger(self::EVENT_IDENTITY_GET, $identity);
        if ($trigger instanceof ResponseCollection) {
            return $trigger->last();
        }

        return $identity;
    }

    /**
     * @param \Zend\EventManager\EventManager $eventManager
     * @return AuthService
     */
    public function setEventManager($eventManager)
    {
        $this->eventManager = $eventManager;

        return $this;
    }

    /**
     * @return \Zend\EventManager\EventManager
     */
    public function getEventManager()
    {
        if (!$this->eventManager) {
            $this->setEventManager(
                new EventManager(__CLASS__)
            );
        }
        return $this->eventManager;
    }

    /**
     * @param \Zend\Authentication\Storage\StorageInterface $storage
     * @return AuthService
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * @return \Zend\Authentication\Storage\StorageInterface
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @param \Zend\Authentication\AuthenticationService $authService
     * @return AuthService
     */
    public function setAuthService($authService)
    {
        $this->authService = $authService;

        return $this;
    }

    /**
     * @return \Zend\Authentication\AuthenticationService
     */
    public function getAuthService()
    {
        if (!$this->authService) {
            $this->authService = new AuthenticationService($this->getStorage());
        }
        return $this->authService;
    }
}