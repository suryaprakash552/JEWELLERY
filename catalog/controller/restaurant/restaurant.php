<?php
namespace Opencart\Catalog\Controller\Restaurant;

class Restaurant extends \Opencart\System\Engine\Controller {
    
    public function index(): void {
        $this->response->addHeader('Content-Type: application/json');
        
        $this->load->model('restaurant/restaurant');
        
        $restaurants = $this->model_restaurant_restaurant->getRestaurants();
        
        $this->response->setOutput(json_encode([
            "status" => "success",
            "data" => $restaurants
        ]));
    }
}
