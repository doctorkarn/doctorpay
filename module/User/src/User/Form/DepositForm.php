<?php

namespace User\Form;

use Zend\Form\Form;

class DepositForm extends Form
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
                'label' => 'Deposit Amount: ',
            ),
            'attributes' => array(
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'channel',
            'type' => 'Text',
            'options' => array(
                'label' => 'Channel (e.g. KBANK, SCB, BBL) ',
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
                'value' => 'Deposit',
                'id' => 'submitbutton',
                'class' => 'btn btn-success',
            ),
        ));
    }
}