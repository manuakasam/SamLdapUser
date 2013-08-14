<?php
namespace SamLdapUser\Controller;

use Zend\Authentication\Storage\Session;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LoginController extends AbstractActionController
{
    public function loginAction()
    {
        $viewModel = new ViewModel();
        $viewModel->setTemplate('sam-ldap-user/login/login.phtml');

        $serviceLocator = $this->getServiceLocator();
        $authService    = $serviceLocator->get('SamLdapUser\Service\AuthService');

        if ($authService->hasIdentity()) {
            return $this->redirect()->toRoute('user');
        }

        $form    = $serviceLocator->get('SamLdapUser\Form\LoginForm');
        $request = $this->getRequest();
        $viewModel->setTerminal($request->isXmlHttpRequest());

        if (!$request->isPost()) {
            return $viewModel->setVariable('form', $form);
        }

        $form->setData($request->getPost());
        if (!$form->isValid()) {
            return $viewModel->setVariable('form', $form);
        }

        $data       = $form->getData();
        $authResult = $authService->authenticate($data['username'], $data['password']);

        if (!$authResult) {
            $this->flashMessenger()->addErrorMessage(
                'Login WRONG'
            );

            return $this->redirect()->toRoute('user/login');
        }

        var_dump($authService->getIdentity());
        die("YATTA!");
    }
}