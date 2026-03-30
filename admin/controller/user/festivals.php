<?php
namespace Opencart\Admin\Controller\User;
/**
 * Class Language
 *
 * @package Opencart\Admin\Controller\Localisation
 */
class Festivals extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return void
	 */
	public function index(): void {
		$this->load->language('user/festivals');

		$this->document->setTitle($this->language->get('heading_title'));

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('user/festivals', 'user_token=' . $this->session->data['user_token'] . $url)
		];

		$data['add'] = $this->url->link('user/festivals.form', 'user_token=' . $this->session->data['user_token'] . $url);
		$data['delete'] = $this->url->link('user/festivals.delete', 'user_token=' . $this->session->data['user_token']);

		$data['list'] = $this->getList();

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('user/festivals', $data));
	}

	/**
	 * List
	 *
	 * @return void
	 */
	public function list(): void {
		$this->load->language('user/festivals');

		$this->response->setOutput($this->getList());
	}

	/**
	 * Get List
	 *
	 * @return string
	 */
	public function getList(): string {

		if (isset($this->request->get['sort'])) {
			$sort = (string)$this->request->get['sort'];
		} else {
			$sort = 'festival';
		}
		

		if (isset($this->request->get['order'])) {
			$order = (string)$this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['action'] = $this->url->link('user/festivals.list', 'user_token=' . $this->session->data['user_token'] . $url);

		// Language
		$data['festivals'] = [];
		$filter_data = [
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit' => $this->config->get('config_pagination_admin')
		];

		$this->load->model('user/festivals');

		$results = $this->model_user_festivals->getFestivals($filter_data);
        
		foreach ($results as $result) {
			$data['festivals'][] = [
			    'festivals_id' => $result['festivals_id'],
                'date'         => $result['date'],
                'festival'     => $result['festival'],
				'edit' => $this->url->link('user/festivals.form', 'user_token=' . $this->session->data['user_token'] . '&festivals_id=' . $result['festivals_id'] . $url)
			];
		}

		$url = '';
        //print_r($data);
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}
        $data['sort_date'] = $this->url->link('user/festivals.list', 'user_token=' . $this->session->data['user_token'] . '&sort=date' . $url);
        $data['sort_festival'] = $this->url->link('user/festivals.list', 'user_token=' . $this->session->data['user_token'] . '&sort=festival' . $url);
		/*$data['sort_sort_order'] = $this->url->link('user/festivals.list', 'user_token=' . $this->session->data['user_token'] . '&sort=sort_order' . $url);*/
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$festivals_total = $this->model_user_festivals->getTotalFestivals();

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $festivals_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('user/festivals.list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($festivals_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($festivals_total - $this->config->get('config_pagination_admin'))) ? $festivals_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $festivals_total, ceil($festivals_total / $this->config->get('config_pagination_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		return $this->load->view('user/festivals_list', $data);
	}

	/**
	 * Form
	 *
	 * @return void
	 */
	public function form(): void {
    $this->load->language('user/festivals');
    $this->document->setTitle($this->language->get('heading_title'));

    $data['text_form'] = !isset($this->request->get['festivals_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

    $url = '';

    if (isset($this->request->get['festival'])) {
        $url .= '&festival=' . urlencode(html_entity_decode($this->request->get['festival'], ENT_QUOTES, 'UTF-8'));
    }
    if (isset($this->request->get['date'])) {
        $url .= '&date=' . urlencode(html_entity_decode($this->request->get['date'], ENT_QUOTES, 'UTF-8'));
    }
    if (isset($this->request->get['sort'])) {
        $url .= '&sort=' . $this->request->get['sort'];
    }
    if (isset($this->request->get['order'])) {
        $url .= '&order=' . $this->request->get['order'];
    }
    if (isset($this->request->get['page'])) {
        $url .= '&page=' . $this->request->get['page'];
    }

    $data['breadcrumbs'] = [];
    $data['breadcrumbs'][] = [
        'text' => $this->language->get('text_home'),
        'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
    ];
    $data['breadcrumbs'][] = [
        'text' => $this->language->get('heading_title'),
        'href' => $this->url->link('user/festivals', 'user_token=' . $this->session->data['user_token'] . $url)
    ];

    $data['save'] = $this->url->link('user/festivals.save', 'user_token=' . $this->session->data['user_token']);
    $data['back'] = $this->url->link('user/festivals', 'user_token=' . $this->session->data['user_token'] . $url);

    $festivals_info = [];

        if (isset($this->request->get['festivals_id'])) {
            $this->load->model('user/festivals');
            $festivals_info = $this->model_user_festivals->getFestivalsById((int)$this->request->get['festivals_id']);
        }
        
        $data['festivals_id'] = $festivals_info['festivals_id'] ?? 0;
        $data['festival']     = $festivals_info['festival'] ?? '';
        $data['date']         = $festivals_info['date'] ?? '';


    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('user/festivals_form', $data));
}


	/**
	 * Save
	 *
	 * @return void
	 */
	public function save(): void {
    $this->load->language('user/festivals');
    $json = [];

    if (!$this->user->hasPermission('modify', 'user/festivals')) {
        $json['error']['warning'] = $this->language->get('error_permission');
    }

    $required = [
        'festivals_id' => '',
        'festival'     => '',
        'date'         => ''
    ];

    $post_info = $this->request->post + $required;

    // ✅ Simple validation if custom functions don't exist
    if (empty($post_info['festival'])) {
        $json['error']['festival'] = $this->language->get('error_festival');
    }
    if (empty($post_info['date'])) {
        $json['error']['date'] = $this->language->get('error_date');
    }

    $this->load->model('user/festivals');

    $festivals_info = $this->model_user_festivals->getFestivalsByFestival($post_info['festival']);

    if ($festivals_info && (!$post_info['festivals_id'] || $festivals_info['festivals_id'] != $post_info['festivals_id'])) {
        $json['error']['warning'] = $this->language->get('error_exists');
    }

    if (!$json) {
        if (!$post_info['festivals_id']) {
            $json['festivals_id'] = $this->model_user_festivals->addFestivals($post_info);
        } else {
            $this->model_user_festivals->editFestivals((int)$post_info['festivals_id'], $post_info);
        }
        $json['success'] = $this->language->get('text_success');
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

	
	


	/**
	 * Delete
	 *
	 * @return void
	 */
	public function delete(): void {
		$this->load->language('user/festivals');

		$json = [];

		if (isset($this->request->post['selected'])) {
			$selected = (array)$this->request->post['selected'];
		} else {
			$selected = [];
		} 

		if (!$this->user->hasPermission('modify', 'user/festivals')) {
			$json['error'] = $this->language->get('error_permission');
		}
        
	if (!$json) {
			$this->load->model('user/festivals');
			foreach ($selected as $festivals_id) {
				$this->model_user_festivals->deleteFestivals($festivals_id);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}