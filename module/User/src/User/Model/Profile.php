<?php

namespace User\Model;

// Add these import statements
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Profile
{
    public $id;
    public $inputFilter;
    
    public $temp_id;
    public $account_id;
    public $first_name;
    public $last_name;
    public $company_name;
    public $address_line1;
    public $address_line2;
    public $postal_code;
    public $city;
    public $country;
    public $mobile_phone;
    public $home_phone;
    public $work_phone;
    public $created_at;

    public function exchangeArray($data)
    {
        $this->account_id = (!empty($data['account_id'])) ? $data['account_id'] : null;
        $this->first_name = (!empty($data['first_name'])) ? $data['first_name'] : null;
        $this->last_name = (!empty($data['last_name'])) ? $data['last_name'] : null;
        $this->company_name = (!empty($data['company_name'])) ? $data['company_name'] : null;
        $this->address_line1 = (!empty($data['address_line1'])) ? $data['address_line1'] : null;
        $this->address_line2 = (!empty($data['address_line2'])) ? $data['address_line2'] : null;
        $this->postal_code     = (!empty($data['postal_code'])) ? $data['postal_code'] : null;
        $this->city         = (!empty($data['city'])) ? $data['city'] : null;
        $this->country      = (!empty($data['country'])) ? $data['country'] : null;
        $this->mobile_phone = (!empty($data['mobile_phone'])) ? $data['mobile_phone'] : null;
        $this->home_phone = (!empty($data['home_phone'])) ? $data['home_phone'] : null;
        $this->work_phone = (!empty($data['work_phone'])) ? $data['work_phone'] : null;
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
                'name'     => 'first_name',
                'required' => true,
                'filters'  => $std_str_filter,
                'validators' => $std_str_validator,
            ));

            $inputFilter->add(array(
                'name'     => 'last_name',
                'required' => true,
                'filters'  => $std_str_filter,
                'validators' => $std_str_validator,
            ));
            
            $inputFilter->add(array(
                'name'     => 'company_name',
                'required' => false,
                'filters'  => $std_str_filter,
                'validators' => $std_str_validator,
            ));
            
            // should be continue for every attributes.....

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}