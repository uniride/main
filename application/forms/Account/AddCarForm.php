<?php

class Form_Account_AddCarForm extends Zend_Form
{
	public function init()
	{
		$translation = Zend_Registry::get('Zend_Translate');

		$this->setName('addcarForm');
		
		$manufacturer = new Zend_Form_Element_Text('c_Manufacturer');
        $manufacturer->class = 'formtext';
        $manufacturer->setLabel($translation->translate('Hersteller:'))
			 	->setAttrib('size', 50)
				->setAttrib('maxlength', 50)
				->setAttrib('class', 'auth')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('Regex', false, array('/^[a-zA-Z0-9\!\\ \_\-\?\%]+$/'))
				->addErrorMessage($translation->translate('Hersteller ist leer!'));	

		$model = new Zend_Form_Element_Text('c_Model');
        $model->class = 'formtext';
        $model->setLabel($translation->translate('Modell:'))
			 	->setAttrib('size', 50)
				->setAttrib('maxlength', 50)
				->setAttrib('class', 'auth')
				->setRequired(false)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('Regex', false, array('/^[a-zA-Z0-9\!\\ \_\-\?\%]+$/'))
				->addErrorMessage($translation->translate('Modell ist leer!'));

        $seats = new Zend_Form_Element_Text('c_Seats');
        $seats->class = 'formtext';
        $seats->setLabel($translation->translate('Sitzplätze:'))
        ->setAttrib('size', 50)
        ->setAttrib('maxlength', 50)
        ->setAttrib('class', 'auth')
        ->setRequired(true)
        ->addFilter('StripTags')
        ->addFilter('StringTrim')
        ->addValidator('Regex', false, array('/^[0-9]+$/'))
        ->addErrorMessage($translation->translate('Sitzplätze ist leer!'));
     
		
		$sign = new Zend_Form_Element_Text('c_Sign');
        $sign->class = 'formtext';
        $sign->setLabel($translation->translate('Kennzeichen:'))
        ->setAttrib('size', 50)
        ->setAttrib('maxlength', 50)
        ->setAttrib('class', 'auth')
        ->setRequired(false)
        ->addFilter('StripTags')
        ->addFilter('StringTrim')
        ->addValidator('Regex', false, array('/^[a-zA-Z0-9\!\_\\ \-\?\%]+$/'))
        ->addErrorMessage($translation->translate('Kennzeichen ist leer!'));
		
		$color = new Zend_Form_Element_Text('c_Color');
        $color->class = 'formtext';
        $color->setLabel($translation->translate('Farbe:'))
        ->setAttrib('size', 50)
        ->setAttrib('maxlength', 50)
        ->setAttrib('class', 'auth')
        ->setRequired(false)
        ->addFilter('StripTags')
        ->addFilter('StringTrim')
        ->addErrorMessage($translation->translate('Farbe ist leer!'));
      
		$addCar = new Zend_Form_Element_Submit('submitAddCar');
        $addCar->class = 'formtext';
        $addCar->setLabel($translation->translate('Fahrzeug hinzufügen'));
        $addCar->setValue('submitAddCar')
               ->setDecorators(array(
                   array('ViewHelper',
                   array('helper' => 'formSubmit'))
               )	   
			   );	

        $this->addElements(array($manufacturer,
								 $model,
								 $color,
						         $seats,
								 $sign,
						         $addCar
								 ));
            
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            array('Description', array('placement' => 'prepend')),
            'Form'
        ));						
    }
}