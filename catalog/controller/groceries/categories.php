<?php
namespace Opencart\Catalog\Controller\Groceries;

class Categories extends \Opencart\System\Engine\Controller {
    
    public function login(): void {

        $this->response->addHeader('Content-Type: application/json');

        $email    = $this->request->post['email'] ?? '';
        $password = $this->request->post['password'] ?? '';

        if (!$email || !$password) {
            $this->response->setOutput(json_encode([
                "status" => "error",
                "message" => "Email and Password required"
            ]));
            return;
        }

        $this->load->model('groceries/categories');

        $customer = $this->model_groceries_categories->loginCustomer($email, $password);

        if (!$customer) {
            $this->response->setOutput(json_encode([
                "status" => "error",
                "message" => "Invalid credentials"
            ]));
            return;
        }

        $this->response->setOutput(json_encode([
            "status" => "success",
            "token"  => $customer['token'],
            "customer_id" => $customer['customer_id']
        ]));
    }
    
    public function send_otp(){

        $json=[];
        if(!$this->request->post){
        
        $input=json_decode(file_get_contents("php://input"),true);
        
        $this->request->post=$input;
        
        }
        $validate=$this->validate_send_otp($this->request->post);
        
        
        if($validate['success']=="1"){
        
        $json['telephone']=$this->request->post['telephone'];

        $json=$this->load->controller('ws/transactions/common.send_otp',$json);
        
        }else{
        
        $json=$validate;
        
        }

        $this->response->addHeader('Content-Type: application/json');
        
        $this->response->setOutput(json_encode($json));
        
        }

        
       public function verify_otp(): void {

        $this->response->addHeader('Content-Type: application/json');

        $json=[];

        if(!$this->request->post){
        
        $input=json_decode(file_get_contents("php://input"),true);
        
        $this->request->post=$input;
        
        }

        $validate =$this->validate_verify_otp($this->request->post);
        
        if($validate['success']=="0"){
        
        $this->response->setOutput(json_encode($validate));
        
        return;
        
        }

        $this->load->model('ws/transactions/common');
        
        $validate_record =$this->model_ws_transactions_common->VERIFY_CUSTOMER_OTP($this->request->post);
        
        if(!$validate_record['exstatus']){
        
        $this->response->setOutput(
        json_encode([
        
            "success"=>"0",
            "message"=>"Invalid OTP"
            
            ]));
        
        return;
        
        }

        $record_input =json_decode($validate_record['input'],true);
        if(
        
        $record_input['telephone']!=$this->request->post['telephone']){
        
        $this->response->setOutput(
            json_encode([
            
            "success"=>"0",
            "message"=>"Wrong Input"
            
            ]));
        
        return;
        
        }

        $new_ref =$this->model_ws_transactions_common->RELEASE_OTP_ATTEMPTS($this->request->post);

        $this->load->model('groceries/categories');
    
        $customer =$this->model_groceries_categories->loginCustomerOtp($this->request->post['telephone']);

        if(!$customer){
        
        $this->response->setOutput(
            json_encode([
        
            "success"=>"0",
            "message"=>"Customer not found"
            
            ]));
        
        return;
        
        }

        $json=[
        
            "success"=>"1",
            "otp_ref"=>$new_ref,
            "customer_id"=>$customer['customer_id'],
            "token"=>$customer['token'],
            "message"=>"OTP Verified Login Success"
        ];
        
        $this->response->setOutput(json_encode($json));
        }

    public function validate_send_otp($raw){
    
        if(!isset($raw['telephone'])||empty($raw['telephone'])||!is_numeric($raw['telephone'])){
        
            return [
            "success"=>"0",
            "message"=>"Invalid Telephone"
            ];
        
          }
        
            return [
            "success"=>"1",
            "message"=>"OK"
            ];
        
         }
        
        
        public function validate_verify_otp($raw){
        
        if(!isset($raw['telephone'])||!is_numeric($raw['telephone'])){
        
            return [
            "success"=>"0",
            "message"=>"Invalid Telephone"
            ];
        
        }
        
        
        if(!isset($raw['otp'])||!is_numeric($raw['otp'])){
        
            return [
            "success"=>"0",
            "message"=>"Invalid OTP"
            ];
        
        }
        
        
        if(empty($raw['otp_ref'])){
        
            return [
            "success"=>"0",
            "message"=>"OTP Ref Missing"
            ];
        
        }
        
            return [
            "success"=>"1"
            ];
        
        }


    private function validateToken() {

    $token = $this->request->get['token'] ?? '';

    if (!$token) {
        return false;
    }

    $this->load->model('groceries/categories');

    $customer_id = $this->model_groceries_categories->validateToken($token);

    return $customer_id;
    }
    
    public function logout(): void {

    $this->response->addHeader('Content-Type: application/json');

    $token = $this->request->get['token'] ?? '';

    if (!$token) {
        $this->response->setOutput(json_encode([
            "status" => "error",
            "message" => "Token missing"
        ]));
        return;
    }

    $this->load->model('groceries/categories');

    $logout = $this->model_groceries_categories->logoutCustomer($token);

    if ($logout) {
        $this->response->setOutput(json_encode([
            "status" => "success",
            "message" => "Logged out successfully"
        ]));
    } else {
        $this->response->setOutput(json_encode([
            "status" => "error",
            "message" => "Invalid token"
        ]));
    }
}

    public function getInitialData(): void {

    $this->response->addHeader('Content-Type: application/json');
    
    if (!$this->validateToken()) {
    
    $this->response->setOutput(json_encode([
    "status"=>"error",
    "message"=>"Invalid Token"
    ]));
    
    return;
    }
    
    $this->load->model('groceries/categories');
    
    
    // 1 RANDOM PRODUCTS
    
    $random_products=$this->model_groceries_categories->getRandomProducts();
    
    
    $main_categories =$this->model_groceries_categories->getMainCategories();

    

    $offer_categories=$this->model_groceries_categories->getOfferCategories();

    $offers=[];
    
    foreach($offer_categories as $offer){
    
    $products=$this->model_groceries_categories->getOfferProducts($offer['category_id'],0,6);
    
    $offers[]=[
    
    "category_id"=>$offer['category_id'],
    "name"=>$offer['name'],
    "products"=>$products
    
    ];
    
    }
    
    $this->response->setOutput(json_encode([
                "status"=>"success",
                
                "random_products"=>$random_products,
                
                "categories"=>$main_categories,
                
                "offers"=>$offers
                
                ]));
    
    }
    
    public function getOfferMoreProducts(){

        if(!$this->validateToken()){
        
        echo json_encode([
         "status"=>"error"
        ]);
        
        return;
        
        }
        
        $category_id=(int)$this->request->get['category_id'];

        $this->load->model('groceries/categories');
        
        $products=$this->model_groceries_categories->getOfferProducts($category_id,$start,$limit);
        
        echo json_encode([
        
        "status"=>"success",
        "products"=>$products
        
        ]);
        
        }



   public function getCategoryData(): void {

    $this->response->addHeader('Content-Type: application/json');
    
    if(!$this->validateToken()){
        $this->response->setOutput(json_encode([
    
            "status"=>"error",
            "message"=>"Invalid Token"
            
            ]));
            
            return;
            
            }
    
    $category_id = (int)($this->request->post['category_id'] ?? $this->request->get['category_id'] ?? 0);
    
    $this->load->model('groceries/categories');

    $category_products=$this->model_groceries_categories->getProductsOnly($category_id, 0, 5);

    $subcategories=$this->model_groceries_categories->getSubCategories($category_id);
    foreach($subcategories as &$subcategory){
    
    $subcategory['products']=$this->model_groceries_categories->getProductsOnly($subcategory['category_id'], 0, 5);
    
    }

    $this->response->setOutput(
    
        json_encode([
        
        "status"=>"success",
        "products"=>$category_products,
        "subcategories"=>$subcategories
        ])
    
    );

}

public function getProductDetails(){

    $this->response->addHeader('Content-Type: application/json');

    if(!$this->validateToken()){
        $this->response->setOutput(json_encode([
            "status"=>"error",
            "message"=>"Invalid Token"
        ]));
        return;
    }

    $product_id = (int)$this->request->get['product_id'];

    $this->load->model('groceries/categories');

    // main product
    $product = $this->model_groceries_categories->getProductDetails($product_id);

    // related products
    $related_products = $this->model_groceries_categories->getRelatedProducts($product_id);

    $this->response->setOutput(json_encode([
        "status"=>"success",
        "product"=>$product,
        "related_products"=>$related_products
    ]));
}

    public function addOrder(): void {

    $this->response->addHeader('Content-Type: application/json');
    
    $customer_id = $this->validateToken();
    
    if(!$customer_id){
    
    $this->response->setOutput(json_encode([
            
            "status"=>"error",
            "message"=>"Invalid Token"
            
        ]));
        
        return;
    
    }
    
    try{
        
        $post = $this->request->post;
        
        $raw = file_get_contents("php://input");
        
        if($raw){
        
        $json = json_decode($raw,true);
        
        if(json_last_error()===JSON_ERROR_NONE){
        
        $post=array_merge($post,$json);
        
        }
    
    }
    
    $orderDetails = $post['orderDetails'] ?? $post;
    
    $cart_products = $orderDetails['CartProducts'] ?? [];
    
    $invoiceInfo = $orderDetails['InvoiceInfo'] ?? [];
    
    if(empty($cart_products)){
    
        throw new \Exception("Cart Empty");
    
    }

    $name = trim($orderDetails['CustomerName'] ?? 'Guest');
    
    $parts = explode(' ',$name,2);
    
    $firstname = $parts[0] ?? 'Guest';
    
    $lastname = $parts[1] ?? '';
    
    $order_data=[
        
        'invoice_prefix'=>'APP-',
        
        'invoice_no'=>time(),
        
        'customer_id'=>$customer_id,
        
        'customer_group_id'=>$customer_id,
        
        'sellerId'=>0,
        
        'quote_id'=>0,
        
        'pre_order_id'=>0,
        
        'firstname'=>$firstname,
        
        'lastname'=>$lastname,
        
        'email'=>$orderDetails['Email'] ?? '',
        
        'telephone'=>$orderDetails['Mobile'] ?? '',
        
        'custom_field'=>[],
        
        'payment_method'=>[
        
        'name'=>$orderDetails['PaymentThrough'] ?? '',
        
        'code'=>strtolower($orderDetails['PaymentThrough'] ?? '')
    
    ],
    
        'comment'=>$orderDetails['Note'] ?? '',
        
        'total'=>(float)($invoiceInfo['TotalBeforeRoundoff'] ?? 0),
        
        'products'=>[]
    
    ];

    foreach($cart_products as $p){
    
    $order_data['products'][]=[
    
        'product_id'=>(int)($p['product_id'] ?? 0),
        
        'name'=>$p['name'] ?? '',
        
        'model'=>'',
        
        'option'=>[],
        
        'quantity'=>(int)($p['quantity'] ?? 1),
        
        'price'=>(float)($p['price'] ?? 0),
        
        'total'=>(float)($p['total'] ?? 0),
        
        'excluded'=>!empty($p['excluded'])?1:0
    
    ];
    
    }
    
    $invoice_extra=[
    
        'customer_group_id'=>$customer_id,
        
        'cash_amount'=>(float)($orderDetails['CashAmount'] ?? 0),
        
        'upi_amount'=>(float)($orderDetails['UPIAmount'] ?? 0),
        
        'coupon'=>$orderDetails['coupon'] ?? '',
        
        'credit_points'=>0,
        
        'creditpointsused'=>0,
        
        'discount'=>(float)($invoiceInfo['DiscountIncluded'] ?? 0),
        
        'number_of_items'=>(int)($invoiceInfo['NumberOfItems'] ?? 0),
        
        'quantity_of_items'=>(int)($invoiceInfo['QuantityTotal'] ?? 0),
        
        'sub_total'=>(float)($invoiceInfo['SUBTotal'] ?? 0),
        
        'total_tax'=>(float)($invoiceInfo['TotalTax'] ?? 0),
        
        'roundoff_amount'=>(float)($invoiceInfo['RoundOffAmount'] ?? 0),
        
        'amount_through'=>$orderDetails['PaymentThrough'] ?? '',
        
        'pending_amount'=>0,
        
        'returnable_balance'=>0,
        
        'total_received'=>(float)($invoiceInfo['TotalBeforeRoundoff'] ?? 0),
        
        'balance'=>0,
        
        'save_advance'=>false
    
    ];
    $this->load->model('checkout/order');
    
    $order_id=$this->model_checkout_order->addOrder($order_data,$invoice_extra);
    
    if(!$order_id){
    
        throw new \Exception("Order Failed");
    
    }
    
    
    $this->model_checkout_order->addHistory(
    
        $order_id,
        
        5,
        
        "Mobile App Order",
        
        false,
        
        true
    
    );

    $this->response->setOutput(json_encode([
    
        "status"=>"success",
        
        "order_id"=>$order_id
    
    ]));
    
    }catch(\Throwable $e){
    
    $this->response->setOutput(json_encode([
    
    "status"=>"error",
    
    "message"=>$e->getMessage()
    
    ]));
    
    }

}

    protected function validateImg($imageString)
        {
            //error_reporting(0);
            $img=imagecreatefromstring(base64_decode($imageString));
            if(!$img || !isset($img) || empty($img))
            {
                return array("success"=>0,"message"=>"error_data");
            }
            
            imagepng($img,'tmp.png');
            $size = getimagesize('tmp.png');
            unlink('tmp.png');
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg','image/JPEG', 'image/PNG', 'image/GIF', 'image/JPG'];
            
            $file_size = strlen(base64_decode($imageString));

            if (!empty($size['mime'])&&$file_size < ($this->config->get('config_file_max_size') * 1024 * 1024))
            {
                if (in_array($size['mime'], $allowed)) 
                {
                    return array("success"=>1,"message"=>"accepted");
                }else
                    {
                        return array("success"=>0,"message"=>"error_extension");
                    }
            }else
                {
                    return array("success"=>0,"message"=>"error_size");
                }
        }
        
        private function saveBase64Image(string $imageString): string {

                    
                    $validate = $this->validateImg($imageString);

            if(!$validate['success']){
            
            throw new \Exception('Invalid Image');
            
            }

            
            $dir = DIR_IMAGE . 'catalog/products/';
            
            if(!is_dir($dir)){
            
            mkdir($dir,0777,true);
            
            }
            
            
            // ---------- UNIQUE FILE NAME ----------
            
            $file = 'product_'.date('YmdHis').'_'.mt_rand(1000,9999).'.jpg';
            
            $filepath = $dir.$file;
            
         
            $image = imagecreatefromstring(base64_decode($imageString));
            
            if(!$image){
            
            throw new \Exception('Image Decode Failed');
            
            }
            
        
            imagejpeg($image,$filepath,90);
            
            imagedestroy($image);

            return 'catalog/products/'.$file;
            
            }
    public function addProduct(): void {
            
            $this->response->addHeader('Content-Type: application/json');
            
            $customer_id = $this->validateToken();
            
            if(!$customer_id){
            
            $this->response->setOutput(json_encode([
            
                    "status"=>"error",
                    "message"=>"Invalid Token"
                    
                    ]));
                    
                    return;
            
            }
            
            try{
                
                $post = $this->request->post;
                
                $raw = file_get_contents("php://input");
                
                if($raw){
                
                $json = json_decode($raw,true);
                
                if(json_last_error() === JSON_ERROR_NONE){
                
                $post = array_merge($post,$json);
                
                }
                
            }
            
            
            $this->load->model('product/addproducts');
            
            
            if(!empty($post['image_base64'])){
            
            $post['image'] = $this->saveBase64Image(
            
            $post['image_base64']
            
            );
            
            unset($post['image_base64']);
            
            }
            
        
            
            $post += [
            
            'product_id'=>0,
            
            'product_description'=>[],
            
            'product_category'=>[],
            
            'sku'=>'',
            
            'upc'=>'',
            
            'box_id'=>'',
            
            'rack_code'=>'RACK-00',
            
            'category_id'=>0,
            
            'price'=>0,
            
            'special_price'=>0,
            
            'additional_price'=>0,
            
            'wholesale_price'=>0,
            
            'quantity'=>1,
            
            'minimum'=>1,
            
            'subtract'=>1,
            
            'status'=>1,
            
            'max_quantity'=>1,
            
            'r_tag'=>'R888',
            
            'w_tag'=>'W999',
            
            'image'=>'',
            
            'piece_id' => (int)($post['piece_id'] ?? 0),
            
            'pos_status'=>1,
            
            'pos_quentity'=>0
            
            ];
            
            
            $base = (float)$post['price'];
            
            $add = (float)$post['additional_price'];
            
            $post['price'] = $base + ($base * ($add/100));
            
            
            foreach($post['product_description'] as $lang=>$value){
            
            if(empty($value['name'])){
            
            throw new \Exception("Product name required");
            
            }
            
            }
            
            
            $product_id = (int)$post['product_id'];
            
            if($product_id > 0){
            
            $existing = $this->model_product_addproducts->getProduct($product_id);
            
            if(!$existing){
            
            throw new \Exception("Invalid Product ID");
            
            }
            
            $this->model_product_addproducts->edit($product_id,$post);
            
            }else{
            
            
            $type = $post['barcode_type'] ?? 'unit';
            
            if(empty($post['sku']) && empty($post['upc'])){
            
            if($type === 'box'){
            
            $post['upc'] = $this->model_product_addproducts->generateBoxBarcode();
            
            $post['sku'] = '';
            
            }else{
            
            $post['sku'] = $this->model_product_addproducts->getNextUnitBarcode();
            
            $post['upc'] = '';
            
            }
            
            }
            
            
            $product_id = $this->model_product_addproducts->add($post);
            
            }
            
            
            $this->model_product_addproducts->savePosProduct(
            
            $product_id,
            
            (int)$post['pos_status'],
            
            (int)$post['pos_quentity']
            
            );
            
         
            $this->response->setOutput(json_encode([
            
            "status"=>"success",
            
            "product_id"=>$product_id
            
            ]));
            
            }catch(\Throwable $e){
            
            $this->response->setOutput(json_encode([
            
            "status"=>"error",
            
            "message"=>$e->getMessage()
            
            ]));
            
            }
            
            }
    
    public function addCategory(): void {

        $this->response->addHeader('Content-Type: application/json');
        
        $customer_id = $this->validateToken();
        
        if (!$customer_id) {
        
        $this->response->setOutput(json_encode([
        
            "status"=>"error",
            "message"=>"Invalid Token"
            
            ]));
        
        return;
        
        }
        
        try{
        
        $post = $this->request->post;
        
        $raw = file_get_contents("php://input");
        
        if($raw){
        
            $json = json_decode($raw,true);
        
        if(json_last_error()===JSON_ERROR_NONE){
        
            $post=array_merge($post,$json);
        
            }
        
        }
        
        $name = trim($post['name'] ?? '');
        
        if(!$name){
        
        throw new \Exception("Category name required");
        
        }
        
        $this->load->model('groceries/categories');
        
        $category_id = $this->model_groceries_categories->addCategory($name);
        
        $this->response->setOutput(json_encode([
        
            "status"=>"success",
            
            "category_id"=>$category_id,
            
            "name"=>$name
            
            ]));
        
        }catch(\Throwable $e){
        
        $this->response->setOutput(json_encode([
        
            "status"=>"error",
            
            "message"=>$e->getMessage()
            
            ]));
        
        }

}

public function addPiece(): void {

        $this->response->addHeader('Content-Type: application/json');
        
        if(!$this->validateToken()){
        $this->response->setOutput(json_encode([
    
            "status"=>"error",
            "message"=>"Invalid Token"
            
            ]));
            
            return;
            
            }

        $json = [];

        // POST DATA
        $piece = $this->request->post['piece'] ?? '';

        if (!$piece) {
            $json['error'] = 'Piece is required';
        }

        if (!$json) {

            $this->load->model('groceries/categories');

            $piece_id = $this->model_groceries_categories->addPiece($piece);

            $json['success'] = true;
            $json['message'] = 'Piece Added Successfully';
            $json['piece_id'] = $piece_id;
        }

        $this->response->setOutput(json_encode($json));
    }
    
    public function getPieces(): void {

        $this->response->addHeader('Content-Type: application/json');
    
        $agentId = $this->validateToken();
    
        if (!$agentId) {
            $this->response->setOutput(json_encode([
                "status"  => "error",
                "message" => "Invalid Token"
            ]));
            return;
        }
    
        $this->load->model('groceries/categories');
    
        $pieces = $this->model_groceries_categories->getPieces();
    
        $this->response->setOutput(json_encode([
            "status" => "success",
            "data"   => $pieces
        ]));
    }
    
    public function getOrdersbyDate(): void {

        $this->response->addHeader('Content-Type: application/json');
        
 
        $agentId = $this->validateToken();
        
        if (!$agentId) {
        
        $this->response->setOutput(json_encode([
        
        "status"  => "error",
        "message" => "Invalid Token"
        
        ]));
        
        return;
        
        }
        
        
        $from_date = $this->request->get['from_date'] ?? '';
        $to_date   = $this->request->get['to_date'] ?? '';
        
        $order_id  = $this->request->get['order_id'] ?? '';
        $mobile    = $this->request->get['mobile'] ?? '';
        $name      = $this->request->get['name'] ?? '';
        
 
        if (empty($from_date) || empty($to_date)) {
        
        $today = date('Y-m-d');
        
        $from_date = $today;
        $to_date   = $today;
        
        }
        
        
        $this->load->model('groceries/categories');
        
        
        $orders = $this->model_groceries_categories->getOrdersByDateRange($agentId,$from_date,$to_date,$order_id,$mobile,$name);
        
        $totals = $this->model_groceries_categories->getOrderTotalsByDateRange($from_date,$to_date,$agentId);

        $this->response->setOutput(json_encode([
        
            "status"       => "success",
            
            "total_orders" => count($orders),
            
            "totals"       => $totals,
            
            "data"         => $orders
            
            ]));
        
        }
        
        public function getOrdersforMonths(): void {

        $this->response->addHeader('Content-Type: application/json');
        
 
        $agentId = $this->validateToken();
        
        if (!$agentId) {
        
        $this->response->setOutput(json_encode([
        
        "status"  => "error",
        "message" => "Invalid Token"
        
        ]));
        
        return;
        
        }
        
        
        $from_date = $this->request->get['from_date'] ?? '';
        $to_date   = $this->request->get['to_date'] ?? '';
        
        $order_id  = $this->request->get['order_id'] ?? '';
        $mobile    = $this->request->get['mobile'] ?? '';
        $name      = $this->request->get['name'] ?? '';
        
 
        if (empty($from_date) || empty($to_date)) {

            $to_date   = date('Y-m-d');
            $from_date = date('Y-m-d', strtotime('-6 months'));
        
        }
        
        
        $this->load->model('checkout/order');
        
        
        $orders = $this->model_checkout_order->getOrdersByDateRange($agentId,$from_date,$to_date,$order_id,$mobile,$name);
        
        $totals = $this->model_checkout_order->getOrderTotalsByDateRange($from_date,$to_date,$agentId);

        $this->response->setOutput(json_encode([
        
            "status"       => "success",
            
            "total_orders" => count($orders),
            
            "totals"       => $totals,
            
            "data"         => $orders
            
            ]));
        
        }
        
        public function cancelOrder(){
         
         
         $this->response->addHeader('Content-Type: application/json');
        
 
        $agentId = $this->validateToken();
        
        if (!$agentId) {
        
        $this->response->setOutput(json_encode([
        
        "status"  => "error",
        "message" => "Invalid Token"
        
        ]));
        
        return;
        
        }
        
        try {
        $order_id = (int)($this->request->post['order_id'] ?? 0);

        if ($order_id <= 0) {
            throw new \Exception('Invalid order id');
        }

        $this->load->model('checkout/order');

        if ($this->model_checkout_order->isOrderCancelled($order_id)) {
            throw new \Exception('Order already cancelled');
        }

        $this->model_checkout_order->cancelOrderFull($order_id);

        $this->response->setOutput(json_encode([
            'status' => 'success'
        ]));
    } catch (Throwable $e) {
        $this->response->setOutput(json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]));
    }
        }
        
      public function invoice(): void {
        
        
        // ---------- TOKEN ----------
        
        $customer_id = $this->validateToken();
        
        if(!$customer_id){
        
        $this->response->setOutput("Invalid Token");
        
        return;
        
        }
        
        
        // ---------- ORDER ----------
        
        $order_id = (int)($this->request->get['order_id'] ?? 0);
        
        if(!$order_id){
        
        $this->response->setOutput("Order Missing");
        
        return;
        
        }
        
        
        // ---------- LOAD MODEL ----------
        
        $this->load->model('checkout/order');
        
        $this->load->model('tool/upload');
        
        $order_info =
        $this->model_checkout_order
        ->getOrderdetails($order_id);
        
        if(!$order_info){
        
        $this->response->setOutput("Order Not Found");
        
        return;
        
        }
        
        
        // ---------- PRODUCTS ----------
        
        $product_data=[];
        
        $products =
        $this->model_checkout_order
        ->getProducts($order_id);
        
        foreach($products as $product){
        
        $product_data[]=[
        
        'name'=>$product['name'],
        
        'quantity'=>$product['quantity'],
        
        'price'=>$product['price'],
        
        'total'=>$product['price']
        *
        $product['quantity'],
        
        'excluded'=>!empty($product['excluded'])?1:0
        
        ];
        
        }
        
        
        // ---------- TOTALS ----------
        
        $total_data=[];
        
        $totals =
        $this->model_checkout_order
        ->getTotals($order_id);
        
        foreach($totals as $total){
        
        $total_data[]=$total;
        
        }
        
        
        // ---------- VIEW DATA ----------
        
        $data['orders'][]=[
        
        'order_id'=>$order_id,
        
        'invoice_no'=>
        
        $order_info['invoice_prefix'].
        $order_info['invoice_no'],
        
        'date_added'=>$order_info['date_added'],
        
        'store_name'=>$this->config
        ->get('config_name'),
        
        'store_address'=>$this->config
        ->get('config_address'),
        
        'store_telephone'=>$this->config
        ->get('config_telephone'),
        
        'email'=>$order_info['email'],
        
        'telephone'=>$order_info['telephone'],
        
        'payment_method'=>$order_info['payment_method'],
        
        'product'=>$product_data,
        
        'total'=>$total_data,
        
        'comment'=>$order_info['comment'],
        
        'invoice'=>[
        
        'sub_total'=>$order_info['sub_total'] ?? 0,
        
        'discount'=>$order_info['discount'] ?? 0,
        
        'total_tax'=>$order_info['total_tax'] ?? 0,
        
        'roundoff_amount'=>$order_info['roundoff_amount'] ?? 0,
        
        'total_received'=>$order_info['total_received'] ?? 0,
        
        'cash_amount'=>$order_info['cash_amount'] ?? 0,
        
        'upi_amount'=>$order_info['upi_amount'] ?? 0,
        
        'pending_amount'=>$order_info['pending_amount'] ?? 0,
        
        'due_amount'=>$order_info['balance'] ?? 0,
        
        'returnable_balance'=>
        $order_info['returnable_balance'] ?? 0
        
        ]
        
        ];
        
        
        // optional small print
        
        $data['small_print'] =
        !empty($this->request->get['small_print']);
        
       // ---------- JSON RESPONSE ----------

        $this->response->addHeader('Content-Type: application/json');
        
        $this->response->setOutput(json_encode([
            'status' => true,
            'data'   => $data
        ]));

    }

    public function getOrdersbyId()
    {
        $this->response->addHeader("Content-Type: application/json");
        $customer_id = $this->validateToken();

            if(!$customer_id){
            
            $this->response->setOutput("Invalid Token");
            
            return;
            
            }

        $order_id = $this->request->get["order_id"] ?? 0;

        if (!$order_id) {
            return $this->response->setOutput(
                json_encode([
                    "status" => "error",
                    "message" => "Order ID missing",
                ])
            );
        }

        $this->load->model("checkout/order");

        $details = $this->model_checkout_order->getFullOrderDetails($order_id);

        if (!$details) {
            return $this->response->setOutput(
                json_encode([
                    "status" => "error",
                    "message" => "Order not found",
                ])
            );
        }

        return $this->response->setOutput(
            json_encode([
                "status" => "success",
                "data" => $details,
            ])
        );
    }
    
     public function addAddress(): void {

        $this->response->addHeader('Content-Type: application/json');
        
        $customer_id = $this->validateToken();
        
        if(!$customer_id){
        
            $this->response->setOutput(json_encode([
            "status"=>"error",
            "message"=>"Invalid Token"
            ]));
        
        return;
        
        }
        
        $post = $this->request->post;
        
        $raw = file_get_contents("php://input");
        
        if($raw){
        
        $json = json_decode($raw,true);
        
        if(json_last_error()===JSON_ERROR_NONE){
        
        $post = array_merge($post,$json);
        
            }
        
         }
        
        $firstname = $post['firstname'] ?? '';
        $lastname  = $post['lastname'] ?? '';
        $contact  = $post['contact'] ?? '';
        $company   = $post['company'] ?? '';
        $address_1 = $post['address_1'] ?? '';
        $address_2 = $post['address_2'] ?? '';
        $city      = $post['city'] ?? '';
        $postcode  = $post['postcode'] ?? '';
        $country_id= $post['country_id'] ?? 99;
        $zone_id   = $post['zone_id'] ?? 0;
        
        if(!$firstname || !$address_1 || !$city){
        
            $this->response->setOutput(json_encode([
            "status"=>"error",
            "message"=>"Required fields missing"
            ]));
            
            return;
        
        }
        
        $this->load->model('groceries/categories');
        
        $address_id = $this->model_groceries_categories->addAddress([
                                            "customer_id"=>$customer_id,
                                            "firstname"=>$firstname,
                                            "contact"=>$contact,
                                            "lastname"=>$lastname,
                                            "company"=>$company,
                                            "address_1"=>$address_1,
                                            "address_2"=>$address_2,
                                            "city"=>$city,
                                            "postcode"=>$postcode,
                                            "country_id"=>$country_id,
                                            "zone_id"=>$zone_id
                                            ]);
        
        $this->response->setOutput(json_encode([
                                                "status"=>"success",
                                                "address_id"=>$address_id
                                                ]));
    
    }

    public function getAddress(): void {

        $this->response->addHeader('Content-Type: application/json');
        
        $customer_id = $this->validateToken();
        
        if(!$customer_id){
            
            $this->response->setOutput(json_encode([
            "status"=>"error",
            "message"=>"Invalid Token"
            ]));
            
            return;
        
        }
        
        $telephone  = $this->request->get['telephone'] ?? '';
        $address_id = (int)($this->request->get['address_id'] ?? 0);
        
        $this->load->model('groceries/categories');
        
        $addresses = $this->model_groceries_categories->getAddress($customer_id,$telephone,$address_id);
        
        if(!$addresses){
        
            $this->response->setOutput(json_encode([
                                            "status"=>"error",
                                            "message"=>"No address found"
                                            ]));
        
                                            return;
        
        }
        
        $this->response->setOutput(json_encode([
                                            "status"=>"success",
                                            "data"=>$addresses
                                            ]));
                                            
    }
    
    public function editAddress(): void {

$this->response->addHeader('Content-Type: application/json');

$customer_id = $this->validateToken();

if(!$customer_id){

$this->response->setOutput(json_encode([
"status"=>"error",
"message"=>"Invalid Token"
]));

return;

}

$post = $this->request->post;

$raw = file_get_contents("php://input");

if($raw){

$json = json_decode($raw,true);

if(json_last_error() === JSON_ERROR_NONE){

$post = array_merge($post,$json);

}

}

$address_id = (int)($post['address_id'] ?? 0);

if(!$address_id){

$this->response->setOutput(json_encode([
"status"=>"error",
"message"=>"Address ID required"
]));

return;

}

$firstname = $post['firstname'] ?? '';
$lastname  = $post['lastname'] ?? '';
$contact   = $post['contact'] ?? '';
$company   = $post['company'] ?? '';
$address_1 = $post['address_1'] ?? '';
$address_2 = $post['address_2'] ?? '';
$city      = $post['city'] ?? '';
$postcode  = $post['postcode'] ?? '';
$country_id= $post['country_id'] ?? 99;
$zone_id   = $post['zone_id'] ?? 0;

$this->load->model('groceries/categories');

$updated = $this->model_groceries_categories->editAddress($customer_id,$address_id,[
"firstname"=>$firstname,
"lastname"=>$lastname,
"contact"=>$contact,
"company"=>$company,
"address_1"=>$address_1,
"address_2"=>$address_2,
"city"=>$city,
"postcode"=>$postcode,
"country_id"=>$country_id,
"zone_id"=>$zone_id
]);

if(!$updated){

$this->response->setOutput(json_encode([
"status"=>"error",
"message"=>"Address not found"
]));

return;

}

$this->response->setOutput(json_encode([
"status"=>"success",
"message"=>"Address updated successfully"
]));

}

public function deleteAddress(): void {

$this->response->addHeader('Content-Type: application/json');

$customer_id = $this->validateToken();

if(!$customer_id){

$this->response->setOutput(json_encode([
"status"=>"error",
"message"=>"Invalid Token"
]));

return;

}

$post = $this->request->post;

$raw = file_get_contents("php://input");

if($raw){

$json = json_decode($raw,true);

if(json_last_error() === JSON_ERROR_NONE){

$post = array_merge($post,$json);

}

}

$address_id = (int)($post['address_id'] ?? 0);

if(!$address_id){

$this->response->setOutput(json_encode([
"status"=>"error",
"message"=>"Address ID required"
]));

return;

}

$this->load->model('groceries/categories');

$deleted = $this->model_groceries_categories->deleteAddress($customer_id,$address_id);

if(!$deleted){

$this->response->setOutput(json_encode([
"status"=>"error",
"message"=>"Address not found"
]));

return;

}

$this->response->setOutput(json_encode([
"status"=>"success",
"message"=>"Address deleted successfully"
]));

}

public function getCoupon(): void {

$this->response->addHeader('Content-Type: application/json');

$customer_id = $this->validateToken();

if(!$customer_id){

$this->response->setOutput(json_encode([
"status"=>"error",
"message"=>"Invalid Token"
]));

return;
}
        $this->load->model('groceries/categories');

        $coupon = $this->model_groceries_categories->getCoupon($customer_id);
        
        $this->response->setOutput(json_encode([
"status"=>"success",
"coupons"=>$coupon
]));
        
}

public function applycoupon()
    {
        $customer_id = $this->validateToken();

        if(!$customer_id){
        
        return $this->response->setOutput(
        
        json_encode([
        
        "status"=>"error",
        
        "message"=>"Invalid Token"
        
        ])
        
        );
        
        }
        
        $this->load->language("extension/total/coupon");
        $this->load->model("checkout/order");

        $json = [];

        $coupon = $this->request->post["coupon"] ?? "";
        $grand_total = (float) ($this->request->post["grand_total"] ?? 0);

        if (!$coupon) {
            $json["error"] = $this->language->get("error_empty");
            return $this->response->setOutput(json_encode($json));
        }

        $coupon_info = $this->model_checkout_order->getCoupon($coupon);

        if (!$coupon_info) {
            $json["error"] = $this->language->get("error_coupon");
            return $this->response->setOutput(json_encode($json));
        }

        // ✅ Minimum bill validation
        if (
            $coupon_info["minimum_total"] > 0 &&
            $grand_total < (float) $coupon_info["minimum_total"]
        ) {
            $json["error"] = sprintf(
                "This coupon requires a minimum bill of ₹ %.2f. Your total is ₹ %.2f.",
                (float) $coupon_info["minimum_total"],
                $grand_total
            );
            return $this->response->setOutput(json_encode($json));
        }

        // ✅ IMPORTANT: return coupon.total also
        $json["success"] = $this->language->get("text_success");
        $json["coupon_info"] = [
            "coupon_id" => $coupon_info["coupon_id"],
            "code" => $coupon_info["code"],
            "name" => $coupon_info["name"],
            "type" => $coupon_info["type"],
            "discount" => $coupon_info["discount"],
            "total" => (float) $coupon_info["total"], // ⭐ THIS WAS MISSING
            "minimum_total" => (float) $coupon_info["minimum_total"],
        ];

        return $this->response->setOutput(json_encode($json));
    }
}
