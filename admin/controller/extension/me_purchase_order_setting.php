<?php
namespace Opencart\Admin\Controller\extension;
class Mepurchaseordersetting extends \Opencart\System\Engine\Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/me_purchase_order_setting');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
		
		$this->load->model('extension/me_purchase_order');
		//$this->model_extension_me_purchase_order->createtable();

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('me_purchase_order_setting', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['payment_name'])) {
			$data['error_payment_name'] = $this->error['payment_name'];
		} else {
			$data['error_payment_name'] = array();
		}
		
		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}
		
		$labels = array('heading_title','entry_status','tab_export_column','button_save','button_cancel','text_edit','tab_column','tab_support','entry_currency','tab_general','tab_payment_method','tab_shipping_method','entry_name','text_enabled','text_disabled','button_remove','button_add','entry_order_status','entry_update_stock','help_update_stock','entry_default_order_status','help_default_order_status','entry_geo_zone','entry_total','entry_sort_order','entry_tax_class','entry_price','text_all_zones','text_none','tab_order_status','entry_customfeedis_status','entry_tax','entry_balance','entry_po_comment','entry_prefix');
		
		foreach($labels as $label){
			$data[$label] = $this->language->get($label);
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/me_purchase_order_setting', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/me_purchase_order_setting', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'], true);
		
		###  Get All Languages
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		### Get All Customer Fields
		$this->load->model('customer/custom_field');
		$data['custom_fields'] = $this->model_customer_custom_field->getCustomFields();
		
		### Order Statuses
		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		$data['columns'] = array('serial_no' => $this->language->get('column_serial_no'),'sku' => $this->language->get('column_sku'),'description' => $this->language->get('column_description'),'image' => $this->language->get('column_image'),'model' => $this->language->get('column_model'),'color' => $this->language->get('column_color'),'weight' => $this->language->get('column_weight'),'dimension' => $this->language->get('column_dimension'),'stock' => $this->language->get('column_stock'),'price' => $this->language->get('column_price'),'sale_price' => $this->language->get('column_sale_price'),'discount' => $this->language->get('column_discount'),'quantity' => $this->language->get('column_quantity'),'total' => $this->language->get('column_total'));
		
		$data['export_columns'] = array('serial_no' => $this->language->get('column_serial_no'),'sku' => $this->language->get('column_sku'),'description' => $this->language->get('column_description'),'model' => $this->language->get('column_model'),'color' => $this->language->get('column_color'),'weight' => $this->language->get('column_weight'),'dimension' => $this->language->get('column_dimension'),'stock' => $this->language->get('column_stock'),'price' => $this->language->get('column_price'),'quantity' => $this->language->get('column_quantity'),'total' => $this->language->get('column_total'));

		$data['xexport_columns'] = array('order_id' => $this->language->get('entry_order_id'),'po_no' => $this->language->get('entry_po_number'),'store' => $this->language->get('entry_store'),'store_url' => $this->language->get('entry_store_url'),'buyer_id' => $this->language->get('entry_buyer_id'),'buyer_company' => $this->language->get('entry_buyer_company'),'buyer_address' => $this->language->get('entry_buyer_address'),'buyer_zip_code' => $this->language->get('entry_buyer_zip_code'),'buyer_tel' => $this->language->get('entry_buyer_tel'),'buyer_contact' => $this->language->get('entry_buyer_contact'),'buyer_email' => $this->language->get('entry_buyer_email'),'supplier_id' => $this->language->get('entry_supplier_id'),'supplier_company' => $this->language->get('entry_supplier_company'),'supplier_address' => $this->language->get('entry_supplier_address'),'supplier_zip_code' => $this->language->get('entry_supplier_zip_code'),'supplier_tel' => $this->language->get('entry_supplier_tel'),'supplier_contact' => $this->language->get('entry_supplier_contact'),'supplier_email' => $this->language->get('entry_supplier_email'),'supplier_contact_tel' => $this->language->get('entry_supplier_contact_tel'),'payment_method' => $this->language->get('entry_payment_method'),'payment_code' => $this->language->get('entry_payment_code'),'shipping_method' => $this->language->get('entry_shipping_method'),'shipping_code' => $this->language->get('entry_shipping_code'),'shipping_term' => $this->language->get('entry_shipping_term'),'product' => $this->language->get('entry_excel_product'),'product_option' => $this->language->get('entry_excel_product_option'),'total' => $this->language->get('entry_total'),'total_details' => $this->language->get('entry_total_details'),'order_status' => $this->language->get('entry_order_status'),'delivery_date' => $this->language->get('entry_delivery_date'),'user_id' => $this->language->get('entry_user_id'),'username' => $this->language->get('entry_username'),'language_id' => $this->language->get('entry_language_id'),'language_code' => $this->language->get('entry_language_code'),'currency_id' => $this->language->get('entry_currency_id'),'currency_code' => $this->language->get('entry_currency_code'),'currency_value' => $this->language->get('entry_currency_value'),'attachment' => $this->language->get('entry_attachment'),'comment' => $this->language->get('entry_comment'),'stock_add' => $this->language->get('entry_stock_add'),'date_added' => $this->language->get('entry_date_added'),'date_modified' => $this->language->get('entry_date_modified'));
		
		$this->load->model('localisation/currency');

		$data['currencies'] = $this->model_localisation_currency->getCurrencies();
		
		### General Tab
		if (isset($this->request->post['me_purchase_order_setting_exportformat'])) {
			$data['me_purchase_order_setting_exportformat'] = $this->request->post['me_purchase_order_setting_exportformat'];
		} else {
			$data['me_purchase_order_setting_exportformat'] = $this->config->get('me_purchase_order_setting_exportformat');
		}

		if (isset($this->request->post['me_purchase_order_setting_currency'])) {
			$data['me_purchase_order_setting_currency'] = $this->request->post['me_purchase_order_setting_currency'];
		} else {
			$data['me_purchase_order_setting_currency'] = $this->config->get('me_purchase_order_setting_currency');
		}
		
		### General Tab
		if (isset($this->request->post['me_purchase_order_setting_column'])) {
			$data['me_purchase_order_setting_column'] = $this->request->post['me_purchase_order_setting_column'];
		} else {
			$data['me_purchase_order_setting_column'] = $this->config->get('me_purchase_order_setting_column');
		}
		
		$columns = $this->config->get('me_purchase_order_setting_column');
		
		$data['purchase_order_setting'] = array();
		$sortcolumns = array();
		if($columns){
			if(isset($columns['size'])){
				unset($columns['size']);
			}
			if(isset($columns['ali_link'])){
				unset($columns['ali_link']);
			}

			if(!isset($columns['sale_price'])){
				$columns['sale_price'] = array(
					'sort_order' => 13,
					'status' => isset($column['status']) ? $column['status'] : ''
				);
			}
			if(!isset($columns['discount'])){
				$columns['discount'] = array(
					'sort_order' => 13,
					'status' => isset($column['status']) ? $column['status'] : ''
				);
			}
			foreach($columns as $key => $column){
				$sortcolumns[] = array(
					'key' => $key,
					'sort_order' => $column['sort_order'],
					'name' => $this->language->get('entry_'.$key),
					'status' => isset($column['status']) ? $column['status'] : ''
				);
			}
            
            usort($sortcolumns, function($a, $b) {
                return $a['sort_order'] <=> $b['sort_order'];
            });
            
			function sortcolumn( $a, $b ){
				return $a['sort_order'] < $b['sort_order'] ? -1 : 1;
			}
			
		}
		
		foreach($sortcolumns as $column){
			$data['purchase_order_setting'][$column['key']] = array(
				'sort_order' => $column['sort_order'],
				'status' => $column['status'],
				'name' => $this->language->get('entry_'.$column['key']),
				'sort' => isset($data['sort_'.$column['key']]) ? $data['sort_'.$column['key']] : ''
			);
		}
		
		if (isset($this->request->post['me_purchase_order_setting_export_column'])) {
			$data['me_purchase_order_setting_export_column'] = $this->request->post['me_purchase_order_setting_export_column'];
		} else {
			$data['me_purchase_order_setting_export_column'] = $this->config->get('me_purchase_order_setting_export_column');
		}
		
		$columns = $this->config->get('me_purchase_order_setting_export_column');
		
		$data['purchase_order_export_setting'] = array();
		$sortcolumns = array();
		
		if($columns){
			if(isset($columns['size'])){
				unset($columns['size']);
			}
			if(isset($columns['ali_link'])){
				unset($columns['ali_link']);
			}
			foreach($columns as $key => $column){
				$sortcolumns[] = array(
					'key' => $key,
					'sort_order' => $column['sort_order'],
					'name' => $this->language->get('entry_'.$key),
					'status' => isset($column['status']) ? $column['status'] : ''
				);
			}
			
		
		}
		
		foreach($sortcolumns as $column){
			$data['purchase_order_export_setting'][$column['key']] = array(
				'sort_order' => $column['sort_order'],
				'status' => $column['status'],
				'name' => $this->language->get('entry_'.$column['key']),
				'sort' => isset($data['sort_'.$column['key']]) ? $data['sort_'.$column['key']] : ''
			);
		}

		if (isset($this->request->post['me_purchase_order_setting_xexport_column_all'])) {
			$data['me_purchase_order_setting_xexport_column_all'] = $this->request->post['me_purchase_order_setting_xexport_column_all'];
		} else {
			$data['me_purchase_order_setting_xexport_column_all'] = $this->config->get('me_purchase_order_setting_xexport_column_all');
		}

		if (isset($this->request->post['me_purchase_order_setting_xexport_column'])) {
			$data['me_purchase_order_setting_xexport_column'] = $this->request->post['me_purchase_order_setting_xexport_column'];
		} else {
			$data['me_purchase_order_setting_xexport_column'] = $this->config->get('me_purchase_order_setting_xexport_column');
		}
		
		$columns = $this->config->get('me_purchase_order_setting_xexport_column');
		
		$data['purchase_order_xexport_setting'] = array();
		$sortcolumns = array();
		
		if($columns){
			foreach($columns as $key => $column){
				$sortcolumns[] = array(
					'key' => $key,
					'sort_order' => $column['sort_order'],
					'name' => $this->language->get('entry_'.$key),
					'status' => isset($column['status']) ? $column['status'] : ''
				);
			}
			
// 			function sortxcolumns( $a, $b ){
// 				return $a['sort_order'] < $b['sort_order'] ? -1 : 1;
// 			}
			
// 			usort($sortcolumns, "sortxcolumns");
		}
		
		foreach($sortcolumns as $column){
			$data['purchase_order_xexport_setting'][$column['key']] = array(
				'sort_order' => $column['sort_order'],
				'status' => $column['status'],
				'name' => $this->language->get('entry_'.$column['key']),
				'sort' => isset($data['sort_'.$column['key']]) ? $data['sort_'.$column['key']] : ''
			);
		}
		
		if (isset($this->request->post['me_purchase_order_setting_orderstatus'])) {
			$data['me_purchase_order_setting_orderstatus'] = $this->request->post['me_purchase_order_setting_orderstatus'];
		} elseif($this->config->get('me_purchase_order_setting_orderstatus')) {
			$data['me_purchase_order_setting_orderstatus'] = $this->config->get('me_purchase_order_setting_orderstatus');
		}else{
			$data['me_purchase_order_setting_orderstatus'] = '';
		}
		
		if (isset($this->request->post['me_purchase_order_setting_dorderstatus'])) {
			$data['me_purchase_order_setting_dorderstatus'] = $this->request->post['me_purchase_order_setting_dorderstatus'];
		} elseif($this->config->get('me_purchase_order_setting_dorderstatus')) {
			$data['me_purchase_order_setting_dorderstatus'] = $this->config->get('me_purchase_order_setting_dorderstatus');
		}else{
			$data['me_purchase_order_setting_dorderstatus'] = '';
		}
		
		if (isset($this->request->post['me_purchase_order_setting_customfeedisstatus'])) {
			$data['me_purchase_order_setting_customfeedisstatus'] = $this->request->post['me_purchase_order_setting_customfeedisstatus'];
		} elseif($this->config->get('me_purchase_order_setting_customfeedisstatus')) {
			$data['me_purchase_order_setting_customfeedisstatus'] = $this->config->get('me_purchase_order_setting_customfeedisstatus');
		}else{
			$data['me_purchase_order_setting_customfeedisstatus'] = '';
		}
		
		if (isset($this->request->post['me_purchase_order_setting_tax'])) {
			$data['me_purchase_order_setting_tax'] = $this->request->post['me_purchase_order_setting_tax'];
		} elseif($this->config->get('me_purchase_order_setting_tax')) {
			$data['me_purchase_order_setting_tax'] = $this->config->get('me_purchase_order_setting_tax');
		}else{
			$data['me_purchase_order_setting_tax'] = '';
		}
		
		if (isset($this->request->post['me_purchase_order_setting_balance'])) {
			$data['me_purchase_order_setting_balance'] = $this->request->post['me_purchase_order_setting_balance'];
		} elseif($this->config->get('me_purchase_order_setting_balance')) {
			$data['me_purchase_order_setting_balance'] = $this->config->get('me_purchase_order_setting_balance');
		}else{
			$data['me_purchase_order_setting_balance'] = '';
		}
		
		if (isset($this->request->post['me_purchase_order_setting_prefix'])) {
			$data['me_purchase_order_setting_prefix'] = $this->request->post['me_purchase_order_setting_prefix'];
		} elseif($this->config->get('me_purchase_order_setting_prefix')) {
			$data['me_purchase_order_setting_prefix'] = $this->config->get('me_purchase_order_setting_prefix');
		}else{
			$data['me_purchase_order_setting_prefix'] = '';
		}
		
		if (isset($this->request->post['me_purchase_order_setting_comment'])) {
			$data['me_purchase_order_setting_comment'] = $this->request->post['me_purchase_order_setting_comment'];
		} elseif($this->config->get('me_purchase_order_setting_comment')) {
			$data['me_purchase_order_setting_comment'] = $this->config->get('me_purchase_order_setting_comment');
		}else{
			$data['me_purchase_order_setting_comment'] = '';
		}
		
		if (isset($this->request->post['me_purchase_order_setting_order_status'])) {
			$data['order_statuss'] = $this->request->post['me_purchase_order_setting_order_status'];
		} elseif($this->config->get('me_purchase_order_setting_order_status')) {
			$data['order_statuss'] = $this->config->get('me_purchase_order_setting_order_status');
		}else{
			$orderstatus = array();
			$orderstatus[] = array(
				'name' => 'Pending',
				'status' => 1,
			);
			$orderstatus[] = array(
				'name' => 'Received',
				'status' => 1,
			);
			$data['order_statuss'] = $orderstatus;
		}
		
		if (isset($this->request->post['me_purchase_order_setting_payment_method'])) {
			$data['payment_methods'] = $this->request->post['me_purchase_order_setting_payment_method'];
		} elseif($this->config->get('me_purchase_order_setting_payment_method')) {
			$data['payment_methods'] = $this->config->get('me_purchase_order_setting_payment_method');
		}else{
			$data['payment_methods'] = array();
		}
		
		if (isset($this->request->post['me_purchase_order_setting_shipping_method'])) {
			$data['shipping_methods'] = $this->request->post['me_purchase_order_setting_shipping_method'];
		} elseif($this->config->get('me_purchase_order_setting_shipping_method')) {
			$data['shipping_methods'] = $this->config->get('me_purchase_order_setting_shipping_method');
		}else{
			$data['shipping_methods'] = array();
		}
		
		$this->load->model('localisation/tax_class');
		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
		
		$this->load->model('localisation/geo_zone');
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/setting', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/me_purchase_order_setting')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if(isset($this->request->post['me_purchase_order_setting_shipping_method'])){
			foreach($this->request->post['me_purchase_order_setting_shipping_method'] as $code => $shipping_method){
				if ((md_strlen($shipping_method['name']) < 1) || (md_strlen($shipping_method['name']) > 255)) {
					$this->error['name'][$code] = $this->language->get('error_name');
				}
			}
		}
		
		if(isset($this->request->post['me_purchase_order_setting_payment_method'])){
			foreach($this->request->post['me_purchase_order_setting_payment_method'] as $code => $payment_method){
				if ((md_strlen($payment_method['name']) < 1) || (md_strlen($payment_method['name']) > 255)) {
					$this->error['payment_name'][$code] = $this->language->get('error_name');
				}
			}
		}
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}
}