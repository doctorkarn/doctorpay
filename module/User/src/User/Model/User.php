<?php

namespace User\Model;

// Add these import statements
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class User
{
    const STATUS_ACTIVE = 1;
    const STATUS_PENDING = 0;
    const STATUS_BANNED = -1;
    
    const ROLE_SUPERADMIN = 9;
    const ROLE_ADMIN = 8;
    const ROLE_BUSINESS = 2;
    const ROLE_PERSONAL = 1;
    
    public $id;
    public $inputFilter;
    
    public $account_id;
    public $username;
    public $password;
    public $pin;
    public $email;
    public $balance;
    public $optional_email;
    public $role;
    public $status;
    public $type;
    public $created_at;

    public function exchangeArray($data)
    {
        $this->account_id   = (!empty($data['account_id'])) ? $data['account_id'] : null;
        $this->username     = (!empty($data['username'])) ? $data['username'] : null;
        $this->password     = (!empty($data['password'])) ? $data['password'] : null;
        $this->pin     = (!empty($data['pin'])) ? $data['pin'] : null;
        $this->email        = (!empty($data['email'])) ? $data['email'] : null;
        $this->balance        = (!empty($data['balance'])) ? $data['balance'] : 0;
        $this->optional_email     = (!empty($data['optional_email'])) ? $data['optional_email'] : null;
        $this->role         = (!empty($data['role'])) ? $data['role'] : null;
        $this->status       = (!empty($data['status'])) ? $data['status'] : null;
        $this->type         = (!empty($data['type'])) ? $data['type'] : null;
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
                'name'     => 'email',
                'required' => true,
            ));
            
            $inputFilter->add(array(
                'name'     => 'optional_email',
                'required' => false,
            ));
            
//            $inputFilter->add(array(
//                'name'     => 'balance',
//                'required' => true,
//                'filters'  => $std_double_filter,
//            ));

            $inputFilter->add(array(
                'name'     => 'username',
                'required' => true,
                'filters'  => $std_str_filter,
                'validators' => $std_str_validator,
            ));

            $inputFilter->add(array(
                'name'     => 'password',
                'required' => true,
                'filters'  => $std_str_filter,
                'validators' => $std_str_validator,
            ));
            
            // should be continue for every attributes.....

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}