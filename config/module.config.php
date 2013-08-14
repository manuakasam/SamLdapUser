<?php
return array(
    'sam_ldap_user' => array(
        'server_config' => array(
            'server1' => array(
                'host'                 => 'NW18001.stadt-duisburg.de',
                'port'                 => 636,
                'useSsl'               => true,
                'useStartTls'          => false,
                'bindRequiresDn'       => true,
                'baseDn'               => 'ou=Backbone,o=DU',
                'accountFilterFormat'  => 'cn=%s',
                'accountCanonicalForm' => 2,
            )
        )
    ),
    'controllers'     => array(
        'invokables' => array(
            'SamLdapUser\Controller\User'   => 'SamLdapUser\Controller\UserController',
            'SamLdapUser\Controller\Login'  => 'SamLdapUser\Controller\LoginController',
            'SamLdapUser\Controller\Logout' => 'SamLdapUser\Controller\LogoutController',
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'SamLdapUser\Config'              => 'SamLdapUser\Config\ConfigFactory',
            'SamLdapUser\ServerConfig'        => 'SamLdapUser\Config\ServerConfigFactory',
            'SamLdapUser\Service\AuthService' => 'SamLdapUser\Service\AuthServiceFactory',
            'SamLdapUser\Form\LoginForm'      => 'SamLdapUser\Form\LoginFormFactory',
            'SamLdapUser\Authentication\Storage' => 'SamLdapUser\Authentication\StorageFactory'
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'router'          => array(
        'routes' => array(
            'user' => array(
                'type'          => 'Zend\Mvc\Router\Http\Literal',
                'options'       => array(
                    'route'    => '/user',
                    'defaults' => array(
                        'controller' => 'SamLdapUser\Controller\User',
                        'action'     => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes'  => array(
                    'login'  => array(
                        'type'    => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route'       => '/login[/:redirect]',
                            'defaults'    => array(
                                'controller' => 'SamLdapUser\Controller\Login',
                                'action'     => 'login'
                            ),
                            'constraints' => array(
                                'redirect' => '[a-z]+'
                            )
                        )
                    ),
                    'logout' => array(
                        'type'    => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route'    => '/logout',
                            'defaults' => array(
                                'controller' => 'SamLdapUser\Controller\Logout',
                                'action'     => 'logout'
                            )
                        )
                    )
                )
            )
        )
    )
);