<?php
namespace Opencart\Catalog\Controller\Restaurant;

class Restaurant extends \Opencart\System\Engine\Controller {
    
    public function index(): void {
        $this->response->addHeader('Content-Type: application/json');
        
        $this->load->model('restaurant/restaurant');
        
        $pincode = $this->request->get['pincode'] ?? $this->request->post['pincode'] ?? '';
        $is_veg = $this->request->get['is_veg'] ?? $this->request->post['is_veg'] ?? '';
        
        $restaurants = $this->model_restaurant_restaurant->getRestaurants($pincode, $is_veg);
        
        $this->response->setOutput(json_encode([
            "status" => "success",
            "data" => $restaurants
        ]));
    }
    
    public function topRated(): void {
        $this->response->addHeader('Content-Type: application/json');
        
        $this->load->model('restaurant/restaurant');
        
        $pincode = $this->request->get['pincode'] ?? $this->request->post['pincode'] ?? '';
        $is_veg = $this->request->get['is_veg'] ?? $this->request->post['is_veg'] ?? '';
        
        $restaurants = $this->model_restaurant_restaurant->getTopRatedRestaurants($pincode, $is_veg);
        
        $this->response->setOutput(json_encode([
            "status" => "success",
            "data" => $restaurants
        ]));
    }
}
