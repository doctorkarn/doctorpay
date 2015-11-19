<?php

namespace User\Model;

use Zend\Db\TableGateway\TableGateway;

class ProfileTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getProfile($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('account_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveProfile(Profile $profile)
    {
        $data = array(
            'account_id' => $profile->account_id,
            'first_name' => $profile->first_name,
            'last_name'  => $profile->last_name,
            'company_name'  => $profile->company_name,
            'address_line1'  => $profile->address_line1,
            'address_line2'  => $profile->address_line2,
            'postal_code'  => $profile->postal_code,
            'city'  => $profile->city,
            'country'  => $profile->country,
            'mobile_phone'  => $profile->mobile_phone,
            'home_phone'  => $profile->home_phone,
            'work_phone'  => $profile->work_phone,
            'created_at' => date("Y-m-d H:i:s"),
        );

        $id = (int) $profile->account_id;
        if ($id == 0) {
            $data['account_id'] = $profile->temp_id;
            $this->tableGateway->insert($data);
        } else {
            if ($this->getProfile($id)) {
                $this->tableGateway->update($data, array('account_id' => $id));
            } else {
                throw new \Exception('Profile id does not exist');
            }
        }
    }

    public function deleteUser($id)
    {
        $this->tableGateway->delete(array('account_id' => (int) $id));
    }
}