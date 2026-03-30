<?php
namespace Opencart\Admin\Controller\Extension\PurpletreePos\Pos;
class Postables extends \Opencart\System\Engine\Controller {
private $error = array();
public function index(): void {
    
$this->load->language('extension/purpletree_pos/postables');


$this->document->setTitle($this->language->get('heading_title'));


$this->load->model('extension/purpletree_pos/postables');


$this->getList();
}


public function add(): void {
$this->load->language('extension/purpletree_pos/postables');


$this->document->setTitle($this->language->get('heading_title'));


$this->load->model('extension/purpletree_pos/postables');


if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
$this->model_extension_purpletree_pos_postables->addPosTable($this->request->post);


$this->session->data['success'] = $this->language->get('text_success');


$this->response->redirect($this->url->link('extension/purpletree_pos/pos/postables', 'user_token=' . $this->session->data['user_token'] . $this->buildUrlParams(), true));
}


$this->getForm();
}


public function edit(): void {
$this->load->language('extension/purpletree_pos/postables');


$this->document->setTitle($this->language->get('heading_title'));


$this->load->model('extension/purpletree_pos/postables');


if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
$this->model_extension_purpletree_pos_postables->editPosTable($this->request->get['pos_table_id'], $this->request->post);


$this->session->data['success'] = $this->language->get('text_success');


$this->response->redirect($this->url->link('extension/purpletree_pos/pos/postables', 'user_token=' . $this->session->data['user_token'] . $this->buildUrlParams(), true));
}


$this->getForm();
}


public function delete(): void {
$this->load->language('extension/purpletree_pos/posstables');
$this->document->setTitle($this->language->get('heading_title'));


$this->load->model('extension/purpletree_pos/postables');


if (isset($this->request->post['selected']) && $this->validateDelete()) {
foreach ($this->request->post['selected'] as $pos_table_id) {
$this->model_extension_purpletree_pos_postables->deletePosTable((int)$pos_table_id);
}


$this->session->data['success'] = $this->language->get('text_success');
}


$this->getList();
}


protected function getList(): void {
$url = $this->buildUrlParams();
$sort = $this->request->get['sort'] ?? 'table_name';
    $order = $this->request->get['order'] ?? 'ASC';
    $page = (int)($this->request->get['page'] ?? 1);

$data['breadcrumbs'] = [];
$data['breadcrumbs'][] = [
'text' => $this->language->get('text_home'),
'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
];
$data['breadcrumbs'][] = [
'text' => $this->language->get('heading_title'),
'href' => $this->url->link('extension/purpletree_pos/pos/postables', 'user_token=' . $this->session->data['user_token'] . $url, true)
];


$data['add'] = $this->url->link('extension/purpletree_pos/pos/postables.add', 'user_token=' . $this->session->data['user_token'] . $url, true);
$data['delete'] = $this->url->link('extension/purpletree_pos/pos/postables.delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

$filter_data = [
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * 10,
            'limit' => 10
    ];


$data['pos_tables'] = [];


$pos_table_total = $this->model_extension_purpletree_pos_postables->getTotalPosTables();
$results = $this->model_extension_purpletree_pos_postables->getPosTables($filter_data);


foreach ($results as $result) {
$data['pos_tables'][] = [
'pos_table_id' => $result['pos_table_id'],
'table_name' => $result['table_name'],
'members' => $result['members'],
'status' => (int)$result['status'],
'date_added' => $result['date_added'],
'edit' => $this->url->link('extension/purpletree_pos/pos/postables.edit', 'user_token=' . $this->session->data['user_token'] . '&pos_table_id=' . $result['pos_table_id'] . $url)
];
}


$data['sort_table_name'] = $this->url->link('extension/purpletree_pos/postables', 'user_token=' . $this->session->data['user_token'] . '&sort=table_name' . $this->toggleOrder($order));
$data['sort_members'] = $this->url->link('extension/purpletree_pos/postables', 'user_token=' . $this->session->data['user_token'] . '&sort=members' . $this->toggleOrder($order));


$data['user_token'] = $this->session->data['user_token'];


// Pagination
$data['pagination'] = $this->load->controller('common/pagination', [
'total' => $pos_table_total,
'page' => $page,
'limit' => 10,
'url' => $this->url->link('extension/purpletree_pos/postables', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
]);


$data['results'] = sprintf($this->language->get('text_pagination'), ($pos_table_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($pos_table_total - 10)) ? $pos_table_total : ((($page - 1) * 10) + 10), $pos_table_total, ceil($pos_table_total / 10));


$data['sort'] = $sort;
$data['order'] = $order;


$data['header'] = $this->load->controller('common/header');
$data['column_left'] = $this->load->controller('common/column_left');
$data['footer'] = $this->load->controller('common/footer');


$this->response->setOutput($this->load->view('extension/purpletree_pos/pos_table_list', $data));
}
protected function getForm(): void {
$this->load->language('extension/purpletree_pos/postables');


$data['text_form'] = !isset($this->request->get['pos_table_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');


$data['user_token'] = $this->session->data['user_token'];


$url = $this->buildUrlParams();


$data['breadcrumbs'] = [];
$data['breadcrumbs'][] = [
'text' => $this->language->get('text_home'),
'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
];
$data['breadcrumbs'][] = [
'text' => $this->language->get('heading_title'),
'href' => $this->url->link('extension/purpletree_pos/postables', 'user_token=' . $this->session->data['user_token'] . $url)
];


if (!isset($this->request->get['pos_table_id'])) {
$data['save'] = $this->url->link('extension/purpletree_pos/pos/postables.add', 'user_token=' . $this->session->data['user_token'] . $url);
} else {
$data['save'] = $this->url->link('extension/purpletree_pos/pos/postables.edit', 'user_token=' . $this->session->data['user_token'] . '&pos_table_id=' . (int)$this->request->get['pos_table_id'] . $url);
}


$data['back'] = $this->url->link('extension/purpletree_pos/pos/postables', 'user_token=' . $this->session->data['user_token'] . $url);


$data['error_warning'] = $this->error['warning'] ?? '';
$data['error_table_name'] = $this->error['table_name'] ?? '';
$data['error_members'] = $this->error['members'] ?? '';


$pos_table_info = [];
if (isset($this->request->get['pos_table_id']) && $this->request->server['REQUEST_METHOD'] != 'POST') {
$this->load->model('extension/purpletree_pos/postables');
$pos_table_info = $this->model_extension_purpletree_pos_postables->getPosTable((int)$this->request->get['pos_table_id']);
}


$data['pos_table_id'] = $pos_table_info['pos_table_id'] ?? 0;
$data['table_name'] = $this->request->post['table_name'] ?? ($pos_table_info['table_name'] ?? '');
$data['members'] = $this->request->post['members'] ?? ($pos_table_info['members'] ?? 1);
$data['status'] = $this->request->post['status'] ?? ($pos_table_info['status'] ?? 1);


$data['header'] = $this->load->controller('common/header');
$data['column_left'] = $this->load->controller('common/column_left');
$data['footer'] = $this->load->controller('common/footer');


$this->response->setOutput($this->load->view('extension/purpletree_pos/pos_table_form', $data));
}
protected function validateForm(): bool {
$this->error = [];


if (!$this->user->hasPermission('modify', 'extension/purpletree_pos/pos/postables')) {
$this->error['warning'] = $this->language->get('error_permission');
}


$table_name = $this->request->post['table_name'] ?? '';
$members = (int)($this->request->post['members'] ?? 0);


if (oc_strlen($table_name) < 1 || oc_strlen($table_name) > 64) {
$this->error['table_name'] = $this->language->get('error_table_name');
}


if ($members < 1 || $members > 4) {
$this->error['members'] = $this->language->get('error_members');
}


return !$this->error;
}


protected function validateDelete(): bool {
if (!$this->user->hasPermission('modify', 'extension/purpletree_pos/pos/postables')) {
$this->error['warning'] = $this->language->get('error_permission');
}


return !isset($this->error['warning']);
}


private function buildUrlParams(): string {
$url = '';
if (isset($this->request->get['sort'])) $url .= '&sort=' . $this->request->get['sort'];
if (isset($this->request->get['order'])) $url .= '&order=' . $this->request->get['order'];
if (isset($this->request->get['page'])) $url .= '&page=' . (int)$this->request->get['page'];
return $url;
}


private function toggleOrder(string $order): string {
return '&order=' . ($order == 'ASC' ? 'DESC' : 'ASC');
}
}