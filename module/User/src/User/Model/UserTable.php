<?php

namespace User\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;

class UserTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select(function (Select $select) {
            $select->where('status', User::STATUS_ACTIVE);
            $select->order('account_id DESC');
        });
        
//        $resultSet = $this->tableGateway
//                ->select(array('status' => User::STATUS_ACTIVE));
        return $resultSet;
    }
    
    public function fetchAllJoin()
    {
        $adapter = new \Zend\Db\Adapter\Adapter(array(
            'driver' => 'Mysqli',
            'database' => 'doctorpay',
            'username' => 'root',
            'password' => ''
        ));
        
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('account_id', 'email', 'balance', 'updated_at'));
        $select->from('users');
        $select->join(
            'profiles',
            'users.account_id = profiles.account_id',
            array('first_name', 'last_name')
        );
        $select->order('account_id DESC');
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        
        return $results;
    }
    
    public function fetchAllJoinByID($id)
    {
        $adapter = new \Zend\Db\Adapter\Adapter(array(
            'driver' => 'Mysqli',
            'database' => 'doctorpay',
            'username' => 'root',
            'password' => ''
        ));
        
        $sql = new Sql($adapter);
        $select = $sql->select();
//        $select->columns(array('account_id', 'email', 'balance', 'updated_at'));
        $select->from('users');
        $select->join(
            'profiles',
            'users.account_id = profiles.account_id'
//            array('first_name', 'last_name')
        );
        $select->where(array('users.account_id' => $id));
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        
        return $results;
    }

    public function getUser($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('account_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function getUserIDByUsername($name)
    {
        $rowset = $this->tableGateway->select(array('username' => $name));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row->account_id;
    }

    public function saveUser(User $user)
    {
        $data = array(
            'username' => $user->username,
            'password'  => $user->password,
            'pin'  => $user->pin,
            'email'  => $user->email,
            'balance'  => $user->balance,
            'optional_email'  => $user->optional_email,
            'created_at' => date("Y-m-d H:i:s"),
        );

        $id = (int) $user->account_id;
        if ($id == 0) {
            $data['role'] = User::ROLE_PERSONAL;
            $data['status'] = User::STATUS_ACTIVE;
            $data['type'] = "personal_user";
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->lastInsertValue;
        } else {
            if ($this->getUser($id)) {
                $this->tableGateway->update($data, array('account_id' => $id));
            } else {
                throw new \Exception('User id does not exist');
            }
        }
        
        return $id;
    }

    public function deleteUser($id)
    {
        // hard-delete
//        $this->tableGateway->delete(array('account_id' => (int) $id));
        
        // soft-delete
        $data['status'] = User::STATUS_BANNED;
        $this->tableGateway->update($data, array('account_id' => (int) $id));
        return $id;
    }
}