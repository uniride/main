<?php

class Form_Auth_LoginForm extends Zend_Form
{
	public function init()
	{
		$translation = Zend_Registry::get('Zend_Translate');

		$this->setName('loginForm');
		$user = new Zend_Form_Element_Text('email');
        $user->class = 'formtext';
        $user->setLabel($translation->translate('Email:'))
			 	->setAttrib('size', 50)
				->setAttrib('maxlength', 50)
				->setAttrib('class', 'auth')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringToLower')
				->addFilter('StringTrim')
				->addValidator('Regex', false, array('!^\w[\w|\.|\-]+@\w[\w|\.|\-]+\.[a-zA-Z]{2,4}$!'))
				->addErrorMessage($translation->translate('Email ist leer oder nicht richtig!'));	

		$password = new Zend_Form_Element_Password('password');
        $password->class = 'formtext';
        $password->setLabel($translation->translate('Passwort:'))
				->setAttrib('size', 50)
				->setAttrib('maxlength', 50)				
				->setAttrib('class', 'auth')
				->setRequired(true)
				->addErrorMessage($translation->translate('Passwort ist leer oder nicht richtig!'));			
      
		$login = new Zend_Form_Element_Submit('submitLogin');
        $login->class = 'formtext';
        $login->setLabel($translation->translate('Login'));
        $login->setValue('submitLogin')
               ->setDecorators(array(
                   array('ViewHelper',
                   array('helper' => 'formSubmit'))
               )	   
			   );	   
   

        $this->addElements(array($user,
						         $password,
						         $login));
            
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            array('Description', array('placement' => 'prepend')),
            'Form'
        ));						
    }
}