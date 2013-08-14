<?php
namespace SamLdapUser\Form;

use Zend\EventManager\EventManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoginFormFactory implements FactoryInterface
{
    const EVENT_FORM_CREATED = 'sam-ldap-user.form.created';

    /**
     * @var EventManager
     */
    protected $eventManager;

    public function __construct()
    {
        $this->eventManager = new EventManager(__CLASS__);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $form = new LoginForm();

        $this->eventManager->trigger(self::EVENT_FORM_CREATED, $form);

        return $form;
    }
}