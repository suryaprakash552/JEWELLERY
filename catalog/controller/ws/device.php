<?php
namespace Opencart\Catalog\Controller\Ws;
class Device extends \Opencart\System\Engine\Controller {
        
    public function webhookcallbacks()
        {
            $json=array();
            $this->load->language('ws/transactions/common');
            $this->load->model('ws/transactions/common');
            $request=json_decode(file_get_contents("php://input"),true);
            $this->model_ws_transactions_common->trackDeviceRequestResponse($request,'REQUEST');
            $keys = array(
            				'userid',
            				'username',
            				'operationid',
            				'vendorid',
            				'type',
            				'date',
            				'time',
            				'devicename',
            				'deviceip',
            				'deviceid',
            				'recognition'
            			);
			foreach ($keys as $key) {
				if (!isset($request[$key])) {
                $json['success'] = "0";
                $json['message'] = "error_" . $key;
                
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
                return;
			    }
			}
            $json = $this->load->controller('ws/transactions/webhooks.device_MTL001_01', $request);
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
}