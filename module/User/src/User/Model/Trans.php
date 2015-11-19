<?php

namespace User\Model;

// Add these import statements
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Trans
{
    const TRANS_DEPOSIT = 1; 
    const TRANS_WITHDRAW = 2; 
    const TRANS_PAYMENT = 3; 

    public $id;
    public $inputFilter;
    
    public $account_id;
    public $type;
    public $channel;
    public $amount;
    public $created_at;

    public function exchangeArray($data)
    {
        $this->account_id   = (!empty($data['account_id'])) ? $data['account_id'] : null;
        $this->type   = (!empty($data['type'])) ? $data['type'] : null;
        $this->channel     = (!empty($data['channel'])) ? $data['channel'] : null;
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
                'name'     => 'account_id',
                'required' => true,
                'filters'  => $std_int_filter,
            ));

            $inputFilter->add(array(
                'name'     => 'type',
                'required' => true,
                'filters'  => $std_int_filter,
            ));
            
            $inputFilter->add(array(
                'name'     => 'channel',
                'required' => true,
                'filters'  => $std_str_filter,
                'validators' => $std_str_validator,
            ));

            $inputFilter->add(array(
                'name'     => 'amount',
                'required' => true,
//                'filters'  => $std_double_filter,
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}