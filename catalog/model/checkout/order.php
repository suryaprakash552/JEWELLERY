<?php
namespace Opencart\Catalog\Model\Checkout;
/**
 * Class Order
 *
 * Can be called using $this->load->model('checkout/order');
 *
 * @package Opencart\Catalog\Model\Checkout
 */
class Order extends \Opencart\System\Engine\Model {
	/**
	 * Add Order
	 *
	 * Create a new order record in the database.
	 *
	 * @param array<string, mixed> $data array of data
	 *
	 * @return int returns the primary key of the new order record
	 *
	 * @example
	 *
	 * $order_data = [
	 *     'subscription_id'        => 1,
	 *     'invoice_prefix'         => 'INV-',
	 *     'store_id'               => 1,
	 *     'store_name'             => 'Your Store',
	 *     'store_url'              => '',
	 *     'customer_id'            => 1,
	 *     'customer_group_id'      => 1,
	 *     'firstname'              => 'John',
	 *     'lastname'               => 'Doe',
	 *     'email'                  => 'demo@opencart.com',
	 *     'telephone'              => '1234567890',
	 *     'custom_field'           => [],
	 *     'payment_address_id'     => 1,
	 *     'payment_firstname'      => 'John',
	 *     'payment_lastname'       => 'Doe',
	 *     'payment_company'        => '',
	 *     'payment_address_1'      => 'Address 1',
	 *     'payment_address_2'      => 'Address 2',
	 *     'payment_city'           => '',
	 *     'payment_postcode'       => '',
	 *     'payment_country'        => 'United Kingdom',
	 *     'payment_country_id'     => 222,
	 *     'payment_zone'           => 'Lancashire',
	 *     'payment_zone_id'        => 3563,
	 *     'payment_address_format' => '',
	 *     'payment_custom_field'   => [],
	 *     'payment_method'         => [
	 *         'name' => 'Payment Name',
	 *         'code' => 'Payment Code'
	 *      ],
	 *      'shipping_address_id'     => 1,
	 *      'shipping_firstname'      => 'John',
	 *      'shipping_lastname'       => 'Doe',
	 *      'shipping_company'        => '',
	 *      'shipping_address_1'      => 'Address 1',
	 *      'shipping_address_2'      => 'Address 2',
	 *      'shipping_city'           => '',
	 *      'shipping_postcode'       => '',
	 *      'shipping_country'        => 'United Kingdom',
	 *      'shipping_country_id'     => 222,
	 *      'shipping_zone'           => 'Lancashire',
	 *      'shipping_zone_id'        => 3563,
	 *      'shipping_address_format' => '',
	 *      'shipping_custom_field'   => [],
	 *      'shipping_method'         => [
	 *          'name' => 'Shipping Name',
	 *          'code' => 'Shipping Code'
	 *      ],
	 *      'comment'         => '',
	 *      'total'           => '0.0000',
	 *      'affiliate_id'    => 0,
	 *      'commission'      => '0.0000',
	 *      'marketing_id'    => 0,
	 *      'tracking'        => '',
	 *      'language_id'     => 1,
	 *      'language_code'   => 'en-gb',
	 *      'currency_id'     => 1,
	 *      'currency_code'   => 'USD',
	 *      'currency_value'  => '1.00000000',
	 *      'ip'              => '',
	 *      'forwarded_ip'    => '',
	 *      'user_agent'      => '',
	 *      'accept_language' => ''
	 * ];
	 *
	 * $this->load->model('checkout/order');
	 *
	 * $this->model_checkout_order->addOrder($order_data);
	 */
	/*public function addOrder(array $data): int {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET `subscription_id` = '" . (int)$data['subscription_id'] . "', `invoice_prefix` = '" . $this->db->escape($data['invoice_prefix']) . "', `store_id` = '" . (int)$data['store_id'] . "', `store_name` = '" . $this->db->escape($data['store_name']) . "', `store_url` = '" . $this->db->escape($data['store_url']) . "', `customer_id` = '" . (int)$data['customer_id'] . "', `customer_group_id` = '" . (int)$data['customer_group_id'] . "', `firstname` = '" . $this->db->escape($data['firstname']) . "', `lastname` = '" . $this->db->escape($data['lastname']) . "', `email` = '" . $this->db->escape($data['email']) . "', `telephone` = '" . $this->db->escape($data['telephone']) . "', `custom_field` = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : '') . "', `payment_address_id` = '" . (int)$data['payment_address_id'] . "', `payment_firstname` = '" . $this->db->escape($data['payment_firstname']) . "', `payment_lastname` = '" . $this->db->escape($data['payment_lastname']) . "', `payment_company` = '" . $this->db->escape($data['payment_company']) . "', `payment_address_1` = '" . $this->db->escape($data['payment_address_1']) . "', `payment_address_2` = '" . $this->db->escape($data['payment_address_2']) . "', `payment_city` = '" . $this->db->escape($data['payment_city']) . "', `payment_postcode` = '" . $this->db->escape($data['payment_postcode']) . "', `payment_country` = '" . $this->db->escape($data['payment_country']) . "', `payment_country_id` = '" . (int)$data['payment_country_id'] . "', `payment_zone` = '" . $this->db->escape($data['payment_zone']) . "', `payment_zone_id` = '" . (int)$data['payment_zone_id'] . "', `payment_address_format` = '" . $this->db->escape($data['payment_address_format']) . "', `payment_custom_field` = '" . $this->db->escape(isset($data['payment_custom_field']) ? json_encode($data['payment_custom_field']) : '') . "', `payment_method` = '" . $this->db->escape($data['payment_method'] ? json_encode($data['payment_method']) : '') . "', `shipping_address_id` = '" . (int)$data['shipping_address_id'] . "', `shipping_firstname` = '" . $this->db->escape($data['shipping_firstname']) . "', `shipping_lastname` = '" . $this->db->escape($data['shipping_lastname']) . "', `shipping_company` = '" . $this->db->escape($data['shipping_company']) . "', `shipping_address_1` = '" . $this->db->escape($data['shipping_address_1']) . "', `shipping_address_2` = '" . $this->db->escape($data['shipping_address_2']) . "', `shipping_city` = '" . $this->db->escape($data['shipping_city']) . "', `shipping_postcode` = '" . $this->db->escape($data['shipping_postcode']) . "', `shipping_country` = '" . $this->db->escape($data['shipping_country']) . "', `shipping_country_id` = '" . (int)$data['shipping_country_id'] . "', `shipping_zone` = '" . $this->db->escape($data['shipping_zone']) . "', `shipping_zone_id` = '" . (int)$data['shipping_zone_id'] . "', `shipping_address_format` = '" . $this->db->escape($data['shipping_address_format']) . "', `shipping_custom_field` = '" . $this->db->escape(isset($data['shipping_custom_field']) ? json_encode($data['shipping_custom_field']) : '') . "', `shipping_method` = '" . $this->db->escape($data['shipping_method'] ? json_encode($data['shipping_method']) : '') . "', `comment` = '" . $this->db->escape($data['comment']) . "', `total` = '" . (float)$data['total'] . "', `affiliate_id` = '" . (int)$data['affiliate_id'] . "', `commission` = '" . (float)$data['commission'] . "', `marketing_id` = '" . (int)$data['marketing_id'] . "', `tracking` = '" . $this->db->escape($data['tracking']) . "', `language_id` = '" . (int)$data['language_id'] . "', `language_code` = '" . $this->db->escape($data['language_code']) . "', `currency_id` = '" . (int)$data['currency_id'] . "', `currency_code` = '" . $this->db->escape($data['currency_code']) . "', `currency_value` = '" . (float)$data['currency_value'] . "', `ip` = '" . $this->db->escape((string)$data['ip']) . "', `forwarded_ip` = '" . $this->db->escape((string)$data['forwarded_ip']) . "', `user_agent` = '" . $this->db->escape((string)$data['user_agent']) . "', `accept_language` = '" . $this->db->escape((string)$data['accept_language']) . "', `date_added` = NOW(), `date_modified` = NOW()");

		$order_id = $this->db->getLastId();

		// Products
		if (!empty($data['products'])) {
			foreach ($data['products'] as $product) {
				$this->model_checkout_order->addProduct($order_id, $product);
			}
		}

		// Totals
		if (!empty($data['totals'])) {
			foreach ($data['totals'] as $total) {
				$this->model_checkout_order->addTotal($order_id, $total);
			}
		}

		return $order_id;
	}*/


public function addOrder(array $data, array $invoice_extra = []): int {
/*
foreach ($data['products'] as $product) {

    $product_id = (int)($product['product_id'] ?? 0);
    if ($product_id <= 0) continue;

    // Check if product is BOX
    $boxCheck = $this->db->query("
        SELECT product_id
        FROM `" . DB_PREFIX . "product`
        WHERE product_id = '" . (int)$product_id . "'
        AND upc IS NOT NULL
        AND upc != ''
        LIMIT 1
    ");

    if (!$boxCheck->num_rows) {
        continue; // not a box
    }

    // Fetch child products
    $children = $this->db->query("
        SELECT p.product_id, p.max_quantity,
               COALESCE(pp.pos_quentity, 0) AS pos_qty
        FROM `" . DB_PREFIX . "product` p
        LEFT JOIN `" . DB_PREFIX . "pts_pos_product` pp
            ON pp.product_id = p.product_id
        WHERE p.box_id = '" . (int)$product_id . "'
    ");

    foreach ($children->rows as $child) {

        $required  = (int)$child['max_quantity'];
        $available = (int)$child['pos_qty'];

        if ($available < $required) {
            throw new \Exception(
                'Box cannot be sold. Insufficient stock for one or more items.'
            );
        }
    }
}
*/

    $def = function($key, $default = '') use ($data) {
        return $data[$key] ?? $default;
    };


    $this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET
                                                            `invoice_prefix` = '" . $this->db->escape($def('invoice_prefix')) . "',
                                                            `invoice_no`     = '" . $this->db->escape($def('invoice_no')) . "',
                                                            `customer_id`    = '" . (int)$def('customer_id') . "',
                                                            `pre_order_id`       = '" . (int)$def('pre_order_id',0) . "',
                                                            `quote_id`       = '" . (int)$def('quote_id', 0) . "',
                                                            `customer_group_id` = '" . (int)$def('customer_group_id') . "',
                                                            `sellerId`       = '" . (int)$def('sellerId') . "',
                                                            `firstname`      = '" . $this->db->escape($def('firstname')) . "',
                                                            `lastname`       = '" . $this->db->escape($def('lastname')) . "',
                                                            `email`          = '" . $this->db->escape($def('email')) . "',
                                                            `telephone`      = '" . $this->db->escape($def('telephone')) . "',
                                                            `custom_field`   = '" . $this->db->escape(json_encode($def('custom_field', []))) . "',
                                                            `payment_firstname` = '" . $this->db->escape($def('firstname')) . "',
                                                            `payment_lastname`  = '" . $this->db->escape($def('lastname')) . "',
                                                            `payment_address_1`  = '" . $this->db->escape($def('payment_address_1')) . "',
                                                            `payment_address_2`  = '" . $this->db->escape($def('payment_address_2')) . "',
                                                            `payment_city`  = '" . $this->db->escape($def('payment_city')) . "',
                                                            `payment_postcode`  = '" . $this->db->escape($def('payment_postcode')) . "',
                                                            `payment_country`  = '" . $this->db->escape($def('payment_country')) . "',
                                                            `payment_zone`  = '" . $this->db->escape($def('payment_zone')) . "',
                                                            `payment_method`    = '" . $this->db->escape(json_encode($def('payment_method', []))) . "',
                                                            `comment`           = '" . $this->db->escape($def('comment')) . "',
                                                            `total`             = '" . (float)$def('total', 0) . "',
                                                            `language_id`       = 1,
                                                            `currency_id`       = 1,
                                                            `currency_code`     = 'INR',
                                                            `currency_value`    = '1.00000000',
                                                            `date_added`    = '" . $this->db->escape(date('Y-m-d H:i:s')) . "',
                                                            `date_modified` = '" . $this->db->escape(date('Y-m-d H:i:s')) . "'
                                                            ");

    $order_id = (int)$this->db->getLastId();

        $this->load->model('catalog/product');
        
        foreach ($data['products'] as $product) {
        
            $product_id = (int)($product['product_id'] ?? 0);
            $qty        = (int)($product['quantity'] ?? 1);
        
            if ($product_id <= 0 || $qty <= 0) continue;
        
            // Find box_id for this product
            $box = $this->db->query("SELECT box_id FROM `" . DB_PREFIX . "product` WHERE product_id = '" . $product_id . "'AND box_id IS NOT NULL AND box_id != '' LIMIT 1");
        
            if ($box->num_rows) {
                $box_product_id = (int)$box->row['box_id'];
        
                $this->model_checkout_order->decreaseBoxQuantity($box_product_id, $qty);
            }
        }
$posQtyMap = [];

foreach ($data['products'] as $product) {
    $pid = (int)($product['product_id'] ?? 0);
    $qty = (int)($product['quantity'] ?? 0);

    if ($pid <= 0 || $qty <= 0) continue;

    $posQtyMap[$pid] = ($posQtyMap[$pid] ?? 0) + $qty;
}

foreach ($posQtyMap as $product_id => $qty) {

    $info = $this->db->query("
        SELECT product_id, box_id, upc
        FROM `" . DB_PREFIX . "product`
        WHERE product_id = '" . (int)$product_id . "'
        LIMIT 1
    ");

    if (!$info->num_rows) continue;

    $is_box = !empty($info->row['upc']);
    $box_id = (int)$info->row['box_id'];

    if ($is_box) {

        // Box POS → 0
        $this->db->query("
            UPDATE `" . DB_PREFIX . "pts_pos_product`
            SET pos_quentity = 0
            WHERE product_id = '" . (int)$product_id . "'
        ");

        // All child products POS → 0
        $this->db->query("
            UPDATE `" . DB_PREFIX . "pts_pos_product`
            SET pos_quentity = 0
            WHERE product_id IN (
                SELECT product_id
                FROM `" . DB_PREFIX . "product`
                WHERE box_id = '" . (int)$product_id . "'
            )
        ");
    }


}


    if ($order_id === 0) return 0;

    // Insert products
    if (!empty($data['products'])) {
        foreach ($data['products'] as $product) {

            $excluded = !empty($product['excluded']) ? 1 : 0;

                                                $sql = "INSERT INTO `" . DB_PREFIX . "order_product` SET
                                                    `order_id`   = '" . (int)$order_id . "',
                                                    `product_id` = '" . (int)($product['product_id'] ?? 0) . "',
                                                    `name`       = '" . $this->db->escape($product['name'] ?? '') . "',
                                                    `model`      = '',
                                                    `quantity`   = '" . (int)($product['quantity'] ?? 1) . "',
                                                    `price`      = '" . (float)($product['price'] ?? 0) . "',
                                                    `total`      = '" . (float)($product['total'] ?? 0) . "',
                                                    `gst` = '" . (float)($product['gst'] ?? 0) . "',
                                                    `tax` = '" . (float)($product['tax'] ?? 0) . "',
                                                    `excluded`   = '" . (int)$excluded . "'";
                                                                
            $this->db->query($sql);
        }
    }

    // Insert totals
    if (!empty($data['totals'])) {
        foreach ($data['totals'] as $total) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "order_total` SET
                                                                    `order_id`   = '" . (int)$order_id . "',
                                                                    `code`       = '" . $this->db->escape($total['code']) . "',
                                                                    `title`      = '" . $this->db->escape($total['title']) . "',
                                                                    `value`      = '" . (float)$total['value'] . "',
                                                                    `sort_order` = '" . (int)$total['sort_order'] . "'");
        }
    }
    
           $returnable_balance = (float)($invoice_extra['returnable_balance'] ?? 0);

    // Insert invoice extra fields
    if (!empty($invoice_extra)) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "order_invoice` SET
                                                                        `order_id`           = '" . (int)$order_id . "',
                                                                        `customer_group_id`  = '" . (int)($invoice_extra['customer_group_id'] ?? 0) . "',
                                                                        `cash_amount`        = '" . (float)$invoice_extra['cash_amount'] . "',
                                                                        `upi_amount`         = '" . (float)$invoice_extra['upi_amount'] . "',
                                                                        `coupon`             = '" . $this->db->escape($invoice_extra['coupon']) . "',
                                                                        `credit_points`      = '" . (float)$invoice_extra['credit_points'] . "',
                                                                        `rewards`      = '" . (float)$invoice_extra['creditpointsused'] . "',
                                                                        `discount`           = '" . (float)$invoice_extra['discount'] . "',
                                                                        `number_of_items`    = '" . (int)$invoice_extra['number_of_items'] . "',
                                                                        `quantity_of_items`  = '" . (int)$invoice_extra['quantity_of_items'] . "',
                                                                        `sub_total`          = '" . (float)$invoice_extra['sub_total'] . "',
                                                                        `total_tax`          = '" . (float)$invoice_extra['total_tax'] . "',
                                                                        `roundoff_amount`    = '" . (float)$invoice_extra['roundoff_amount'] . "',
                                                                        `amount_through`     = '" . $this->db->escape($invoice_extra['amount_through']) . "',
                                                                        `pending_amount`     = '" . (float)$invoice_extra['pending_amount'] . "',
                                                                        `returnable_balance` = '" . $returnable_balance . "',
                                                                        `advance_used` = '" . (float)($invoice_extra['advance_used'] ?? 0) . "',
                                                                        `total_received`     = '" . (float)$invoice_extra['total_received'] . "',
                                                                        `balance`            = '" . (float)$invoice_extra['balance'] . "',
                                                                        `date_added`         = NOW()");
    }
    
    if (!empty($data['custom_fields'])) {
    foreach ($data['custom_fields'] as $field) {
        $name  = $this->db->escape($field['name'] ?? '');
        $value = (float)($field['value'] ?? 0); // numeric

        if ($name === '') continue;

        $sql = "INSERT INTO `" . DB_PREFIX . "order_tax_details` SET
                                                                `order_id` = '" . (int)$order_id . "',
                                                                `name`     = '" . $name . "',
                                                                `value`    = '" . $value . "'";
        $this->db->query($sql);
    }
    
    
}

foreach ($posQtyMap as $product_id => $qty) {

    $info = $this->db->query("SELECT upc
                                FROM `" . DB_PREFIX . "product`
                                WHERE product_id = '" . (int)$product_id . "'
                                LIMIT 1
    ");

    if (!$info->num_rows) continue;

    if (!empty($info->row['upc'])) {

        $this->db->query(" UPDATE `" . DB_PREFIX . "pts_pos_product`
                                                    SET pos_quentity = 1
                                                    WHERE product_id = '" . (int)$product_id . "'
        ");
    }
}


    return $order_id;
}

public function editPreviousOrder(int $order_id, array $data, array $invoice_extra = []): bool
{
    if ($order_id <= 0) return false;

    $this->db->query("START TRANSACTION");

    try {
        
         if ($order_id > 0) {

            $oldProducts = $this->db->query("
                SELECT product_id, quantity
                FROM `" . DB_PREFIX . "order_product`
                WHERE order_id = '" . (int)$order_id . "'
            ")->rows;

            foreach ($oldProducts as $old) {
                $product_id = (int)$old['product_id'];
                $qty        = (int)$old['quantity'];

                if ($product_id <= 0 || $qty <= 0) continue;

                $this->db->query("
                    UPDATE `" . DB_PREFIX . "pts_pos_product`
                    SET pos_quentity = pos_quentity + $qty
                    WHERE product_id = '" . (int)$product_id . "'
                ");
            }

        $this->db->query("
            UPDATE `" . DB_PREFIX . "order` SET
                customer_id        = '" . (int)$data['customer_id'] . "',
                customer_group_id  = '" . (int)$data['customer_group_id'] . "',
                sellerId           = '" . (int)$data['sellerId'] . "',
                firstname          = '" . $this->db->escape($data['firstname']) . "',
                lastname           = '" . $this->db->escape($data['lastname']) . "',
                email              = '" . $this->db->escape($data['email']) . "',
                telephone          = '" . $this->db->escape($data['telephone']) . "',
                payment_method     = '" . $this->db->escape(json_encode($data['payment_method'])) . "',
                comment             = '" . $this->db->escape($data['comment']) . "',
                total               = '" . (float)$data['total'] . "',
                date_modified       = NOW()
            WHERE order_id = '" . (int)$order_id . "'
        ");

        $this->db->query("DELETE FROM `" . DB_PREFIX . "order_product` WHERE order_id = '$order_id'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "order_total` WHERE order_id = '$order_id'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "order_invoice` WHERE order_id = '$order_id'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "order_tax_details` WHERE order_id = '$order_id'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "customer_reward` WHERE order_id = '$order_id'");
        
         }


        foreach ($data['products'] as $product) {
            $this->db->query("
                INSERT INTO `" . DB_PREFIX . "order_product` SET
                    order_id   = '$order_id',
                    product_id = '" . (int)$product['product_id'] . "',
                    name       = '" . $this->db->escape($product['name']) . "',
                    quantity   = '" . (int)$product['quantity'] . "',
                    price      = '" . (float)$product['price'] . "',
                    total      = '" . (float)$product['total'] . "',
                    excluded   = '" . (int)$product['excluded'] . "'
            ");
        }

        $this->db->query("
                            INSERT INTO `" . DB_PREFIX . "order_invoice` SET
                                order_id           = '" . (int)$order_id . "',
                                customer_group_id  = '" . (int)$invoice_extra['customer_group_id'] . "',
                                cash_amount        = '" . (float)$invoice_extra['cash_amount'] . "',
                                upi_amount         = '" . (float)$invoice_extra['upi_amount'] . "',
                                coupon             = '" . $this->db->escape($invoice_extra['coupon']) . "',
                                credit_points      = '" . (float)$invoice_extra['credit_points'] . "',
                                rewards            = '" . (float)$invoice_extra['creditpointsused'] . "',
                                discount           = '" . (float)$invoice_extra['discount'] . "',
                                number_of_items    = '" . (int)$invoice_extra['number_of_items'] . "',
                                quantity_of_items  = '" . (int)$invoice_extra['quantity_of_items'] . "',
                                sub_total          = '" . (float)$invoice_extra['sub_total'] . "',
                                total_tax          = '" . (float)$invoice_extra['total_tax'] . "',
                                roundoff_amount    = '" . (float)$invoice_extra['roundoff_amount'] . "',
                                amount_through     = '" . $this->db->escape($invoice_extra['amount_through']) . "',
                                pending_amount     = '" . (float)$invoice_extra['pending_amount'] . "',
                                returnable_balance = '" . (float)$invoice_extra['returnable_balance'] . "',
                                advance_used = '" . (float)$invoice_extra['advance_used'] . "',
                                total_received     = '" . (float)$invoice_extra['total_received'] . "',
                                balance            = '" . (float)$invoice_extra['balance'] . "',
                                date_added         = NOW()
                        ");


        if (!empty($data['custom_fields'])) {
            foreach ($data['custom_fields'] as $tax) {
                $this->db->query("
                    INSERT INTO `" . DB_PREFIX . "order_tax_details` SET
                        order_id = '$order_id',
                        name     = '" . $this->db->escape($tax['name']) . "',
                        value    = '" . (float)$tax['value'] . "'
                ");
            }
        }

        $this->db->query("COMMIT");
        return true;

    } catch (\Throwable $e) {
        $this->db->query("ROLLBACK");
        throw $e;
    }
}



    public function addQuoteOrder(array $data, array $invoice_extra = [], int $previousQuoteId = 0): int
    {
        $this->db->query("START TRANSACTION");
    
        try {
    
            $def = function ($k, $d = '') use ($data) {
                return isset($data[$k]) ? $data[$k] : $d;
            };
    
            if ($previousQuoteId > 0) {
    
                // Ensure quote exists
                $exists = $this->db->query("
                    SELECT order_id FROM `" . DB_PREFIX . "quote_order`
                    WHERE order_id = '" . (int)$previousQuoteId . "'
                ")->num_rows;
    
                if (!$exists) {
                    throw new Exception("Quote not found");
                }
    
                // Update header
                $this->db->query("UPDATE `" . DB_PREFIX . "quote_order` SET
                                                                        firstname     = '" . $this->db->escape($def('firstname')) . "',
                                                                        lastname      = '" . $this->db->escape($def('lastname')) . "',
                                                                        email         = '" . $this->db->escape($def('email')) . "',
                                                                        telephone     = '" . $this->db->escape($def('telephone')) . "',
                                                                        comment       = '" . $this->db->escape($def('comment')) . "',
                                                                        total         = '" . (float)$def('total', 0) . "',
                                                                        date_modified = NOW()
                                                                        WHERE order_id = '" . (int)$previousQuoteId . "'
                                                                ");
    
                $this->db->query("DELETE FROM `" . DB_PREFIX . "quote_product` WHERE order_id = '" . (int)$previousQuoteId . "'");
                $this->db->query("DELETE FROM `" . DB_PREFIX . "quote_invoice` WHERE order_id = '" . (int)$previousQuoteId . "'");
                $this->db->query("DELETE FROM `" . DB_PREFIX . "quote_tax_details` WHERE order_id = '" . (int)$previousQuoteId . "'");
    
                $quote_id = $previousQuoteId;
            }
    
            else {
    
                $this->db->query("INSERT INTO `" . DB_PREFIX . "quote_order` SET
                                                                            invoice_prefix    = '" . $this->db->escape($def('invoice_prefix', 'SGC-')) . "',
                                                                            invoice_no        = '" . $this->db->escape($def('invoice_no', '')) . "',
                                                                            customer_id       = 0,
                                                                            pre_order_id      = 0,
                                                                            customer_group_id = '" . (int)$def('customer_group_id') . "',
                                                                            sellerId          = '" . (int)$def('sellerId') . "',
                                                                            firstname         = '" . $this->db->escape($def('firstname')) . "',
                                                                            lastname          = '" . $this->db->escape($def('lastname')) . "',
                                                                            email             = '" . $this->db->escape($def('email')) . "',
                                                                            telephone         = '" . $this->db->escape($def('telephone')) . "',
                                                                            custom_field      = '" . $this->db->escape(json_encode($def('custom_field', []))) . "',
                                                                            comment           = '" . $this->db->escape($def('comment')) . "',
                                                                            total             = '" . (float)$def('total', 0) . "',
                                                                            order_status_id   = 1,
                                                                            currency_code     = 'INR',
                                                                            currency_value    = '1.00000000',
                                                                            date_added        = NOW(),
                                                                            date_modified     = NOW()
                                                                    ");
    
                $quote_id = (int)$this->db->getLastId();
                if (!$quote_id) {
                    throw new Exception("Failed to create quote");
                }
            }
    
            foreach ($data['products'] as $p) {
                $this->db->query("INSERT INTO `" . DB_PREFIX . "quote_product` SET
                                                                                order_id   = '" . (int)$quote_id . "',
                                                                                product_id = '" . (int)$p['product_id'] . "',
                                                                                name       = '" . $this->db->escape($p['name']) . "',
                                                                                quantity   = '" . (int)$p['quantity'] . "',
                                                                                price      = '" . (float)$p['price'] . "',
                                                                                total      = '" . (float)$p['total'] . "',
                                                                                excluded   = '" . (!empty($p['excluded']) ? 1 : 0) . "'
                                                                        ");
            }
    
            $this->db->query("INSERT INTO `" . DB_PREFIX . "quote_invoice` SET
                                                                            order_id          = '" . (int)$quote_id . "',
                                                                            customer_group_id = '" . (int)$invoice_extra['customer_group_id'] . "',
                                                                            discount          = '" . (float)$invoice_extra['discount'] . "',
                                                                            number_of_items   = '" . (int)$invoice_extra['number_of_items'] . "',
                                                                            quantity_of_items = '" . (int)$invoice_extra['quantity_of_items'] . "',
                                                                            sub_total         = '" . (float)$invoice_extra['sub_total'] . "',
                                                                            total_tax         = '" . (float)$invoice_extra['total_tax'] . "',
                                                                            total_received    = 0,
                                                                            balance           = 0,
                                                                            date_added        = NOW()
                                                                    ");
    
            $this->db->query("COMMIT");
    
            return $quote_id;
    
        } catch (Throwable $e) {
            $this->db->query("ROLLBACK");
            throw $e;
        }
    }
    
    
    public function cancelQuoteOrder(int $order_id): bool
{
    $this->db->query("START TRANSACTION");

    try {

        $query = $this->db->query("
            SELECT order_id, order_status_id 
            FROM `" . DB_PREFIX . "quote_order`
            WHERE order_id = '" . (int)$order_id . "'
        ");

        if (!$query->num_rows) {
            return false;
        }

        if ((int)$query->row['order_status_id'] === 7) {
            return false;
        }

        $this->db->query("
            UPDATE `" . DB_PREFIX . "quote_order`
            SET order_status_id = 7,
                date_modified = NOW()
            WHERE order_id = '" . (int)$order_id . "'
        ");

        $this->db->query("COMMIT");

        return true;

    } catch (Throwable $e) {
        $this->db->query("ROLLBACK");
        throw $e;
    }
}


public function completeQuote(int $quote_id): bool
{
    if ($quote_id <= 0) {
        return false;
    }

    $query = $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "quote_order` WHERE order_id = '" . (int)$quote_id . "'");

    if (!$query->num_rows) {
        return false;
    }

    $this->db->query("UPDATE `" . DB_PREFIX . "quote_order` SET 
                                                            order_status_id = 5,
                                                            date_modified = NOW()
                                                            WHERE order_id = '" . (int)$quote_id . "'
                                                    ");

    return true;
}

public function addPOSCustomer($input, $data) {

    $telephone = trim($input['telephone']);
    $firstname = trim($data['firstname']);
    $lastname  = trim($data['lastname']);
    $email     = isset($data['email']) ? trim($data['email']) : (isset($input['email']) ? trim($input['email']) : '');

    // 1) Check customer by telephone
    $check = $this->db->query("SELECT customer_id FROM " . DB_PREFIX . "customer 
                               WHERE telephone = '" . $this->db->escape($telephone) . "' 
                               LIMIT 1");

    // -------------------------------
    // IF CUSTOMER EXISTS → UPDATE
    // -------------------------------
    if ($check->num_rows) {

        $customer_id = (int)$check->row['customer_id'];

       $emailSql = ($email !== '') 
    ? "'" . $this->db->escape($email) . "'" 
    : "''";

        // update customer details
        $this->db->query("UPDATE " . DB_PREFIX . "customer 
                          SET firstname = '" . $this->db->escape($firstname) . "',
                              lastname  = '" . $this->db->escape($lastname) . "',
                              email     = " . $emailSql . ",
                              password  = ''
                          WHERE customer_id = '" . $customer_id . "'");

        // ensure wallet row exists
        $walletCheck = $this->db->query("SELECT customerid FROM " . DB_PREFIX . "manage_wallet 
                                         WHERE customerid = '" . $customer_id . "' LIMIT 1");

        if (!$walletCheck->num_rows) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "manage_wallet 
                              SET customerid = '" . $customer_id . "'");
        }

        // return updated row
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer c 
                                   LEFT JOIN " . DB_PREFIX . "manage_wallet w 
                                   ON c.customer_id = w.customerid 
                                   WHERE c.customer_id = '" . $customer_id . "'");

        return $query->row;
    }

    // -------------------------------
    // IF NOT EXISTS → INSERT NEW
    // -------------------------------
    $emailSql = ($email !== '') 
    ? "'" . $this->db->escape($email) . "'" 
    : "''";

    $this->db->query("INSERT INTO " . DB_PREFIX . "customer 
                  SET customer_group_id = 1,
                      language_id       = 1,
                      status            = 1,
                      firstname         = '" . $this->db->escape($firstname) . "', 
                      lastname          = '" . $this->db->escape($lastname) . "', 
                      email             = " . $emailSql . ", 
                      password          = '',
                      telephone         = '" . $this->db->escape($telephone) . "', 
                      date_added        = NOW()");


    $customer_id = $this->db->getLastId();

    // create wallet row
    $this->db->query("INSERT INTO " . DB_PREFIX . "manage_wallet 
                      SET customerid = '" . (int)$customer_id . "'");

    // return row
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer c 
                               LEFT JOIN " . DB_PREFIX . "manage_wallet w 
                               ON c.customer_id = w.customerid 
                               WHERE c.customer_id = '" . (int)$customer_id . "'");

    return $query->row;
}

public function doWalletCredit($credit)
{
    if ($credit['transactiontype'] == 'DEBIT') {

        $this->db->query("UPDATE `" . DB_PREFIX . "manage_wallet`
            SET amount = amount - " . (float)$credit['amount'] . "
            WHERE customerid = '" . (int)$credit['customerid'] . "'");

    } else {

        $this->db->query("UPDATE `" . DB_PREFIX . "manage_wallet`
            SET amount = amount + " . (float)$credit['amount'] . "
            WHERE customerid = '" . (int)$credit['customerid'] . "'");
    }

    if ($this->db->countAffected() > 0) {

        $balance = $this->getWalletInfo($credit['customerid'])['amount'];

        $this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET
            customer_id = '" . $credit['customerid'] . "',
            order_id = '".$credit['order_id']."',
            description = '" . $credit['description'] . "',
            transactiontype = '" . $credit['transactiontype'] . "',
            transactionsubtype = '".$credit['transactionsubtype']."',
            amount = '" . $credit['amount'] . "',
            balance = '" . $balance . "',
            date_added = NOW(),
            txtid = '" . $credit['txtid'] . "'");

        return true;

    } else {
        return false;
    }
}
		
		public function getWalletInfo($customerid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_wallet` o WHERE o.customerid = '" . (int)$customerid . "'");
    	if ($query->num_rows) 
    	{
    	    $result=$query->row;
    	    $result['exstatus']=true;
    	    return $result;
    	} else {
    		$result['exstatus']=false;
    		return $result;
    	}
    }
    
public function walletTradeHistory($customerid,$raw=array())
   {
       	$sql = "SELECT * FROM " . DB_PREFIX . "customer_transaction p WHERE p.customer_id = '" . (int)$customerid. "'";

		if (!empty($raw['from_date'])) {
			$sql .= " AND date(p.date_added) >= '".$this->db->escape($raw['from_date'])."'";
		}
		
		if (!empty($raw['to_date'])) {
			$sql .= " AND date(p.date_added) <= '".$this->db->escape($raw['to_date'])."'";
		}
		
		if (!empty($raw['txtid'])) {
			$sql .= " AND p.txtid = '" . $this->db->escape($raw['txtid']) . "%'";
		}

		if (!empty($raw['transactiontype'])) {
    			$sql .= " AND p.transactiontype = 'CREDIT'";
		}
		
		if (!empty($raw['transactionsubtype'])) {
    			$sql .= " AND p.transactionsubtype = 'TRADE'";
		}
        $sql .= " ORDER BY p.customer_transaction_id DESC";
		$query = $this->db->query($sql);
		return $query->rows;
   }
   
   public function doWalletAepsCredit($credit)
{
    // CREDIT = increase AEPS wallet
    if ($credit['transactiontype'] == 'CREDIT') {

        $this->db->query("UPDATE `" . DB_PREFIX . "manage_wallet`
            SET aeps_amount = IFNULL(aeps_amount,0) + " . (float)$credit['amount'] . "
            WHERE customerid = '" . (int)$credit['customerid'] . "'");

    } 
    // DEBIT = decrease AEPS wallet
    else if ($credit['transactiontype'] == 'DEBIT') {

        $this->db->query("UPDATE `" . DB_PREFIX . "manage_wallet`
            SET aeps_amount = IFNULL(aeps_amount,0) - " . (float)$credit['amount'] . "
            WHERE customerid = '" . (int)$credit['customerid'] . "'");
    }

    if ($this->db->countAffected() > 0) {

        $walletInfo = $this->getWalletInfo($credit['customerid']);
        $balance = isset($walletInfo['aeps_amount']) ? (float)$walletInfo['aeps_amount'] : 0;

        $this->db->query("INSERT INTO `" . DB_PREFIX . "customer_transaction`
            SET customer_id = '" . (int)$credit['customerid'] . "',
                order_id = '" . (int)$credit['order_id'] . "',
                description = '" . $this->db->escape($credit['description']) . "',
                transactiontype = '" . $this->db->escape($credit['transactiontype']) . "',
                transactionsubtype = '" . $this->db->escape($credit['transactionsubtype']) . "',
                amount = '" . (float)$credit['amount'] . "',
                balance = '" . (float)$balance . "',
                date_added = NOW(),
                txtid = '" . $this->db->escape($credit['txtid']) . "'");

        return true;
    }

    return false;
}


 public function getCustomerByMobile($mobile) {
        $query = $this->db->query("
            SELECT *
            FROM " . DB_PREFIX . "customer
            WHERE telephone = '" . $this->db->escape($mobile) . "'
            LIMIT 1
        ");

        return $query->row;
    }

    // 2️⃣ Get wallet details by customer_id
    public function getWalletByCustomerId($customer_id) {
        $query = $this->db->query("
            SELECT *
            FROM " . DB_PREFIX . "manage_wallet
            WHERE customerid = " . (int)$customer_id
        );

        return $query->row;
    }

    // 3️⃣ Get transactions by customer_id
    public function getTransactionsByCustomerId($customer_id) {
        $query = $this->db->query("
            SELECT *
            FROM " . DB_PREFIX . "customer_transaction
            WHERE customer_id = " . (int)$customer_id . "
            ORDER BY date_added DESC
        ");

        return $query->rows;
    }

public function decreaseBoxQuantity(int $box_product_id, int $qty): void {
    $this->db->query("
        UPDATE `" . DB_PREFIX . "product`
        SET max_quantity = GREATEST(max_quantity - " . (int)$qty . ", 0)
        WHERE product_id = '" . (int)$box_product_id . "'
    ");
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

$sql = "SELECT

/* STATUS 5 CASH */
(
    COALESCE(SUM(
        CASE 
            WHEN o.order_status_id = 5 
            THEN oi.cash_amount
            ELSE 0
        END
    ),0)
    -
    COALESCE(SUM(
        CASE 
            WHEN o.order_status_id = 5 
            AND oi.cash_amount > 0 
            AND oi.upi_amount = 0
            THEN oi.returnable_balance
            ELSE 0
        END
    ),0)
) AS status5_cash,


/* STATUS 5 UPI */
(
    COALESCE(SUM(
        CASE 
            WHEN o.order_status_id = 5 
            THEN oi.upi_amount
            ELSE 0
        END
    ),0)
    -
    COALESCE(SUM(
        CASE 
            WHEN o.order_status_id = 5 
            AND oi.upi_amount > 0 
            AND oi.cash_amount = 0
            THEN oi.returnable_balance
            ELSE 0
        END
    ),0)
) AS status5_upi,


/* STATUS 6 CASH */
(
    COALESCE(SUM(
        CASE 
            WHEN o.order_status_id = 6 
            THEN oi.cash_amount
            ELSE 0
        END
    ),0)
    -
    COALESCE(SUM(
        CASE 
            WHEN o.order_status_id = 6 
            AND oi.cash_amount > 0 
            AND oi.upi_amount = 0
            THEN oi.returnable_balance
            ELSE 0
        END
    ),0)
) AS status6_cash,


/* STATUS 6 UPI */
(
    COALESCE(SUM(
        CASE 
            WHEN o.order_status_id = 6 
            THEN oi.upi_amount
            ELSE 0
        END
    ),0)
    -
    COALESCE(SUM(
        CASE 
            WHEN o.order_status_id = 6 
            AND oi.upi_amount > 0 
            AND oi.cash_amount = 0
            THEN oi.returnable_balance
            ELSE 0
        END
    ),0)
) AS status6_upi,


/* SUBTOTAL */

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


/* TOTAL RECEIVED */

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


/* RETURNABLE */

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


/* BALANCE */

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
AND o.customer_group_id = '" . (int)$agentId . "'";

return $this->db->query($sql)->row;

}


public function getQuoteFullDetails(int $quote_id)
{
 
    $quote = $this->db->query("SELECT qo.*,
            qs.name AS quote_status
        FROM `" . DB_PREFIX . "quote_order` qo
        LEFT JOIN `" . DB_PREFIX . "order_status` qs
            ON qs.order_status_id = qo.order_status_id
            AND qs.language_id = '" . (int)$this->config->get('config_language_id') . "'
        WHERE qo.order_id = '" . (int)$quote_id . "'
    ")->row;

    if (!$quote) {
        return false;
    }

    // Products
    $products = $this->db->query("
        SELECT *
        FROM `" . DB_PREFIX . "quote_product`
        WHERE order_id = '" . (int)$quote_id . "'
    ")->rows;

    // Invoice
    $invoice = $this->db->query("
        SELECT *
        FROM `" . DB_PREFIX . "quote_invoice`
        WHERE order_id = '" . (int)$quote_id . "'
    ")->row;

    // Tax
    $tax = $this->db->query("
        SELECT *
        FROM `" . DB_PREFIX . "quote_tax_details`
        WHERE order_id = '" . (int)$quote_id . "'
    ")->rows;

    return [
        "quote_info"  => $quote,
        "products"    => $products,
        "invoice"     => $invoice,
        "tax_details" => $tax
    ];
}


public function getQuotesByDateRange($agentId, $from_date = '', $to_date = '', $order_id = '', $mobile = '', $name = '')

{
    $sql = "SELECT qo.order_id FROM `" . DB_PREFIX . "quote_order` qo WHERE qo.customer_group_id = '" . (int)$agentId . "'";

    $isSearch = !empty($quote_id) || !empty($mobile) || !empty($name);

    if (!$isSearch && !empty($from_date) && !empty($to_date)) {
        $sql .= " AND DATE(qo.date_added) >= '" . $this->db->escape($from_date) . "'";
        $sql .= " AND DATE(qo.date_added) <= '" . $this->db->escape($to_date) . "'";
    }

    if (!empty($quote_id)) {
        $sql .= " AND qo.order_id LIKE '%" . $this->db->escape($quote_id) . "%'";
    }

    if (!empty($mobile)) {
        $sql .= " AND qo.telephone LIKE '%" . $this->db->escape($mobile) . "%'";
    }

    if (!empty($name)) {
        $sql .= " AND (
            qo.firstname LIKE '%" . $this->db->escape($name) . "%'
            OR qo.lastname LIKE '%" . $this->db->escape($name) . "%'
            OR CONCAT(qo.firstname,' ',qo.lastname) LIKE '%" . $this->db->escape($name) . "%'
        )";
    }

    $sql .= " ORDER BY qo.order_id DESC";

    $quotes = $this->db->query($sql)->rows;

    $full = [];

    foreach ($quotes as $q) {
        $full[] = $this->getQuoteFullDetails((int)$q['order_id']);
    }

    return $full;
}



public function getQuoteTotalsByDateRange($from_date, $to_date, $agentId)
{
    $sql = "
        SELECT
            COALESCE(SUM(qi.sub_total), 0)        AS total_subtotal,
            COALESCE(SUM(qi.discount), 0)         AS total_discount,
            COALESCE(SUM(qi.total_tax), 0)        AS total_tax,
            COUNT(qo.order_id)                    AS total_quotes
        FROM `" . DB_PREFIX . "quote_order` qo
        INNER JOIN `" . DB_PREFIX . "quote_invoice` qi
            ON qi.order_id = qo.order_id
        WHERE DATE(qo.date_added) >= '" . $this->db->escape($from_date) . "'
          AND DATE(qo.date_added) <= '" . $this->db->escape($to_date) . "'
          AND qo.customer_group_id = '" . (int)$agentId . "'
    ";

    return $this->db->query($sql)->row;
}


public function getQuoteByAgent($order_id, $agent_id)
{
    // Step 1: Get agent group (wholesale / retail)
    $agent = $this->db->query("
        SELECT customer_group_id
        FROM `" . DB_PREFIX . "customer`
        WHERE customer_id = '" . (int)$agent_id . "'
    ")->row;

    if (!$agent) {
        return false;
    }

    $agent_group = (int)$agent['customer_group_id'];

    // Step 2: Get quote
    $quote = $this->db->query("
        SELECT *
        FROM `" . DB_PREFIX . "quote_order`
        WHERE order_id = '" . (int)$order_id . "'
    ")->row;

    if (!$quote) {
        return false;
    }

    // Step 3: Find quote agent group
    $quote_agent = $this->db->query("
        SELECT customer_group_id
        FROM `" . DB_PREFIX . "customer`
        WHERE customer_id = '" . (int)$quote['customer_group_id'] . "'
    ")->row;

    if (!$quote_agent) {
        return false;
    }

    $quote_group = (int)$quote_agent['customer_group_id'];

    // Step 4: Validate wholesale / retail match
    if ($agent_group != $quote_group) {
        return false;
    }

    // Products
    $products = $this->db->query("
        SELECT *
        FROM `" . DB_PREFIX . "quote_product`
        WHERE order_id = '" . (int)$order_id . "'
    ")->rows;

    // Invoice
    $invoice = $this->db->query("
        SELECT *
        FROM `" . DB_PREFIX . "quote_invoice`
        WHERE order_id = '" . (int)$order_id . "'
    ")->row;

    // Tax
    $tax = $this->db->query("
        SELECT *
        FROM `" . DB_PREFIX . "quote_tax_details`
        WHERE order_id = '" . (int)$order_id . "'
    ")->rows;

    return [
        "quote_info"  => $quote,
        "products"    => $products,
        "invoice"     => $invoice,
        "tax_details" => $tax
    ];
}


public function getEditOrderDetails($order_id, $agent_id)
{
    // Step 1: Get agent group
    $agent = $this->db->query("
        SELECT customer_group_id
        FROM `" . DB_PREFIX . "customer`
        WHERE customer_id = '" . (int)$agent_id . "'
    ")->row;

    if (!$agent) {
        return false;
    }

    $agent_group = (int)$agent['customer_group_id'];

    // Step 2: Get order
    $order = $this->db->query("
        SELECT *
        FROM `" . DB_PREFIX . "order`
        WHERE order_id = '" . (int)$order_id . "'
    ")->row;

    if (!$order) {
        return false;
    }

    // Step 3: Get order agent group
    $order_agent = $this->db->query("
        SELECT customer_group_id
        FROM `" . DB_PREFIX . "customer`
        WHERE customer_id = '" . (int)$order['customer_group_id'] . "'
    ")->row;

    if (!$order_agent) {
        return false;
    }

    $order_group = (int)$order_agent['customer_group_id'];

    // Step 4: Validate wholesale/retail match
    if ($agent_group != $order_group) {
        return false;
    }

    // Products
    $products = $this->db->query("
        SELECT *
        FROM `" . DB_PREFIX . "order_product`
        WHERE order_id = '" . (int)$order_id . "'
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

    // Totals
    $totals = $this->db->query("
        SELECT *
        FROM `" . DB_PREFIX . "order_total`
        WHERE order_id = '" . (int)$order_id . "'
    ")->rows;

    return [
        "order_info" => $order,
        "products" => $products,
        "invoice" => $invoice,
        "tax_details" => $tax,
        "totals" => $totals
    ];
}

public function getTables() {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pos_table` WHERE status = '1' ORDER BY pos_table_id ASC");

        return $query->rows;
    }
    
public function addLiveTableInfo($table_id, $order_id, $members) {
    $this->db->query("INSERT INTO `" . DB_PREFIX . "pos_table_info` SET 
                                                                      table_id = '" . (int)$table_id . "',
                                                                      order_id = '" . $this->db->escape($order_id) . "',
                                                                      members = '" . (int)$members . "',
                                                                      added_at = NOW()");
}


public function getLiveTableData($table_id) {
    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pos_table_info` WHERE table_id = '" . (int)$table_id . "'");
    return $query->rows;
}

public function clearLiveTableOrder($id) {
    $this->db->query("DELETE FROM `" . DB_PREFIX . "pos_table_info`  WHERE id = '" . (int)$id . "'");
}

public function addHoldOrder($data)
{
    $order_id = (int)$data['order_id']; 
    $name  = $product_info['name'] ?? '';
    $price = $product_info['price'] ?? 0;
    $qty   = (int)$product['quantity'];
    foreach ($data['products'] as $product) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "order_product` SET
                                                    order_id   = '" . $order_id . "',
                                                    product_id = '" . (int)$product['product_id'] . "',
                                                    name       = '" . $this->db->escape($product['name']) . "',
                                                    quantity   = '" . (int)$product['quantity'] . "',
                                                    price      = '" . (float)$product['price'] . "',
                                                    total      = '" . (float)$product['total'] . "'");
    }

    return $order_id;
}


public function updateHoldOrder($data)
{
    $order_id = (int)$data['order_id'];

    // Delete previous products
    $this->db->query("DELETE FROM `" . DB_PREFIX . "order_product` WHERE order_id = '" . $order_id . "'");

    // Insert new products
    if (!empty($data['products'])) {
        foreach ($data['products'] as $product) {

            $sql = "INSERT INTO `" . DB_PREFIX . "order_product` SET
                                                    `order_id`   = '" . $order_id . "',
                                                    `product_id` = '" . (int)($product['product_id'] ?? 0) . "',
                                                    `name`       = '" . $this->db->escape($product['name'] ?? '') . "',
                                                    `model`      = '',
                                                    `quantity`   = '" . (int)($product['quantity'] ?? 1) . "',
                                                    `price`      = '" . (float)($product['price'] ?? 0) . "',
                                                    `total`      = '" . (float)($product['total'] ?? 0) . "'";

            $this->db->query($sql);
        }
    }
}


	
	public function getOrderdetails(int $order_id): array {

    // Fetch main order row
    $order_query = $this->db->query("
        SELECT *,
        (
            SELECT os.name 
            FROM `" . DB_PREFIX . "order_status` os 
            WHERE os.order_status_id = o.order_status_id 
            AND os.language_id = '" . (int)$this->config->get('config_language_id') . "'
        ) AS order_status
        FROM `" . DB_PREFIX . "order` o 
        WHERE o.order_id = '" . (int)$order_id . "'
    ");

    if (!$order_query->num_rows) {
        return [];
    }

    $order = $order_query->row;

    // --- Country (Payment)
    $this->load->model('localisation/country');
    $country_info = $this->model_localisation_country->getCountry($order['payment_country_id']);

    $payment_iso_code_2 = $country_info['iso_code_2'] ?? '';
    $payment_iso_code_3 = $country_info['iso_code_3'] ?? '';

    // --- Zone (Payment)
    $this->load->model('localisation/zone');
    $zone_info = $this->model_localisation_zone->getZone($order['payment_zone_id']);
    $payment_zone_code = $zone_info['code'] ?? '';

    // --- Country (Shipping)
    $country_info = $this->model_localisation_country->getCountry($order['shipping_country_id']);
    $shipping_iso_code_2 = $country_info['iso_code_2'] ?? '';
    $shipping_iso_code_3 = $country_info['iso_code_3'] ?? '';

    // --- Zone (Shipping)
    $zone_info = $this->model_localisation_zone->getZone($order['shipping_zone_id']);
    $shipping_zone_code = $zone_info['code'] ?? '';

    // --- Reward Points
    $reward = 0;
    $products = $this->getProducts($order_id);
    foreach ($products as $p) {
        $reward += $p['reward'];
    }

    // --- Language
    $this->load->model('localisation/language');
    $language_info = $this->model_localisation_language->getLanguage($order['language_id']);
    $language_code = $language_info['code'] ?? $this->config->get('config_language');

    // =============================
    // ⭐ FETCH order_invoice TABLE
    // =============================
    $invoice_query = $this->db->query("
        SELECT *
        FROM `" . DB_PREFIX . "order_invoice`
        WHERE order_id = '" . (int)$order_id . "'
        LIMIT 1
    ");

    $invoice = $invoice_query->num_rows ? $invoice_query->row : [
        'cash_amount'        => 0,
        'upi_amount'         => 0,
        'coupon'             => '',
        'credit_points'      => 0,
        'discount'           => 0,
        'number_of_items'    => 0,
        'quantity_of_items'  => 0,
        'sub_total'          => 0,
        'total_tax'          => 0,
        'roundoff_amount'    => 0,
        'amount_through'     => '',
        'pending_amount'     => 0,
        'returnable_balance' => 0,
        'total_received'     => 0
    ];

    // =============================
    // ⭐ FINAL RETURN ARRAY
    // =============================

    return [

        // Products
        'products' => $products,

        // Custom fields
        'custom_field'          => $order['custom_field'] ? json_decode($order['custom_field'], true) : [],
        'payment_custom_field'  => $order['payment_custom_field'] ? json_decode($order['payment_custom_field'], true) : [],
        'shipping_custom_field' => $order['shipping_custom_field'] ? json_decode($order['shipping_custom_field'], true) : [],

        // Payment details
        'payment_zone_code'  => $payment_zone_code,
        'payment_iso_code_2' => $payment_iso_code_2,
        'payment_iso_code_3' => $payment_iso_code_3,
        'payment_method'     => $order['payment_method'] ? json_decode($order['payment_method'], true) : [],

        // Shipping details
        'shipping_zone_code'  => $shipping_zone_code,
        'shipping_iso_code_2' => $shipping_iso_code_2,
        'shipping_iso_code_3' => $shipping_iso_code_3,
        'shipping_method'     => $order['shipping_method'] ? json_decode($order['shipping_method'], true) : [],

        // Order other info
        'reward'        => $reward,
        'language_code' => $language_code

    ] + $order + $invoice;   // ⭐ MERGE ALL DATA INCLUDING INVOICE
}

public function getLastOrder($customer_id) {
    $query = $this->db->query("
        SELECT order_id, total 
        FROM `" . DB_PREFIX . "order`
        WHERE customer_id = '" . (int)$customer_id . "'
          AND order_status_id > 0
        ORDER BY order_id DESC
        LIMIT 1
    ");

    return $query->row;
}


public function getFullProductByBarcode($barcode) {

    $barcode = trim($barcode);

    if (!$barcode) {
        return false;
    }

    // Only allow 8 or 15 digits
    if (!preg_match('/^\d{8}$|^\d{15}$/', $barcode)) {
        return false;
    }

    // Find product_id by sku/upc
    if (strlen($barcode) == 8) {
        $q = $this->db->query("SELECT product_id FROM `" . DB_PREFIX . "product`
            WHERE sku = '" . $this->db->escape($barcode) . "'
            LIMIT 1");
    } else {
        $q = $this->db->query("SELECT product_id FROM `" . DB_PREFIX . "product`
            WHERE upc = '" . $this->db->escape($barcode) . "'
            LIMIT 1");
    }

    if (!$q->num_rows) {
        return false;
    }

    $product_id = (int)$q->row['product_id'];
    $language_id = (int)$this->config->get('config_language_id');

    // FULL product table + description
    $product = $this->db->query("
        SELECT 
            p.*,
            c.*,
            pd.name,
            pd.description,
            pd.meta_title,
            pd.meta_description,
            pd.meta_keyword,

            pos.id AS pos_id,
            pos.pos_quentity,
            pos.pos_status,

            ptc.category_id,
            cd.name AS category_name

        FROM `" . DB_PREFIX . "product` p

        LEFT JOIN `" . DB_PREFIX . "product_description` pd 
            ON (p.product_id = pd.product_id 
            AND pd.language_id = '" . (int)$language_id . "')

        LEFT JOIN `" . DB_PREFIX . "pts_pos_product` pos
            ON (p.product_id = pos.product_id)

        LEFT JOIN `" . DB_PREFIX . "product_to_category` ptc
            ON (p.product_id = ptc.product_id)
        LEFT JOIN `" . DB_PREFIX . "category` c
            ON (ptc.category_id = c.category_id )

        LEFT JOIN `" . DB_PREFIX . "category_description` cd
            ON (ptc.category_id = cd.category_id 
            AND cd.language_id = '" . (int)$language_id . "')

        WHERE p.product_id = '" . (int)$product_id . "'

        LIMIT 1
    ");

    if ($product->num_rows) {
        return $product->row; // returns full row data
    }

    return false;
}


public function cancelOrderFull(int $order_id): void
{
    $this->db->query("START TRANSACTION");

    try {
        $this->restorePosQuantity($order_id);
        $this->markOrderCancelled($order_id);
        $this->revertRewardPoints($order_id);

        $this->db->query("COMMIT");
    } catch (Throwable $e) {
        $this->db->query("ROLLBACK");
        throw $e;
    }
}

private function restorePosQuantity(int $order_id): void
{
    $products = $this->db->query(" SELECT product_id, quantity FROM `" . DB_PREFIX . "order_product`WHERE order_id = '" . (int)$order_id . "'");

    foreach ($products->rows as $p) {
        $this->db->query("UPDATE `" . DB_PREFIX . "pts_pos_product` SET pos_quentity = pos_quentity + " . (int)$p['quantity'] . " WHERE product_id = '" . (int)$p['product_id'] . "'");
    }
}

/*private function revertWalletTransactions(int $order_id): void
{
    $txns = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_transaction` WHERE order_id = '" . (int)$order_id . "'");

    foreach ($txns->rows as $t) {

        if ($t['transactionsubtype'] === 'TRADE') {
            $this->db->query("UPDATE `" . DB_PREFIX . "manage_wallet` SET amount = amount - " . (float)$t['amount'] . " WHERE customerid = '" . (int)$t['customer_id'] . "'");
        }

        if ($t['transactionsubtype'] === 'AEPS') {
            $this->db->query("UPDATE `" . DB_PREFIX . "manage_wallet` SET aeps_amount = aeps_amount - " . (float)$t['amount'] . "WHERE customerid = '" . (int)$t['customer_id'] . "'");
        }
    }

    // mark transactions cancelled
    $this->db->query("UPDATE `" . DB_PREFIX . "customer_transaction` SET description = CONCAT(description, ' (CANCELLED)')WHERE order_id = '" . (int)$order_id . "'");
}*/

private function markOrderCancelled(int $order_id): void
{
    // 7 = Cancelled (use your status id)
    $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = 7, date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");

    $this->db->query("INSERT INTO `" . DB_PREFIX . "order_history` SET order_id = '" . (int)$order_id . "', order_status_id = 7,notify = 0,comment = 'Order cancelled via POS',date_added = NOW()");
}

/*private function cleanupOrderData(int $order_id): void
{
    $tables = [
        'order_invoice',
        'order_tax_details',
        'order_total'
    ];

    foreach ($tables as $t) {
        $this->db->query("
            DELETE FROM `" . DB_PREFIX . $t . "`
            WHERE order_id = '" . (int)$order_id . "'
        ");
    }
}*/

public function isOrderCancelled(int $order_id): bool
{
    $q = $this->db->query("SELECT order_status_id FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "' LIMIT 1");

    return $q->num_rows && (int)$q->row['order_status_id'] === 7;
}

private function revertRewardPoints(int $order_id): void
{
    // Fetch reward entries for this order
    $rewards = $this->db->query(" SELECT customer_reward_id, customer_id, points, status FROM `" . DB_PREFIX . "customer_reward` WHERE order_id = '" . (int)$order_id . "'AND status = 'active'");

    if (!$rewards->num_rows) {
        return;
    }

    foreach ($rewards->rows as $r) {

        // Cancel earned rewards
        $this->db->query("UPDATE `" . DB_PREFIX . "customer_reward` SET status = 'clear', description = CONCAT(description, ' (ORDER CANCELLED)')WHERE customer_reward_id = '" . (int)$r['customer_reward_id'] . "'");
    }

    $invoice = $this->db->query("SELECT rewards FROM `" . DB_PREFIX . "order_invoice` WHERE order_id = '" . (int)$order_id . "'LIMIT 1");

    if ($invoice->num_rows && (float)$invoice->row['rewards'] > 0) {

        $used_points = (float)$invoice->row['rewards'];
        $customer_id = (int)$rewards->row['customer_id'];

        $this->db->query("INSERT INTO `" . DB_PREFIX . "customer_reward` SET customer_id = '" . $customer_id . "',order_id = '" . (int)$order_id . "',points = '" . $used_points . "',status = 'active',
                description = 'Reward points restored due to order cancellation',
                date_added = NOW()");
    }
}


public function updateDailyAmount() {

        $this->db->query("UPDATE `" . DB_PREFIX . "manage_wallet`
                                                            SET pre_amount = pre_amount + amount, amount = 0
                                                            WHERE customerid in (select customer_id from xwzk_pts_pos_agent where agent_status='2')
                                                              AND amount > 0");

        return $this->db->countAffected();
    }
    
    public function getQuoteOrderdetails(int $quote_id): array
    {
        $query = $this->db->query("SELECT qo.*, qi.* FROM `" . DB_PREFIX . "quote_order` qo LEFT JOIN `" . DB_PREFIX . "quote_invoice` qi ON qi.order_id = qo.order_id WHERE qo.order_id = '" . (int)$quote_id . "'");
    
        return $query->row ?? [];
    }

    public function getQuoteProducts(int $quote_id): array
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "quote_product` WHERE order_id = '" . (int)$quote_id . "'");
    
        return $query->rows;
    }
    
    
    public function getFullOrderTaxExemptData(int $order_id,float $tax_exempt_percent,float $gst_percent) 
    {

    $order = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "' LIMIT 1")->row;

    if (!$order) {
        return [];
    }

    $invoice = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_invoice` WHERE order_id = '" . (int)$order_id . "' LIMIT 1")->row;

    $products = $this->db->query(" SELECT * FROM `" . DB_PREFIX . "order_product` WHERE order_id = '" . (int)$order_id . "' AND excluded = 0")->rows;

    $items = [];
    $total_qty = 0;
    $total_taxable = 0;

    foreach ($products as $p) {

        $price = (float)$p['price'];
        $qty   = (int)$p['quantity'];

    
        $rate = ($price * $tax_exempt_percent) / 100;
        $taxable_value = $rate * $qty;

        $items[] = [
            'name'           => $p['name'],
            'qty'            => $qty,
            'rate'           => round($rate, 2),
            'gst'            => $gst_percent,
            'taxable_value'  => round($taxable_value, 2)
        ];

        $total_qty     += $qty;
        $total_taxable += $taxable_value;
    }

    return [
        'order_id'       => $order['order_id'],
        'invoice_no'     => $order['invoice_prefix'] . $order['invoice_no'],
        'customer_name'  => trim($order['firstname'] . ' ' . $order['lastname']),
        'telephone'      => $order['telephone'],
        'order_date'     => date('d-m-Y', strtotime($order['date_added'])),
        'sub_total'      => $invoice['sub_total'] ?? 0,
        'total_tax'      => $invoice['total_tax'] ?? 0,
        'discount'       => $invoice['discount'] ?? 0,
        'roundoff'       => $invoice['roundoff_amount'] ?? 0,
        'total_received' => $invoice['total_received'] ?? 0,
        'balance'        => $invoice['balance'] ?? 0,
        'products'       => $items,
        'total_qty'      => $total_qty,
        'total_taxable'  => round($total_taxable, 2),
        'gst_percent'    => $gst_percent,
        'exempt_percent' => $tax_exempt_percent
    ];
}

public function adjustWallet(
        int $customer_id,
        float $amount,
        string $transactiontype,
        string $transactionsubtype,
        string $description = ''
    ): bool {

        $user_id = (int)$this->customer->getId();

        if ($transactionsubtype === 'AEPS') {
            $column = 'aeps_amount';
        } else {
            $column = 'amount';
        }

        // Update wallet
        if ($transactiontype === 'DEBIT') {
            $this->db->query("
                UPDATE " . DB_PREFIX . "manage_wallet
                SET $column = $column - " . (float)$amount . "
                WHERE customerid = '" . (int)$customer_id . "'
            ");
        } else {
            $this->db->query("
                UPDATE " . DB_PREFIX . "manage_wallet
                SET $column = $column + " . (float)$amount . "
                WHERE customerid = '" . (int)$customer_id . "'
            ");
        }

        if (!$this->db->countAffected()) {
            return false;
        }

        // Get updated balance
        $wallet = $this->db->query("
            SELECT $column AS balance
            FROM " . DB_PREFIX . "manage_wallet
            WHERE customerid = '" . (int)$customer_id . "'
        ");

        $balance = (float)$wallet->row['balance'];

        // Insert transaction
        $this->db->query("
            INSERT INTO " . DB_PREFIX . "customer_transaction SET
                customer_id = '" . (int)$customer_id . "',
                order_id = '0',
                description = '" . $this->db->escape($description) . "',
                transactiontype = '" . $this->db->escape($transactiontype) . "',
                transactionsubtype = '" . $this->db->escape($transactionsubtype) . "',
                amount = '" . (float)$amount . "',
                balance = '" . $balance . "',
                user_id = '" . $user_id . "',
                date_added = NOW(),
                txtid = '" . date('YmdHis') . "'
        ");

        return true;
    }
    
    
    
    public function getMainCategories() {

    $sql = "SELECT c.category_id, cd.name
            FROM `" . DB_PREFIX . "category` c
            JOIN `" . DB_PREFIX . "category_description` cd
                ON c.category_id = cd.category_id
            WHERE c.parent_id = 0
            AND c.offer = 1
            AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
            ORDER BY cd.name ASC";

    $query = $this->db->query($sql);

    return $query->rows;
}
public function getAllProducts($start = 0, $limit = 20) {

    $start = (int)$start;
    $limit = 20; // Force 20 always

    if ($start < 0) $start = 0;

    $sql = "SELECT p.product_id, pd.name
            FROM `" . DB_PREFIX . "product` p
            JOIN `" . DB_PREFIX . "product_description` pd
                ON p.product_id = pd.product_id
            WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
            ORDER BY p.product_id DESC
            LIMIT " . $start . ", " . $limit;

    $query = $this->db->query($sql);

    return $query->rows;
}
public function getSubCategories($category_id) {

    $category_id = (int)$category_id;

    $sql = "SELECT c.category_id, cd.name
            FROM `" . DB_PREFIX . "category` c
            JOIN `" . DB_PREFIX . "category_description` cd
                ON c.category_id = cd.category_id
            WHERE c.parent_id = '" . $category_id . "'
            AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
            ORDER BY cd.name ASC";

    $query = $this->db->query($sql);

    return $query->rows;
}
public function getProductsByCategory($category_id, $start = 0, $limit = 20) {

    $category_id = (int)$category_id;
    $start = (int)$start;
    $limit = 20; //

    if ($start < 0) $start = 0;

    $sql = "SELECT p.product_id, pd.name
            FROM `" . DB_PREFIX . "product_to_category` pc
            JOIN `" . DB_PREFIX . "product` p
                ON pc.product_id = p.product_id
            JOIN `" . DB_PREFIX . "product_description` pd
                ON p.product_id = pd.product_id
            WHERE pc.category_id = '" . $category_id . "'
            AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
            ORDER BY p.product_id DESC
            LIMIT " . $start . ", " . $limit;

    $query = $this->db->query($sql);

    return $query->rows;
}

public function getCategoriesAutocomplete($data = [])
{

    $sql = "SELECT c.category_id, cd.name
            FROM " . DB_PREFIX . "category c
            LEFT JOIN " . DB_PREFIX . "category_description cd
            ON (c.category_id = cd.category_id)
            WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

    if (!empty($data['filter_name'])) {

        $sql .= " AND cd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
    }

    $sql .= " AND c.status = '1'";

    $sql .= " ORDER BY cd.name ASC";

    if (isset($data['start']) || isset($data['limit'])) {

        if ($data['start'] < 0) {
            $data['start'] = 0;
        }

        if ($data['limit'] < 1) {
            $data['limit'] = 10;
        }

        $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
    }

    $query = $this->db->query($sql);

    return $query->rows;
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


	/**
	 * Edit Order
	 *
	 * Edit order record in the database.
	 *
	 * @param int                  $order_id primary key of the order record
	 * @param array<string, mixed> $data     array of data
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $order_data = [
	 *     'subscription_id'        => 1,
	 *     'invoice_prefix'         => 'INV-',
	 *     'store_id'               => 1,
	 *     'store_name'             => 'Your Store',
	 *     'store_url'              => '',
	 *     'customer_id'            => 1,
	 *     'customer_group_id'      => 1,
	 *     'firstname'              => 'John',
	 *     'lastname'               => 'Doe',
	 *     'email'                  => 'demo@opencart.com',
	 *     'telephone'              => '1234567890',
	 *     'custom_field'           => [],
	 *     'payment_address_id'     => 1,
	 *     'payment_firstname'      => 'John',
	 *     'payment_lastname'       => 'Doe',
	 *     'payment_company'        => '',
	 *     'payment_address_1'      => 'Address 1',
	 *     'payment_address_2'      => 'Address 2',
	 *     'payment_city'           => '',
	 *     'payment_postcode'       => '',
	 *     'payment_country'        => 'United Kingdom',
	 *     'payment_country_id'     => 222,
	 *     'payment_zone'           => 'Lancashire',
	 *     'payment_zone_id'        => 3563,
	 *     'payment_address_format' => '',
	 *     'payment_custom_field'   => [],
	 *     'payment_method'         => [
	 *         'name' => 'Payment Name',
	 *         'code' => 'Payment Code'
	 *      ],
	 *      'shipping_address_id'     => 1,
	 *      'shipping_firstname'      => 'John',
	 *      'shipping_lastname'       => 'Doe',
	 *      'shipping_company'        => '',
	 *      'shipping_address_1'      => 'Address 1',
	 *      'shipping_address_2'      => 'Address 2',
	 *      'shipping_city'           => '',
	 *      'shipping_postcode'       => '',
	 *      'shipping_country'        => 'United Kingdom',
	 *      'shipping_country_id'     => 222,
	 *      'shipping_zone'           => 'Lancashire',
	 *      'shipping_zone_id'        => 3563,
	 *      'shipping_address_format' => '',
	 *      'shipping_custom_field'   => [],
	 *      'shipping_method'         => [
	 *          'name' => 'Shipping Name',
	 *          'code' => 'Shipping Code'
	 *      ],
	 *      'comment'         => '',
	 *      'total'           => '0.0000',
	 *      'affiliate_id'    => 0,
	 *      'commission'      => '0.0000',
	 *      'marketing_id'    => 0,
	 *      'tracking'        => '',
	 *      'language_id'     => 1,
	 *      'language_code'   => 'en-gb',
	 *      'currency_id'     => 1,
	 *      'currency_code'   => 'USD',
	 *      'currency_value'  => '1.00000000',
	 *      'ip'              => '',
	 *      'forwarded_ip'    => '',
	 *      'user_agent'      => '',
	 *      'accept_language' => ''
	 * ];
	 *
	 * $this->load->model('checkout/order');
	 *
	 * $this->model_checkout_order->editOrder($order_id, $order_data);
	 */
	public function editOrder(int $order_id, array $data): void {

		$this->addHistory($order_id, (int)$this->config->get('config_void_status_id'));

		$order_info = $this->getOrder($order_id);

		if ($order_info) {
			// 2. Merge the old order data with the new data
			foreach ($order_info as $key => $value) {
				if (!isset($data[$key])) {
					$data[$key] = $value;
				}
			}

			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET `subscription_id` = '" . (int)$data['subscription_id'] . "', `invoice_prefix` = '" . $this->db->escape((string)$data['invoice_prefix']) . "', `store_id` = '" . (int)$data['store_id'] . "', `store_name` = '" . $this->db->escape((string)$data['store_name']) . "', `store_url` = '" . $this->db->escape((string)$data['store_url']) . "', `customer_id` = '" . (int)$data['customer_id'] . "', `customer_group_id` = '" . (int)$data['customer_group_id'] . "', `firstname` = '" . $this->db->escape((string)$data['firstname']) . "', `lastname` = '" . $this->db->escape((string)$data['lastname']) . "', `email` = '" . $this->db->escape((string)$data['email']) . "', `telephone` = '" . $this->db->escape((string)$data['telephone']) . "', `custom_field` = '" . $this->db->escape(json_encode($data['custom_field'])) . "', `payment_address_id` = '" . (int)$data['payment_address_id'] . "', `payment_firstname` = '" . $this->db->escape((string)$data['payment_firstname']) . "', `payment_lastname` = '" . $this->db->escape((string)$data['payment_lastname']) . "', `payment_company` = '" . $this->db->escape((string)$data['payment_company']) . "', `payment_address_1` = '" . $this->db->escape((string)$data['payment_address_1']) . "', `payment_address_2` = '" . $this->db->escape((string)$data['payment_address_2']) . "', `payment_city` = '" . $this->db->escape((string)$data['payment_city']) . "', `payment_postcode` = '" . $this->db->escape((string)$data['payment_postcode']) . "', `payment_country` = '" . $this->db->escape((string)$data['payment_country']) . "', `payment_country_id` = '" . (int)$data['payment_country_id'] . "', `payment_zone` = '" . $this->db->escape((string)$data['payment_zone']) . "', `payment_zone_id` = '" . (int)$data['payment_zone_id'] . "', `payment_address_format` = '" . $this->db->escape((string)$data['payment_address_format']) . "', `payment_custom_field` = '" . $this->db->escape(isset($data['payment_custom_field']) ? json_encode($data['payment_custom_field']) : '') . "', `payment_method` = '" . $this->db->escape($data['payment_method'] ? json_encode($data['payment_method']) : '') . "', `shipping_address_id` = '" . (int)$data['shipping_address_id'] . "', `shipping_firstname` = '" . $this->db->escape((string)$data['shipping_firstname']) . "', `shipping_lastname` = '" . $this->db->escape((string)$data['shipping_lastname']) . "', `shipping_company` = '" . $this->db->escape((string)$data['shipping_company']) . "', `shipping_address_1` = '" . $this->db->escape((string)$data['shipping_address_1']) . "', `shipping_address_2` = '" . $this->db->escape((string)$data['shipping_address_2']) . "', `shipping_city` = '" . $this->db->escape((string)$data['shipping_city']) . "', `shipping_postcode` = '" . $this->db->escape((string)$data['shipping_postcode']) . "', `shipping_country` = '" . $this->db->escape((string)$data['shipping_country']) . "', `shipping_country_id` = '" . (int)$data['shipping_country_id'] . "', `shipping_zone` = '" . $this->db->escape((string)$data['shipping_zone']) . "', `shipping_zone_id` = '" . (int)$data['shipping_zone_id'] . "', `shipping_address_format` = '" . $this->db->escape((string)$data['shipping_address_format']) . "', `shipping_custom_field` = '" . $this->db->escape(isset($data['shipping_custom_field']) ? json_encode($data['shipping_custom_field']) : '') . "', `shipping_method` = '" . $this->db->escape($data['shipping_method'] ? json_encode($data['shipping_method']) : '') . "', `comment` = '" . $this->db->escape((string)$data['comment']) . "', `total` = '" . (float)$data['total'] . "', `affiliate_id` = '" . (int)$data['affiliate_id'] . "', `commission` = '" . (float)$data['commission'] . "', `date_modified` = NOW() WHERE `order_id` = '" . (int)$order_id . "'");

			// Products
			$this->model_checkout_order->deleteProducts($order_id);

			if (!empty($data['products'])) {
				foreach ($data['products'] as $product) {
					$this->model_checkout_order->addProduct($order_id, $product);
				}
			}

			// Totals
			$this->model_checkout_order->deleteTotals($order_id);

			if (!empty($data['totals'])) {
				foreach ($data['totals'] as $total) {
					$this->model_checkout_order->addTotal($order_id, $total);
				}
			}
		}
	}

	/**
	 * Edit Transaction ID
	 *
	 * Edit order transaction record in the database.
	 *
	 * @param int    $order_id       primary key of the order record
	 * @param string $transaction_id primary key of the transaction record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('checkout/order');
	 *
	 * $this->model_checkout_order->editTransactionId($order_id, $transaction_id);
	 */
	public function editTransactionId(int $order_id, string $transaction_id): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET `transaction_id` = '" . $this->db->escape($transaction_id) . "' WHERE `order_id` = '" . (int)$order_id . "'");
	}

	/**
	 * Edit Order Status ID
	 *
	 * Edit order status record in the database.
	 *
	 * @param int $order_id        primary key of the order record
	 * @param int $order_status_id primary key of the order status record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('checkout/order');
	 *
	 * $this->model_checkout_order->editOrderStatusId($order_id, $order_status_id);
	 */
	public function editOrderStatusId(int $order_id, int $order_status_id): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET `order_status_id` = '" . (int)$order_status_id . "' WHERE `order_id` = '" . (int)$order_id . "'");
	}

	/**
	 * Edit Comment
	 *
	 * Edit order comment record in the database.
	 *
	 * @param int    $order_id primary key of the order record
	 * @param string $comment
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('checkout/order');
	 *
	 * $this->model_checkout_order->editComment($order_id, $comment);
	 */
	public function editComment(int $order_id, string $comment): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET `comment` = '" . $this->db->escape($comment) . "' WHERE `order_id` = '" . (int)$order_id . "'");
	}

	/**
	 * Delete Order
	 *
	 * Delete order record in the database.
	 *
	 * @param int $order_id primary key of the order record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('checkout/order');
	 *
	 * $this->model_checkout_order->deleteOrder($order_id);
	 */
	public function deleteOrder(int $order_id): void {
		// Void the order first so it restocks products
		$this->model_checkout_order->addHistory($order_id, (int)$this->config->get('config_void_status_id'));

		$this->db->query("DELETE FROM `" . DB_PREFIX . "order` WHERE `order_id` = '" . (int)$order_id . "'");

		$this->model_checkout_order->deleteProducts($order_id);
		$this->model_checkout_order->deleteTotals($order_id);
		$this->model_checkout_order->deleteHistories($order_id);

		// Transaction
		$this->load->model('account/transaction');

		$this->model_account_transaction->deleteTransactionsByOrderId($order_id);

		// Reward
		$this->load->model('account/reward');

		$this->model_account_reward->deleteRewardsByOrderId($order_id);
	}

	/**
	 * Get Order
	 *
	 * Get the record of the order record in the database.
	 *
	 * @param int $order_id primary key of the order record
	 *
	 * @return array<string, mixed> order record that has order ID
	 *
	 * @example
	 *
	 * $this->load->model('checkout/order');
	 *
	 * $order_info = $this->model_checkout_order->getOrder($order_id);
	 */
	/*public function getOrder(int $order_id): array {
		$order_query = $this->db->query("SELECT *, (SELECT `os`.`name` FROM `" . DB_PREFIX . "order_status` `os` WHERE `os`.`order_status_id` = `o`.`order_status_id` AND `os`.`language_id` = `o`.`language_id`) AS `order_status` FROM `" . DB_PREFIX . "order` `o` WHERE `o`.`order_id` = '" . (int)$order_id . "'");

		if ($order_query->num_rows) {
			$order_data = $order_query->row;

			// Country
			$this->load->model('localisation/country');

			// Zone
			$this->load->model('localisation/zone');

			$order_data['custom_field'] = $order_query->row['custom_field'] ? json_decode($order_query->row['custom_field'], true) : [];

			foreach (['payment', 'shipping'] as $column) {
				$country_info = $this->model_localisation_country->getCountry($order_query->row[$column . '_country_id']);

				if ($country_info) {
					$order_data[$column . '_iso_code_2'] = $country_info['iso_code_2'];
					$order_data[$column . '_iso_code_3'] = $country_info['iso_code_3'];
				} else {
					$order_data[$column . '_iso_code_2'] = '';
					$order_data[$column . '_iso_code_3'] = '';
				}

				$zone_info = $this->model_localisation_zone->getZone($order_query->row[$column . '_zone_id']);

				if ($zone_info) {
					$order_data[$column . '_zone_code'] = $zone_info['code'];
				} else {
					$order_data[$column . '_zone_code'] = '';
				}

				$order_data[$column . '_custom_field'] = $order_query->row[$column . '_custom_field'] ? json_decode($order_query->row[$column . '_custom_field'], true) : [];

				// Payment and shipping method details
				$order_data[$column . '_method'] = json_decode($order_query->row[$column . '_method'], true);
			}

			$order_data['products'] = $this->getProducts($order_id);
			$order_data['totals'] = $this->getTotals($order_id);

			return $order_data;
		}

		return [];
	}*/
	
	public function getOrder(int $order_id): array {
		$order_query = $this->db->query("SELECT *, (SELECT `os`.`name` FROM `" . DB_PREFIX . "order_status` `os` WHERE `os`.`order_status_id` = `o`.`order_status_id` AND `os`.`language_id` = `o`.`language_id`) AS `order_status` FROM `" . DB_PREFIX . "order` `o` WHERE `o`.`order_id` = '" . (int)$order_id . "'");

		if ($order_query->num_rows) {
			$order_data = $order_query->row;

			// Country
			$this->load->model('localisation/country');

			// Zone
			$this->load->model('localisation/zone');

			$order_data['custom_field'] = $order_query->row['custom_field'] ? json_decode($order_query->row['custom_field'], true) : [];

			foreach (['payment', 'shipping'] as $column) {
				$country_info = $this->model_localisation_country->getCountry($order_query->row[$column . '_country_id']);

				if ($country_info) {
					$order_data[$column . '_iso_code_2'] = $country_info['iso_code_2'];
					$order_data[$column . '_iso_code_3'] = $country_info['iso_code_3'];
				} else {
					$order_data[$column . '_iso_code_2'] = '';
					$order_data[$column . '_iso_code_3'] = '';
				}

				$zone_info = $this->model_localisation_zone->getZone($order_query->row[$column . '_zone_id']);

				if ($zone_info) {
					$order_data[$column . '_zone_code'] = $zone_info['code'];
				} else {
					$order_data[$column . '_zone_code'] = '';
				}

				// custom fields
                    $order_data[$column . '_custom_field'] =
                        !empty($order_query->row[$column . '_custom_field'])
                        ? (json_decode($order_query->row[$column . '_custom_field'], true) ?: [])
                        : [];
                    
                    // payment / shipping method (safe decode)
                    $raw_method = $order_query->row[$column . '_method'] ?? '';
                    
                    if (!is_string($raw_method) || trim($raw_method) === '') {
                        $order_data[$column . '_method'] = [];
                    } else {
                        $decoded = json_decode($raw_method, true);
                        $order_data[$column . '_method'] = is_array($decoded) ? $decoded : [];
                    }

			}

			$order_data['products'] = $this->getProducts($order_id);
			$order_data['totals'] = $this->getTotals($order_id);

			return $order_data;
		}

		return [];
	}

	/**
	 * Add Product
	 *
	 * Create a new order product record in the database.
	 *
	 * @param int                  $order_id primary key of the order record
	 * @param array<string, mixed> $data     array of data
	 *
	 * @return int returns the primary key of the new order product record
	 *
	 * @example
	 *
	 * $order_product_data = [
	 *     'product_id' => 1,
	 *     'master_id'  => 0,
	 *     'name'       => 'Product Name',
	 *     'model'      => 'Product Model',
	 *     'quantity'   => 1,
	 *     'price'      => 0.0000,
	 *     'total'      => 0.0000,
	 *     'tax'        => 0.0000,
	 *     'reward'     => 0
	 * ];
	 *
	 * $this->load->model('checkout/order');
	 *
	 * $this->model_checkout_order->addProduct($order_id, $order_product_data);
	 */
	/*public function addProduct(int $order_id, array $data): int {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "order_product` SET `order_id` = '" . (int)$order_id . "', `product_id` = '" . (int)$data['product_id'] . "', `master_id` = '" . (int)$data['master_id'] . "', `name` = '" . $this->db->escape($data['name']) . "', `model` = '" . $this->db->escape($data['model']) . "', `quantity` = '" . (int)$data['quantity'] . "', `price` = '" . (float)$data['price'] . "', `total` = '" . (float)$data['total'] . "', `tax` = '" . (float)$data['tax'] . "', `reward` = '" . (int)$data['reward'] . "'");

		$order_product_id = $this->db->getLastId();

		if (!empty($data['option'])) {
			foreach ($data['option'] as $option) {
				$this->model_checkout_order->addOption($order_id, $order_product_id, $option);
			}
		}

		// If subscription add details
		if (!empty($data['subscription'])) {
			$this->model_checkout_order->addSubscription($order_id, $order_product_id, $data['subscription'] + ['quantity' => $data['quantity']]);
		}

		return $order_product_id;
	}*/


public function addProduct(int $order_id, array $data): int {

    // SAFETY: Provide defaults for missing keys
    $product_id  = (int)($data['product_id']  ?? 0);
    $master_id   = (int)($data['master_id']   ?? 0);
    $name        = $this->db->escape($data['name'] ?? '');
    $model       = $this->db->escape($data['model'] ?? '');
    $quantity    = (int)($data['quantity']    ?? 1);
    $price       = (float)($data['price']     ?? 0);
    $total       = (float)($data['total']     ?? 0);
    $tax         = (float)($data['tax']       ?? 0);
    $reward      = (int)($data['reward']      ?? 0);
    $options     = $data['option']            ?? [];
    $subscription= $data['subscription']      ?? [];

    // Insert product
    $this->db->query("INSERT INTO `" . DB_PREFIX . "order_product`SET
                                                                `order_id`   = '" . (int)$order_id . "',
                                                                `product_id` = '" . $product_id . "',
                                                                `master_id`  = '" . $master_id . "',
                                                                `name`       = '" . $name . "',
                                                                `model`      = '" . $model . "',
                                                                `quantity`   = '" . $quantity . "',
                                                                `price`      = '" . $price . "',
                                                                `total`      = '" . $total . "',
                                                                `tax`        = '" . $tax . "',
                                                                `reward`     = '" . $reward . "'
                                                        ");

    $order_product_id = $this->db->getLastId();

    // Insert options safely
    if (!empty($options)) {
        foreach ($options as $option) {
            $this->model_checkout_order->addOption($order_id, $order_product_id, $option);
        }
    }

    // Insert subscription safely
    if (!empty($subscription)) {
        $subscription['quantity'] = $quantity;
        $this->model_checkout_order->addSubscription($order_id, $order_product_id, $subscription);
    }

    return $order_product_id;
}

	/**
	 * Delete Products
	 *
	 * Delete order product record in the database.
	 *
	 * @param int $order_id         primary key of the order record
	 * @param int $order_product_id primary key of the order product record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('checkout/order');
	 *
	 * $this->model_checkout_order->deleteProducts($order_id);
	 */
	public function deleteProducts(int $order_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = '" . (int)$order_id . "'");

		$this->deleteOptions($order_id);
		$this->deleteSubscription($order_id);
	}

	/**
	 * Get Product
	 *
	 * Get the record of the order product record in the database.
	 *
	 * @param int $order_id         primary key of the order record
	 * @param int $order_product_id primary key of the order product record
	 *
	 * @return array<int, array<string, mixed>> product record that has order ID, order product ID
	 *
	 * @example
	 *
	 * $this->load->model('checkout/order');
	 *
	 * $order_product = $this->model_checkout_order->getProduct($order_id, $order_product_id);
	 */
	public function getProduct(int $order_id, int $order_product_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = '" . (int)$order_id . "' AND `order_product_id` = '" . (int)$order_product_id . "'");

		return $query->row;
	}

	/**
	 * Get Products
	 *
	 * Get the record of the order product records in the database.
	 *
	 * @param int $order_id primary key of the order record
	 *
	 * @return array<int, array<string, mixed>> product records that have order ID
	 *
	 * @example
	 *
	 * $this->load->model('checkout/order');
	 *
	 * $order_products = $this->model_checkout_order->getProducts($order_id);
	 */
	public function getProducts(int $order_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = '" . (int)$order_id . "'");

		return $query->rows;
	}

	/**
	 * Add Option
	 *
	 * Create a new order option record in the database.
	 *
	 * @param int                  $order_id         primary key of the order record
	 * @param int                  $order_product_id primary key of the order product record
	 * @param array<string, mixed> $data             array of data
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $order_option_data = [
	 *     'product_option_id'       => 1,
	 *     'product_option_value_id' => 1,
	 *     'name'                    => 'Option Name',
	 *     'value'                   => 'Option Value',
	 *     'type'                    => 'radio'
	 * ];
	 *
	 * $this->load->model('checkout/order');
	 *
	 * $this->model_checkout_order->addOption($order_id, $order_product_id, $order_option_data);
	 */
	public function addOption(int $order_id, int $order_product_id, array $data): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "order_option` SET `order_id` = '" . (int)$order_id . "', `order_product_id` = '" . (int)$order_product_id . "', `product_option_id` = '" . (int)$data['product_option_id'] . "', `product_option_value_id` = '" . (int)$data['product_option_value_id'] . "', `name` = '" . $this->db->escape($data['name']) . "', `value` = '" . $this->db->escape($data['value']) . "', `type` = '" . $this->db->escape($data['type']) . "'");
	}

	/**
	 * Delete Options
	 *
	 * Delete order option records in the database.
	 *
	 * @param int $order_id         primary key of the order record
	 * @param int $order_product_id primary key of the order product record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('checkout/order');
	 *
	 * $this->model_checkout_order->deleteOptions($order_id);
	 */
	public function deleteOptions(int $order_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "order_option` WHERE `order_id` = '" . (int)$order_id . "'");
	}

	/**
	 * Get Options
	 *
	 * Get the record of the order option records in the database.
	 *
	 * @param int $order_id         primary key of the order record
	 * @param int $order_product_id primary key of the order product record
	 *
	 * @return array<int, array<string, mixed>> option records that have order ID, order product ID
	 *
	 * @example
	 *
	 * $this->load->model('checkout/order');
	 *
	 * $order_options = $this->model_checkout_order->getOptions($order_id, $order_product_id);
	 */
	public function getOptions(int $order_id, int $order_product_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_option` WHERE `order_id` = '" . (int)$order_id . "' AND `order_product_id` = '" . (int)$order_product_id . "'");

		return $query->rows;
	}

	/**
	 * Add Subscription
	 *
	 * Create a order subscription record in the database.
	 *
	 * @param int                  $order_id         primary key of the order record
	 * @param int                  $order_product_id primary key of the order product record
	 * @param array<string, mixed> $data             array of data
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $order_subscription_data = [
	 *     'product_id'           => 1,
	 *     'subscription_plan_id' => 1,
	 *     'trial_price'          => 0.0000,
	 *     'trial_tax'            => 0.0000,
	 *     'trial_frequency'      => 'month',
	 *     'trial_cycle'          => 5,
	 *     'trial_duration'       => 1,
	 *     'trial_status'         => 1,
	 *     'price'                => 0.0000,
	 *     'tax'                  => 0.0000,
	 *     'frequency'            => 'month',
	 *     'cycle'                => 5,
	 *     'duration'             => 1
	 * ];
	 *
	 * $this->load->model('checkout/order');
	 *
	 * $this->model_checkout_order->addSubscription($order_id, $order_product_id, $order_subscription_data);
	 */
	public function addSubscription(int $order_id, int $order_product_id, array $data): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "order_subscription` SET `order_product_id` = '" . (int)$order_product_id . "', `order_id` = '" . (int)$order_id . "', `product_id` = '" . (int)$data['product_id'] . "', `subscription_plan_id` = '" . (int)$data['subscription_plan_id'] . "', `trial_price` = '" . (float)$data['trial_price'] . "', `trial_tax` = '" . (float)$data['trial_tax'] . "', `trial_frequency` = '" . $this->db->escape($data['trial_frequency']) . "', `trial_cycle` = '" . (int)$data['trial_cycle'] . "', `trial_duration` = '" . (int)$data['trial_duration'] . "', `trial_status` = '" . (int)$data['trial_status'] . "', `price` = '" . (float)$data['price'] . "', `tax` = '" . (float)$data['tax'] . "', `frequency` = '" . $this->db->escape($data['frequency']) . "', `cycle` = '" . (int)$data['cycle'] . "', `duration` = '" . (int)$data['duration'] . "'");
	}

	/**
	 * Delete Subscription
	 *
	 * Delete order subscription record in the database.
	 *
	 * @param int $order_id         primary key of the order record
	 * @param int $order_product_id primary key of the order product record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('checkout/order');
	 *
	 * $this->model_checkout_order->deleteSubscription($order_id);
	 */
	public function deleteSubscription(int $order_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "order_subscription` WHERE `order_id` = '" . (int)$order_id . "'");
	}

	/**
	 * Get Subscription
	 *
	 * Get the record of the order subscription record in the database.
	 *
	 * @param int $order_id         primary key of the order record
	 * @param int $order_product_id primary key of the order product record
	 *
	 * @return array<string, mixed> subscription record that have order ID, order product ID
	 *
	 * @example
	 *
	 * $this->load->model('checkout/order');
	 *
	 * $order_subscription_info = $this->model_checkout_order->getSubscription($order_id, $order_product_id);
	 */
	public function getSubscription(int $order_id, int $order_product_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_subscription` WHERE `order_id` = '" . (int)$order_id . "' AND `order_product_id` = '" . (int)$order_product_id . "'");

		return $query->row;
	}

	/**
	 * Get Subscriptions
	 *
	 * Get the record of the order subscription records in the database.
	 *
	 * @param int $order_id primary key of the order record
	 *
	 * @return array<int, array<string, mixed>> subscription records that have order ID
	 *
	 * @example
	 *
	 * $this->load->model('checkout/order');
	 *
	 * $results = $this->model_checkout_order->getSubscriptions($order_id);
	 */
	public function getSubscriptions(int $order_id): array {
		$query = $this->db->query("SELECT *, `os`.`price`, `os`.`tax` FROM `" . DB_PREFIX . "order_subscription` `os` LEFT JOIN `" . DB_PREFIX . "order_product` `op` ON(`os`.`order_product_id` = `op`.`order_product_id`) WHERE `os`.`order_id` = '" . (int)$order_id . "'");

		return $query->rows;
	}

	/**
	 * Get Total Orders By Subscription ID
	 *
	 * Get the total number of total orders by subscription records in the database.
	 *
	 * @param int $subscription_id primary key of the subscription record
	 *
	 * @return int total number of order that have subscription ID
	 *
	 * @example
	 *
	 * $this->load->model('checkout/order');
	 *
	 * $subscription_total = $this->model_checkout_order->getTotalOrdersBySubscriptionId($subscription_id);
	 */
	public function getTotalOrdersBySubscriptionId(int $subscription_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "order` WHERE `subscription_id` = '" . (int)$subscription_id . "' AND `customer_id` = '" . (int)$this->customer->getId() . "'");

		return (int)$query->row['total'];
	}

	/**
	 * Add Total
	 *
	 * Create a new order total record in the database.
	 *
	 * @param int                  $order_id primary key of the order record
	 * @param array<string, mixed> $data     array of data
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $order_total_data = [
	 *     'extension' => '',
	 *     'code'      => '',
	 *     'title'     => 'Order Total Title',
	 *     'value'     => 0.0000,
	 *     'sort_order'
	 * ];
	 *
	 * $this->load->model('checkout/order');
	 *
	 * $this->model_checkout_order->addTotal($order_id, $order_total_data);
	 */
	public function addTotal(int $order_id, array $data): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "order_total` SET `order_id` = '" . (int)$order_id . "', `extension` = '" . $this->db->escape($data['extension']) . "', `code` = '" . $this->db->escape($data['code']) . "', `title` = '" . $this->db->escape($data['title']) . "', `value` = '" . (float)$data['value'] . "', `sort_order` = '" . (int)$data['sort_order'] . "'");
	}

	/**
	 * Delete Totals
	 *
	 * Delete order total records in the database.
	 *
	 * @param int $order_id primary key of the order record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('checkout/order');
	 *
	 * $this->model_checkout_order->deleteTotals($order_id);
	 */
	public function deleteTotals(int $order_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "order_total` WHERE `order_id` = '" . (int)$order_id . "'");
	}

	/**
	 * Get Totals
	 *
	 * Get the record of the order total records in the database.
	 *
	 * @param int $order_id primary key of the order record
	 *
	 * @return array<int, array<string, mixed>> total records that have order ID
	 *
	 * @example
	 *
	 * $this->load->model('checkout/order');
	 *
	 * $order_totals = $this->model_checkout_order->getTotals($order_id);
	 */
	public function getTotals(int $order_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE `order_id` = '" . (int)$order_id . "' ORDER BY `sort_order` ASC");

		return $query->rows;
	}

	/**
	 * Add History
	 *
	 * Create a new order history record in the database.
	 *
	 * @param int    $order_id        primary key of the order record
	 * @param int    $order_status_id primary key of the order status record
	 * @param string $comment
	 * @param bool   $notify
	 * @param bool   $override
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('checkout/order');
	 *
	 * $this->model_checkout_order->addHistory($order_id, $order_status_id, $comment, $notify, $override);
	 */
	public function addHistory(int $order_id, int $order_status_id, string $comment = '', bool $notify = false, bool $override = false): void {
		$order_info = $this->model_checkout_order->getOrder($order_id);

		if ($order_info) {
			// Load subscription model
			$this->load->model('account/customer');

			$customer_info = $this->model_account_customer->getCustomer($order_info['customer_id']);

			// Fraud Detection Enable / Disable
			if ($customer_info && $customer_info['safe']) {
				$safe = true;
			} else {
				$safe = false;
			}

			// Only do the fraud check if the customer is not on the safe list and the order status is changing into the complete or process order status
			if (!$safe && !$override && in_array($order_status_id, (array)$this->config->get('config_processing_status') + (array)$this->config->get('config_complete_status'))) {
				// Anti-Fraud
				$this->load->model('setting/extension');

				$extensions = $this->model_setting_extension->getExtensionsByType('fraud');

				foreach ($extensions as $extension) {
					if ($this->config->get('fraud_' . $extension['code'] . '_status')) {
						$this->load->model('extension/' . $extension['extension'] . '/fraud/' . $extension['code']);

						$key = 'model_extension_' . $extension['extension'] . '_fraud_' . $extension['code'];

						if (isset($this->{$key}->check)) {
							$fraud_status_id = $this->{$key}->check($order_info);

							if ($fraud_status_id) {
								$order_status_id = $fraud_status_id;
							}
						}
					}
				}
			}

			// Products
			$order_products = $this->model_checkout_order->getProducts($order_id);

			// Subscriptions
			$order_subscriptions = $this->model_checkout_order->getSubscriptions($order_id);

			// Totals
			$order_totals = $this->model_checkout_order->getTotals($order_id);

			// If current order status is not processing or complete but new status is processing or complete then commence completing the order
			if (!in_array($order_info['order_status_id'], (array)$this->config->get('config_processing_status') + (array)$this->config->get('config_complete_status')) && in_array($order_status_id, (array)$this->config->get('config_processing_status') + (array)$this->config->get('config_complete_status'))) {
				// Redeem coupon and reward points
				foreach ($order_totals as $order_total) {
					$this->load->model('extension/' . $order_total['extension'] . '/total/' . $order_total['code']);

					$key = 'model_extension_' . $order_total['extension'] . '_total_' . $order_total['code'];

					if (isset($this->{$key}->confirm)) {
						// Confirm coupon and reward points
						$fraud_status_id = $this->{$key}->confirm($order_info, $order_total);

						// If the balance on the coupon and reward points is not enough to cover the transaction or has already been used then the fraud order status is returned.
						if ($fraud_status_id) {
							$order_status_id = $fraud_status_id;
						}
					}
				}

				foreach ($order_products as $order_product) {
					// Stock subtraction
					$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `quantity` = (`quantity` - " . (int)$order_product['quantity'] . ") WHERE `product_id` = '" . (int)$order_product['product_id'] . "' AND `subtract` = '1'");

					// Stock subtraction from master product
					if ($order_product['master_id']) {
						$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `quantity` = (`quantity` - " . (int)$order_product['quantity'] . ") WHERE `product_id` = '" . (int)$order_product['master_id'] . "' AND `subtract` = '1'");
					}

					$order_options = $this->model_checkout_order->getOptions($order_id, $order_product['order_product_id']);

					foreach ($order_options as $order_option) {
						$this->db->query("UPDATE `" . DB_PREFIX . "product_option_value` SET `quantity` = (`quantity` - " . (int)$order_product['quantity'] . ") WHERE `product_option_value_id` = '" . (int)$order_option['product_option_value_id'] . "' AND `subtract` = '1'");
					}
				}
			}

			// If order status becomes complete status
			if (!in_array($order_info['order_status_id'], (array)$this->config->get('config_complete_status')) && in_array($order_status_id, (array)$this->config->get('config_complete_status'))) {
				// Affiliate add commission if complete status
				if ($order_info['affiliate_id'] && $this->config->get('config_affiliate_auto')) {
					// Add commission if sale is linked to affiliate referral.
					$this->load->model('account/customer');

					if (!$this->model_account_customer->getTotalTransactionsByOrderId($order_id)) {
						$this->model_account_customer->addTransaction($order_info['affiliate_id'], $this->language->get('text_order_id') . ' #' . $order_id, $order_info['commission'], $order_id);
					}
				}

				// Add subscription
				$this->load->model('checkout/subscription');

				foreach ($order_subscriptions as $key => $order_subscription) {
					$subscription_product_data = [];

					foreach ($order_subscriptions as $subscription) {
						if ($subscription['subscription_plan_id'] == $order_subscription['subscription_plan_id']) {
							$subscription_product_data[] = [
								'option'      => $this->model_checkout_order->getOptions($order_id, $order_subscription['order_product_id']),
								'trial_price' => $order_subscription['trial_price'],
								'trial_tax'   => $order_subscription['trial_tax'],
								'price'       => $order_subscription['price'],
								'tax'         => $order_subscription['tax']
							] + $order_subscription;

							unset($order_subscriptions[$key]);
						}
					}

					$subscription_data = [
						'trial_price'          => array_sum(array_column($subscription_product_data, 'trial_price')),
						'trial_tax'            => array_sum(array_column($subscription_product_data, 'trial_tax')),
						'price'                => array_sum(array_column($subscription_product_data, 'price')),
						'tax'                  => array_sum(array_column($subscription_product_data, 'tax')),
						'subscription_product' => $subscription_product_data,
						'language'             => $order_info['language_code'],
						'currency'             => $order_info['currency_code']
					] + $order_info + $order_subscription;

					$subscription_info = $this->model_checkout_subscription->getProductByOrderProductId($order_id, $order_subscription['order_product_id']);

					if (!$subscription_info) {
						$subscription_id = $this->model_checkout_subscription->addSubscription($subscription_data);
					} else {
						$this->model_checkout_subscription->editSubscription($subscription_info['subscription_id'], $subscription_data);

						$subscription_id = $subscription_info['subscription_id'];
					}

					// Add history and set active subscription
					$this->model_checkout_subscription->addHistory($subscription_id, (int)$this->config->get('config_subscription_active_status_id'));
				}
			}

			// If old order status is the processing or complete status but new status is not then commence restock, and remove coupon and reward history
			if (in_array($order_info['order_status_id'], (array)$this->config->get('config_processing_status') + (array)$this->config->get('config_complete_status')) && !in_array($order_status_id, (array)$this->config->get('config_processing_status') + (array)$this->config->get('config_complete_status'))) {
				// Restock
				foreach ($order_products as $order_product) {
					$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `quantity` = (`quantity` + " . (int)$order_product['quantity'] . ") WHERE `product_id` = '" . (int)$order_product['product_id'] . "' AND `subtract` = '1'");

					// Restock the master product stock level if product is a variant
					if ($order_product['master_id']) {
						$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `quantity` = (`quantity` + " . (int)$order_product['quantity'] . ") WHERE `product_id` = '" . (int)$order_product['master_id'] . "' AND `subtract` = '1'");
					}

					$order_options = $this->model_checkout_order->getOptions($order_id, $order_product['order_product_id']);

					foreach ($order_options as $order_option) {
						$this->db->query("UPDATE `" . DB_PREFIX . "product_option_value` SET `quantity` = (`quantity` + " . (int)$order_product['quantity'] . ") WHERE `product_option_value_id` = '" . (int)$order_option['product_option_value_id'] . "' AND `subtract` = '1'");
					}
				}

				// Remove coupon and reward points history
				foreach ($order_totals as $order_total) {
					$this->load->model('extension/' . $order_total['extension'] . '/total/' . $order_total['code']);

					$key = 'model_extension_' . $order_total['extension'] . '_total_' . $order_total['code'];

					if (isset($this->{$key}->unconfirm)) {
						$this->{$key}->unconfirm($order_info);
					}
				}
			}

			// If order status is no longer complete status
			if (in_array($order_info['order_status_id'], (array)$this->config->get('config_complete_status')) && !in_array($order_status_id, (array)$this->config->get('config_complete_status'))) {
				// Suspend subscription
				$this->load->model('checkout/subscription');

				foreach ($order_products as $order_product) {
					// Subscription status set to suspend
					$subscription_info = $this->model_checkout_subscription->getProductByOrderProductId($order_id, $order_product['order_product_id']);

					if ($subscription_info) {
						// Add history and set suspended subscription
						$this->model_checkout_subscription->addHistory($subscription_info['subscription_id'], (int)$this->config->get('config_subscription_suspended_status_id'));
					}
				}

				// Affiliate remove commission.
				if ($order_info['affiliate_id']) {
					$this->load->model('account/transaction');

					$this->model_account_transaction->deleteTransaction($order_info['customer_id'], $order_id);
				}
			}

			// Update the DB with the new statuses
			$this->model_checkout_order->editOrderStatusId($order_id, $order_status_id);

			$this->db->query("INSERT INTO `" . DB_PREFIX . "order_history` SET `order_id` = '" . (int)$order_id . "', `order_status_id` = '" . (int)$order_status_id . "', `notify` = '" . (int)$notify . "', `comment` = '" . $this->db->escape($comment) . "', `date_added` = NOW()");

			$this->cache->delete('product');
		}
	}

	/**
	 * Delete Order Histories
	 *
	 * Delete order history records in the database.
	 *
	 * @param int $order_id primary key of the order record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('checkout/order');
	 *
	 * $this->model_checkout_order->deleteHistories($order_id);
	 */
	public function deleteHistories(int $order_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "order_history` WHERE `order_id` = '" . (int)$order_id . "'");
	}
	
	
	public function getCoupon($code) {
    $status = true;

    $coupon_query = $this->db->query("
        SELECT * FROM `" . DB_PREFIX . "coupon`
        WHERE code = '" . $this->db->escape($code) . "'
          AND ((date_start = '0000-00-00' OR date_start < NOW())
          AND (date_end = '0000-00-00' OR date_end > NOW()))
          AND status = '1'
    ");

    if (!$coupon_query->num_rows) {
        return false;
    }

    // Uses total
    $coupon_total = $this->getTotalCouponHistoriesByCoupon($code);
    if ($coupon_query->row['uses_total'] > 0 && $coupon_total >= $coupon_query->row['uses_total']) {
        return false;
    }

    // Logged-in check
    if ($coupon_query->row['logged'] && !$this->customer->getId()) {
        return false;
    }

    // Uses per customer
    if ($this->customer->getId()) {
        $customer_total = $this->getTotalCouponHistoriesByCustomerId($code, $this->customer->getId());
        if ($coupon_query->row['uses_customer'] > 0 && $customer_total >= $coupon_query->row['uses_customer']) {
            return false;
        }
    }

    return [
        'coupon_id'     => $coupon_query->row['coupon_id'],
        'code'          => $coupon_query->row['code'],
        'name'          => $coupon_query->row['name'],
        'type'          => $coupon_query->row['type'],
        'discount'      => $coupon_query->row['discount'],
        'shipping'      => $coupon_query->row['shipping'],
        'total'         => (float)$coupon_query->row['total'],
        'minimum_total' => $coupon_query->row['minimum_total'],
        'uses_total'    => $coupon_query->row['uses_total'],
        'uses_customer' => $coupon_query->row['uses_customer']
    ];
}


	public function getTotal($total) {
		if (isset($this->session->data['coupon'])) {
			$this->load->language('extension/total/coupon', 'coupon');

			$coupon_info = $this->getCoupon($this->session->data['coupon']);

			if ($coupon_info) {
				$discount_total = 0;

				if (!$coupon_info['product']) {
					$sub_total = $this->cart->getSubTotal();
				} else {
					$sub_total = 0;

					foreach ($this->cart->getProducts() as $product) {
						if (in_array($product['product_id'], $coupon_info['product'])) {
							$sub_total += $product['total'];
						}
					}
				}

				// HARD CAP discount by coupon.total
$max_allowed = (float)$coupon_info['total'];

if ($coupon_info['type'] == 'F') {
    $coupon_info['discount'] = min(
        $coupon_info['discount'],
        $sub_total,
        $max_allowed > 0 ? $max_allowed : $coupon_info['discount']
    );
}


				foreach ($this->cart->getProducts() as $product) {
					$discount = 0;

					if (!$coupon_info['product']) {
						$status = true;
					} else {
						$status = in_array($product['product_id'], $coupon_info['product']);
					}

					if ($status) {
						if ($coupon_info['type'] == 'F') {
							$discount = $coupon_info['discount'] * ($product['total'] / $sub_total);
						} elseif ($coupon_info['type'] == 'P') {
							$discount = $product['total'] / 100 * $coupon_info['discount'];
							if ($coupon_info['total'] > 0) {
        $discount = min($discount, $coupon_info['total']);
    }
						}

						if ($product['tax_class_id']) {
							$tax_rates = $this->tax->getRates($product['total'] - ($product['total'] - $discount), $product['tax_class_id']);

							foreach ($tax_rates as $tax_rate) {
								if ($tax_rate['type'] == 'P') {
									$total['taxes'][$tax_rate['tax_rate_id']] -= $tax_rate['amount'];
								}
							}
						}
					}

					$discount_total += $discount;
				}

				if ($coupon_info['shipping'] && isset($this->session->data['shipping_method'])) {
					if (!empty($this->session->data['shipping_method']['tax_class_id'])) {
						$tax_rates = $this->tax->getRates($this->session->data['shipping_method']['cost'], $this->session->data['shipping_method']['tax_class_id']);

						foreach ($tax_rates as $tax_rate) {
							if ($tax_rate['type'] == 'P') {
								$total['taxes'][$tax_rate['tax_rate_id']] -= $tax_rate['amount'];
							}
						}
					}

					$discount_total += $this->session->data['shipping_method']['cost'];
				}

				// If discount greater than total
				if ($discount_total > $total['total']) {
					$discount_total = $total['total'];
				}

				if ($discount_total > 0) {
					$total['totals'][] = array(
						'code'       => 'coupon',
						'title'      => sprintf($this->language->get('coupon')->get('text_coupon'), $this->session->data['coupon']),
						'value'      => -$discount_total,
						'sort_order' => $this->config->get('total_coupon_sort_order')
					);

					$total['total'] -= $discount_total;
				}
			}
		}
	}

	public function confirm($order_info, $order_total) {
		$code = '';

		$start = strpos($order_total['title'], '(') + 1;
		$end = strrpos($order_total['title'], ')');

		if ($start && $end) {
			$code = substr($order_total['title'], $start, $end - $start);
		}

		if ($code) {
			$status = true;
			
			$coupon_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "coupon` WHERE code = '" . $this->db->escape($code) . "' AND status = '1'");

			if ($coupon_query->num_rows) {
				$coupon_total = $this->getTotalCouponHistoriesByCoupon($code);
	
				if ($coupon_query->row['uses_total'] > 0 && ($coupon_total >= $coupon_query->row['uses_total'])) {
					$status = false;
				}
				
				if ($order_info['customer_id']) {
					$customer_total = $this->getTotalCouponHistoriesByCustomerId($code, $order_info['customer_id']);
					
					if ($coupon_query->row['uses_customer'] > 0 && ($customer_total >= $coupon_query->row['uses_customer'])) {
						$status = false;
					}
				}
			} else {
				$status = false;	
			}

			if ($status) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "coupon_history` SET coupon_id = '" . (int)$coupon_query->row['coupon_id'] . "', order_id = '" . (int)$order_info['order_id'] . "', customer_id = '" . (int)$order_info['customer_id'] . "', amount = '" . (float)$order_total['value'] . "', date_added = NOW()");
			} else {
				return $this->config->get('config_fraud_status_id');
			}
		}
	}

	public function unconfirm($order_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "coupon_history` WHERE order_id = '" . (int)$order_id . "'");
	}
	
	public function getTotalCouponHistoriesByCoupon($coupon) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "coupon_history` ch LEFT JOIN `" . DB_PREFIX . "coupon` c ON (ch.coupon_id = c.coupon_id) WHERE c.code = '" . $this->db->escape($coupon) . "'");	
		
		return $query->row['total'];
	}
	
	public function getTotalCouponHistoriesByCustomerId($coupon, $customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "coupon_history` ch LEFT JOIN `" . DB_PREFIX . "coupon` c ON (ch.coupon_id = c.coupon_id) WHERE c.code = '" . $this->db->escape($coupon) . "' AND ch.customer_id = '" . (int)$customer_id . "'");
		
		return $query->row['total'];
	}
	
	
}
