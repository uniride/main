<?php

class Plugin_Language extends Zend_Controller_Plugin_Abstract {

        public function routeShutdown(Zend_Controller_Request_Abstract $request) {

                $language = $request->getParam ( "language", "de" );

                $locale = new Zend_Locale ( $language );

                $router = Zend_Controller_Front::getInstance()->getRouter();
                $router->setGlobalParam('language', $locale->getLanguage());

                Zend_Registry::set('Zend_Locale',$locale);
                $pathToTranslation = APPLICATION_PATH.'/translations/'.Zend_Registry::get('Zend_Locale')->getLanguage().'.csv';
                $translate = new Zend_Translate(array(
										        'adapter' => 'csv',
										       	'content' => $pathToTranslation,
										        'delimiter' => ','));
                Zend_Registry::set('Zend_Translate', $translate);

        }
}