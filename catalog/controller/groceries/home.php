<?php
namespace Opencart\Catalog\Controller\Groceries;

class Home extends \Opencart\System\Engine\Controller {
    
    private function validateToken() {

    $token = $this->request->get['token'] ?? '';

    if (!$token) {
        return false;
    }

    $this->load->model('groceries/categories');

    $customer_id = $this->model_groceries_categories->validateToken($token);

    return $customer_id;
    }
    
    public function addorder()
    {
        $this->response->addHeader("Content-Type: application/json");
        
        $agentId = $this->validateToken();

        if(!$agentId){
        
        return $this->response->setOutput(
        
        json_encode([
        
        "status"=>"error",
        
        "message"=>"Invalid Token"
        
        ])
        
        );
        
        }

        $post = $this->request->post;

        $raw = file_get_contents("php://input");
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $post = array_merge($post, $decoded);
            }
        }
        $get = function ($arr, $key, $default = "") {
            return isset($arr[$key]) ? $arr[$key] : $default;
        };
        $money = function ($v) {
            return (float) preg_replace("/[^0-9.\-]/", "", (string) $v);
        };

        try {
            $orderDetails = $get($post, "orderDetails", []);
            $incoming_tax_details = $get($orderDetails, "taxDetails", []);
            $previousOrderId = (int) $get($orderDetails, "previousOrderId", 0);
            $activeQuoteId = (int) $get($orderDetails, "activeQuoteId", 0);
            $editOrderId = (int) $get($orderDetails, "previourseditorderid", 0);
            $customer_id = $get($orderDetails, "customerIdNumber", 0);
            $customer_name = $get($orderDetails, "CustomerName", "");
            $payment_address_1 = $get($orderDetails, "Payment_address_1", "");
            $payment_address_2 = $get($orderDetails, "Payment_address_2", "");
            $payment_city = $get($orderDetails, "Payment_city", "");
            $payment_postcode = $get($orderDetails, "Payment_postcode", "");
            $payment_country = $get($orderDetails, "Payment_country", "");
            $payment_zone = $get($orderDetails, "Payment_zone", "");
            $email = $get($orderDetails, "Email", "");
            $mobile = $get($orderDetails, "Mobile", "");
            // $customer_group_id = (int) $get($orderDetails,"customer_group_id",0
            // );

            // if ($customer_group_id === 0 && $agentId) {
            //     $this->load->model("account/customer");
            //     $agent_info = $this->model_account_customer->getCustomer((int) $agentId);

            //     if (!empty($agent_info["customer_group_id"])) {
            //         $customer_group_id =(int) $agent_info["customer_group_id"];
            //     }
            // }

            $invoiceInfo = $get($orderDetails, "InvoiceInfo", []);

            $subtotal = $money($get($invoiceInfo, "SUBTotal", 0));
            $discount = $money($get($invoiceInfo, "DiscountIncluded", 0));
            $tax_included = $money($get($invoiceInfo, "TaxIncluded", 0));
            $total_before_round = $money($get($invoiceInfo, "TotalBeforeRoundoff", 0));
            $roundoff_amount = $money($get($invoiceInfo, "RoundOffAmount", 0));
            $qty_total = (int) $get($invoiceInfo, "QuantityTotal", 0);
            $items_count = (int) $get($invoiceInfo, "NumberOfItems", 0);
            $credits_for_order = $money($get($invoiceInfo, "credits_for_order", 0));
            $creditpointsused = $money($get($orderDetails, "CreditPointsUsed", 0));
            $redeem_points_status = filter_var($get($orderDetails, "redeem_points_status", false),FILTER_VALIDATE_BOOLEAN);

            $coupon = $get($invoiceInfo, "Coupon", "");
            $coupon_amount = $money($get($invoiceInfo, "CouponAmount", 0));
            $coupon_final = $coupon;
            if (!empty($coupon) && $coupon_amount > 0) {
                $coupon_final = $coupon . '-' . number_format($coupon_amount, 2, '.', '');
            }
            $total_tax = $money($get($invoiceInfo, "TotalTax", 0));

            $full_invoice_number = $get($invoiceInfo, "InvoiceNumber", "");
            // Reward calculation
            $reward_points = 0;
            
            if ($subtotal >= 1000) {
                $reward_points = floor($subtotal / 1000) * 5;
            }


            $invoice_prefix = "";
            $invoice_no = "";

            if (!empty($full_invoice_number)) {
                $parts = explode("-", $full_invoice_number, 2);
                $invoice_prefix = isset($parts[0]) ? $parts[0] . "-" : "";
                $invoice_no = $parts[1] ?? "";
            }

            $paymentThrough = $get($orderDetails, "PaymentThrough", "");
            $cash_amount = $money($get($orderDetails, "CashAmount", 0));
            $upi_amount = $money($get($orderDetails, "UPIAmount", 0));
            $advance_used = $money($get($orderDetails, "AdvanceUsed", 0));
            $total_received = $money(
                $get($orderDetails, "TotalReceivedAmount", 0)
            );
            $pending_amount = $money($get($orderDetails, "PendingAmount", 0));
            $return_balance = $money(
                $get($orderDetails, "ReturnableBalance", 0)
            );
            $saveAdvance = (bool)$get($orderDetails, "SaveReturnableAsAdvance", false);

            $dueAmountUsed  = (bool)$get($orderDetails, "DueAmountUsed", false);
            $dueAmountValue = $money($get($orderDetails, "DueAmountValue", 0));

            $note = $get($orderDetails, "Note", "");
            $cart_products = $get($orderDetails, "CartProducts", []);
            $order_data = [];
            $order_data["invoice_prefix"] = $invoice_prefix;
            $order_data["invoice_no"] = $invoice_no;

            $order_data["customer_id"] = (int) $customer_id;
            $order_data["customer_group_id"] = $agentId;
            $order_data["sellerId"] = (int) $get($orderDetails, "SellerId", 0);
            $order_data["quote_id"] = $activeQuoteId;
            $order_data["pre_order_id"] = $previousOrderId;
            //$order_data["pre_amount"] = $previousAmount;
            $name_parts = explode(" ", $customer_name, 2);
            $order_data["firstname"] = $name_parts[0] ?? "";
            $order_data["lastname"] = $name_parts[1] ?? "";

            $order_data["email"] = $email;
            $order_data["telephone"] = $mobile;
            $order_data["custom_field"] = [];
            
            $order_data["payment_method"] = [
                "name" => $paymentThrough,
                "code" => strtolower($paymentThrough),
            ];
            
            $order_data["payment_address_1"] = $payment_address_1;
            $order_data["payment_address_2"] = $payment_address_2;
            $order_data["payment_city"] = $payment_city;
            $order_data["payment_postcode"] = $payment_postcode;
            $order_data["payment_country"] = $payment_country;
            $order_data["payment_zone"] = $payment_zone;

            $order_data["total"] = $total_before_round;

            $order_data["products"] = [];
            foreach ($cart_products as $p) {
                $order_data["products"][] = [
                    "product_id" => (int) ($p["product_id"] ?? 0),
                    "name" => $p["name"] ?? "",
                    "model" => "",
                    "option" => [],
                    "quantity" => (int) ($p["quantity"] ?? 1),
                    "price" => (float) ($p["price"] ?? 0),
                    "total" => (float) ($p["total"] ?? 0),
                    "gst" => (float) ($p["gst_percent"] ?? 0),
                    "tax" => (float) ($p["row_gst"] ?? 0),
                    "excluded"   => !empty($p["excluded"]) ? 1 : 0
                ];
            }

            $order_data["comment"] = $note;
            $invoice_extra = [
                "customer_group_id" => $agentId,
                "cash_amount" => $cash_amount,
                "upi_amount" => $upi_amount,
                "coupon" => $coupon_final,
                "credit_points" => $reward_points,
                "discount" => $discount,
                "number_of_items" => $items_count,
                "quantity_of_items" => $qty_total,
                "sub_total" => $subtotal,
                "total_tax" => $total_tax,
                "roundoff_amount" => $roundoff_amount,
                "amount_through" => $paymentThrough,
                "pending_amount" => $pending_amount,
                "returnable_balance" => $return_balance,
                "advance_used" => $advance_used,
                "total_received" => $total_received,
                "creditpointsused" => $creditpointsused,
                "balance" => $dueAmountValue,
                "save_advance" => $saveAdvance
            ];
            $order_data["custom_fields"] = [];
            if (
                !empty($incoming_tax_details) &&
                is_array($incoming_tax_details)
            ) {
                foreach ($incoming_tax_details as $td) {
                    if (!empty($td["name"])) {
                        $order_data["custom_fields"][] = [
                            "name" => (string) $td["name"],
                            "value" => (float) ($td["value"] ?? 0),
                        ];
                    }
                }
            }

            $this->load->model("checkout/order");
            if ($editOrderId > 0) {
                $this->model_checkout_order->editPreviousOrder($editOrderId, $order_data, $invoice_extra);
                $order_id = $editOrderId;
            } else {
                $order_id = $this->model_checkout_order->addOrder($order_data, $invoice_extra);
            }

            if ($activeQuoteId > 0) {
                $this->model_checkout_order->completeQuote($activeQuoteId);
            }


            if (!$order_id) {
                throw new Exception("Order creation failed");
            }

            if ($editOrderId > 0) {

                $this->model_checkout_order->addHistory(
                    $order_id,
                    17,
                    "",
                    false,
                    true
                );
            
            // 2️⃣ RETURN ORDER
            } elseif ($previousOrderId > 0) {
            
                $this->model_checkout_order->addHistory(
                    $previousOrderId,
                    4,
                    "",
                    false,
                    true
                );
            
                $this->model_checkout_order->addHistory(
                    $order_id,
                    6, // Return Completed
                    "",
                    false,
                    true
                );
            
            } else {
            
                $this->model_checkout_order->addHistory(
                    $order_id,
                    5, // Complete
                    "",
                    false,
                    true
                );
            }
            

            $this->session->data['dqr12_device_send'] = [
            
            'type'   => 'success',
            
            'amount' => $upi_amount,
            
            'time'   => time()
            
            ];


            $saveAdvance = (bool) $get($orderDetails, "SaveReturnableAsAdvance", false);
            $dueAmountUsed  = (bool) $get($orderDetails, "DueAmountUsed", false);
            $dueAmountValue = (float) preg_replace("/[^0-9.\-]/", "", (string) $get($orderDetails, "DueAmountValue", 0));
            $includedDA = (bool)$get($orderDetails, "includedDA", false);
            $includedDAAmount = $money($get($orderDetails, "includedDAAmount", 0));
            
            if ($customer_id > 0) {
                
                // Deduct advance when AA is used
                if ($advance_used > 0) {
                
                    $debit = [
                        "customerid" => $customer_id,
                        "order_id" => $order_id,
                        "amount" => $advance_used,
                        "description" => "Advance used in order #" . $order_id,
                        "transactiontype" => "DEBIT",
                        "transactionsubtype" => "TRADE",
                        "txtid" => $order_id,
                    ];
                
                    $this->model_checkout_order->doWalletCredit($debit);
                }
            
                if ($saveAdvance && $return_balance > 0) {
            
                    $credit = [
                        "customerid" => $customer_id,
                        "order_id" => $order_id,
                        "amount" => $return_balance,
                        "description" => "Returnable saved as advance - Order #" . $order_id,
                        "transactiontype" => "CREDIT",
                        "transactionsubtype" => "TRADE",
                        "txtid" => $order_id,
                    ];
            
                    $this->model_checkout_order->doWalletCredit($credit);
                }
            
                // 2) Due -> manage_wallet.aeps_amount (AEPS)
                if ($dueAmountValue > 0) {
            
                    $aepsCredit = [
                        "customerid" => $customer_id,
                        "order_id" => $order_id,
                        "amount" => $dueAmountValue,
                        "description" => "Due amount added - Order #" . $order_id,
                        "transactiontype" => "CREDIT",
                        "transactionsubtype" => "AEPS",
                        "txtid" => $order_id,
                    ];
            
                    // you must add this function in model_checkout_order
                    $this->model_checkout_order->doWalletAepsCredit($aepsCredit);
                }
                
                // Reduce AEPS amount if DA is used in order
            if ($includedDA && $includedDAAmount > 0) {
            
                $aepsDebit = [
                    "customerid" => $customer_id,
                    "order_id" => $order_id,
                    "amount" => $includedDAAmount,
                    "description" => "Due amount used in order #" . $order_id,
                    "transactiontype" => "DEBIT",
                    "transactionsubtype" => "AEPS",
                    "txtid" => $order_id,
                ];
            
                $this->model_checkout_order->doWalletAepsCredit($aepsDebit);
            }

$this->load->model("account/reward");

if ($customer_id > 0) {


    if ($redeem_points_status) {

        $this->model_account_reward->clearAllRewards($customer_id);
    }

    if ($reward_points > 0) {

        $this->model_account_reward->addReward([
            "customer_id" => $customer_id,
            "order_id"    => $order_id,
            "points"      => $reward_points,
            "description" => "Reward earned for order #$order_id",
            "status"      => "active",
        ]);
    }
}


        // if previous order_id is available remove the reward points for the order to the customer(cancelled order)
        // if previous order_id is available remove the reward points for the previous order then insert new reward points to the customer(return and edit order)
            }

            $this->load->model("account/customer");

            $credit = [
                "customerid" => $agentId,
                "order_id" => $order_id,
                "amount" => $subtotal,
                "description" => $mobile,
                "transactiontype" => "CREDIT",
                "transactionsubtype" => "TRADE",
                "txtid" => $order_id,
            ];
            return $this->response->setOutput(
                json_encode([
                    "status" => "success",
                    "order_id" => $order_id,
                    "walletUpdate" => $this->model_checkout_order->doWalletCredit(
                        $credit
                    ),
                ])
            );
        } catch (Throwable $e) {
            return $this->response->setOutput(
                json_encode([
                    "status" => "error",
                    "message" => $e->getMessage(),
                ])
            );
        }
    }
    
    public function autocomplete()
    {
        $agentId = $this->validateToken();

        if(!$agentId){
        
        return $this->response->setOutput(
        
        json_encode([
        
        "status"=>"error",
        
        "message"=>"Invalid Token"
        
        ])
        
        );
        
        }
        
        $json = [];

        if (isset($this->request->get["filter"])) {
            if (isset($this->request->get["filter"])) {
                $filter_name = $this->request->get["filter"];
            } else {
                $filter_name = "";
            }

            $filter_data = [
                "filter_name" => $filter_name,
                "start" => 0,
                "limit" => 5,
            ];

            $this->load->model("extension/purpletree_pos/pos/posproduct");
            $results = $this->model_extension_purpletree_pos_pos_posproduct->getCustomers(
                $filter_data
            );
            if ($results != null) {
                foreach ($results as $result) {
                    $address = [];
                    $default_address = [];
                    if (isset($result["customer_id"])) {
                        $address_id = $this->model_extension_purpletree_pos_pos_posproduct->getAddressId(
                            $result["customer_id"]
                        );

                        if ($address_id != null) {
                            $default_address = $this->model_extension_purpletree_pos_pos_posproduct->getCustmerAddress(
                                $address_id,
                                $result["customer_id"]
                            );
                            $address = $this->model_extension_purpletree_pos_pos_posproduct->getAddresses(
                                $result["customer_id"]
                            );
                        }
                    }
                    $this->load->model("ws/transactions/common");
                    $rewards = $this->model_ws_transactions_common->getReward(
                        $result["customer_id"]
                    );
                    $wallet_info = $this->model_ws_transactions_common->getWalletInfo(
                        $result["customer_id"]
                    );
                    
                    $this->load->model("checkout/order");
                        $last_order = $this->model_checkout_order->getLastOrder($result['customer_id']);
                        
                        $last_order_id = isset($last_order['order_id']) ? $last_order['order_id'] : 0;
                        $last_order_total = isset($last_order['total']) ? $last_order['total'] : 0;
                     //$wallet=[];
                    $wallet = isset($wallet_info["amount"])
                        ? $wallet_info["amount"]
                        : 0;
                    $aeps = isset($wallet_info["aeps_amount"])
                        ? $wallet_info["aeps_amount"]
                        : 0;

                    $json[] = [
                        "customer_id" => $result["customer_id"],
                        "customer_group_id" => $result["customer_group_id"],
                        "name" => $result["name"],
                        "customer_group" => $result["customer_group"],
                        "firstname" => $result["firstname"],
                        "lastname" => $result["lastname"],
                        "email" => $result["email"],
                        "telephone" => $result["telephone"],
                        "custom_field" => $result["custom_field"]
                            ? json_decode($result["custom_field"], true)
                            : [],
                        "address" => $address,
                        "default_address" => $default_address,
                        "rewards" => $rewards,
                        "wallet" => $wallet,
                        "due_amount" => $aeps,
                        "last_order_id" => $last_order_id,
                        "last_order_total" => $last_order_total,
                    ];
                }
            }
        }

        $sort_order = [];

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value["name"];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    
    public function applycoupon()
    {
        $agentId = $this->validateToken();

        if(!$agentId){
        
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
    
    public function searchByMobile() {

        $this->response->addHeader('Content-Type: application/json');
        
         $agentId = $this->validateToken();

        if(!$agentId){
        
        return $this->response->setOutput(
        
        json_encode([
        
        "status"=>"error",
        
        "message"=>"Invalid Token"
        
        ])
        
        );
        
        }
        

        $mobile = $this->request->post['mobile'] ?? '';

        if (!$mobile) {
            return $this->response->setOutput(json_encode([
                'status'  => 'error',
                'message' => 'Mobile number is required'
            ]));
        }

        $this->load->model("checkout/order");

        $customer = $this->model_checkout_order->getCustomerByMobile($mobile);

        if (!$customer) {
            return $this->response->setOutput(json_encode([
                'status'  => 'error',
                'message' => 'Customer not found'
            ]));
        }

        $customer_id = (int)$customer['customer_id'];

        $wallet = $this->model_checkout_order->getWalletByCustomerId($customer_id);

        $transactions = $this->model_checkout_order->getTransactionsByCustomerId($customer_id);

        return $this->response->setOutput(json_encode([
            'status'       => 'success',
            'customer'     => $customer,
            'wallet'       => $wallet,
            'transactions' => $transactions
        ]));
    }
    
    public function adjustTrade() {

    $this->response->addHeader('Content-Type: application/json');
    $agentId = $this->validateToken();

    if(!$agentId){
        return $this->response->setOutput(json_encode([
            "status"=>"error",
            "message"=>"Invalid Token"
        ]));
    }

    $input = json_decode(file_get_contents('php://input'), true);

    $customer_id = (int)($input['customer_id'] ?? 0);
    $amount      = (float)($input['amount'] ?? 0);
    $type        = strtoupper($input['type'] ?? '');
    $description = trim($input['description'] ?? '');

    if (!$customer_id || $amount <= 0 || !in_array($type, ['CREDIT','DEBIT'])) {
        return $this->response->setOutput(json_encode([
            "message" => "Invalid input"
        ]));
    }

   $this->load->model("checkout/order");

    $result = $this->model_checkout_order
        ->adjustWallet($customer_id, $amount, $type, 'TRADE', $description);

    if ($result) {
        $json['status'] = 'success';
    } else {
        $json['message'] = 'Wallet update failed';
    }

    return $this->response->setOutput(json_encode($json));
}

public function adjustDue() {

    $this->response->addHeader('Content-Type: application/json');

    $agentId = $this->validateToken();

    if(!$agentId){
        return $this->response->setOutput(json_encode([
            "status"=>"error",
            "message"=>"Invalid Token"
        ]));
    }

    $input = json_decode(file_get_contents('php://input'), true);

    $customer_id = (int)($input['customer_id'] ?? 0);
    $amount      = (float)($input['amount'] ?? 0);
    $type        = strtoupper($input['type'] ?? '');
    $description = trim($input['description'] ?? '');

    if (!$customer_id || $amount <= 0 || !in_array($type, ['CREDIT','DEBIT'])) {
        return $this->response->setOutput(json_encode([
            "message" => "Invalid input"
        ]));
    }

    $this->load->model("checkout/order");

    $result = $this->model_checkout_order
        ->adjustWallet($customer_id, $amount, $type, 'AEPS', $description);

    if ($result) {
        $json['status'] = 'success';
    } else {
        $json['message'] = 'Wallet update failed';
    }

    return $this->response->setOutput(json_encode($json));
}

public function addQuoteOrder()
{
    $this->response->addHeader("Content-Type: application/json");
    
    $agentId = $this->validateToken();

    if(!$agentId){
        return $this->response->setOutput(json_encode([
            "status"=>"error",
            "message"=>"Invalid Token"
        ]));
    }

    try {

        $raw = file_get_contents("php://input");
        $payload = json_decode($raw, true);

        if (!is_array($payload) || empty($payload['quotation_details'])) {
            throw new \Exception("Invalid quotation payload");
        }

        $q = $payload['quotation_details'];
        $previousQuoteId = (int)($q['quote_id'] ?? 0);
        
        $full_invoice_number = trim($q['invoice_number'] ?? '');
        
        $invoice_prefix = 'SGC-';
        $invoice_no     = date('YmdHis');
        
        if ($full_invoice_number !== '') {
            // Remove prefix if JS already sent SGC-
            if (strpos($full_invoice_number, $invoice_prefix) === 0) {
                $invoice_no = substr($full_invoice_number, strlen($invoice_prefix));
            } else {
                // Fallback: store whole value as invoice_no
                $invoice_no = $full_invoice_number;
            }
        }



        $money = function ($v) {
            return (float) preg_replace("/[^0-9.\-]/", "", (string)$v);
        };

        $customer_name = trim($q['customer_name'] ?? '');
        $telephone     = trim($q['telephone'] ?? '');
        $email         = trim($q['email'] ?? '');

        if ($customer_name === '' || $telephone === '') {
            throw new \Exception("Customer name and phone are required");
        }

        $name_parts = explode(" ", $customer_name, 2);


        if (empty($q['products']) || !is_array($q['products'])) {
            throw new Exception("No products found in quotation");
        }

        $products = [];
        foreach ($q['products'] as $p) {
            $products[] = [
                "product_id" => (int)($p['product_id'] ?? 0),
                "name"       => $p['name'] ?? '',
                "quantity"   => (int)($p['quantity'] ?? 1),
                "price"      => (float)($p['price'] ?? 0),
                "total"      => (float)($p['total'] ?? 0),
                "excluded"   => !empty($p['excluded']) ? 1 : 0
            ];
        }



        $order_data = [
            "invoice_prefix" => $invoice_prefix,
            "invoice_no"     => $invoice_no,
            "customer_id"       => 0,
            "customer_group_id" => $agentId,
            "sellerId"          => $agentId,
            "pre_order_id"      => 0,
            "firstname"         => $name_parts[0] ?? '',
            "lastname"          => $name_parts[1] ?? '',
            "email"             => $email,
            "telephone"         => $telephone,
            "custom_field"      => [],
            "payment_method"    => [],
            "comment"           => 'Quotation generated',
            "total"             => $money($q['final_total'] ?? 0),
            "products"          => $products
        ];


        $invoice_extra = [
            "customer_group_id" => $agentId,
            "discount"          => $money($q['discount'] ?? 0),
            "number_of_items"   => count($products),
            "quantity_of_items" => array_sum(array_column($products, 'quantity')),
            "sub_total"         => $money($q['net_total'] ?? 0),
            "total_tax"         => $money($q['total_tax'] ?? 0),
            "roundoff_amount"   => 0,
            "pending_amount"    => 0,
            "returnable_balance"=> 0,
            "cash_amount"       => 0,
            "upi_amount"        => 0,
            "coupon"            => '',
            "credit_points"     => 0,
            "creditpointsused"  => 0,
            "amount_through"    => '',
            "total_received"    => 0,
            "balance"           => 0
        ];


        $this->load->model('checkout/order');

        $quote_id = $this->model_checkout_order->addQuoteOrder($order_data,$invoice_extra,$previousQuoteId);

        if (!$quote_id) {
            throw new \Exception("Quote creation failed");
        }

        return $this->response->setOutput(json_encode([
            "status"   => "success",
            "quote_id" => $quote_id
        ]));

    } catch (Throwable $e) {
        return $this->response->setOutput(json_encode([
            "status"  => "error",
            "message" => $e->getMessage()
        ]));
    }
}

public function cancelQuoteOrder()
{
    $this->response->addHeader("Content-Type: application/json");
    $agentId = $this->validateToken();

    if(!$agentId){
        return $this->response->setOutput(json_encode([
            "status"=>"error",
            "message"=>"Invalid Token"
        ]));
    }
    

    try {

        $order_id = (int)($this->request->post['order_id'] ?? 0);

        if (!$order_id) {
            throw new Exception("order_id is required");
        }

        $this->load->model('checkout/order');

        $result = $this->model_checkout_order->cancelQuoteOrder($order_id);

        if (!$result) {
            throw new Exception("Order not found or already cancelled");
        }

        return $this->response->setOutput(json_encode([
            "success" => true,
            "message" => "Quote cancelled successfully"
        ]));

    } catch (Throwable $e) {
        return $this->response->setOutput(json_encode([
            "success" => false,
            "error"   => $e->getMessage()
        ]));
    }
}

public function getQuotesByDate()
{
    $this->response->addHeader("Content-Type: application/json");
    
    $agentId = $this->validateToken();

    if(!$agentId){
        return $this->response->setOutput(json_encode([
            "status"=>"error",
            "message"=>"Invalid Token"
        ]));
    }

    $from_date = $this->request->get["from_date"] ?? "";
    $to_date   = $this->request->get["to_date"] ?? "";
    $order_id  = $this->request->get["order_id"] ?? "";
    $mobile    = $this->request->get["mobile"] ?? "";
    $name      = $this->request->get["name"] ?? "";

    if (empty($from_date) || empty($to_date)) {
        $today = date("Y-m-d");
        $from_date = $today;
        $to_date   = $today;
    }


    $this->load->model("checkout/order");

    $quotes = $this->model_checkout_order->getQuotesByDateRange($agentId,$from_date,$to_date,$order_id,$mobile,$name);

    $totals = $this->model_checkout_order->getQuoteTotalsByDateRange($from_date, $to_date, $agentId);

    return $this->response->setOutput(json_encode([
        "status"       => "success",
        "from_date"    => $from_date,
        "to_date"      => $to_date,
        "agent_id"     => $agentId,
        "total_quotes" => count($quotes),
        "totals"       => $totals,
        "data"         => $quotes
    ]));
}

   public function getWalletInfo()
{
    $this->response->addHeader("Content-Type: application/json");

    $agentId = $this->validateToken();

    if(!$agentId){
        return $this->response->setOutput(json_encode([
            "status"=>"error",
            "message"=>"Invalid Token"
        ]));
    }

    $from_date =
        $this->request->get["from_date"] ??
        ($this->request->post["from_date"] ?? "");

    $to_date =
        $this->request->get["to_date"] ??
        ($this->request->post["to_date"] ?? "");

    if (empty($from_date) || empty($to_date)) {
        $today = date("Y-m-d");
        $from_date = $today;
        $to_date   = $today;
    }

    $this->load->model("account/customer");

    $customer_info = $this->model_account_customer->getCustomer($agentId);

    $customer_id = !empty($customer_info)
        ? (int)$customer_info["customer_id"]
        : 0;

    $this->load->model("checkout/order");

    $wallet_info = $this->model_checkout_order->getWalletInfo($customer_id);

    $raw = [
        "from_date" => $from_date,
        "to_date"   => $to_date,
    ];

    $walletHistory = $this->model_checkout_order->walletTradeHistory(
        $customer_id,
        $raw
    );

    $walletHistory["walletAmount"] = $wallet_info["amount"] ?? 0;

    return $this->response->setOutput(json_encode($walletHistory));
}


public function generateUpiQr() {
    $this->response->addHeader('Content-Type: application/json');
    
    $agentId = $this->validateToken();

    if(!$agentId){
        return $this->response->setOutput(json_encode([
            "status"=>"error",
            "message"=>"Invalid Token"
        ]));
    }

    try {

        // Get amount from POST
        $amount = isset($this->request->post['amount'])
            ? (float)$this->request->post['amount']
            : 0;

        if ($amount <= 0) {
            throw new \Exception('Invalid amount');
        }

        $upi_id = "7075026338@ybl";
        $name   = "Myteknoland";

        $upi_url = "upi://pay?pa=" . $upi_id .
                   "&pn=" . urlencode($name) .
                   "&am=" . $amount .
                   "&cu=INR";

            $this->session->data['dqr12_device_send'] = [
            
            'upi_url' => $upi_url,
            
            'amount'  => $amount,
            
            'vpa'     => $upi_id,
            
            'time'    => time()
            
            ];

        require_once($_SERVER['DOCUMENT_ROOT'] . '/phpqrcode/qrlib.php');


        // 🔹 Create QR folder if not exists
        $folder = DIR_IMAGE . 'pos_qr/';
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $file = 'pos_qr/upi_' . time() . '_' . rand(1000,9999) . '.png';
        $full_path = DIR_IMAGE . $file;

        \QRcode::png($upi_url, $full_path, QR_ECLEVEL_L, 5);

        $qr_url = $this->config->get('config_url') . 'image/' . $file;

        $this->response->setOutput(json_encode([
            'status'   => 'success',
            'amount'   => $amount,
            'upi_url'  => $upi_url,
            'qr_image' => $qr_url
        ]));

    } catch (\Throwable $e) {

        $this->response->setOutput(json_encode([
            'status'  => 'error',
            'message' => $e->getMessage()
        ]));
    }
}

public function generateUpiQr1() {
    $this->response->addHeader('Content-Type: application/json');
    
    $agentId = $this->validateToken();

    if(!$agentId){
        return $this->response->setOutput(json_encode([
            "status"=>"error",
            "message"=>"Invalid Token"
        ]));
    }

    try {

        // Get amount from POST
        $amount = isset($this->request->post['amount'])
            ? (float)$this->request->post['amount']
            : 0;

        if ($amount <= 0) {
            throw new \Exception('Invalid amount');
        }

        $upi_id = "9741957694@ybl";
        $name   = "MTL";

        $upi_url = "upi://pay?pa=" . $upi_id .
                   "&pn=" . urlencode($name) .
                   "&am=" . $amount .
                   "&cu=INR";
                   
                   // SEND TO DEVICE SESSION

                    $this->session->data['dqr12_device_send'] = [
                    
                    'upi_url' => $upi_url,
                    
                    'amount'  => $amount,
                    
                    'vpa'     => $upi_id,
                    
                    'time'    => time()
                    
                    ];

        // 🔹 Load QR library
        require_once($_SERVER['DOCUMENT_ROOT'] . '/phpqrcode/qrlib.php');


        // 🔹 Create QR folder if not exists
        $folder = DIR_IMAGE . 'pos_qr/';
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $file = 'pos_qr/upi_' . time() . '_' . rand(1000,9999) . '.png';
        $full_path = DIR_IMAGE . $file;

        \QRcode::png($upi_url, $full_path, QR_ECLEVEL_L, 5);

        $qr_url = $this->config->get('config_url') . 'image/' . $file;

        $this->response->setOutput(json_encode([
            'status'   => 'success',
            'amount'   => $amount,
            'upi_url'  => $upi_url,
            'qr_image' => $qr_url
        ]));

    } catch (\Throwable $e) {

        $this->response->setOutput(json_encode([
            'status'  => 'error',
            'message' => $e->getMessage()
        ]));
    }
}

}