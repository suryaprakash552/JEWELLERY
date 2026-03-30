<?php
namespace Opencart\Admin\Controller\User;
class Calendar extends \Opencart\System\Engine\Controller {
    /* Show calendar page (the view you already created).
     */
    public function index(): void {
        $this->load->language('user/calendar');
        $this->document->setTitle($this->language->get('heading_title'));
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('user/calendar', 'user_token=' . $this->session->data['user_token'])
        ];
        $data['heading_title'] = $this->language->get('heading_title');
        $data['user_token']    = $this->session->data['user_token'];
        // $this->load->model('user/calendar'); 
        // // Fetch events for that date only
        // $events = $this->model_user_calendar->getEvents();
        //$data['events']      = $events;
        // Load standard layout parts
        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');
        //print_r($data);
        // Render view (twig template)
        $this->response->setOutput($this->load->view('user/calendar', $data));
    }
    /* Return events for FullCalendar (JSON).
     * Only one date is fetched (default: today).
     */
    public function events(): void {
         $this->load->model('user/calendar');

        // Get events from model
        $results = $this->model_user_calendar->getEvents($this->request->get['year']);

        // Map to FullCalendar JSON format
        $events = [];
        foreach ($results as $result) {
            $events[] = $result; 
        }
     
        // Return JSON
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($events));
   }
}