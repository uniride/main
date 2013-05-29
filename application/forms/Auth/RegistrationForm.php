<?php

class Form_Auth_RegistrationForm extends Zend_Form
{
	public function init()
	{
		$translation = Zend_Registry::get('Zend_Translate');

		$this->setName('RegistrationForm');
		
		$email = new Zend_Form_Element_Text('u_Email');
        $email->class = 'formtext';
        $email->setLabel($translation->translate('E-Mail Adresse:'))
			 	->setAttrib('size', 50)
				->setAttrib('maxlength', 50)
				->setAttrib('class', 'auth')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('Regex', false, array('!^\w[\w|\.|\-]+@\w[\w|\.|\-]+\.[a-zA-Z]{2,4}$!'))
				->addErrorMessage($translation->translate('E-Mail Adresse fehlt oder ist falsch!'));

		$password = new Zend_Form_Element_Password('u_Password');
		$password->class = 'formtext';
		$password->setLabel($translation->translate('Passwort:'))
				->setRequired(true)
				->addErrorMessage($translation->translate('Passwort fehlt'));
		
		$password2 = new Zend_Form_Element_Password('u_Password2');
		$password2->class = 'formtext';
		$password2->setLabel($translation->translate('Passwort wiederholen:'))
				->setRequired(true)
				->addErrorMessage($translation->translate('Passwort-Wiederholung fehlt'));
		
		$firstname = new Zend_Form_Element_Text('u_Firstname');
        $firstname->class = 'formtext';
        $firstname->setLabel($translation->translate('Vorname:'))
			 	->setAttrib('size', 50)
				->setAttrib('maxlength', 50)
				->setAttrib('class', 'auth')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('Regex', false, array('/^[a-zA-Z\ ]+$/'))
				->addErrorMessage($translation->translate('Vorname fehlt!'));	

		$lastname = new Zend_Form_Element_Text('u_Lastname');
        $lastname->class = 'formtext';
        $lastname->setLabel($translation->translate('Nachname:'))
			 	->setAttrib('size', 50)
				->setAttrib('maxlength', 50)
				->setAttrib('class', 'auth')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('Regex', false, array('/^[a-zA-Z\ ]+$/'))
				->addErrorMessage($translation->translate('Nachname fehlt!'));	
		
		$gender = new Zend_Form_Element_Radio('u_Gender');
        $gender->class = 'formtext';
        $gender->setLabel($translation->translate('Geschlecht:'))
			 	->setRequired(true)
				->setMultiOptions(array('male'=>'männlich', 'female'=>'weiblich'))
				->addErrorMessage($translation->translate('Bitte Geschlecht angeben!'));	
		
		$submit = new Zend_Form_Element_Submit('submitRegistration');
        $submit->class = 'formtext';
        $submit->setLabel($translation->translate('Registrieren'));
        $submit->setValue('submitRegistration')
               ->setDecorators(array(
                   array('ViewHelper',
                   array('helper' => 'formSubmit'))
               )	   
			   );
		
        $this->addElements(array($email,
								 $password,
								 $firstname,
								 $lastname,
								 $gender,
								 $submit
								 ));
            
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            array('Description', array('placement' => 'prepend')),
            'Form'
        ));						
    }
}