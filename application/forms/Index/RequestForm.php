<?php

class Form_Index_RequestForm extends Zend_Form
{
	public function __construct($options = NULL, $destinationsArray)
	{
		parent::__construct($options);
		$translation = Zend_Registry::get('Zend_Translate');

		$this->setName('RequestForm');
		
		$start = new Zend_Form_Element_Text('start');
        $start->class = 'formtext';
        $start->setLabel($translation->translate('Start:'))
				->setAttrib('class', 'index')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringToLower')
				->addFilter('StringTrim')
				->addValidator('Regex', false, array('!^\w[\w|\.|\-]+@\w[\w|\.|\-]+\.[a-zA-Z]{2,4}$!'))
				->addErrorMessage($translation->translate('Bitte Startadresse erfassen!'));	
		
		$destination = new Zend_Form_Element_Select('destination');
        $destination->class = 'formtext';
        $destination->setLabel($translation->translate('Ziel:'))
				->setAttrib('class', 'auth')
				->setRequired(true)
				->addMultiOptions($destinationsArray)
				->addErrorMessage($translation->translate('Bitte Ziel auswählen'));			
      
		$submitReq = new Zend_Form_Element_Submit('submitRequest');
        $submitReq->class = 'formtext';
        $submitReq->setLabel($translation->translate('Suchen'));
        $submitReq->setValue('submitRequest')
               ->setDecorators(array(
                   array('ViewHelper',
                   array('helper' => 'formSubmit'))
               )	   
			   );	   
   

        $this->addElements(array($start,
						         $destination,
						         $submitReq));
            
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            array('Description', array('placement' => 'prepend')),
            'Form'
        ));						
    }
}