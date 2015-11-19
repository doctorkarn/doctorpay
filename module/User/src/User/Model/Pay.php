<?php

namespace User\Model;

// Add these import statements
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Pay
{
    public $id;
    public $inputFilter;
    
    public $payer_id;
    public $payee_id;
    public $amount;
    public $created_at;

    public function exchangeArray($data)
    {
        $this->payer_id   = (!empty($data['payer_id'])) ? $data['payer_id'] : null;
        $this->payee_id   = (!empty($data['payee_id'])) ? $data['payee_id'] : null;
        $this->amount     = (!empty($data['amount'])) ? $data['amount'] : null;
        $this->created_at = (!empty($data['created_at'])) ? $data['created_at'] : null;
    }
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
    // Add content to these methods:
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        $std_int_filter = array(
                    array('name' => 'Int'),
                );
        
//        $std_double_filter = array(
//                    array('name' => 'Double'),
//                );
        
        $std_str_filter = array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                );
        
        $std_str_validator = array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    )
                );
        
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name'     => 'payer_id',
                'required' => true,
                'filters'  => $std_int_filter,
            ));

            $inputFilter->add(array(
                'name'     => 'payee_id',
                'required' => true,
                'filters'  => $std_int_filter,
            ));

//            $inputFilter->add(array(
//                'name'     => 'amount',
//                'required' => true,
//                'filters'  => $std_double_filter,
//            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}