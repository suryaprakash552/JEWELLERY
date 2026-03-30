<?php
namespace Opencart\Catalog\Controller\Extension\PurpletreePos\Pos;
class Report extends \Opencart\System\Engine\Controller {
		private $error = array();
		
		public function filterReport() {
		$report_filter_data	= $this->request->post;
		$json['reports']=array();
		$this->load->model('extension/purpletree_pos/pos/report');
		$results = $this->model_extension_purpletree_pos_pos_report->getPosReports($report_filter_data);
		if(!empty($results)){
			foreach ($results as $result) {
			$json['reports'][] = array(
				'date_start' => date($this->language->get('date_format_short'), strtotime($result['date_start'])),
				'date_end'   => date($this->language->get('date_format_short'), strtotime($result['date_end'])),
				'orders'     => $result['orders'],
				'products'   => $result['products'],
				'tax'        => $this->currency->format((float)$result['tax'], $this->config->get('config_currency')),
				'total'      => $this->currency->format((float)$result['total'], $this->config->get('config_currency')),
				'g_total'      => $result['total']
				
			);
		}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		}
}?>