<?php
namespace Opencart\Catalog\Model\Groceries;

class Categories extends \Opencart\System\Engine\Model {

    public function loginCustomer($email, $password) {

        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` WHERE email = '" . $this->db->escape($email) . "' AND status = 1 LIMIT 1");

        if (!$query->num_rows) {
            return false;
        }

        $customer = $query->row;

        if (!password_verify($password, $customer['password'])) {
            return false;
        }

        $token = bin2hex(random_bytes(32));

        $this->db->query("UPDATE `" . DB_PREFIX . "customer` SET token = '" . $this->db->escape($token) . "' WHERE customer_id = '" . (int)$customer['customer_id'] . "'");

        return [
            "customer_id" => $customer['customer_id'],
            "token" => $token
        ];
    }

    public function validateToken($token) {

    $query = $this->db->query("SELECT customer_id FROM `" . DB_PREFIX . "customer` WHERE token = '" . $this->db->escape($token) . "' AND status = 1 LIMIT 1");

    if ($query->num_rows) {
        return $query->row['customer_id'];
    }

    return false;
}

    public function getMainCategories() {

        $sql = "SELECT * FROM `" . DB_PREFIX . "category` c
                JOIN `" . DB_PREFIX . "category_description` cd
                ON c.category_id = cd.category_id
                WHERE c.parent_id = 0
                AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        return $this->db->query($sql)->rows;
    }


    public function getAllProducts() {

        $sql = "SELECT *
                FROM `" . DB_PREFIX . "product` p
                JOIN `" . DB_PREFIX . "product_description` pd
                ON p.product_id = pd.product_id
                WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
                ORDER BY p.product_id DESC
                LIMIT " . (int)$start . ", " . (int)$limit;

        return $this->db->query($sql)->rows;
    }

    public function getSubCategories($category_id) {

        $sql = "SELECT *
                FROM `" . DB_PREFIX . "category` c
                JOIN `" . DB_PREFIX . "category_description` cd
                ON c.category_id = cd.category_id
                WHERE c.parent_id = '" . (int)$category_id . "'
                AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        return $this->db->query($sql)->rows;
    }

    public function getProductsByCategory($category_id) {

        $sql = "SELECT *
                FROM `" . DB_PREFIX . "product_to_category` pc
                JOIN `" . DB_PREFIX . "product` p
                ON pc.product_id = p.product_id
                JOIN `" . DB_PREFIX . "product_description` pd
                ON p.product_id = pd.product_id
                WHERE pc.category_id = '" . (int)$category_id . "'
                AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        return $this->db->query($sql)->rows;
    }
    
    public function getProductsByParentCategory($category_id){

        $sql="SELECT
        
        p.product_id,
        pc.category_id,
        pd.name,
        p.price,
        p.image
        
        FROM " . DB_PREFIX . "product_to_category pc
        
        JOIN " . DB_PREFIX . "product p
        ON pc.product_id=p.product_id
        
        JOIN " . DB_PREFIX . "product_description pd
        ON p.product_id=pd.product_id WHERE
        
        (
        
        pc.category_id='".$this->db->escape($category_id)."'
        
        OR
        
        pc.category_id IN (SELECT category_id FROM ".DB_PREFIX."category WHERE parent_id='".$this->db->escape($category_id)."'))
        
        AND pd.language_id='".(int)$this->config->get('config_language_id')."'";
        
        return $this->db->query($sql)->rows;
    
    }

   public function getProductsOnly($category_id, $start = 0, $limit = 5){

    $sql = "SELECT 
            p.product_id,
            p.price,
            p.special_price,
            p.image,
            pd.name,
            c.category_id,
            c.gst,
            pp.pos_status,
            pp.pos_quentity,

            ptp.piece_id,
            ps.piece

            FROM " . DB_PREFIX . "product_to_category pc

            JOIN " . DB_PREFIX . "product p
            ON pc.product_id = p.product_id

            JOIN " . DB_PREFIX . "product_description pd
            ON p.product_id = pd.product_id

            JOIN " . DB_PREFIX . "category c
            ON pc.category_id = c.category_id

            LEFT JOIN " . DB_PREFIX . "pts_pos_product pp
            ON p.product_id = pp.product_id

            LEFT JOIN " . DB_PREFIX . "piece_to_product ptp
            ON p.product_id = ptp.product_id

            LEFT JOIN " . DB_PREFIX . "pieces ps
            ON ptp.piece_id = ps.piece_id

            WHERE pc.category_id = '" . (int)$category_id . "'
            AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'

            ORDER BY p.product_id DESC
            LIMIT " . (int)$start . ", " . (int)$limit;

    return $this->db->query($sql)->rows;
}
    
    public function getRandomProducts() {
        $sql = "SELECT *
        FROM `" . DB_PREFIX . "product` p
        JOIN `" . DB_PREFIX . "product_description` pd
        ON p.product_id = pd.product_id
        WHERE pd.language_id='".(int)$this->config->get('config_language_id')."'
        ORDER BY RAND()
        LIMIT 9";
        
        return $this->db->query($sql)->rows;
        
        }
        
        public function getOfferCategories() {

        $sql="SELECT *
        FROM `" . DB_PREFIX . "category` c
        JOIN `" . DB_PREFIX . "category_description` cd
        ON c.category_id=cd.category_id
        WHERE c.offer='1'
        AND c.status='1'
        AND cd.language_id='".(int)$this->config->get('config_language_id')."'";
        
        return $this->db->query($sql)->rows;
        
        }
        
        public function getOfferProducts($category_id){

        $sql="SELECT *
        FROM `" . DB_PREFIX . "product_to_category` pc
        
        JOIN `" . DB_PREFIX . "product` p
        ON pc.product_id=p.product_id
        
        JOIN `" . DB_PREFIX . "product_description` pd
        ON p.product_id=pd.product_id
        
        WHERE pc.category_id='".(int)$category_id."'
        
        AND pd.language_id='".(int)$this->config->get('config_language_id')."'";
        
        return $this->db->query($sql)->rows;
        
        }

public function getProductDetails($product_id){

    $sql = "SELECT 
            p.*,
            pp.*,
            pd.name,
            pd.description
            
            FROM `" . DB_PREFIX . "product` p
            
            JOIN `" . DB_PREFIX . "product_description` pd
            ON p.product_id = pd.product_id
            
            LEFT JOIN " . DB_PREFIX . "pts_pos_product pp
            ON p.product_id = pp.product_id
            
            WHERE p.product_id = '" . (int)$product_id . "'
            
            AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

    return $this->db->query($sql)->row;
}

public function getRelatedProducts($product_id){

    $sql = "SELECT 
            p.product_id,
            p.price,
            p.image,
            pd.name,
            pp.*

            FROM `" . DB_PREFIX . "product_related` pr

            JOIN `" . DB_PREFIX . "product` p
            ON pr.related_id = p.product_id

            JOIN `" . DB_PREFIX . "product_description` pd
            ON p.product_id = pd.product_id
            LEFT JOIN " . DB_PREFIX . "pts_pos_product pp
            ON p.product_id = pp.product_id

            WHERE pr.product_id = '" . (int)$product_id . "'

            AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

    return $this->db->query($sql)->rows;
}

public function loginCustomerOtp($telephone){

// CHECK CUSTOMER EXISTS

$query = $this->db->query(

"SELECT customer_id
FROM " . DB_PREFIX . "customer
WHERE telephone='" . $this->db->escape($telephone) . "'
AND status='1'
LIMIT 1"

);


// EXISTING CUSTOMER

if($query->num_rows){

$customer_id = $query->row['customer_id'];

}else{

// -------- CREATE NEW CUSTOMER --------

// REQUIRED EMAIL IN OPENCART

$email = $telephone . '@otpuser.com';


// CHECK DUPLICATE EMAIL

$checkEmail = $this->db->query(

"SELECT customer_id FROM " . DB_PREFIX . "customer
WHERE email='".$this->db->escape($email)."'"

);

if($checkEmail->num_rows){

$email = $telephone . time() . '@otpuser.com';

}


// PASSWORD = Admin@123

$password = password_hash(
'Admin@123',
PASSWORD_DEFAULT
);


// INSERT CUSTOMER

$this->db->query(

"INSERT INTO ".DB_PREFIX."customer SET

firstname='OTP',

lastname='User',

email='".$this->db->escape($email)."',

telephone='".$this->db->escape($telephone)."',

password='".$this->db->escape($password)."',

newsletter='0',

customer_group_id='1',

status='1',

date_added=NOW()"

);

$customer_id = $this->db->getLastId();

}


// TOKEN

$token = bin2hex(random_bytes(32));


$this->db->query(

"UPDATE ".DB_PREFIX."customer

SET token='".$this->db->escape($token)."'

WHERE customer_id='".(int)$customer_id."'"

);


// RESPONSE

return [

"customer_id"=>$customer_id,

"token"=>$token

];

}

public function logoutCustomer($token) {

    $query = $this->db->query("
        SELECT customer_id 
        FROM " . DB_PREFIX . "customer
        WHERE token = '" . $this->db->escape($token) . "'
        LIMIT 1
    ");

    if (!$query->num_rows) {
        return false;
    }

    $this->db->query("
        UPDATE " . DB_PREFIX . "customer 
        SET token = '' 
        WHERE customer_id = '" . (int)$query->row['customer_id'] . "'
    ");

    return true;
}

public function addCategory(string $name): int {


// -------- CATEGORY INSERT --------

$this->db->query("INSERT INTO `" . DB_PREFIX . "category` SET

parent_id='0',

sort_order='0',

offer='0',

gst='0',

status='1'

");

$category_id = (int)$this->db->getLastId();


// -------- DESCRIPTION --------

$this->db->query("INSERT INTO `" . DB_PREFIX . "category_description` SET

category_id='".(int)$category_id."',

language_id='".(int)$this->config->get('config_language_id')."',

name='".$this->db->escape($name)."',

description='',

meta_title='".$this->db->escape($name)."',

meta_description='',

meta_keyword=''"

);


// -------- STORE --------

$this->db->query("INSERT INTO `" . DB_PREFIX . "category_to_store` SET

category_id='".(int)$category_id."',

store_id='0'

");


// -------- PATH --------

$this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET

category_id='".(int)$category_id."',

path_id='".(int)$category_id."',

level='0'

");


return $category_id;

}

public function getFullOrderDetails(int $order_id) {

    $order = $this->db->query("
        SELECT 
            o.*,
            os.name AS order_status
        FROM `" . DB_PREFIX . "order` o
        LEFT JOIN `" . DB_PREFIX . "order_status` os
            ON os.order_status_id = o.order_status_id
            AND os.language_id = '" . (int)$this->config->get('config_language_id') . "'
        WHERE o.order_id = '" . (int)$order_id . "'
    ")->row;

    if (!$order) {
        return false;
    }

    // Products
    $products = $this->db->query("
        SELECT *
        FROM `" . DB_PREFIX . "order_product`
        WHERE order_id = '" . (int)$order_id . "'
    ")->rows;

    // Totals
    $totals = $this->db->query("
        SELECT *
        FROM `" . DB_PREFIX . "order_total`
        WHERE order_id = '" . (int)$order_id . "'
        ORDER BY sort_order
    ")->rows;

    // Invoice
    $invoice = $this->db->query("
        SELECT *
        FROM `" . DB_PREFIX . "order_invoice`
        WHERE order_id = '" . (int)$order_id . "'
    ")->row;

    // Tax
    $tax = $this->db->query("
        SELECT *
        FROM `" . DB_PREFIX . "order_tax_details`
        WHERE order_id = '" . (int)$order_id . "'
    ")->rows;

    // History
    $history = $this->db->query("
        SELECT 
            oh.*,
            os.name AS status_name
        FROM `" . DB_PREFIX . "order_history` oh
        LEFT JOIN `" . DB_PREFIX . "order_status` os
            ON os.order_status_id = oh.order_status_id
            AND os.language_id = '" . (int)$this->config->get('config_language_id') . "'
        WHERE oh.order_id = '" . (int)$order_id . "'
        ORDER BY oh.date_added DESC
    ")->rows;

    return [
        'order_info'  => $order,
        'products'    => $products,
        'totals'      => $totals,
        'invoice'     => $invoice,
        'tax_details' => $tax,
        'history'     => $history
    ];
}



public function getOrdersByDateRange($agentId, $from_date = '', $to_date = '', $order_id = '', $mobile = '', $name = '') {

    $sql = "SELECT o.order_id FROM `" . DB_PREFIX . "order` o WHERE o.customer_group_id = '" . (int)$agentId . "'";
    $isSearch = !empty($order_id) || !empty($mobile) || !empty($name);

    if (!$isSearch && !empty($from_date) && !empty($to_date)) {
        $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($from_date) . "'";
        $sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($to_date) . "'";
    }

    if (!empty($order_id)) {
        $sql .= " AND o.order_id LIKE '%" . $this->db->escape($order_id) . "%'";
    }

    if (!empty($mobile)) {
        $sql .= " AND o.telephone LIKE '%" . $this->db->escape($mobile) . "%'";
    }
    if (!empty($name)) {
        $sql .= " AND (
            o.firstname LIKE '%" . $this->db->escape($name) . "%'
            OR o.lastname LIKE '%" . $this->db->escape($name) . "%'
            OR CONCAT(o.firstname,' ',o.lastname) LIKE '%" . $this->db->escape($name) . "%'
        )";
    }

    $sql .= " ORDER BY o.order_id DESC";

    $orders = $this->db->query($sql)->rows;

    $full = [];

    foreach ($orders as $order) {
        $full[] = $this->getFullOrderDetails((int)$order['order_id']);
    }

    return $full;
}


public function getOrderTotalsByDateRange($from_date, $to_date, $agentId) {

    $sql = " SELECT

        /* STATUS 5 CASH */
        COALESCE(SUM(
            CASE 
                WHEN o.order_status_id = 5 
                THEN (oi.cash_amount - 
                        CASE 
                            WHEN oi.cash_amount > 0 
                            THEN oi.returnable_balance 
                            ELSE 0 
                        END
                     )
                ELSE 0
            END
        ),0) AS status5_cash,


        /* STATUS 5 UPI */
        COALESCE(SUM(
            CASE 
                WHEN o.order_status_id = 5 
                THEN (oi.upi_amount - 
                        CASE 
                            WHEN oi.upi_amount > 0 
                            THEN oi.returnable_balance 
                            ELSE 0 
                        END
                     )
                ELSE 0
            END
        ),0) AS status5_upi,


        /* STATUS 6 CASH */
        COALESCE(SUM(
            CASE 
                WHEN o.order_status_id = 6 
                THEN (oi.cash_amount - 
                        CASE 
                            WHEN oi.cash_amount > 0 
                            THEN oi.returnable_balance 
                            ELSE 0 
                        END
                     )
                ELSE 0
            END
        ),0) AS status6_cash,


        /* STATUS 6 UPI */
        COALESCE(SUM(
            CASE 
                WHEN o.order_status_id = 6 
                THEN (oi.upi_amount - 
                        CASE 
                            WHEN oi.upi_amount > 0 
                            THEN oi.returnable_balance 
                            ELSE 0 
                        END
                     )
                ELSE 0
            END
        ),0) AS status6_upi,


        /* OTHER TOTALS */

        COALESCE(SUM(
    CASE 
        WHEN o.order_status_id = 5 
        THEN oi.sub_total 
        ELSE 0 
    END
),0) AS status5_subtotal,

COALESCE(SUM(
    CASE 
        WHEN o.order_status_id = 6 
        THEN oi.sub_total 
        ELSE 0 
    END
),0) AS status6_subtotal,


COALESCE(SUM(
    CASE 
        WHEN o.order_status_id = 5 
        THEN oi.total_received 
        ELSE 0 
    END
),0) AS status5_total_received,

COALESCE(SUM(
    CASE 
        WHEN o.order_status_id = 6 
        THEN oi.total_received 
        ELSE 0 
    END
),0) AS status6_total_received,


COALESCE(SUM(
    CASE 
        WHEN o.order_status_id = 5 
        THEN oi.returnable_balance 
        ELSE 0 
    END
),0) AS status5_returnable,

COALESCE(SUM(
    CASE 
        WHEN o.order_status_id = 6 
        THEN oi.returnable_balance 
        ELSE 0 
    END
),0) AS status6_returnable,


COALESCE(SUM(
    CASE 
        WHEN o.order_status_id = 5 
        THEN oi.balance 
        ELSE 0 
    END
),0) AS status5_balance,

COALESCE(SUM(
    CASE 
        WHEN o.order_status_id = 6 
        THEN oi.balance 
        ELSE 0 
    END
),0) AS status6_balance

        FROM `" . DB_PREFIX . "order` o

        INNER JOIN `" . DB_PREFIX . "order_invoice` oi
        ON oi.order_id = o.order_id

        WHERE DATE(o.date_added) >= '" . $this->db->escape($from_date) . "'
        AND DATE(o.date_added) <= '" . $this->db->escape($to_date) . "'
        AND o.customer_group_id = '" . (int)$agentId . "'
    ";

    return $this->db->query($sql)->row;
}

public function addPiece($piece) {

        $this->db->query("INSERT INTO " . DB_PREFIX . "pieces 
        SET piece = '" . $this->db->escape($piece) . "',
        status = '1',
        date_added = NOW()");

        return $this->db->getLastId();
    }
    
    public function getPieces() {

    $query = $this->db->query("
        SELECT piece_id, piece 
        FROM " . DB_PREFIX . "pieces
        WHERE status = '1'
        ORDER BY piece ASC
    ");

    return $query->rows;
}

public function addAddress($data){

$this->db->query("INSERT INTO " . DB_PREFIX . "address SET

customer_id='".(int)$data['customer_id']."',

firstname='".$this->db->escape($data['firstname'])."',

lastname='".$this->db->escape($data['lastname'])."',

contact='".$this->db->escape($data['contact'])."',

company='".$this->db->escape($data['company'])."',

address_1='".$this->db->escape($data['address_1'])."',

address_2='".$this->db->escape($data['address_2'])."',

city='".$this->db->escape($data['city'])."',

postcode='".$this->db->escape($data['postcode'])."',

country_id='".(int)$data['country_id']."',

zone_id='".(int)$data['zone_id']."',

custom_field='',

`default`='0'

");

return $this->db->getLastId();

}

public function getAddress($customer_id,$telephone='',$address_id=0){

    $sql = "SELECT

            a.*,
            c.telephone
            
            FROM ".DB_PREFIX."address a
            
            JOIN ".DB_PREFIX."customer c
            ON a.customer_id = c.customer_id
            
            WHERE a.customer_id='".(int)$customer_id."'
            ";
            
            if(!empty($telephone)){
            $sql .= " AND c.telephone='".$this->db->escape($telephone)."'";
            }
            
            if(!empty($address_id)){
            $sql .= " AND a.address_id='".(int)$address_id."'";
            }
            
            $sql .= " ORDER BY a.address_id DESC";
            
            $query = $this->db->query($sql);
            
            return $query->rows;
            
    }
    
public function editAddress($customer_id,$address_id,$data){

$query = $this->db->query("
SELECT address_id
FROM ".DB_PREFIX."address
WHERE address_id='".(int)$address_id."'
AND customer_id='".(int)$customer_id."'
");

if(!$query->num_rows){
return false;
}

$this->db->query("
UPDATE ".DB_PREFIX."address SET

firstname='".$this->db->escape($data['firstname'])."',
lastname='".$this->db->escape($data['lastname'])."',
contact='".$this->db->escape($data['contact'])."',
company='".$this->db->escape($data['company'])."',
address_1='".$this->db->escape($data['address_1'])."',
address_2='".$this->db->escape($data['address_2'])."',
city='".$this->db->escape($data['city'])."',
postcode='".$this->db->escape($data['postcode'])."',
country_id='".(int)$data['country_id']."',
zone_id='".(int)$data['zone_id']."'

WHERE address_id='".(int)$address_id."'
AND customer_id='".(int)$customer_id."'
");

return true;

}

public function deleteAddress($customer_id,$address_id){

$query = $this->db->query("
SELECT address_id
FROM ".DB_PREFIX."address
WHERE address_id='".(int)$address_id."'
AND customer_id='".(int)$customer_id."'
");

if(!$query->num_rows){
return false;
}

$this->db->query("
DELETE FROM ".DB_PREFIX."address
WHERE address_id='".(int)$address_id."'
AND customer_id='".(int)$customer_id."'
");

return true;

}

public function getCoupon($customer_id){

$query = $this->db->query("
        SELECT * 
        FROM " . DB_PREFIX . "coupon
        WHERE status = '1'
    ");

    return $query->rows;
}
}