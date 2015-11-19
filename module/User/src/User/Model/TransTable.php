<?php

namespace User\Model;

use Zend\Db\TableGateway\TableGateway;

class TransTable
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

    public function getTrans($id)
    {
        $id  = (int) $id;
        $resultSet = $this->tableGateway->select(array('account_id' => $id));
        return $resultSet;
    }

    public function saveTrans(Trans $trans)
    {
        $data = array(
            'account_id' => $trans->account_id,
            'type'  => $trans->type,
            'channel'  => $trans->channel,
            'amount'  => $trans->amount,
            'created_at' => date("Y-m-d H:i:s"),
        );

        $this->tableGateway->insert($data);
        
        return $trans->amount;
    }
    
    public function generateDeposit($acc_id)
    {
        $banks = array("KBANK", "SCB", "BBL");
        
        $months = 30*24*60*60;
        $rand_time = time() - mt_rand($months*2, $months*3);
        
        $data = array(
            'account_id' => $acc_id,
            'type'  => Trans::TRANS_DEPOSIT,
            'channel'  => $banks[array_rand($banks)],
            'amount'  => mt_rand(1, 100) *100,
            'created_at' => date("Y-m-d H:i:s", $rand_time),
        );

        $this->tableGateway->insert($data);
//        $id = $this->tableGateway->lastInsertValue;
        
        return $data['amount'];
    }
    
    public function generateWithdraw($acc_id)
    {
        $banks = array("KBANK", "SCB", "BBL");
        
        $months = 30*24*60*60;
        $rand_time = time() - mt_rand($months*1, $months*2);
        
        $data = array(
            'account_id' => $acc_id,
            'type'  => Trans::TRANS_WITHDRAW,
            'channel'  => $banks[array_rand($banks)],
            'amount'  => mt_rand(1, 100) *100,
            'created_at' => date("Y-m-d H:i:s", $rand_time),
        );

        $this->tableGateway->insert($data);
//        $id = $this->tableGateway->lastInsertValue;
        
        return $data['amount'];
    }

}