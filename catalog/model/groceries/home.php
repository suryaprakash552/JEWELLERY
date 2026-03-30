<?php
namespace Opencart\Catalog\Model\Groceries;

class Home extends \Opencart\System\Engine\Model {
    
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
			$order_products = $this->model_groceries_home->getProducts($order_id);

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


public function getOrderTotalsByDateRange($from_date, $to_date, $agentId) {

    $sql = " SELECT

            COALESCE(SUM(
                CASE 
                    WHEN o.order_status_id IN (5,6) 
                    THEN oi.cash_amount 
                    ELSE 0 
                END
            ), 0) AS total_cash,

            COALESCE(SUM(
                CASE 
                    WHEN o.order_status_id IN (5,6) 
                    THEN oi.upi_amount 
                    ELSE 0 
                END
            ), 0) AS total_upi,

            COALESCE(SUM(oi.returnable_balance), 0) AS total_returnable,

            COALESCE(SUM(oi.balance), 0) AS balance,

            COALESCE(SUM(
                CASE 
                    WHEN o.order_status_id IN (5,6) 
                    THEN oi.sub_total 
                    ELSE 0 
                END
            ), 0) AS total_subtotal,

            COALESCE(SUM(
                CASE 
                    WHEN o.order_status_id IN (5,6) 
                    THEN oi.total_received 
                    ELSE 0 
                END
            ), 0) AS total_received

        FROM `" . DB_PREFIX . "order` o

        INNER JOIN `" . DB_PREFIX . "order_invoice` oi
            ON oi.order_id = o.order_id

        WHERE DATE(o.date_added) >= '" . $this->db->escape($from_date) . "'
          AND DATE(o.date_added) <= '" . $this->db->escape($to_date) . "'
          AND o.customer_group_id = '" . (int)$agentId . "'
    ";

    return $this->db->query($sql)->row;
}

public function isOrderCancelled(int $order_id): bool
{
    $q = $this->db->query("SELECT order_status_id FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "' LIMIT 1");

    return $q->num_rows && (int)$q->row['order_status_id'] === 7;
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

public function getProducts(int $order_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = '" . (int)$order_id . "'");

		return $query->rows;
	}
	
	public function getTotals(int $order_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE `order_id` = '" . (int)$order_id . "' ORDER BY `sort_order` ASC");

		return $query->rows;
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
    
    public function doWalletAepsCredit($credit)
{
    // ensure wallet row exists
    // $wallet = $this->db->query("SELECT customerid FROM `" . DB_PREFIX . "manage_wallet`
    //                             WHERE customerid = '" . (int)$credit['customerid'] . "' LIMIT 1");

    // if (!$wallet->num_rows) {
    //     $this->db->query("INSERT INTO `" . DB_PREFIX . "manage_wallet`
    //                       SET customerid = '" . (int)$credit['customerid'] . "'");
    // }

    $this->db->query("UPDATE `" . DB_PREFIX . "manage_wallet`
                      SET aeps_amount = IFNULL(aeps_amount,0) + " . (float)$credit['amount'] . "
                      WHERE customerid = '" . (int)$credit['customerid'] . "'");

    if ($this->db->countAffected() > 0) {

        $walletInfo = $this->getWalletInfo($credit['customerid']);
        $balance = isset($walletInfo['aeps_amount']) ? (float)$walletInfo['aeps_amount'] : 0;

        $this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction
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

public function getCustomerByMobile($mobile) {
        $query = $this->db->query("
            SELECT *
            FROM " . DB_PREFIX . "customer
            WHERE telephone = '" . $this->db->escape($mobile) . "'
            LIMIT 1
        ");

        return $query->row;
    }
    
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

public function getQuoteFullDetails(int $quote_id)
{
    // Quote main
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
}                                                           public function getQuoteTotalsByDateRange($from_date, $to_date, $agentId)
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
	
}