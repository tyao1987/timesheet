<?php

return array(
	'home' => array(
			'type' => 'Zend\Mvc\Router\Http\Literal',
			'options' => array(
					'route' => '/',
					'defaults' => array(
							'controller' => 'Application\Controller\Index',
							'action' => 'index',
					),
			),
	),
// 	'stylesheet' => array(
// 			'type' => 'Zend\Mvc\Router\Http\Regex',
// 			'options' => array(
// 					'regex' => '/(?:styles/)?(?<type>core(?:\_distribution)?|extra|noscript|override_[^\.]+|partner_[^\.]+)\.css',
// 					'defaults' => array(
// 							'controller' => 'Application\Controller\CssJs',
// 							'action' => 'stylesheet'
// 					),
// 					'spec' => '/styles/%type%.css'
// 			)
// 	),
    
);