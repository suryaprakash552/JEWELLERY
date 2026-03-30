<?php
class ControllerApiCronjob extends Controller
{
    public function updateClosingBalance()
    {
        $this->load->language('transactions/common');
        //----------------------------------------------------------
        $json=$this->load->controller('transactions/cronjob/updateClosingBalance');
        $this->response->addHeader('Content-Type: application/json');
    	$this->response->setOutput(json_encode($json));
    }
    public function updateWallet()
    {
        $this->load->language('transactions/common');
        //----------------------------------------------------------
        $json=$this->load->controller('transactions/cronjob/updateWallet');
        $this->response->addHeader('Content-Type: application/json');
    	$this->response->setOutput(json_encode($json));
    }
    
     public function pgtransactionstatus()
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $api_info=$this->model_transactions_common->getAPIInfoByType('PG_TX_STATUS');
        if(!$api_info['exstatus'])
        {
           $json['success']="0";
           $json['message']=$this->language->get('error_api');
        }
            
        if($api_info['exstatus'])
        {
            $pgtx_infos=$this->model_transactions_common->getPGInfoByStatus();
            if(!$pgtx_infos['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_pending_pgtx');
            }
            
            if($pgtx_infos['exstatus'])
            {
                unset($pgtx_infos['exstatus']);
                $cred=json_decode($api_info['request'],true);
                foreach($pgtx_infos as $pgtx)
                {
                        $response='';
                        $input=array(
                                        "orderId"=>$pgtx['ourrequestid'],
                                        "agentId"=>$cred['userid_value'],
                                        "secureKey"=>$cred['token_value']
                                    );
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                          CURLOPT_URL => $api_info['url'],
                          CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_ENCODING => '',
                          CURLOPT_MAXREDIRS => 10,
                          CURLOPT_TIMEOUT => 0,
                          CURLOPT_FOLLOWLOCATION => true,
                          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                          CURLOPT_CUSTOMREQUEST => 'POST',
                          CURLOPT_POSTFIELDS =>json_encode($input),
                          CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/json'
                          ),
                        ));
                        
                        $res = curl_exec($curl);
                        curl_close($curl);
                        $response=json_decode($res,true);
                        if(isset($response['txn_status']) && $response['txn_status']=="Success")
                        {
                            $paytmParams = array(
                                    				'MID'=>$cred['userid_value'],
                                    				'TXNID'=>$response['txn_id'],
                                    				'STATUS'=>1,
                                    				'ORDERID'=>$response['txn_id'],
                                    				'RESPMSG'=>'Txn Successful',
                                    				'TXNDATE'=>$response['txn_date'].' '.$response['txn_time'],
                                    				'CURRENCY'=>'INR',
                                    				'BANKNAME'=>$response['BANKNAME'],
                                    				'RESPCODE'=>'',
                                    				'RRN'=>'CALLBACK',
                                    				'TXNAMOUNT'=>$response['txn_amount'],
                                    				'GATEWAYNAME'=>$response['GATEWAYNAME'],
                                    				'PAYMENTMODE'=>$response['txn_mode'],
                                    				'CHECKSUMHASH'=>'',
                                    				'initiator'=>"AUTO",
                                    				'prepaidCard'=>'',
                                    				'VALIDCHECKSUM'=>1,
                                    				'instatus'=>'Success'
                                    			);
                           $this->load->controller('transactions/bankit/pg/webhook',$paytmParams);
                           $json['success']="1";
                           $json['message']=$response['txn_id'].":".$this->language->get('text_success');
                        }elseif(isset($response['txn_status']) && ($response['txn_status']=="Failed" || $response['txn_status']=="Failure"))
                        {
                            $paytmParams = array(
                                    				'MID'=>$cred['userid_value'],
                                    				'TXNID'=>$response['txn_id'],
                                    				'STATUS'=>0,
                                    				'ORDERID'=>$response['txn_id'],
                                    				'RESPMSG'=>'Transaction has been declined	by the bank',
                                    				'TXNDATE'=>$response['txn_date'].' '.$response['txn_time'],
                                    				'CURRENCY'=>'INR',
                                    				'BANKNAME'=>$response['BANKNAME'],
                                    				'RESPCODE'=>'',
                                    				'RRN'=>'CALLBACK',
                                    				'TXNAMOUNT'=>$response['txn_amount'],
                                    				'GATEWAYNAME'=>$response['GATEWAYNAME'],
                                    				'PAYMENTMODE'=>$response['txn_mode'],
                                    				'CHECKSUMHASH'=>'',
                                    				'initiator'=>"AUTO",
                                    				'prepaidCard'=>'',
                                    				'VALIDCHECKSUM'=>1,
                                    				'instatus'=>'Failure'
                                    			);
                           $this->load->controller('transactions/bankit/pg/webhook',$paytmParams);
                           $json['success']="0";
                           $json['message']=$response['txn_id'].":".$this->language->get('text_failed');
                        }else
                            {
                                $json['success']="2";
                                $json['message']=$pgtx['ourrequestid'].":".$this->language->get('text_pending');
                            }
                }
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    public function getRechargeDetailsByAPIRequestId()
    {
        $this->load->language('transactions/common');
        $input = json_decode(file_get_contents("php://input"),true);
        //----------------------------------------------------------
        $json=$this->load->controller('transactions/cronjob/getRechargeDetailsByAPIRequestId',$input);
        $this->response->addHeader('Content-Type: application/json');
    	$this->response->setOutput(json_encode($json));
    }
}