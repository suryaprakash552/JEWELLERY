<?php
namespace Opencart\Admin\Controller\User;
/**
 * Class User
 *
 * @package Opencart\Admin\Controller\User
 */
class User extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return void
	 */
	public function index(): void {
		$this->load->language('user/user');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['filter_username'])) {
			$filter_username = (string)$this->request->get['filter_username'];
		} else {
			$filter_username = '';
		}

		if (isset($this->request->get['filter_name'])) {
			$filter_name = (string)$this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_email'])) {
			$filter_email = (string)$this->request->get['filter_email'];
		} else {
			$filter_email = '';
		}

		if (isset($this->request->get['filter_user_group_id'])) {
			$filter_user_group_id = (int)$this->request->get['filter_user_group_id'];
		} else {
			$filter_user_group_id = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = (bool)$this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}

		if (isset($this->request->get['filter_ip'])) {
			$filter_ip = (string)$this->request->get['filter_ip'];
		} else {
			$filter_ip = '';
		}

		$url = '';

		if (isset($this->request->get['filter_username'])) {
			$url .= '&filter_username=' . urlencode(html_entity_decode($this->request->get['filter_username'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_user_group_id'])) {
			$url .= '&filter_user_group_id=' . $this->request->get['filter_user_group_id'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_ip'])) {
			$url .= '&filter_ip=' . $this->request->get['filter_ip'];
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
			'href' => $this->url->link('user/user', 'user_token=' . $this->session->data['user_token'] . $url)
		];

		$data['add'] = $this->url->link('user/user.form', 'user_token=' . $this->session->data['user_token'] . $url);
		$data['delete'] = $this->url->link('user/user.delete', 'user_token=' . $this->session->data['user_token']);

		$data['list'] = $this->getList();

		// User Group
		$this->load->model('user/user_group');

		$data['user_groups'] = $this->model_user_user_group->getUserGroups();

		$data['filter_username'] = $filter_name;
		$data['filter_name'] = $filter_name;
		$data['filter_email'] = $filter_email;
		$data['filter_user_group_id'] = $filter_user_group_id;
		$data['filter_status'] = $filter_status;
		$data['filter_ip'] = $filter_ip;

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('user/user', $data));
	}

	/**
	 * List
	 *
	 * @return void
	 */
	public function list(): void {
		$this->load->language('user/user');

		$this->response->setOutput($this->getList());
	}

	/**
	 * Get List
	 *
	 * @return string
	 */
	public function getList(): string {

		if (isset($this->request->get['filter_username'])) {
			$filter_username = $this->request->get['filter_username'];
		} else {
			$filter_username = '';
		}

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = '';
		}

		if (isset($this->request->get['filter_user_group_id'])) {
			$filter_user_group_id = (int)$this->request->get['filter_user_group_id'];
		} else {
			$filter_user_group_id = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = (bool)$this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}

		if (isset($this->request->get['filter_ip'])) {
			$filter_ip = (string)$this->request->get['filter_ip'];
		} else {
			$filter_ip = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = (string)$this->request->get['sort'];
		} else {
			$sort = 'username';
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

		if (isset($this->request->get['filter_username'])) {
			$url .= '&filter_username=' . urlencode(html_entity_decode($this->request->get['filter_username'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_user_group_id'])) {
			$url .= '&filter_user_group_id=' . $this->request->get['filter_user_group_id'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_ip'])) {
			$url .= '&filter_ip=' . $this->request->get['filter_ip'];
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

		$data['action'] = $this->url->link('user/user.list', 'user_token=' . $this->session->data['user_token'] . $url);

		// User
		$data['users'] = [];

		$filter_data = [
			'filter_username'      => $filter_username,
			'filter_name'          => $filter_name,
			'filter_email'         => $filter_email,
			'filter_user_group_id' => $filter_user_group_id,
			'filter_status'        => $filter_status,
			'filter_ip'            => $filter_ip,
			'sort'                 => $sort,
			'order'                => $order,
			'start'                => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit'                => $this->config->get('config_pagination_admin')
		];

		$this->load->model('user/user');

		$results = $this->model_user_user->getUsers($filter_data);
/*	    print_r($data);
*/
		foreach ($results as $result) {
			$data['users'][] = [
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'edit'       => $this->url->link('user/user.form', 'user_token=' . $this->session->data['user_token'] . '&user_id=' . $result['user_id'] . $url)
			] + $result;
		}

		$url = '';

		if (isset($this->request->get['filter_username'])) {
			$url .= '&filter_username=' . urlencode(html_entity_decode($this->request->get['filter_username'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_user_group_id'])) {
			$url .= '&filter_user_group_id=' . $this->request->get['filter_user_group_id'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_ip'])) {
			$url .= '&filter_ip=' . $this->request->get['filter_ip'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		$data['sort_username'] = $this->url->link('user/user.list', 'user_token=' . $this->session->data['user_token'] . '&sort=username' . $url);
		$data['sort_name'] = $this->url->link('user/user.list', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url);
		$data['sort_email'] = $this->url->link('user/user.list', 'user_token=' . $this->session->data['user_token'] . '&sort=u.email' . $url);
		$data['sort_user_group'] = $this->url->link('user/user.list', 'user_token=' . $this->session->data['user_token'] . '&sort=user_group' . $url);
		$data['sort_status'] = $this->url->link('user/user.list', 'user_token=' . $this->session->data['user_token'] . '&sort=u.status' . $url);
		$data['sort_date_added'] = $this->url->link('user/user.list', 'user_token=' . $this->session->data['user_token'] . '&sort=u.date_added' . $url);

		$url = '';

		if (isset($this->request->get['filter_username'])) {
			$url .= '&filter_username=' . urlencode(html_entity_decode($this->request->get['filter_username'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_user_group_id'])) {
			$url .= '&filter_user_group_id=' . $this->request->get['filter_user_group_id'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_ip'])) {
			$url .= '&filter_ip=' . $this->request->get['filter_ip'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$user_total = $this->model_user_user->getTotalUsers();
	   /* print_r($data);*/

		$data['pagination'] = $this->load->controller('common/pagination', [
		    
			'total' => $user_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('user/user.list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($user_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($user_total - $this->config->get('config_pagination_admin'))) ? $user_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $user_total, ceil($user_total / $this->config->get('config_pagination_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		return $this->load->view('user/user_list', $data);
	}

	/**
	 * Form
	 *
	 * @return void
	 */
	public function form(): void {

		$this->load->language('user/user');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['text_form'] = !isset($this->request->get['user_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$url = '';

		if (isset($this->request->get['filter_username'])) {
			$url .= '&filter_username=' . urlencode(html_entity_decode($this->request->get['filter_username'], ENT_QUOTES, 'UTF-8'));
		}
			if (isset($this->request->get['filter_employeeid'])) {
			$url .= '&filter_employeeid=' . urlencode(html_entity_decode($this->request->get['filter_employeeid'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_mobilenumber'])) {
			$url .= '&filter_mobilenumber=' . urlencode(html_entity_decode($this->request->get['filter_mobilenumber'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_date_of_birth'])) {
			$url .= '&filter_date_of_birth=' . urlencode(html_entity_decode($this->request->get['filter_date_of_birth'], ENT_QUOTES, 'UTF-8'));
		}


		if (isset($this->request->get['filter_user_group_id'])) {
			$url .= '&filter_user_group_id=' . $this->request->get['filter_user_group_id'];
		}
		if (isset($this->request->get['filter_zone_id'])) {
			$url .= '&filter_zone_id=' . $this->request->get['filter_zone_id'];
		}
		if (isset($this->request->get['filter_designation_id'])) {
			$url .= '&filter_designation_id=' . $this->request->get['filter_designation_id'];
		}
		if (isset($this->request->get['filter_employmenttype_id'])) {
			$url .= '&filter_employmenttype_id=' . $this->request->get['filter_employmenttype_id'];
		}
		if (isset($this->request->get['filter_employmentstatus_id'])) {
			$url .= '&filter_employmentstatus_id=' . $this->request->get['filter_employmentstatus_id'];
		}
		if (isset($this->request->get['filter_sourceofhire_id'])) {
			$url .= '&filter_sourceofhire_id=' . $this->request->get['filter_sourceofhire_id'];
		}
		if (isset($this->request->get['filter_date_of_joining'])) {
			$url .= '&filter_date_of_joining=' . urlencode(html_entity_decode($this->request->get['filter_date_of_joining'], ENT_QUOTES, 'UTF-8'));
		}
		
			if (isset($this->request->get['filter_pan'])) {
			$url .= '&filter_pan=' . urlencode(html_entity_decode($this->request->get['filter_pan'], ENT_QUOTES, 'UTF-8'));
		}
			if (isset($this->request->get['filter_aadhar'])) {
			$url .= '&filter_aadhar=' . urlencode(html_entity_decode($this->request->get['filter_aadhar'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		
		

		if (isset($this->request->get['filter_ip'])) {
			$url .= '&filter_ip=' . $this->request->get['filter_ip'];
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
			'href' => $this->url->link('user/user', 'user_token=' . $this->session->data['user_token'] . $url)
		];

		$data['save'] = $this->url->link('user/user.save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('user/user', 'user_token=' . $this->session->data['user_token'] . $url);
		
		    $this->load->model('user/user');
		    $zone_info = $this->model_user_user->getZones();
			$designation_info = $this->model_user_user->getDesignations();
			$employmenttype_info = $this->model_user_user->getEmploymenttypes();
			$employmentstatus_info = $this->model_user_user->getEmploymentsstatus();
			$sourceofhire_info = $this->model_user_user->getSourceofhire();

		if (isset($this->request->get['user_id'])) {

			$user_info = $this->model_user_user->getUser($this->request->get['user_id']);
			$emp_info = $this->model_user_user->getEmployees($this->request->get['user_id']);
			$salary_info = $this->model_user_user->getSalaries($this->request->get['user_id']);
			
		}else {
                $user_info = [];
                $emp_info  = [];
                $salary_info = [];
            }
        
		if (!empty($user_info)) {
			$data['user_id'] = $user_info['user_id'];
		} else {
			$data['user_id'] = 0;
		}

		if (!empty($user_info)) {
			$data['username'] = $user_info['username'];
		} else {
			$data['username'] = '';
		}
		if (!empty($zone_info)) {
			$data['zones'] = $zone_info;
		} else {
			$data['zones'] = '';
		}
		if (!empty($designation_info)) {
			$data['designations'] = $designation_info;
		} else {
			$data['designations'] = '';
		}
		if (!empty($employmenttype_info)) {
			$data['employmenttypes'] = $employmenttype_info;
		} else { 
			$data['employmenttypes'] = '';
		}
		if (!empty($employmentstatus_info)) {
			$data['listemploymentstatus'] = $employmentstatus_info;
		} else {
			$data['listemploymentstatus'] = '';
		}
		if (!empty($sourceofhire_info)) {
			$data['listsourceofhire'] = $sourceofhire_info;
		} else {
			$data['listsourceofhire'] = '';
		}

		// User Group
		$this->load->model('user/user_group');

		$data['user_groups'] = $this->model_user_user_group->getUserGroups();

		if (!empty($user_info)) {
			$data['user_group_id'] = $user_info['user_group_id'];
		} else {
			$data['user_group_id'] = 0;
		}
		if (!empty($emp_info)) {
			$data['referredby_id'] = $emp_info['referredby_id'];
		} else {
			$data['referredby_id'] = '';
		}
		if (!empty($emp_info)) {
			$data['reportingempid'] = $emp_info['reportingempid'];
		} else {
			$data['reportingempid'] = '';
		}
			if (!empty($emp_info)) {
			$data['zone_id'] = $emp_info['zone_id'];
		} else {
			$data['zone_id'] = '';
		}
			if (!empty($emp_info)) {
			$data['designation_id'] = $emp_info['designation_id'];
		} else {
			$data['designation_id'] = '';
		}
			if (!empty($emp_info)) {
			$data['employmenttype_id'] = $emp_info['employmenttype_id'];
		} else {
			$data['employmenttype_id'] = '';
		}
			if (!empty($emp_info)) {
			$data['employmentstatus_id'] = $emp_info['employmentstatus_id'];
		} else {
			$data['employmentstatus_id'] = 0;
		}
			if (!empty($emp_info)) {
			$data['sourceofhire_id'] = $emp_info['sourceofhire_id'];
		} else {
			$data['sourceofhire_id'] = 0;
		}
		if (!empty($emp_info)) {
			$data['date_of_joining'] = $emp_info['date_of_joining'];
		} else {
			$data['date_of_joining'] = '';
		}
		if (!empty($emp_info)) {
			$data['experience'] = $emp_info['experience'];
		} else {
			$data['experience'] = '';
		}
         $data['total_experience'] = $data['experience']; 
        $data['total_experience_text'] = $data['experience'] . ' Years';
        
        if (!empty($data['date_of_joining'])) {
            $doj = new \DateTime($data['date_of_joining']);
            $today = new \DateTime();
        
            $diff = $doj->diff($today);
        
            $years  = $diff->y + (int)$data['experience'];
            $months = $diff->m;
        
            $data['total_experience']      = $years;
            $data['total_experience_text'] = $years . ' Years ';
        }

        
		if (!empty($emp_info)) {
			$data['pan'] = $emp_info['pan'];
		} else {
			$data['pan'] = '';
		}
		if (!empty($emp_info)) {
			$data['aadhar'] = $emp_info['aadhar'];
		} else {
			$data['aadhar'] = '';
		}
		
		if (!empty($emp_info)) {
			$data['panimage'] = $emp_info['panimage'];
		} else {
			$data['panimage'] = 'no_image.png';
		}
		
		if (!empty($emp_info)) {
			$data['aadharimage'] = $emp_info['aadharimage'];
		} else {
			$data['aadharimage'] = 'no_image.png';
		}
		if (!empty($user_info)) {
			$data['logoimage'] = $user_info['logoimage'];
		} else {
			$data['logoimage'] = 'no_image.png';
		}
		
		//Salary Details
		
		if (!empty($salary_info)) {
			$data['annualctc'] = (float)$salary_info['annualctc'];
		} else {
			$data['annualctc'] = '0';
		}
		if (!empty($salary_info)) {
			$data['basic'] = (float)$salary_info['basic'];
		} else {
			$data['basic'] = '0';
		}
		if (!empty($salary_info)) {
			$data['hra'] = (float)$salary_info['hra'];
		} else {
			$data['hra'] = '0';
		}
		if (!empty($salary_info)) {
			$data['conveyance'] = (float)$salary_info['conveyance'];
		} else {
			$data['conveyance'] = '0';
		}
		if (!empty($salary_info)) {
			$data['variablepay'] = (float)$salary_info['variablepay'];
		} else {
			$data['variablepay'] = '0';
		}
		if (!empty($salary_info)) {
			$data['pf'] = (float)$salary_info['pf'];
		} else {
			$data['pf'] = '0';
		}
			if (!empty($salary_info)) {
			$data['ybasic'] = (float)$salary_info['ybasic'];
		} else {
			$data['ybasic'] = '0';
		}
		if (!empty($salary_info)) {
			$data['yhra'] = (float)$salary_info['yhra'];
		} else {
			$data['yhra'] = '0';
		}
		if (!empty($salary_info)) {
			$data['yconveyance'] = (float)$salary_info['yconveyance'];
		} else {
			$data['yconveyance'] = '0';
		}
		if (!empty($salary_info)) {
			$data['yvariablepay'] = (float)$salary_info['yvariablepay'];
		} else {
			$data['yvariablepay'] = '0';
		}
		if (!empty($salary_info)) {
			$data['ypf'] = (float)$salary_info['ypf'];
		} else {
			$data['ypf'] = '0';
		}
			if (!empty($salary_info)) {
			$data['calbasic'] = (float)$salary_info['calbasic'];
		} else {
			$data['calbasic'] = '0';
		}
		if (!empty($salary_info)) {
			$data['calhra'] = (float)$salary_info['calhra'];
		} else {
			$data['calhra'] = '0';
		}
		if (!empty($salary_info)) {
			$data['calvariablepay'] = (float)$salary_info['calvariablepay'];
		} else {
			$data['calvariablepay'] = '0';
		}
		if (!empty($salary_info)) {
			$data['calpf'] = (float)$salary_info['calpf'];
		} else {
			$data['calpf'] = '0';
		}
		if (!empty($salary_info)) {
			$data['total'] = (float)$salary_info['total'];
		} else {
			$data['total'] = '0';
			
		}if (!empty($salary_info)) {
			$data['ytotal'] = (float)$salary_info['ytotal'];
		} else {
			$data['ytotal'] = '0';
		}
        if (!empty($salary_info)) {
			$data['fixed'] = (float)$salary_info['fixed'];
		} else {
			$data['fixed'] = '0';
			
		}if (!empty($salary_info)) {
			$data['yfixed'] = (float)$salary_info['yfixed'];
		} else {
			$data['yfixed'] = '0';
		}    
		
// 		print_r($data);
		
		// Custom Fields
		$data['custom_fields'] = [];

		$filter_data = [
			'filter_location' => 'payroll',
			'sort'            => 'cf.sort_order',
			'order'           => 'ASC'
		];

		$this->load->model('user/custom_field');

		$custom_fields = $this->model_user_custom_field->getCustomFields($filter_data);

		foreach ($custom_fields as $custom_field) {
			if ($custom_field['status']) {
				$data['custom_fields'][] = [
					'custom_field_value' => $this->model_user_custom_field->getValues($custom_field['custom_field_id'])
				] + $custom_field;
			}
		}

	/*	if (!empty($emp_info)) {
			$data['payroll_custom_field'] = $emp_info['payroll_custom_field'];
		} else {
			$data['payroll_custom_field'] = [];
		}
		*/
		$this->load->model('tool/image');
		$data['panplaceholder'] = $this->model_tool_image->resize($data['panimage'], $this->config->get('config_image_default_width'), $this->config->get('config_image_default_height'));

		if (isset($data['panimage']) && is_file(DIR_IMAGE . html_entity_decode($data['panimage'], ENT_QUOTES, 'UTF-8'))) {
		    $data['panimageinput'] = $data['panimage'];
			$data['panimage'] = $this->model_tool_image->resize($data['panimage'], $this->config->get('config_image_default_width'), $this->config->get('config_image_default_height'));
		} else {
			$data['panimageinput'] = 'no_image.png';
			$data['panimage']=$data['panplaceholder'];
		}
	
		
		$data['aadharplaceholder'] = $this->model_tool_image->resize($data['aadharimage'], $this->config->get('config_image_default_width'), $this->config->get('config_image_default_height'));

		if (isset($data['aadharimage']) && is_file(DIR_IMAGE . html_entity_decode($data['aadharimage'], ENT_QUOTES, 'UTF-8'))) {
		    $data['aadharimageinput'] = $data['aadharimage'];
			$data['aadharimage'] = $this->model_tool_image->resize($data['aadharimage'], $this->config->get('config_image_default_width'), $this->config->get('config_image_default_height'));
		} else {
	    	$data['aadharimageinput'] = 'no_image.png';
	    	$data['aadharimage']=$data['aadharplaceholder'];
		}
		

		$data['logoplaceholder'] = $this->model_tool_image->resize($data['logoimage'], $this->config->get('config_image_default_width'), $this->config->get('config_image_default_height'));

		if (isset($data['logoimage']) && is_file(DIR_IMAGE . html_entity_decode($data['logoimage'], ENT_QUOTES, 'UTF-8'))) {
		    $data['logoimageinput'] = $data['logoimage'];
			$data['logoimage'] = $this->model_tool_image->resize($data['logoimage'], $this->config->get('config_image_default_width'), $this->config->get('config_image_default_height'));
		} else {
			$data['logoimageinput'] = 'no_image.png';
			$data['logoimage']=$data['logoplaceholder'];
		}
		
		
		if (!empty($emp_info)) {
			$data['employeeid'] = $emp_info['employeeid'];
		} else {
			$data['employeeid'] = '';
		}
		if (!empty($emp_info)) {
			$data['mobilenumber'] = $emp_info['mobilenumber'];
		} else {
			$data['mobilenumber'] = '';
		}
		if (!empty($emp_info)) {
            $data['user_gender'] = $emp_info['gender'];
        } else {
            $data['user_gender'] = '1';
        }

		if (!empty($emp_info['date_of_birth'])) {
         $data['date_of_birth'] = $emp_info['date_of_birth'];

            $dob = new \DateTime($emp_info['date_of_birth']);
            $today = new \DateTime();
            $diff = $dob->diff($today);
        
            $data['age'] = $diff->y.' Years ';
        } else {
            $data['date_of_birth'] = '';
            $data['age'] = '';
        }


		if (!empty($user_info)) {
			$data['firstname'] = $user_info['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (!empty($user_info)) {
			$data['lastname'] = $user_info['lastname'];
		} else {
			$data['lastname'] = '';
		}

		if (!empty($user_info)) {
			$data['email'] = $user_info['email'];
		} else {
			$data['email'] = '';
		}
			if (!empty($emp_info)) {
			$data['presentadd'] = $emp_info['presentadd'];
		} else {
			$data['presentadd'] = '';
		}
        	if (!empty($emp_info)) {
			$data['permanentadd'] = $emp_info['permanentadd'];
		} else {
			$data['permanentadd'] = '';
		}
        
        if(!empty($emp_info) and isset($emp_info['zone_id']))
        {
            $data['zone_id']=$emp_info['zone_id'];
        }else
        {
            $data['zone_id']='';
        }
        if(!empty($emp_info) and isset($emp_info['designation_id']))
        {
            $data['designation_id']=$emp_info['designation_id'];
        }else
        {
            $data['designation_id']='';
        }
        if(!empty($emp_info) and isset($emp_info['employmenttype_id']))
        {
            $data['employmenttype_id']=$emp_info['employmenttype_id'];
        }else
        {
            $data['employmenttype_id']='';
        }
        if(!empty($emp_info) and isset($emp_info['employmentstatus_id']))
        {
            $data['employmentstatus_id']=$emp_info['employmentstatus_id'];
        }else
        {
            $data['employmentstatus_id']='';
        }
         if(!empty($emp_info) and isset($emp_info['sourceofhire_id']))
        {
            $data['sourceofhire_id']=$emp_info['sourceofhire_id'];
        }else
        {
            $data['sourceofhire_id']='';
        }
		// Image
	/*	if (!empty($user_info)) {
			$data['image'] = $user_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', $this->config->get('config_image_default_width'), $this->config->get('config_image_default_height'));

		if ($data['image'] && is_file(DIR_IMAGE . html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'))) {
			$data['thumb'] = $this->model_tool_image->resize($data['image'], $this->config->get('config_image_default_width'), $this->config->get('config_image_default_height'));
		} else {
			$data['thumb'] = $data['placeholder'];
		}*/
		

		if (!empty($user_info)) {
			$data['status'] = $user_info['status'];
		} else {
			$data['status'] = 0;
		}

		$data['authorize'] = $this->getAuthorize();
		$data['login'] = $this->getLogin();

		$data['user_token'] = $this->session->data['user_token'];

	/*	$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
*/
	
		
	 /*  $this->load->language('user/user');

       $this->load->model('user/user');


       if (isset($this->request->get['user_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
        $user_info = $this->model_user_user->getUser($this->request->get['user_id']);
        $salary_info = $this->model_user_user->getSalaries($this->request->get['user_id']);
        $emp_info = $this->model_user_user->getEmployees($this->request->get['user_id']);
     }
      $data['username'] = isset($user_info['username']) ? $user_info['username'] : '';

     if (isset($this->request->post['username'])) {
        $data['username'] = $this->request->post['username'];
     } 
     elseif (!empty($user_info)) {
        $data['username'] = $user_info['username'];
     } 
     else {
        $data['username'] = '';
     }

     $data['password'] = '';
     */
     $data['all_users'] = [];
        
        $all_users = $this->model_user_user->getUsers([
            'start' => 0,
            'limit' => 9999
        ]);
        
        foreach ($all_users as $u) {
            $data['all_users'][] = [
                'user_id' => $u['user_id'],
                'name'    => $u['firstname'] . ' ' . $u['lastname']
            ];
        }
        
        if (!empty($data['referredby_id'])) {
            $ref = $this->model_user_user->getUser($data['referredby_id']);
            $data['referredby_name'] = $ref['firstname'] . ' ' . $ref['lastname'];
        } else {
            $data['referredby_name'] = '';
        }
        
        if (!empty($data['reportingempid'])) {
            $rep = $this->model_user_user->getUser($data['reportingempid']);
            $data['reportingemp_name'] = $rep['firstname'] . ' ' . $rep['lastname'];
        } else {
            $data['reportingemp_name'] = '';
        }
        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

        
        
        $this->response->setOutput($this->load->view('user/user_form', $data));

    /*$this->model_user_user->editUser($this->request->get['user_id'], $this->request->post);*/
  

	}
	

	/**
	 * Save
	 *
	 * @return void
	 */
	public function save(): void {
	   
		$this->load->language('user/user');

		$json = [];

		if (!$this->user->hasPermission('modify', 'user/user')) {
		   
			$json['error']['warning'] = $this->language->get('error_permission');
		}

		$required = [
			'user_id'   => '',
			'username'  => '',
			'firstname' => '',
			'lastname'  => '',
			'email'     => ''
			
			
		];

		$post_info = array_merge($required, $this->request->post);


		if (!oc_validate_length($post_info['username'], 3, 20)) {
			$json['error']['username'] = $this->language->get('error_username');
		}

		$this->load->model('user/user');

		$user_info = $this->model_user_user->getUserByUsername($post_info['username']);

		if ($user_info && (!$post_info['user_id'] || ($post_info['user_id'] != $user_info['user_id']))) {
			$json['error']['warning'] = $this->language->get('error_username_exists');
		}

		if (!oc_validate_length($post_info['firstname'], 1, 32)) {
			$json['error']['firstname'] = $this->language->get('error_firstname');
		}

		if (!oc_validate_length($post_info['lastname'], 1, 32)) {
			$json['error']['lastname'] = $this->language->get('error_lastname');
		}

		if (!oc_validate_email($post_info['email'])) {
			$json['error']['email'] = $this->language->get('error_email');
		}
		
        
		$user_info = $this->model_user_user->getUserByEmail($post_info['email']);

		if ($user_info && (!$post_info['user_id'] || ($post_info['user_id'] != $user_info['user_id']))) {
			$json['error']['warning'] = $this->language->get('error_email_exists');
		}

		if ($post_info['password'] || (!isset($post_info['user_id']))) {
			$password = html_entity_decode($post_info['password'], ENT_QUOTES, 'UTF-8');

			if (!oc_validate_length($password, (int)$this->config->get('config_user_password_length'), 40)) {
				$json['error']['password'] = $this->language->get('error_password');
			}

			$required = [];

			if ($this->config->get('config_user_password_uppercase') && !preg_match('/[A-Z]/', $password)) {
				$required[] = $this->language->get('error_password_uppercase');
			}

			if ($this->config->get('config_user_password_lowercase') && !preg_match('/[a-z]/', $password)) {
				$required[] = $this->language->get('error_password_lowercase');
			}

			if ($this->config->get('config_user_password_number') && !preg_match('/[0-9]/', $password)) {
				$required[] = $this->language->get('error_password_number');
			}

			if ($this->config->get('config_user_password_symbol') && !preg_match('/[^a-zA-Z0-9]/', $password)) {
				$required[] = $this->language->get('error_password_symbol');
			}

			if ($required) {
				$json['error']['password'] = sprintf($this->language->get('error_password'), implode(', ', $required), (int)$this->config->get('config_user_password_length'));
			}

			if ($post_info['password'] != $post_info['confirm']) {
				$json['error']['confirm'] = $this->language->get('error_confirm');
			}
		}
	//	print_r($json);
		    $annualctc=$this->request->post['annualctc']?$this->request->post['annualctc']:0;
		    $basic = $this->request->post['basic']?$this->request->post['basic']:0;
		    $hra = $this->request->post['hra']?$this->request->post['hra']:0;
		    $variablepay = $this->request->post['variablepay']?$this->request->post['variablepay']:0; 
		    $pf = $this->request->post['pf']?$this->request->post['pf']:0;
		    
            $post_info['annualctc'] = $annualctc;
            $post_info['monthly'] = $annualctc / 12;
            $post_info['calbasic'] = ($post_info['monthly'] * $basic) / 100;
            $post_info['calhra'] = ($post_info['calbasic'] * $hra) / 100;
            $post_info['calvariablepay'] =($post_info['monthly'] * $variablepay) / 100;
            $post_info['calpf'] = ($post_info['calbasic'] * $pf) / 100;
            $post_info['conveyance'] = $this->request->post['conveyance']?$this->request->post['conveyance']:0;
            $post_info['fixed'] = $post_info['monthly'] - ($post_info['calbasic'] + $post_info['calhra'] + $post_info['calpf'] + $post_info['calvariablepay'] + (float)$post_info['conveyance']);
            /*$post_info['conveyancefixed'] = $post_info['fixed'] - $post_info['conveyance'];*/
            $post_info['total'] = $post_info['calbasic'] + $post_info['calhra'] + $post_info['calvariablepay'] + $post_info['calpf'] + $post_info['fixed'];
            
            
            $post_info['ymonthly'] = $post_info['monthly'] * 12;
            $post_info['ybasic'] = $post_info['calbasic'] * 12;
            $post_info['yhra'] = $post_info['calhra'] * 12;
            $post_info['yvariablepay'] = $post_info['calvariablepay'] * 12;
            $post_info['ypf'] = $post_info['calpf'] * 12;
            $post_info['yconveyance'] = $post_info['conveyance'] * 12;
            $post_info['yfixed'] = $post_info['fixed'] * 12;
            $post_info['ytotal'] = $post_info['total'] * 12;


          //  print_r($post_info);
            
		if (!$json) {
		    
        if (empty($post_info['user_id'])) {
            $json['user_id'] = $this->model_user_user->addUser($post_info);
        } else {
            $this->model_user_user->editUser((int)$post_info['user_id'], $post_info);
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
		$this->load->language('user/user');

		$json = [];

		if (isset($this->request->post['selected'])) {
			$selected = (array)$this->request->post['selected'];
		} else {
			$selected = [];
		}

		if (!$this->user->hasPermission('modify', 'user/user')) {
			$json['error'] = $this->language->get('error_permission');
		}

		foreach ($selected as $user_id) {
			if ($this->user->getId() == $user_id) {
				$json['error']['warning'] = $this->language->get('error_account');
			}
		}

		if (!$json) {
			$this->load->model('user/user');

			foreach ($selected as $user_id) {
				$this->model_user_user->deleteUser($user_id);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Authorize
	 *
	 * @return void
	 */
	public function authorize(): void {
		$this->load->language('user/user');

		$this->response->setOutput($this->getAuthorize());
	}

	/**
	 * Get Authorize
	 *
	 * @return string
	 */
	public function getAuthorize(): string {
		if (isset($this->request->get['user_id'])) {
			$user_id = (int)$this->request->get['user_id'];
		} else {
			$user_id = 0;
		}

		if (isset($this->request->get['page']) && $this->request->get['route'] == 'user/user.login') {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$limit = 10;

		$data['authorizes'] = [];

		$this->load->model('user/user');

		$results = $this->model_user_user->getAuthorizes($user_id, ($page - 1) * $limit, $limit);

		foreach ($results as $result) {
			$data['authorizes'][] = [
				'status'      => $result['status'],
				'date_added'  => date($this->language->get('datetime_format'), strtotime($result['date_added'])),
				'date_expire' => $result['date_expire'] ? date($this->language->get('date_format_short'), strtotime($result['date_expire'])) : '',
				'delete'      => $this->url->link('user/user.deleteAuthorize', 'user_token=' . $this->session->data['user_token'] . '&user_authorize_id=' . $result['user_authorize_id'])
			] + $result;
		}

		$authorize_total = $this->model_user_user->getTotalAuthorizes($user_id);

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $authorize_total,
			'page'  => $page,
			'limit' => $limit,
			'url'   => $this->url->link('user/user.authorize', 'user_token=' . $this->session->data['user_token'] . '&user_id=' . $user_id . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($authorize_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($authorize_total - $limit)) ? $authorize_total : ((($page - 1) * $limit) + $limit), $authorize_total, ceil($authorize_total / $limit));

		return $this->load->view('user/user_authorize', $data);
	}

	/**
	 * Delete Authorize
	 *
	 * @return void
	 */
	public function deleteAuthorize(): void {
		$this->load->language('user/user');

		$json = [];

		if (isset($this->request->get['user_authorize_id'])) {
			$user_authorize_id = (int)$this->request->get['user_authorize_id'];
		} else {
			$user_authorize_id = 0;
		}

		if (isset($this->request->cookie['authorize'])) {
			$token = $this->request->cookie['authorize'];
		} else {
			$token = '';
		}

		if (!$this->user->hasPermission('modify', 'user/user')) {
			$json['error'] = $this->language->get('error_permission');
		}

		$this->load->model('user/user');

		$authorize_info = $this->model_user_user->getAuthorize($user_authorize_id);

		if (!$authorize_info) {
			$json['error'] = $this->language->get('error_authorize');
		}

		if (!$json) {
			$this->model_user_user->deleteAuthorizes($authorize_info['user_id'], $user_authorize_id);

			// If the token is still present, then we enforce the user to log out automatically.
			if ($authorize_info['token'] == $token) {
				$this->session->data['success'] = $this->language->get('text_success');

				$json['redirect'] = $this->url->link('common/login', '', true);
			} else {
				$json['success'] = $this->language->get('text_success');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Login
	 *
	 * @return void
	 */
	public function login(): void {
		$this->load->language('user/user');

		$this->response->setOutput($this->getLogin());
	}

	/**
	 * Get Login
	 *
	 * @return string
	 */
	public function getLogin(): string {
		if (isset($this->request->get['user_id'])) {
			$user_id = (int)$this->request->get['user_id'];
		} else {
			$user_id = 0;
		}

		if (isset($this->request->get['page']) && $this->request->get['route'] == 'user/user.login') {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$limit = 10;

		$data['logins'] = [];

		$this->load->model('user/user');

		$results = $this->model_user_user->getLogins($user_id, ($page - 1) * $limit, $limit);

		foreach ($results as $result) {
			$data['logins'][] = ['date_added' => date($this->language->get('datetime_format'), strtotime($result['date_added']))] + $result;
		}

		$login_total = $this->model_user_user->getTotalLogins($user_id);

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $login_total,
			'page'  => $page,
			'limit' => $limit,
			'url'   => $this->url->link('user/user.login', 'user_token=' . $this->session->data['user_token'] . '&user_id=' . $user_id . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($login_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($login_total - $limit)) ? $login_total : ((($page - 1) * $limit) + $limit), $login_total, ceil($login_total / $limit));

		return $this->load->view('user/user_login', $data);
	}

	/**
	 * Autocomplete
	 *
	 * @return void
	 */
	public function autocomplete(): void {
		$json = [];

		if (isset($this->request->get['filter_username']) || isset($this->request->get['filter_name']) || isset($this->request->get['filter_email'])) {
			if (isset($this->request->get['filter_username'])) {
				$filter_username = $this->request->get['filter_username'];
			} else {
				$filter_username = '';
			}

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_email'])) {
				$filter_email = $this->request->get['filter_email'];
			} else {
				$filter_email = '';
			}

			$filter_data = [
				'filter_username' => $filter_username,
				'filter_name'     => $filter_name,
				'filter_email'    => $filter_email,
				'start'           => 0,
				'limit'           => $this->config->get('config_autocomplete_limit')
			];

			$this->load->model('user/user');

			$results = $this->model_user_user->getUsers($filter_data);

			foreach ($results as $result) {
				$json[] = ['name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))] + $result;
			}
		}

		$sort_order = [];

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['username'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}