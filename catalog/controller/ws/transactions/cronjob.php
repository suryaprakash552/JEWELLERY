<?php 
class ControllerTransactionsCronjob extends Controller 
{
    public function updateClosingBalance()
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $this->model_transactions_common->updateClosingBalance();
        $json['success']="1";
        $json['message']=$this->language->get('text_success');
        return $json;
    }
    public function updateWallet()
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $auto_wallet_info=$this->model_transactions_common->getAutoWalletCustInfo('API');
        if($auto_wallet_info['exstatus'])
        {
            $this->coreUpdateWallet($auto_wallet_info);
        }
        
        $auto_wallet_info=$this->model_transactions_common->getAutoWalletCustInfo('WHITELIST');
        if($auto_wallet_info['exstatus'])
        {
            $this->coreUpdateWallet($auto_wallet_info);
        }
        
        $auto_wallet_info=$this->model_transactions_common->getAutoWalletCustInfo('SUPER');
        if($auto_wallet_info['exstatus'])
        {
            $this->coreUpdateWallet($auto_wallet_info);
        }
        
        $auto_wallet_info=$this->model_transactions_common->getAutoWalletCustInfo('DISTRIBUTOR');
        if($auto_wallet_info['exstatus'])
        {
            $this->coreUpdateWallet($auto_wallet_info);
        }
        
        $auto_wallet_info=$this->model_transactions_common->getAutoWalletCustInfo('RETAILER');
        if($auto_wallet_info['exstatus'])
        {
            $this->coreUpdateWallet($auto_wallet_info);
        }
        
        $json['success']="1";
        $json['message']=$this->language->get('text_success');
        return $json;
    }
    
    protected function coreUpdateWallet($auto_wallet_info)
    {
        unset($auto_wallet_info['exstatus']);
        foreach($auto_wallet_info as $wallet_info)
        {
            $clientid=date('YmdaHis').RAND(100000,999999);
            $cust_info=$this->model_transactions_common->getCustInfo($wallet_info['customerid']);
            $custom_field_value=$this->model_transactions_common->extractJsonByName($cust_info['custom_field'],'AutoWalletLimit');
            if($custom_field_value['exstatus'] && !empty($custom_field_value['value']) && $custom_field_value['value']>=0)
            {
                if($wallet_info['amount']<=$custom_field_value['value'])
                {
                    $custom_field_value=$this->model_transactions_common->extractJsonByName($cust_info['custom_field'],'AutoWalletAmount');
                    if($custom_field_value['exstatus'] && !empty($custom_field_value['value']) && $custom_field_value['value']>0)
                    {
                        $find_parent=$this->model_transactions_common->getParentInfoByChildId($wallet_info['customerid']);
                        if($find_parent['exstatus'])
                        {
                            $parent_wallet_info=$this->model_transactions_common->getWalletInfo($find_parent['customer_id']);
                            if($parent_wallet_info['exstatus'])
                            {
                                $wallet_debit=false;
                                if($parent_wallet_info['amount']>0 && $parent_wallet_info['amount']>=$custom_field_value['value'])
                                {
                                    $debit=array(
                                                    "customerid"=>$find_parent['customer_id'],
                                                    "amount"=>$custom_field_value['value'],
                                                    "order_id"=>"0",
                                                    "description"=>$cust_info['customer_id'].'#'.$cust_info['telephone'].'#'.$custom_field_value['value'],
                                                    "transactiontype"=>'AUTO_MEMBER_TRADE',
                                                    "transactionsubtype"=>$this->language->get('DEBIT'),
                                                    "trns_type"=>$this->language->get('FORWARD'),
                                                    "txtid"=>$clientid
                                                );
                                    $wallet_debit=$this->model_transactions_common->doWalletDebit($debit);
                                }else
                                    {
                                        $wallet_debit=false;
                                    }
                                if($wallet_debit)
                                {
                                    $wallet_credit=false;
                                    $credit=array(
                                                        "customerid"=>$cust_info['customer_id'],
                                                        "amount"=>$custom_field_value['value'],
                                                        "auto_credit"=>0,
                                                        "order_id"=>"0",
                                                        "description"=>$find_parent['customer_id'].'#'.$find_parent['telephone'].'#'.$custom_field_value['value'],
                                                        "transactiontype"=>'AUTO_MEMBER_TRADE',
                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                        "trns_type"=>$this->language->get('RECEIVED'),
                                                        "txtid"=>$clientid
                                                    );
                                    $wallet_credit=$this->model_transactions_common->doWalletCredit($credit);
                                    if(!$wallet_credit)
                                    {
                                        $credit=array(
                                                        "customerid"=>$find_parent['customer_id'],
                                                        "amount"=>$custom_field_value['value'],
                                                        "auto_credit"=>0,
                                                        "order_id"=>"0",
                                                        "description"=>$cust_info['customer_id'].'#'.$cust_info['telephone'].'#'.$custom_field_value['value'],
                                                        "transactiontype"=>'AUTO_MEMBER_TRADE',
                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                        "trns_type"=>$this->language->get('REVERSE'),
                                                        "txtid"=>$clientid
                                                    );
                                        $this->model_transactions_common->doWalletCredit($credit);
                                    }
                                }       
                            }
                            
                        }else
                            {
                                $credit=array(
                                                        "customerid"=>$cust_info['customer_id'],
                                                        "amount"=>$custom_field_value['value'],
                                                        "auto_credit"=>0,
                                                        "order_id"=>"0",
                                                        "description"=>$cust_info['customer_id'].'#'.$cust_info['telephone'].'#'.$custom_field_value['value'],
                                                        "transactiontype"=>'AUTO_ADMIN_TRADE',
                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                        "trns_type"=>$this->language->get('RECEIVED'),
                                                        "txtid"=>$clientid
                                                    );
                                $this->model_transactions_common->doWalletCredit($credit);
                            }
                    }
                }
            }
        }
    }
    
    public function getRechargeDetailsByAPIRequestId($data)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $json=$this->model_transactions_common->getRechargeDetailsByAPIRequestId($data['APIRequestId']);
        return $json;
    }
}