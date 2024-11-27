<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */


return array(

    'session'         => array(
        'config'     => array(
            'class'   => 'Zend\Session\Config\SessionConfig',
            'options' => array(
                'use_cookies'         => true,
                'cookie_lifetime'     => 14400,
                'cookie_httponly'     => true,
                'cookie_secure'       => false,
                'gc_maxlifetime'      => 14400,// 4 hr 7200,
                'remember_me_seconds' => 14400
            )
        ),
    )	
);

