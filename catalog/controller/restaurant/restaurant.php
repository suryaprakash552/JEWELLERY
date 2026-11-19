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

    public function get(): void {
        $this->response->addHeader('Content-Type: application/json');
        
        $this->load->model('restaurant/restaurant');
        
        $id = $this->request->get['id'] ?? $this->request->post['id'] ?? 0;
        
        $restaurant = $this->model_restaurant_restaurant->getRestaurant((int)$id);
        
        if ($restaurant) {
            $this->response->setOutput(json_encode([
                "status" => "success",
                "data" => $restaurant
            ]));
        } else {
            $this->response->setOutput(json_encode([
                "status" => "error",
                "message" => "Restaurant not found"
            ]));
        }
    }

    public function add(): void {
        $this->response->addHeader('Content-Type: application/json');
        
        $this->load->model('restaurant/restaurant');
        
        $data = $this->getInputData();
        $this->processImages($data);
        
        if (empty($data['name'])) {
            $this->response->setOutput(json_encode([
                "status" => "error",
                "message" => "Name is required"
            ]));
            return;
        }
        
        try {
            $restaurant_id = $this->model_restaurant_restaurant->addRestaurant($data);
            
            $this->response->setOutput(json_encode([
                "status" => "success",
                "message" => "Restaurant added successfully",
                "id" => $restaurant_id
            ]));
        } catch (\Exception $e) {
            $this->response->setOutput(json_encode([
                "status" => "error",
                "message" => "Failed to add restaurant: " . $e->getMessage()
            ]));
        }
    }

    public function edit(): void {
        $this->response->addHeader('Content-Type: application/json');
        
        $this->load->model('restaurant/restaurant');
        
        $data = $this->getInputData();
        $this->processImages($data);
        
        if (empty($data['id'])) {
            $this->response->setOutput(json_encode([
                "status" => "error",
                "message" => "Restaurant ID is required"
            ]));
            return;
        }
        
        if (empty($data['name'])) {
            $this->response->setOutput(json_encode([
                "status" => "error",
                "message" => "Name is required"
            ]));
            return;
        }
        
        try {
            $this->model_restaurant_restaurant->editRestaurant($data);
            
            $this->response->setOutput(json_encode([
                "status" => "success",
                "message" => "Restaurant updated successfully"
            ]));
        } catch (\Exception $e) {
            $this->response->setOutput(json_encode([
                "status" => "error",
                "message" => "Failed to update restaurant: " . $e->getMessage()
            ]));
        }
    }

    public function delete(): void {
        $this->response->addHeader('Content-Type: application/json');
        
        $this->load->model('restaurant/restaurant');
        
        $id = $this->request->get['id'] ?? $this->request->post['id'] ?? 0;
        
        if (empty($id)) {
            $this->response->setOutput(json_encode([
                "status" => "error",
                "message" => "Restaurant ID is required"
            ]));
            return;
        }
        
        try {
            $this->model_restaurant_restaurant->deleteRestaurant((int)$id);
            
            $this->response->setOutput(json_encode([
                "status" => "success",
                "message" => "Restaurant deleted successfully"
            ]));
        } catch (\Exception $e) {
            $this->response->setOutput(json_encode([
                "status" => "error",
                "message" => "Failed to delete restaurant: " . $e->getMessage()
            ]));
        }
    }

    private function getInputData(): array {
        // Support JSON body
        $contentType = $this->request->server['CONTENT_TYPE'] ?? '';
        
        if (strpos($contentType, 'application/json') !== false) {
            $json = json_decode(file_get_contents('php://input'), true);
            if (is_array($json)) {
                return $json;
            }
        }
        
        // Fallback to POST data
        return $this->request->post;
    }

    private function processImages(array &$data): void {
        // Logo: file upload or base64 data URI
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $saved = $this->saveUploadedImage($_FILES['logo']['tmp_name'], $_FILES['logo']['name']);
            if ($saved) {
                $data['logo'] = $saved;
            }
        } elseif (isset($data['logo']) && is_string($data['logo']) && strpos($data['logo'], 'data:image') === 0) {
            $saved = $this->saveBase64Image($data['logo']);
            if ($saved) {
                $data['logo'] = $saved;
            }
        }

        // Banner images: file upload (banners[i][image]) or base64
        if (!empty($data['banners']) && is_array($data['banners'])) {
            foreach ($data['banners'] as $i => &$banner) {
                $fileError = $_FILES['banners']['error'][$i]['image'] ?? UPLOAD_ERR_NO_FILE;
                if ($fileError === UPLOAD_ERR_OK) {
                    $saved = $this->saveUploadedImage(
                        $_FILES['banners']['tmp_name'][$i]['image'],
                        $_FILES['banners']['name'][$i]['image']
                    );
                    if ($saved) {
                        $banner['image'] = $saved;
                    }
                } elseif (isset($banner['image']) && is_string($banner['image']) && strpos($banner['image'], 'data:image') === 0) {
                    $saved = $this->saveBase64Image($banner['image']);
                    if ($saved) {
                        $banner['image'] = $saved;
                    }
                }
            }
            unset($banner);
        }
    }

    private function getImageUploadDir(): string {
        $dir = DIR_IMAGE . 'catalog/restaurant/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }

    private function saveUploadedImage(string $tmpPath, string $originalName): ?string {
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($ext, $allowed)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $tmpPath);
            finfo_close($finfo);
            $map = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
            $ext = $map[$mime] ?? '';
        }

        if (empty($ext)) {
            return null;
        }

        $filename = 'rest_' . time() . '_' . uniqid() . '.' . $ext;

        if (move_uploaded_file($tmpPath, $this->getImageUploadDir() . $filename)) {
            return 'catalog/restaurant/' . $filename;
        }

        return null;
    }

    private function saveBase64Image(string $dataUri): ?string {
        if (!preg_match('/^data:image\/(jpeg|jpg|png|gif|webp);base64,/', $dataUri, $m)) {
            return null;
        }

        $ext = $m[1] === 'jpeg' ? 'jpg' : $m[1];
        $binary = base64_decode(substr($dataUri, strpos($dataUri, ',') + 1), true);

        if ($binary === false) {
            return null;
        }

        $filename = 'rest_' . time() . '_' . uniqid() . '.' . $ext;

        if (file_put_contents($this->getImageUploadDir() . $filename, $binary) !== false) {
            return 'catalog/restaurant/' . $filename;
        }

        return null;
    }
}
