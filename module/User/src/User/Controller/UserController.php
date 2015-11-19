<?php

namespace User\Controller;

use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Model\User;          // <-- Add this import
use User\Model\Profile;          // <-- Add this import
use User\Form\UserForm;       // <-- Add this import
use User\Form\LoginForm;       // <-- Add this import
use User\Form\PayForm;       // <-- Add this import
use User\Model\Pay;          // <-- Add this import
use User\Form\DepositForm;       // <-- Add this import
use User\Form\WithdrawForm;       // <-- Add this import
use User\Model\Trans;          // <-- Add this import

class UserController extends AbstractActionController
{
    protected $userTable;
    protected $profileTable;
    protected $transTable;
    protected $payTable;

    public function getUserTable()
    {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('User\Model\UserTable');
        }
        return $this->userTable;
    }
    
    public function getProfileTable()
    {
        if (!$this->profileTable) {
            $sm = $this->getServiceLocator();
            $this->profileTable = $sm->get('User\Model\ProfileTable');
        }
        return $this->profileTable;
    }
    
    public function getTransTable()
    {
        if (!$this->transTable) {
            $sm = $this->getServiceLocator();
            $this->transTable = $sm->get('User\Model\TransTable');
        }
        return $this->transTable;
    }
    
    public function getPayTable()
    {
        if (!$this->payTable) {
            $sm = $this->getServiceLocator();
            $this->payTable = $sm->get('User\Model\PayTable');
        }
        return $this->payTable;
    }
    
    /**
     * generate random data
     * @return null
     */
    public function generateUserAction()
    {
        $cities = array("Bangkok", "Nontaburi", "Samutprakan");
        
        $n = 100 + mt_rand(-20, 20);
        
        for($i=1; $i<=$n; $i++)
        {
            $ii = sprintf('%04d', $i);
            $user = new User();
            $profile = new Profile();
            
            $user->username = "user".$ii;
            $user->password = md5("user".$ii);
            $user->pin = sprintf('%04d', mt_rand(0, 9999));
            $user->email = "user".$ii."@example.com";
            $user->status = User::STATUS_ACTIVE;
            $user->role = User::ROLE_PERSONAL;
            $user->type = "personal_user";

            $profile->first_name = "User".$ii;
            $profile->last_name = "Tester";
            $profile->city = $cities[array_rand($cities)];
            $profile->country = "Thailand";
            $profile->mobile_phone = "08".mt_rand(10000000, 99999999);

            $uid = $this->getUserTable()->saveUser($user);
            $profile->temp_id = $uid;
            $this->getProfileTable()->saveProfile($profile);
        }
        
        return "";
    }
    
    /**
     * generate random data
     * @return null
     */
    public function generateDepositAction()
    {
        set_time_limit(300);
        $n = 800 + mt_rand(-200, 200);
        $max = 110;
        
        for($i=1; $i<=$n; $i++)
        {
            $acc_id = mt_rand(1, $max);
            $amount = $this->getTransTable()->generateDeposit($acc_id);
            
            $user = $this->getUserTable()->getUser($acc_id);
            
            $user->balance += $amount;
            $this->getUserTable()->saveUser($user);
        }
        
        return "";
    }
    
    /**
     * generate random data
     * @return null
     */
    public function generateWithDrawAction()
    {
        set_time_limit(300);
        $n = 300 + mt_rand(-60, 60);
        $max = 110;
        
        for($i=1; $i<=$n; $i++)
        {
            $acc_id = mt_rand(1, $max);
            $amount = $this->getTransTable()->generateWithdraw($acc_id);
            
            $user = $this->getUserTable()->getUser($acc_id);
            
            $user->balance -= $amount;
            $this->getUserTable()->saveUser($user);
        }
        
        return "";
    }
    
    /**
     * generate random data
     * @return null
     */
    public function generatePayAction()
    {
        set_time_limit(300);
        $n = 400 + mt_rand(-80, 80);
        $max = 110;
        
        for($i=1; $i<=$n; $i++)
        {
            $payer_id = mt_rand(1, $max);
            $payee_id = mt_rand(1, $max);
            $amount = $this->getPayTable()->generatePay($payer_id, $payee_id);
            
            $user1 = $this->getUserTable()->getUser($payer_id);
            $user2 = $this->getUserTable()->getUser($payee_id);
            $user1->balance -= $amount;
            $user2->balance += $amount;
            $this->getUserTable()->saveUser($user1);
            $this->getUserTable()->saveUser($user2);
        }
        
        return "";
    }
    
    public function indexAction()
    {
        return new ViewModel(array(
            'users' => $this->getUserTable()->fetchAllJoin(),
        ));
    }
    
    public function viewAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('user', array(
                'action' => 'index'
            ));
        }
        
        $payers = $this->getPayTable()->getPayer($id);
        $payees = $this->getPayTable()->getPayee($id);
        $trans  = $this->getTransTable()->getTrans($id);
        
        $data = array();
        foreach ($payers as $pr) {
            $temp = array(
                'code' => 4,
                'type' => 'Transfer Out',
                'channel' => 'UserID: '.$pr->payee_id,
                'amount' => $pr->amount,
                'created_at' => $pr->created_at,
            );
//            array_push($data, $temp);
            $data[$temp['created_at']] = $temp;
        }
        foreach ($payees as $pr) {
            $temp = array(
                'code' => 3,
                'type' => 'Transfer In',
                'channel' => 'UserID: '.$pr->payer_id,
                'amount' => $pr->amount,
                'created_at' => $pr->created_at,
            );
//            array_push($data, $temp);
            $data[$temp['created_at']] = $temp;
        }
        foreach ($trans as $tr) {
            $temp = array(
                'code' => (int) $tr->type,
                'type' => $tr->type==1 ? 'DEPOSIT' : 'WITHDRAW',
                'channel' => $tr->channel,
                'amount' => $tr->amount,
                'created_at' => $tr->created_at,
            );
//            array_push($data, $temp);
            $data[$temp['created_at']] = $temp;
        }
        krsort($data);
        
        return new ViewModel(array(
            'id' => $id,
            'data' => $data,
            'users' => $this->getUserTable()->fetchAllJoinByID($id),
        ));
    }
    
    public function loginAction()
    {
        $form = new LoginForm();
        $form->get('submit')->setValue('Login');

        $request = $this->getRequest();
        if ($request->isPost()) {
//            $user = new User();
//            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
                $data = $form->getData();

                $dbAdapter = new DbAdapter(array(
                    'driver' => 'Mysqli',
                    'database' => 'doctorpay',
                    'username' => 'root',
                    'password' => ''
                ));
                $authAdapter = new AuthAdapter($dbAdapter,
                                   'users',
                                   'username',
                                   'password',
                                   'MD5(?) AND status = 1'
                                   );
                $authAdapter
                    ->setIdentity($data['username'])
                    ->setCredential($data['password'])
                ;
                $auth = new AuthenticationService();

                $result = $auth->authenticate($authAdapter);
                if (!$result->isValid()) {
                    // Authentication failed; print the reasons why
                    return array(
                        'form' => $form,
                        'msgs' => $result->getMessages(),
                    );
                } else {
                    // Authentication succeeded; the identity ($username) is stored
                    // in the session
    //                $result->getIdentity() = $username;
                    $identity = $auth->getIdentity();
                    $acc_id = $this->getUserTable()->getUserIDByUsername($identity);

                    return $this->redirect()->toRoute('user', array(
                        'action' => 'view',
                        'id' => $acc_id,
                    ));
                }
            }
        }
        return array('form' => $form);
    }
    
    public function logoutAction()
    {
        $auth = new AuthenticationService();
        if ($auth->hasIdentity()) {
            $auth->clearIdentity();
        }
        return $this->redirect()->toRoute('application', array(
            'action' => 'index',
        ));
    }
    
    public function payAction()
    {
        $form = new PayForm();
        $form->get('submit')->setValue('Pay');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $pay = new Pay();
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
                $auth = new AuthenticationService();
                $identity = $auth->getIdentity();
                $acc_id = $this->getUserTable()->getUserIDByUsername($identity);
                
                $pay->exchangeArray($form->getData());
                $pay->payer_id = $acc_id;
                $amount = $this->getPayTable()->savePay($pay);
                
                $user1 = $this->getUserTable()->getUser($pay->payer_id);
                $user2 = $this->getUserTable()->getUser($pay->payee_id);
                $user1->balance -= $amount;
                $user2->balance += $amount;
                $this->getUserTable()->saveUser($user1);
                $this->getUserTable()->saveUser($user2);
                
                return $this->redirect()->toRoute('user', array(
                    'action' => 'view',
                    'id' => $acc_id,
                ));
            }
        }
        return array('form' => $form);
    }
    
    public function depositAction()
    {
        $form = new DepositForm();
        $form->get('submit')->setValue('Deposit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $trans = new Trans();
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
                $auth = new AuthenticationService();
                $identity = $auth->getIdentity();
                $acc_id = $this->getUserTable()->getUserIDByUsername($identity);
                
                $trans->exchangeArray($form->getData());
                $trans->account_id = $acc_id;
                $trans->type = Trans::TRANS_DEPOSIT;
                $amount = $this->getTransTable()->saveTrans($trans);
                
                $user = $this->getUserTable()->getUser($acc_id);
                $user->balance += $amount;
                $this->getUserTable()->saveUser($user);
                
                return $this->redirect()->toRoute('user', array(
                    'action' => 'view',
                    'id' => $acc_id,
                ));
            }
        }
        return array('form' => $form);
    }
    
    public function withdrawAction()
    {
        $form = new WithdrawForm();
        $form->get('submit')->setValue('Withdraw');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $trans = new Trans();
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
                $auth = new AuthenticationService();
                $identity = $auth->getIdentity();
                $acc_id = $this->getUserTable()->getUserIDByUsername($identity);
                
                $trans->exchangeArray($form->getData());
                $trans->account_id = $acc_id;
                $trans->type = Trans::TRANS_WITHDRAW;
                $amount = $this->getTransTable()->saveTrans($trans);
                
                $user = $this->getUserTable()->getUser($acc_id);
                $user->balance -= $amount;
                $this->getUserTable()->saveUser($user);
                
                return $this->redirect()->toRoute('user', array(
                    'action' => 'view',
                    'id' => $acc_id,
                ));
            }
        }
        return array('form' => $form);
    }

    public function addAction()
    {
        $form = new UserForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = new User();
            $profile = new Profile();
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $form->setInputFilter($profile->getInputFilter());
                
                if ($form->isValid()) {
                    $user->exchangeArray($form->getData());
                    $profile->exchangeArray($form->getData());

                    $uid = $this->getUserTable()->saveUser($user);
                    $profile->temp_id = $uid;
                    $this->getProfileTable()->saveProfile($profile);

                    // Redirect to list of users
                    return $this->redirect()->toRoute('user');
                }
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('user', array(
                'action' => 'add'
            ));
        }

        // Get the User with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $user = $this->getUserTable()->getUser($id);
            $profile = $this->getProfileTable()->getProfile($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('user', array(
                'action' => 'index'
            ));
        }

        $form  = new UserForm();
        $form->bind($user);
        $form->bind($profile);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getUserTable()->saveUser($user);
                $this->getProfileTable()->saveProfile($profile);

                // Redirect to list of users
                return $this->redirect()->toRoute('user');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('user');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getUserTable()->deleteUser($id);
                $this->getProfileTable()->deleteProfile($id);
            }

            // Redirect to list of users
            return $this->redirect()->toRoute('user');
        }

        return array(
            'id'    => $id,
            'user' => $this->getUserTable()->getUser($id)
        );
    }
}