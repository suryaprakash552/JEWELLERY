<?php
namespace Opencart\Admin\Controller\Timecard;
class Myreporty extends \Opencart\System\Engine\Controller {
    private $error = array();

    public function index() {
        $this->load->language('timecard/myreporty');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('timecard/myreporty');

        $this->getList();
    }

    public function add() {
        // kept for compatibility - not used in current flow
        $this->load->language('timecard/myreporty');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('timecard/myreporty');

        $this->getForm();
    }

    public function edit() {
        $this->load->language('timecard/myreporty');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('timecard/myreporty');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            if (isset($this->request->post['timecard'])) {
                $timeCard = $this->request->post['timecard'];
                $files = isset($this->request->files['timecard']) ? $this->request->files['timecard'] : array();

                foreach (array_keys($timeCard) as $key) {
                    // prepare single timecard data
                    $single = $timeCard[$key];

                    // prepare files structure for the upload controller (if present)
                    $this->request->files['file'] = array(
                        'name'     => isset($files['name'][$key]) ? $files['name'][$key] : '',
                        'tmp_name' => isset($files['tmp_name'][$key]) ? $files['tmp_name'][$key] : '',
                        'error'    => isset($files['error'][$key]) ? $files['error'][$key] : '',
                        'type'     => isset($files['type'][$key]) ? $files['type'][$key] : ''
                    );

                    // call upload controller only if file present
                    $result = array('code' => '');
                    if (!empty($this->request->files['file']['name'])) {
                        $result = $this->load->controller('tool/reusable.upload');
                        // controller expected to return array with 'code' on success
                        if (!is_array($result)) {
                            $result = array('code' => isset($result['code']) ? $result['code'] : '');
                        }
                    }

                    // save timecard (submit)
                    $this->model_timecard_myreporty->editProduct($this->user->getId(), $single, $result);
                }

                $this->session->data['success'] = $this->language->get('text_success');

                $url = '';

                if (isset($this->request->get['filter_fdate'])) {
                    $url .= '&filter_fdate=' . urlencode(html_entity_decode($this->request->get['filter_fdate'], ENT_QUOTES, 'UTF-8'));
                } else {
                    $filter_fdate = date('Y-m-01');
                    $url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
                }

                if (isset($this->request->get['filter_tdate'])) {
                    $url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
                } else {
                    $filter_tdate = date('Y-m-t');
                    $url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
                }

                if (isset($this->request->get['filter_status'])) {
                    $url .= '&filter_status=' . urlencode(html_entity_decode($this->request->get['filter_status'], ENT_QUOTES, 'UTF-8'));
                }

                $this->response->redirect($this->url->link('timecard/myreporty', 'user_token=' . $this->session->data['user_token'] . $url, true));
                return;
            }
        }

        $this->getForm();
    }

    public function cancel() {
        $this->load->language('timecard/myreporty');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('timecard/myreporty');

        if (isset($this->request->get['timecard_id']) && $this->validateCancel()) {
            $this->model_timecard_myreporty->cancelProduct($this->user->getId(), $this->request->get['timecard_id']);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = $this->buildFilterUrl();

            $this->response->redirect($this->url->link('timecard/myreporty', 'user_token=' . $this->session->data['user_token'] . $url, true));
            return;
        }

        $this->getList();
    }

    public function approve() {
        $this->load->language('timecard/myreporty');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('timecard/myreporty');

        if (isset($this->request->get['timecard_id']) && $this->validateApprove()) {
            $this->model_timecard_myreporty->approveProduct($this->user->getId(), $this->request->get['timecard_id']);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = $this->buildFilterUrl();

            $this->response->redirect($this->url->link('timecard/myreporty', 'user_token=' . $this->session->data['user_token'] . $url, true));
            return;
        }

        $this->getList();
    }

    public function reject() {
        $this->load->language('timecard/myreporty');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('timecard/myreporty');

        if (isset($this->request->get['timecard_id']) && $this->validateReject()) {
            $this->model_timecard_myreporty->rejectProduct($this->user->getId(), $this->request->get['timecard_id']);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = $this->buildFilterUrl();

            $this->response->redirect($this->url->link('timecard/myreporty', 'user_token=' . $this->session->data['user_token'] . $url, true));
            return;
        }

        $this->getList();
    }

    /**
     * Build filter url fragment (reusable)
     */
    protected function buildFilterUrl() {
        $url = '';

        if (isset($this->request->get['filter_fdate'])) {
            $url .= '&filter_fdate=' . urlencode(html_entity_decode($this->request->get['filter_fdate'], ENT_QUOTES, 'UTF-8'));
        } else {
            $filter_fdate = date('Y-m-01');
            $url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_tdate'])) {
            $url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
        } else {
            $filter_tdate = date('Y-m-t');
            $url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_userid'])) {
            $url .= '&filter_userid=' . urlencode(html_entity_decode($this->request->get['filter_userid'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . urlencode(html_entity_decode($this->request->get['filter_status'], ENT_QUOTES, 'UTF-8'));
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

        return $url;
    }

    protected function getList() {
        if (isset($this->request->get['filter_fdate'])) {
            $filter_fdate = $this->request->get['filter_fdate'];
        } else {
            $filter_fdate = date('Y-m-01');
        }

        if (isset($this->request->get['filter_tdate'])) {
            $filter_tdate = $this->request->get['filter_tdate'];
        } else {
            $filter_tdate = date('Y-m-t');
        }

        if (isset($this->request->get['filter_userid'])) {
            $filter_userid = $this->request->get['filter_userid'];
        } else {
            $filter_userid = '';
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = '';
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'p.timecard_id';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        if (isset($this->request->get['page'])) {
            $page = (int)$this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = $this->buildFilterUrl();

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('timecard/myreporty', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['delete'] = $this->url->link('timecard/myreporty.delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['action'] = $this->url->link('timecard/myreporty.edit', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['products'] = array();
        $filter_data = array(
            'filter_fdate'   => $filter_fdate,
            'filter_tdate'   => $filter_tdate,
            'filter_status'  => $filter_status,
            'filter_userid'  => $filter_userid,
            'sort'           => $sort,
            'order'          => $order,
            'start'          => ($page - 1) * $this->config->get('config_pagination_admin'),
            'limit'          => $this->config->get('config_pagination_admin')
        );

        $product_total = $this->model_timecard_myreporty->getTotalProducts($filter_data);
        $results = $this->model_timecard_myreporty->getProducts($filter_data);
        $projects = $this->model_timecard_myreporty->getProjects();
        $tasks = $this->model_timecard_myreporty->getTasks();

        $data['projects'] = !empty($projects) ? $projects : '';
        $data['tasks'] = !empty($tasks) ? $tasks : '';

        $i = 1;
        $data['timecards'] = array();

        foreach ($results as $result) {
            $this->load->model('tool/upload');
            $docresults = array();

            if (!empty($result['doccode']) && is_string($result['doccode'])) {
                $docresults = $this->model_tool_upload->getUploadByCode($result['doccode']);
            }

            $data['timecards'][] = array(
                'srno' => $i,
                'timecard_id' => $result['timecard_id'],
                'projectname' => $result['projectname'],
                'emp' => $result['firstname'] . " " . $result['lastname'] . " " . $result['user_id'],
                'taskname' => $result['taskname'],
                'description' => $result['description'],
                'work_from_home' => $result['work_from_home'],
                'approval_document' => isset($docresults['code']) ? $this->url->link('tool/reusable.download', 'user_token=' . $this->session->data['user_token'] . '&code=' . $docresults['code'] . $url, true) : '',
                'hours' => $result['hours'],
                'date' => $result['date'],
                'approvedby' => (!empty($result['approvedby']) ? trim($result['mgr_firstname'] . ' ' . $result['mgr_lastname']) : ''),
                'status' => $result['status'],
                'docname' => isset($docresults['name']) ? $docresults['name'] : '',
                'approve' => $this->url->link('timecard/myreporty.approve', 'user_token=' . $this->session->data['user_token'] . '&timecard_id=' . $result['timecard_id'] . $url, true),
                'reject' => $this->url->link('timecard/myreporty.reject', 'user_token=' . $this->session->data['user_token'] . '&timecard_id=' . $result['timecard_id'] . $url, true)
            );
            $i++;
        }

        $data['user_token'] = $this->session->data['user_token'];
        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $data['success'] = isset($this->session->data['success']) ? $this->session->data['success'] : '';
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $data['selected'] = isset($this->request->post['selected']) ? (array)$this->request->post['selected'] : array();

        // Pagination & Results text
        $url = $this->buildFilterUrl();

        $data['sort_date'] = $this->url->link('timecard/myreporty', 'user_token=' . $this->session->data['user_token'] . '&sort=p.timecard_id' . $url, true);
        $data['sort_order'] = $this->url->link('timecard/myreporty', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

        $product_total = $this->model_timecard_myreporty->getTotalProducts($filter_data);
        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $product_total,
            'page'  => $page,
            'limit' => $this->config->get('config_pagination_admin'),
            'url'   => $this->url->link('timecard/myreporty', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
        ]);

        $data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($product_total - $this->config->get('config_pagination_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $product_total, ceil($product_total / $this->config->get('config_pagination_admin')));

        $data['filter_fdate'] = $filter_fdate;
        $data['filter_tdate'] = $filter_tdate;
        $data['filter_userid'] = $filter_userid;
        $data['filter_status'] = $filter_status;

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('timecard/myreporty_dis_form', $data));
    }

    protected function getForm() {
        $data['text_form'] = $this->language->get('text_edit');

        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

        $url = '';
        if (isset($this->request->get['filter_date'])) {
            $url .= '&filter_date=' . urlencode(html_entity_decode($this->request->get['filter_date'], ENT_QUOTES, 'UTF-8'));
        } else {
            $filter_fdate = date('Y-m-01');
            $url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_project'])) {
            $url .= '&filter_project=' .  urlencode(html_entity_decode($this->request->get['filter_project'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_task'])) {
            $url .= '&filter_task=' .  urlencode(html_entity_decode($this->request->get['filter_task'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_work_from_home'])) {
            $url .= '&filter_work_from_home=' .  urlencode(html_entity_decode($this->request->get['filter_work_from_home'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_approval_document'])) {
            $url .= '&filter_approval_document=' .  urlencode(html_entity_decode($this->request->get['filter_approval_document'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_hours'])) {
            $url .= '&filter_hours=' .  urlencode(html_entity_decode($this->request->get['filter_hours'], ENT_QUOTES, 'UTF-8'));
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('timecard/myreporty', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        if (isset($this->request->get['packageid']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $data['action'] = $this->url->link('timecard/myreporty.edit', 'user_token=' . $this->session->data['user_token'] . '&packageid=' . $this->request->get['packageid'] . $url, true);
        }

        $data['cancel'] = $this->url->link('timecard/myreporty', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $product_info = array();
        if (isset($this->request->get['packageid']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $product_info = $this->model_timecard_myreporty->getProduct($this->request->get['packageid']);
        }

        $data['user_token'] = $this->session->data['user_token'];

        // keep compatibility for existing fields used in template
        $data['packagename'] = isset($product_info['packagename']) ? $product_info['packagename'] : '';
        $data['price'] = isset($product_info['price']) ? $product_info['price'] : '';
        $data['modifiedby'] = isset($product_info['modifiedby']) ? $product_info['modifiedby'] : '';
        $data['status'] = isset($product_info['status']) ? $product_info['status'] : '';

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('timecard/myreporty_dis_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'timecard/myreporty')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    protected function validateCancel() {
        if (!$this->user->hasPermission('modify', 'timecard/myreporty')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function validateApprove() {
        if (!$this->user->hasPermission('modify', 'timecard/myreporty')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function validateReject() {
        if (!$this->user->hasPermission('modify', 'timecard/myreporty')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function validateCopy() {
        if (!$this->user->hasPermission('modify', 'timecard/myreporty')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_packagename'])) {
            $this->load->model('timecard/myreporty');

            $filter_packagename = $this->request->get['filter_packagename'] ?? '';

            $limit = isset($this->request->get['limit']) ? (int)$this->request->get['limit'] : 5;

            $filter_data = array(
                'filter_packagename' => $filter_packagename,
                'start' => 0,
                'limit' => $limit
            );

            $results = $this->model_timecard_myreporty->getProducts($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'id' => isset($result['telephone']) ? $result['telephone'] : '',
                    'name' => strip_tags(html_entity_decode(isset($result['telephone']) ? $result['telephone'] : '', ENT_QUOTES, 'UTF-8'))
                );
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
