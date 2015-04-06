<?php
namespace SamLdapUser\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class LogoutController extends AbstractActionController
{
    public function logoutAction()
    {
        $service = $this->getServiceLocator()->get('SamLdapUser\Service\AuthService');

        if ($service->hasIdentity())
        {
            $service->clearIdentity();
        }
        
        return $this->redirect()->toRoute('user/login');
    }
}
