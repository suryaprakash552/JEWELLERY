<?php
namespace Opencart\Admin\Controller\Extension\PurpletreePos\Events\Catalog;
class Product extends \Opencart\System\Engine\Controller {
	public function product_form(&$route, &$data, &$output) {
     
$data['module_purpletree_pos_status'] = $this->config->get('module_purpletree_pos_status');

   if($data['module_purpletree_pos_status']){
     $this->load->model('extension/purpletree_pos/posproduct');

			$product_info = $this->model_extension_purpletree_pos_posproduct->getproduct($data['product_id']);
               if (isset($this->request->post['pos'])) {
					$data['pos'] = $this->request->post['pos'];
				} elseif (!empty($product_info)) {
					$data['pos'] = $product_info['pos_status'];
				} else {
					$data['pos'] = '';
				}
				if (isset($this->request->post['pos_quentity'])) {
					$data['pos_quentity'] = $this->request->post['pos_quentity'];
				} elseif (!empty($product_info)) {
					$data['pos_quentity'] = $product_info['pos_quentity'];
				} else {
					$data['pos_quentity'] = '';
				}
		$find = array();
		$replace = array();
		$data['tab_pos']= 'POS';
		
		$find[] = '<li class="nav-item"><a href="#tab-report" data-bs-toggle="tab" class="nav-link">'.$data['tab_report'].'</a></li>';
		$find[] = '<div id="tab-report" class="tab-pane">';		 	
		
		$replace[] = '<li class="nav-item"><a href="#tab-report" data-bs-toggle="tab" class="nav-link">'.$data['tab_report'].'</a></li>
        <li class="nav-item"><a href="#tab-pos" data-bs-toggle="tab" class="nav-link">'.$data['tab_pos'].'</a></li>';
		
		$replace[] = $this->load->view('extension/purpletree_pos/events/catalog/product_form', $data);
		$output = str_replace($find,$replace,$output);
   }	
	}
	
	public function addproduct(&$route, &$data, &$product_id): void {
	if($this->config->get('module_purpletree_pos_status')){
	if (isset($this->request->post['pos'])) {
					$data['pos'] = $this->request->post['pos'];
				} elseif (!empty($data)) {
					$data['pos'] = $data['pos_status'];
				} else {
					$data['pos'] = '';
				}
				if (isset($this->request->post['pos_quentity'])) {
					$data['pos_quentity'] = $this->request->post['pos_quentity'];
				} elseif (!empty($data)) {
					$data['pos_quentity'] = $data['pos_quentity'];
				} else {
					$data['pos_quentity'] = '';
				}
			$this->db->query("INSERT INTO " . DB_PREFIX . "pts_pos_product SET product_id = '" . (int)$product_id . "',pos_quentity = '" . (int)$data['pos_quentity']. "',pos_status = '" . (int)$data['pos']. "'");
			
			/** update or insert into POS products **/
				if (isset($product_id)) {
				$query= $this->db->query("SELECT * FROM " . DB_PREFIX . "pts_pos_product WHERE product_id = '".(int)$product_id."'");
				if($query->num_rows>0){
				$this->db->query("UPDATE " . DB_PREFIX . "pts_pos_product SET pos_status = '" . (int)$data['pos'] . "', pos_quentity = '" . (int)$data['pos_quentity'] . "' WHERE product_id = '".(int)$product_id."'");
				}else{
			$this->db->query("INSERT INTO " . DB_PREFIX . "pts_pos_product SET product_id = '" . (int)$product_id . "',pos_quentity = '" . (int)$data['pos_quentity']. "',pos_status = '" . (int)$data['pos']. "'");
			}
			}
	}
	}
	public function editproduct(&$route, &$product_data, &$output): void {
	if($this->config->get('module_purpletree_pos_status')){
		if (isset($this->request->post['pos'])) {
					$data['pos'] = $this->request->post['pos'];
				} elseif (!empty($product_data)) {
					$data['pos'] = $product_data['pos_status'];
				} else {
					$data['pos'] = '';
				}
				if (isset($this->request->post['pos_quentity'])) {
					$data['pos_quentity'] = $this->request->post['pos_quentity'];
				} elseif (!empty($product_data)) {
					$data['pos_quentity'] = $product_data['pos_quentity'];
				} else {
					$data['pos_quentity'] = '';
				}

	$product_id = $product_data[0];
/** update or insert into POS products **/
				if (isset($product_id)) {
				$query= $this->db->query("SELECT * FROM " . DB_PREFIX . "pts_pos_product WHERE product_id = '".(int)$product_id."'");
				if($query->num_rows>0){
				$this->db->query("UPDATE " . DB_PREFIX . "pts_pos_product SET pos_status = '" . (int)$data['pos'] . "', pos_quentity = '" . (int)$data['pos_quentity'] . "' WHERE product_id = '".(int)$product_id."'");
				}else{
			$this->db->query("INSERT INTO " . DB_PREFIX . "pts_pos_product SET product_id = '" . (int)$product_id . "',pos_quentity = '" . (int)$data['pos_quentity']. "',pos_status = '" . (int)$data['pos']. "'");
			}
			}
	}
	}
	
	public function getproduct(&$route, &$data, &$product_id): void {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "product` p LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.`product_id` = pd.`product_id`) LEFT JOIN `" . DB_PREFIX . "pts_pos_product` ppp ON (p.`product_id` = ppp.`product_id`)  WHERE p.`product_id` = '" . (int)$product_id . "' AND pd.`language_id` = '" . (int)$this->config->get('config_language_id') . "'");
		}
	
}
?>