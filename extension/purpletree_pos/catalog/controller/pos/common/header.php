<?php
namespace Opencart\Catalog\Controller\Extension\PurpletreePos\Pos\Common;
class Header extends \Opencart\System\Engine\Controller {
	public function index() {
		$data = array();
				// Analytics
		$this->load->model('setting/extension');

		$data['analytics'] = array();

		// $analytics = $this->model_setting_extension->getExtensions('analytics');

		// foreach ($analytics as $analytic) {
			// if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
				// $data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
			// }
		// }

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['heading_title1'] = $this->document->getTitle();
		$data['seller_logo'] = '/admin/view/image/logo.png';
		
		$this->load->language('extension/module/purpletree_sellerpanel');  
		$this->load->language('extension/purpletree_pos/pos/header');  
		$this->load->language('account/ptsregister');  
		$data['name'] = $this->config->get('config_name');

			if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
				$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
			} else {
				$data['logo'] = '';
			}
		$data['home'] = $this->url->link('common/home');	
			$this->load->model('tool/image');
			$data['image'] = $this->model_tool_image->resize('catalog/no_image_seller.png', 40, 40);
			$data['sellerprofile'] 			= $this->url->link('account/edit', '', true);
			$data['direction'] = $this->language->get('direction');
		
			$data['lang'] = $this->language->get('code');
			//$data['language'] = $this->load->controller('extension/account/pos/common/language');
			$data['baseurl']=HTTP_SERVER;
			$data['stylespts'] = $this->document->getStyles();
			$data['scriptspts'] = $this->document->getScripts('header');
			
	
		return $this->load->view('extension/purpletree_pos/pos/header', $data);
	}
}