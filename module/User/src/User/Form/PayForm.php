<?php

namespace User\Form;

use Zend\Form\Form;

class PayForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('login');
        
        // USER/ACCOUNT info.
        $this->add(array(
            'name' => 'amount',
            'type' => 'Number',
            'options' => array(
                'label' => 'Transfer Amount: ',
            ),
            'attributes' => array(
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'payee_id',
            'type' => 'Text',
            'options' => array(
                'label' => 'TO (Payee ID, 1 - 100): ',
            ),
            'attributes' => array(
                'class' => 'form-control',
            ),
        ));
        
        // submit button
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Pay',
                'id' => 'submitbutton',
                'class' => 'btn btn-success',
            ),
        ));
    }
}