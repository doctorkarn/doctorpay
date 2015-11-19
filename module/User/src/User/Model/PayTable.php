<?php

namespace User\Model;

use Zend\Db\TableGateway\TableGateway;

class PayTable
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

    public function getPayer($id)
    {
        $id  = (int) $id;
        $resultSet = $this->tableGateway->select(array('payer_id' => $id));
        return $resultSet;
    }
    
    public function getPayee($id)
    {
        $id  = (int) $id;
        $resultSet = $this->tableGateway->select(array('payee_id' => $id));
        return $resultSet;
    }

    public function savePay(Pay $pay)
    {
        $data = array(
            'payer_id' => $pay->payer_id,
            'payee_id' => $pay->payee_id,
            'amount'  => $pay->amount,
            'created_at' => date("Y-m-d H:i:s"),
        );

        $this->tableGateway->insert($data);
        
        return $pay->amount;
    }
    
    public function generatePay($payer_id, $payee_id)
    {
        $months = 2*30*24*60*60;
        $rand_time = time() - mt_rand(0, $months);
        
        $data = array(
            'payer_id' => $payer_id,
            'payee_id' => $payee_id,
            'amount'  => mt_rand(1, 100) *100,
            'created_at' => date("Y-m-d H:i:s", $rand_time),
        );

        $this->tableGateway->insert($data);
//        $id = $this->tableGateway->lastInsertValue;
        
        return $data['amount'];
    }
    

}