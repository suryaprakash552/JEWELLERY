<?php
namespace Opencart\Catalog\Controller\Extension\PurpletreePos\Pos;
class Home extends \Opencart\System\Engine\Controller
{
    private $error = [];
    public function index()
    {
        if (isset($_COOKIE["pos_login"]) && $_COOKIE["pos_login"] == "true") {
            setcookie("pos_login", "false", time() + 86400 * 30 * 5, "/");
        }

        $data = [];
        if (!$this->config->get("module_purpletree_pos_status")) {
            $this->response->redirect(
                $this->url->link("common/home", "", true)
            );
        }
        $this->load->language("extension/purpletree_pos/pos/agentlogin");
        $this->load->language("product/product");
        $this->load->language("extension/purpletree_pos/extension/pos/home");

        if ($this->customer->isLogged()) {
            $data["agent_load_data"] = false;
            $this->load->model("account/customer");
            if (
                isset($_COOKIE["pos_login"]) &&
                $_COOKIE["pos_login"] == "true"
            ) {
                $data["agent_load_data"] = true;
                $this->load->model("extension/purpletree_pos/pos/agent");
                $this->load->model("localisation/country");
                $this->load->model("localisation/zone");
                $this->load->model("localisation/order_status");
                $data["loggedcus"] = $this->customer->getId();
                $agent_detail = [];
                $agent_detail = $this->model_extension_purpletree_pos_pos_agent->is_agent(
                    $this->customer->getId()
                );

                if ($agent_detail) {
                    $this->load->model("catalog/product");
                    $this->load->model(
                        "extension/purpletree_pos/pos/posproduct"
                    );
                    
                    $data["posData"]["agent_flags"] = [
                        "wallet"        => (int)$agent_detail["wallet"],
                        "return_order"  => (int)$agent_detail["return_order"],
                        "cancel_order"  => (int)$agent_detail["cancel_order"],
                        "delete_order"  => (int)$agent_detail["delete_order"]
                    ];
                    
                    $data["agent_flags"] = $agent_detail;

                    if ($this->request->server["HTTPS"]) {
                        $server = $this->config->get("config_ssl");
                    } else {
                        $server = $this->config->get("config_url");
                    }
                    //$data['image_path'] = $server . 'image/catalog/pos/';
                    $data["home"] = $this->url->link(
                        "extension/purpletree_pos/pos/home",
                        "",
                        true
                    );
                    // //POS Products
                    // $posproducts = $this->model_extension_purpletree_pos_pos_posproduct->getProductid();
                    // $data["products"] = [];
                    // if (!empty($posproducts)) {
                    //     foreach ($posproducts as $posproduct) {
                    //         $data["products"][
                    //             $posproduct["product_id"]
                    //         ] = $this->POSProducts($posproduct);
                    //     }
                    // }

                    //POS Popular Products
                    $data["popular_products"] = [];
                    $populars = [];
                    if (!empty($populars)) {
                        foreach ($populars as $key => $value) {
                            if (
                                array_key_exists(
                                    $value["product_id"],
                                    $data["products"]
                                )
                            ) {
                                $data["popular_products"][
                                    $value["product_id"]
                                ] = $data["products"][$value["product_id"]];
                            }
                        }
                    }

                    //get All categories form table
                    $this->load->model(
                        "extension/purpletree_pos/pos/posproduct"
                    );
                    $data["posData"]["productForCart"] = [];
                    $data["posData"]["currentDate"] = date("Y-m-d");

                    $data["posData"]["allCategories"] = [];
                    $data["posData"]["allCategoriesDescription"] = [];
                    $data["posData"]["allTopCategory"] = [];
                    $data["posData"][
                        "allCategories"
                    ] = [];
                    $data["posData"][
                        "allCategoriesDescription"
                    ] = [];
                    $data["posData"][
                        "getProducttoCategory"
                    ] = [];

                    $data["posData"][
                        "allTopCategory"
                    ] = [];

                    // coupon code
                    $data["posData"]["coupon_product"] = [];
                    $data["posData"][
                        "coupon_product"
                    ] = $this->model_extension_purpletree_pos_pos_posproduct->getAllCoupon();

                    // product data
                    // $data["posData"][
                    //     "posProducts"
                    // ] = $this->model_extension_purpletree_pos_pos_posproduct->getPosProducts();
                    $data["posData"][
                        "posProductDescription"
                    ] = $this->model_extension_purpletree_pos_pos_posproduct->getPosProductDescription();
                    // $data["posData"][
                    //     "posProductOption"
                    // ] = $this->model_extension_purpletree_pos_pos_posproduct->getPosProductOption();
                    // $data["posData"][
                    //     "posOption"
                    // ] = $this->model_extension_purpletree_pos_pos_posproduct->getPosOption();
                    // $data["posData"][
                    //     "posOptionDescription"
                    // ] = $this->model_extension_purpletree_pos_pos_posproduct->getPosOptionDescription();
                    // $data["posData"][
                    //     "posProductOptionValue"
                    // ] = $this->model_extension_purpletree_pos_pos_posproduct->getPosProductOptionValue();
                    // $data["posData"][
                    //     "posOptionValue"
                    // ] = $this->model_extension_purpletree_pos_pos_posproduct->getPosOptionValue();
                    // $data["posData"][
                    //     "posOptionValueDescription"
                    // ] = $this->model_extension_purpletree_pos_pos_posproduct->getPosOptionValueDescription();
                    // $data["posData"][
                    //     "posProductDiscount"
                    // ] = $this->model_extension_purpletree_pos_pos_posproduct->getPosProductDiscount();
                    // if (version_compare(VERSION, "4.0.2.3", "<=")) {
                    //     $data["posData"][
                    //         "posProductSpecial"
                    //     ] = $this->model_extension_purpletree_pos_pos_posproduct->getPosProductSpecial();
                    // }
                    // $data["posData"][
                    //     "posProductReward"
                    // ] = $this->model_extension_purpletree_pos_pos_posproduct->getPosProductReward();
                    // $data["posData"][
                    //     "weightUnit"
                    // ] = $this->model_extension_purpletree_pos_pos_posproduct->getWeightUnit();
                    // $data["posData"][
                    //     "lengthUnit"
                    // ] = $this->model_extension_purpletree_pos_pos_posproduct->getLengthUnit();

                    $data["posData"][
                        "currency"
                    ] = $this->model_extension_purpletree_pos_pos_posproduct->getCurrency();

                    $data["posData"]["lang"][
                        "decimal_point"
                    ] = $this->language->get("decimal_point");
                    $data["posData"]["lang"][
                        "thousand_point"
                    ] = $this->language->get("thousand_point");
                    $data["posData"]["currency_code"] = $this->config->get(
                        "config_currency"
                    );

                    $data["posData"]["language_id"] = $this->config->get(
                        "config_language_id"
                    );
                    $data["posData"]["currency_id"] = $this->currency->getId(
                        $this->config->get("config_currency")
                    );
                    $data["posData"][
                        "currency_value"
                    ] = $this->currency->getValue(
                        $this->config->get("config_currency")
                    );
                    $data["posData"]["ip"] =
                        $this->request->server["REMOTE_ADDR"];
                    $order_data["ip"] = $this->request->server["REMOTE_ADDR"];

                    if (
                        !empty($this->request->server["HTTP_X_FORWARDED_FOR"])
                    ) {
                        $data["posData"]["forwarded_ip"] =
                            $this->request->server["HTTP_X_FORWARDED_FOR"];
                    } elseif (
                        !empty($this->request->server["HTTP_CLIENT_IP"])
                    ) {
                        $data["posData"]["forwarded_ip"] =
                            $this->request->server["HTTP_CLIENT_IP"];
                    } else {
                        $data["posData"]["forwarded_ip"] = "";
                    }

                    if (isset($this->request->server["HTTP_USER_AGENT"])) {
                        $data["posData"]["user_agent"] =
                            $this->request->server["HTTP_USER_AGENT"];
                    } else {
                        $data["posData"]["user_agent"] = "";
                    }

                    if (isset($this->request->server["HTTP_ACCEPT_LANGUAGE"])) {
                        $data["posData"]["accept_language"] =
                            $this->request->server["HTTP_ACCEPT_LANGUAGE"];
                    } else {
                        $data["posData"]["accept_language"] = "";
                    }

                    $data["posData"]["invoice_prefix"] = $this->config->get(
                        "config_invoice_prefix"
                    );
                    $data["posData"]["store_id"] = $this->config->get(
                        "config_store_id"
                    );
                    $data["posData"]["config_address"] = $this->config->get(
                        "config_address"
                    );
                    $data["posData"]["store_name"] = $this->config->get(
                        "config_name"
                    );
                    $data["posData"]["store_url"] = $this->config->get(
                        "config_url"
                    );

                    $data["posData"]["config_country_id"] = $this->config->get(
                        "config_country_id"
                    );
                    $data["posData"][
                        "countries"
                    ] = $this->model_localisation_country->getCountries(
                        "country_id"
                    );
                    $data["posData"][
                        "returnreason"
                    ] = $this->model_extension_purpletree_pos_pos_posproduct->getReturnReason(
                        $this->config->get("config_language_id")
                    );
                    $data["posData"][
                        "returnaction"
                    ] = $this->model_extension_purpletree_pos_pos_posproduct->getReturnAction(
                        $this->config->get("config_language_id")
                    );
                    $data["posData"][
                        "returnstatus"
                    ] = $this->model_extension_purpletree_pos_pos_posproduct->getReturnStatus(
                        $this->config->get("config_language_id")
                    );
                    $data["posData"][
                        "taxclass"
                    ] = $this->model_extension_purpletree_pos_pos_posproduct->getTaxClass();
                    $data["posData"][
                        "ordertotal"
                    ] = $this->model_extension_purpletree_pos_pos_posproduct->getOrderTotal();
                    $data["posData"][
                        "country_data"
                    ] = $this->model_localisation_country->getCountry(
                        $this->config->get("config_country_id")
                    );
                    $data["posData"]["config_zone_id"] = $this->config->get(
                        "config_zone_id"
                    );

                    $data["posData"][
                        "zone_data"
                    ] = $this->model_localisation_zone->getZone(
                        $this->config->get("config_zone_id")
                    );
                    $data["posData"][
                        "zone"
                    ] = $this->model_extension_purpletree_pos_pos_posproduct->getZone();
                    $data["posData"][
                        "order_status"
                    ] = $this->model_localisation_order_status->getOrderStatuses();
                    $data["posData"]["config_tax_default"] = $this->config->get(
                        "config_tax_default"
                    );
                    $data["posData"][
                        "config_tax_customer"
                    ] = $this->config->get("config_tax_customer");

                    // POS Agent shipping and payment address

                    $data["posData"]["shipping_address"]["status"] = false;
                    $data["posData"]["payment_address"]["status"] = false;

                    if (isset($this->session->data["shipping_address"])) {
                        $data["posData"]["shipping_address"]["status"] = true;
                        $data["posData"]["shipping_address"] =
                            $this->session->data["shipping_address"];
                    }

                    if (isset($this->session->data["payment_address"])) {
                        $data["posData"]["payment_address"]["status"] = true;
                        $data["posData"]["payment_address"] =
                            $this->session->data["payment_address"];
                    }
                    // POS Agent shipping and payment address
                    $data["posData"]["config_tax"] = $this->config->get(
                        "config_tax"
                    );
                    // product data
                    // All customer details
                    // $data["posData"]["products"] = $data["products"];
                    // $data["posData"]["popular_products"] =
                    //     $data["popular_products"];
                    $data["posData"][
                        "rateNameData"
                    ] = $this->model_extension_purpletree_pos_pos_posproduct->rateNameData();
                    $data["posData"][
                        "shippingAddressData"
                    ] = $this->model_extension_purpletree_pos_pos_posproduct->shippingAddressData();
                    $data["posData"][
                        "paymentAddressData"
                    ] = $this->model_extension_purpletree_pos_pos_posproduct->paymentAddressData();
                    $data["posData"][
                        "storeAddressData"
                    ] = $this->model_extension_purpletree_pos_pos_posproduct->storeAddressData();

                    $data["posData"][
                        "allpos_customer"
                    ] = $this->model_extension_purpletree_pos_pos_posproduct->getCustomersList();
                    $data["posData"][
                        "ptscustomergroup_name"
                    ] = $this->model_extension_purpletree_pos_pos_posproduct->getCustomerGroupname();

                    //Coupon Data
                    $data["posData"][
                        "coupon"
                    ] = $this->model_extension_purpletree_pos_pos_posproduct->getCoupon();
                    $data["posData"][
                        "coupon_category"
                    ] = $this->model_extension_purpletree_pos_pos_posproduct->getCouponCategory();
                    $data["posData"][
                        "coupon_product"
                    ] = $this->model_extension_purpletree_pos_pos_posproduct->getCouponProduct();
                    $data["posData"][
                        "coupon_history"
                    ] = $this->model_extension_purpletree_pos_pos_posproduct->getCouponHistory();
                    $data["posData"][
                        "category_path"
                    ] = $this->model_extension_purpletree_pos_pos_posproduct->getCategoryPath();
                    $data["posData"]["agent_id"] = $this->customer->getId();
                    //$data['posData']['orders'] = $this->getPosOrders($this->customer->getId());

                    $data["posData"][
                        "module_purpletree_pos_receipt_detail"
                    ] = $this->config->get(
                        "module_purpletree_pos_receipt_detail"
                    );

                    $data["posData"][
                        "module_purpletree_pos_receipt_store_detail"
                    ] = $this->config->get(
                        "module_purpletree_pos_receipt_store_detail"
                    );
                    $this->load->model(
                        "extension/purpletree_pos/pos/posproduct"
                    );
                    $isPosAdmin = $this->model_extension_purpletree_pos_pos_posproduct->isPosAdmin();
                    if ($isPosAdmin) {
                        $data["posData"][
                            "agents"
                        ] = $this->model_extension_purpletree_pos_pos_posproduct->getAgents();
                    }
                    $this->load->model("tool/image");
                    if (
                        isset($this->request->post["config_logo"]) &&
                        is_file(DIR_IMAGE . $this->request->post["config_logo"])
                    ) {
                        $data["posData"][
                            "logo"
                        ] = $this->model_tool_image->resize(
                            $this->request->post["config_logo"],
                            100,
                            100
                        );
                    } elseif (
                        $this->config->get("config_logo") &&
                        is_file(DIR_IMAGE . $this->config->get("config_logo"))
                    ) {
                        $data["posData"][
                            "logo"
                        ] = $this->model_tool_image->resize(
                            $this->config->get("config_logo"),
                            100,
                            100
                        );
                    }

                    $data["footer"] = $this->load->controller(
                        "extension/purpletree_pos/pos/common/footer"
                    );
                    $data["header"] = $this->load->controller(
                        "extension/purpletree_pos/pos/common/header"
                    );
                    $data["POS"] = json_encode($data["posData"]);
                }
            }
            $data["logout"] = $this->url->link("account/logout", "", true);
            $this->load->model("extension/purpletree_pos/pos/posproduct");
            $data[
                "isPosAdmin"
            ] = $this->model_extension_purpletree_pos_pos_posproduct->isPosAdmin();
            $data[
                "isPosAgent"
            ] = $this->model_extension_purpletree_pos_pos_posproduct->isPosAgent();
            $data["baseurl"] = HTTP_SERVER;
            $data["version_compare"] = version_compare(
                VERSION,
                "4.0.2.0",
                ">="
            );
            $data["version4103"] = version_compare(VERSION, "4.1.0.3", ">=");
            $customer_info = $this->model_account_customer->getCustomer(
                $this->customer->getId()
            );
            $data["business_class"] = "RETAIL";
            if (
                !empty($customer_info) &&
                (int) $customer_info["customer_group_id"] === 3
            ) {
                $data["business_class"] = "WHOLESALE";
            }
            $data["posData"]["business_class"] = $data["business_class"];
            $data["posData"]["customer_group_id"] =
                (int) $customer_info["customer_group_id"];
            $data["agentBusinessClass"] = $data["business_class"];
            //$data['image_path'] = HTTP_SERVER.'extension/purpletree_pos/catalog/view/playsound/';
            $data["agentEmail"] = $this->customer->getEmail();
            $data["agentId"] = $this->customer->getId();
            /*print_r($data["isPosAgent"]);
             die;*/
            $this->response->setOutput(
                $this->load->view("extension/purpletree_pos/pos/index2", $data)
            );
        } else {
            $this->response->redirect(
                $this->url->link("common/home", "", true)
            );
        }
    }

    public function allTopCategory()
    {
        $this->load->model("catalog/category");
        $this->load->model("catalog/product");

        $categories = $this->model_catalog_category->getCategories(0);

        foreach ($categories as $category) {
            $children_data = [];

            $children = $this->model_catalog_category->getCategories(
                $category["category_id"]
            );

            foreach ($children as $child) {
                $filter_data = [
                    "filter_category_id" => $child["category_id"],
                    "filter_sub_category" => true,
                ];

                $children_data[] = [
                    "name" =>
                        $child["name"] .
                        ($this->config->get("config_product_count")
                            ? " (" .
                                $this->model_catalog_product->getTotalProducts(
                                    $filter_data
                                ) .
                                ")"
                            : ""),
                    "child_category_id" =>
                        $category["category_id"] . "_" . $child["category_id"],
                ];
            }
            if (version_compare(VERSION, "4.0.2.3", "<=")) {
                $data["categories"][] = [
                    "name" => $category["name"],
                    "column" => $category["column"] ? $category["column"] : 1,
                    "top_category_id" => $category["category_id"],
                    "children" => $children_data,
                ];
            } else {
                $data["categories"][] = [
                    "name" => $category["name"],
                    //'column'   				=> $category['column'] ? $category['column'] : 1,
                    "top_category_id" => $category["category_id"],
                    "children" => $children_data,
                ];
            }
        }
        return $data["categories"];
    }

    public function POSProducts($posData = [])
    {
        $data = [];

        if (!empty($posData)) {
            $product_info = [];
            $this->load->model("catalog/product");
            $product_info = $this->model_catalog_product->getProduct(
                $posData["product_id"]
            );
            $data["categories"] = [];

            $this->load->model('catalog/category');
            
            $categories =$this->model_catalog_product->getCategories($posData["product_id"]);
                
                foreach ($categories as $category) {
                
                $category_info =$this->model_catalog_category->getCategory($category['category_id']);
                
                if ($category_info) {
                
                $data["categories"][] = [
                
                "category_id" => $category_info["category_id"],
                
                "name" => $category_info["name"],
                "gst" => $category_info["gst"]
                
                ];
                
                }
                
                }
            if ($product_info) {
                $data["pos_quantity"] = $posData["pos_quentity"];
                $data["pos_status"] = $posData["pos_status"];

                $data["heading_title"] = $product_info["name"];
                $data["text_minimum"] = sprintf(
                    $this->language->get("text_minimum"),
                    $product_info["minimum"]
                );
                $data["text_login"] = sprintf(
                    $this->language->get("text_login"),
                    $this->url->link("account/login", "", true),
                    $this->url->link("account/register", "", true)
                );

                $this->load->model("catalog/review");

                $data["tab_review"] = sprintf(
                    $this->language->get("tab_review"),
                    $product_info["reviews"]
                );

                $data["product_id"] = (int) $posData["product_id"];

                $data["subtract"] = $product_info["subtract"];
                $data["mrp"] = $product_info["mrp"];
                $data["provided_discount"] = $product_info["provided_discount"];
                $data["sku"] = $product_info["sku"];
                $data["isbn"] = $product_info["isbn"];
                $data["jan"] = $product_info["jan"];
                $data["mpn"] = $product_info["mpn"];
                $data["ean"] = $product_info["ean"];
                $data["upc"] = $product_info["upc"];
                $data["weight"] = (float) $product_info["weight"];
                $data["length"] = (float) $product_info["length"];
                $data["width"] = (float) $product_info["width"];
                $data["height"] = (float) $product_info["height"];
                $data["weight_class_id"] =
                    (int) $product_info["weight_class_id"];
                $data["length_class_id"] =
                    (int) $product_info["length_class_id"];
                if (version_compare(VERSION, "4.0.2.3", ">=")) {
                    $data["manufacturer"] = "";
                } else {
                    $data["manufacturer"] = $product_info["manufacturer"];
                }
                $data["tax_class_id"] = (int) $product_info["tax_class_id"];
                $data["manufacturers"] = $this->url->link(
                    "product/manufacturer/info",
                    "manufacturer_id=" . $product_info["manufacturer_id"]
                );
                $data["model"] = $product_info["model"];
                $data["reward"] = $product_info["reward"];
                $data["points"] = $product_info["points"];
                $data["description"] = html_entity_decode(
                    $product_info["description"],
                    ENT_QUOTES,
                    "UTF-8"
                );

                if ($product_info["description"] != "") {
                    $data["description"] =
                        substr(
                            trim(
                                strip_tags(
                                    html_entity_decode(
                                        $product_info["description"],
                                        ENT_QUOTES,
                                        "UTF-8"
                                    )
                                )
                            ),
                            0,
                            (int) $this->config->get(
                                "theme_" .
                                    $this->config->get("config_theme") .
                                    "_product_description_length"
                            )
                        ) . "..";
                } else {
                    $data["description"] = '';
                }

                if ($product_info["quantity"] <= 0) {
                    $data["stock"] = isset($product_info["stock_status"])
                        ? $product_info["stock_status"]
                        : "";
                } elseif ($this->config->get("config_stock_display")) {
                    $data["stock"] = $product_info["quantity"];
                } else {
                    $data["stock"] = $this->language->get("text_instock");
                }
                $this->load->model("tool/image");
                $product_info["image"] =
                    $product_info["image"] ?? "no_image.png";
                if (
                    is_file(
                        DIR_IMAGE .
                            html_entity_decode(
                                $product_info["image"],
                                ENT_QUOTES,
                                "UTF-8"
                            )
                    )
                ) {
                    $data["popup"] = $this->model_tool_image->resize(
                        html_entity_decode(
                            $product_info["image"],
                            ENT_QUOTES,
                            "UTF-8"
                        ),
                        $this->config->get("config_image_popup_width"),
                        $this->config->get("config_image_popup_height")
                    );
                } else {
                    $data["popup"] = "no_image.png";
                }

                if ($product_info["image"]) {
                    $data["thumb"] = $this->model_tool_image->resize(
                        $product_info["image"],
                        200,
                        200
                    );
                    $data["small_thumb"] = $this->model_tool_image->resize(
                        $product_info["image"],
                        50,
                        50
                    );
                    $data["thumb_popup"] = $this->model_tool_image->resize(
                        $product_info["image"],
                        350,
                        350
                    );
                } else {
                    $data["thumb"] = $this->model_tool_image->resize(
                        "no_image.png",
                        200,
                        200
                    );
                    $data["small_thumb"] = $this->model_tool_image->resize(
                        "no_image.png",
                        50,
                        50
                    );
                    $data["thumb_popup"] = $this->model_tool_image->resize(
                        "no_image.png",
                        350,
                        350
                    );
                }
                $data["images"] = [];
                $results = $this->model_catalog_product->getImages(
                    $posData["product_id"]
                );

                foreach ($results as $result) {
                    $data["images"][] = [
                        "popup" => $this->model_tool_image->resize(
                            html_entity_decode(
                                $result["image"],
                                ENT_QUOTES,
                                "UTF-8"
                            ),
                            $this->config->get("config_image_popup_width"),
                            $this->config->get("config_image_popup_height")
                        ),
                        "thumb" => $this->model_tool_image->resize(
                            html_entity_decode(
                                $result["image"],
                                ENT_QUOTES,
                                "UTF-8"
                            ),
                            $this->config->get("config_image_additional_width"),
                            $this->config->get("config_image_additional_height")
                        ),
                    ];
                }
                
                

                if (
                    $this->customer->isLogged() ||
                    !$this->config->get("config_customer_price")
                ) {
                    $data["price"] = (float) $product_info["price"];
                } else {
                    $data["price"] = false;
                }
                if (
                    $this->customer->isLogged() ||
                    !$this->config->get("config_customer_price")
                ) {
                    $data["price"] = (float) $product_info["price"];

                    $data["wholesale_price"] = isset(
                        $product_info["wholesale_price"]
                    )
                        ? (float) $product_info["wholesale_price"]
                        : 0;
                    $this->load->model("account/customer");
                    $customer_info = $this->model_account_customer->getCustomer(
                        $this->customer->getId()
                    );

                    if (
                        !empty($customer_info) &&
                        $customer_info["customer_group_id"] == 3
                    ) {
                        if (
                            !empty($data["wholesale_price"]) &&
                            $data["wholesale_price"] > 0
                        ) {
                            $data["price"] = $data["wholesale_price"];
                        }
                    }
                } else {
                    $data["price"] = false;
                }

                $data[
                    "proTax"
                ] = $this->model_extension_purpletree_pos_pos_posproduct->getProductTax(
                    $data["price"],
                    $data["tax_class_id"]
                );
                //print_r($data['tax']);

                if ((float) $product_info["special"]) {
                    $data["special"] = (float) $product_info["special"];
                } else {
                    $data["special"] = false;
                }

                if ($this->config->get("config_tax")) {
                    $data["tax"] = (float) $product_info["special"]
                        ? (float) $product_info["special"]
                        : (float) $product_info["price"];
                } else {
                    $data["tax"] = false;
                }

                $discounts = $this->model_catalog_product->getDiscounts(
                    $posData["product_id"]
                );
                $data["discounts"] = [];

                foreach ($discounts as $discount) {
                    $data["discounts"][] = [
                        "quantity" => $discount["quantity"],
                        "price" => (float) $discount["price"],
                    ];
                }
                $data["options"] = [];

                foreach (
                    $this->model_catalog_product->getOptions(
                        $posData["product_id"]
                    )
                    as $option
                ) {
                    $product_option_value_data = [];

                    foreach ($option["product_option_value"] as $option_value) {
                        if (
                            !$option_value["subtract"] ||
                            $option_value["quantity"] > 0
                        ) {
                            if (
                                (($this->config->get("config_customer_price") &&
                                    $this->customer->isLogged()) ||
                                    !$this->config->get(
                                        "config_customer_price"
                                    )) &&
                                (float) $option_value["price"]
                            ) {
                                $price = (float) $option_value["price"];
                            } else {
                                $price = false;
                            }
                            if (
                                is_file(
                                    DIR_IMAGE .
                                        html_entity_decode(
                                            $option_value["image"],
                                            ENT_QUOTES,
                                            "UTF-8"
                                        )
                                )
                            ) {
                                $image = $this->model_tool_image->resize(
                                    html_entity_decode(
                                        $option_value["image"],
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ),
                                    50,
                                    50
                                );
                            } else {
                                $image = "no_image.png";
                            }
                            $product_option_value_data[] = [
                                "product_option_value_id" =>
                                    $option_value["product_option_value_id"],
                                "option_value_id" =>
                                    $option_value["option_value_id"],
                                "name" => $option_value["name"],
                                "image" => $image,
                                "price" => $price,
                                "price_prefix" => $option_value["price_prefix"],
                            ];
                        }
                    }

                    $data["options"][] = [
                        "product_option_id" => $option["product_option_id"],
                        "product_option_value" => $product_option_value_data,
                        "option_id" => $option["option_id"],
                        "name" => $option["name"],
                        "type" => $option["type"],
                        "value" => $option["value"],
                        "required" => $option["required"],
                    ];
                }

                if ($product_info["minimum"]) {
                    $data["minimum"] = $product_info["minimum"];
                } else {
                    $data["minimum"] = 1;
                }

                $data["review_status"] = $this->config->get(
                    "config_review_status"
                );

                if (
                    $this->config->get("config_review_guest") ||
                    $this->customer->isLogged()
                ) {
                    $data["review_guest"] = true;
                } else {
                    $data["review_guest"] = false;
                }

                if ($this->customer->isLogged()) {
                    $data["customer_name"] =
                        $this->customer->getFirstName() .
                        "&nbsp;" .
                        $this->customer->getLastName();
                } else {
                    $data["customer_name"] = "";
                }

                $data["reviews"] = sprintf(
                    $this->language->get("text_reviews"),
                    (int) $product_info["reviews"]
                );
                $data["rating"] = (int) $product_info["rating"];

                // Captcha
                if (
                    $this->config->get(
                        "captcha_" .
                            $this->config->get("config_captcha") .
                            "_status"
                    ) &&
                    in_array(
                        "review",
                        (array) $this->config->get("config_captcha_page")
                    )
                ) {
                    $data["captcha"] = $this->load->controller(
                        "extension/captcha/" .
                            $this->config->get("config_captcha")
                    );
                } else {
                    $data["captcha"] = "";
                }

                $data["share"] = $this->url->link(
                    "product/product",
                    "product_id=" . (int) $posData["product_id"]
                );

                $data[
                    "attribute_groups"
                ] = $this->model_catalog_product->getAttributes(
                    $posData["product_id"]
                );
                $data["tags"] = [];

                if ($product_info["tag"]) {
                    $tags = explode(",", $product_info["tag"]);

                    foreach ($tags as $tag) {
                        $data["tags"][] = [
                            "tag" => trim($tag),
                            "href" => $this->url->link(
                                "product/search",
                                "tag=" . trim($tag)
                            ),
                        ];
                    }
                }

                $data["recurrings"] = $this->model_catalog_product->getImages(
                    $posData["product_id"]
                );
            }
        }
        return $data;
    }

    public function getTaxes()
    {
        $tax_data = [];

        if ($product["tax_class_id"]) {
            $tax_rates = $this->tax->getRates(
                $product["price"],
                $product["tax_class_id"]
            );

            foreach ($tax_rates as $tax_rate) {
                if (!isset($tax_data[$tax_rate["tax_rate_id"]])) {
                    $tax_data[$tax_rate["tax_rate_id"]] =
                        $tax_rate["amount"] * $product["quantity"];
                } else {
                    $tax_data[$tax_rate["tax_rate_id"]] +=
                        $tax_rate["amount"] * $product["quantity"];
                }
            }
        }
    }

    public function allCustomerData()
    {
        $this->load->model("extension/purpletree_pos/pos/posproduct");
        $this->load->model("account/customer");

        if (isset($this->request->post["customer_group_id"])) {
            $data["customer_group_id"] =
                $this->request->post["customer_group_id"];
        } else {
            $data["customer_group_id"] = "";
        }
        if (isset($this->request->post["firstname"])) {
            $data["firstname"] = $this->request->post["firstname"];
        } else {
            $data["firstname"] = "";
        }
        if (isset($this->request->post["lastname"])) {
            $data["lastname"] = $this->request->post["lastname"];
        } else {
            $data["lastname"] = "";
        }
        if (isset($this->request->post["email"])) {
            $data["email"] = $this->request->post["email"];
        } else {
            $data["email"] = "";
        }
        if (isset($this->request->post["telephone"])) {
            $data["telephone"] = $this->request->post["telephone"];
        } else {
            $data["telephone"] = "";
        }
        $filter_data = [
            "email" => $data["email"],
            "telephone" => $data["telephone"],
        ];
        $json = [];
        $customer_fulldata = "";
        $results = $this->model_extension_purpletree_pos_pos_posproduct->getCustomerEmails(
            $filter_data
        );
        if ($results != "") {
            $status = "error";
            $msg = "This email or Telephone is already exit.";
        } else {
            $status = "success";
            $msg = "Customer add successfully";
            $customer_id = $this->model_account_customer->addCustomer(
                $this->request->post
            );
            //echo "insert data not found";
            if ($customer_id) {
                $customer_fulldata = $this->model_extension_purpletree_pos_pos_posproduct->getCustomerFullData(
                    $customer_id
                );
            }
        }
        $json = [
            "customer_fulldata" => $customer_fulldata,
            "status" => $status,
            "msg" => $msg,
        ];
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }

    public function getProductBySku()
    {
        $json = [];

        if (isset($this->request->get["sku"])) {
            $sku = $this->request->get["sku"];

            $this->load->model("extension/purpletree_pos/pos/posproduct");

            $products = $this->model_extension_purpletree_pos_pos_posproduct->getPosProductBySku(
                $sku
            );

                    if ($products) {
                        foreach($products as $product){
                        $posData = $this->model_extension_purpletree_pos_pos_posproduct->getProductByid(
                            $product["product_id"]
                        );
                        $json[] =  $this->POSProducts($posData);
                    }
                    }else {
                        $json = [
                            "status" => "not_found",
                        ];
                    }
        }

        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }

    public function autocomplete()
    {
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
    
    public function categoryAutocomplete()
    {
        $json = [];
    
        if (isset($this->request->get['filter'])) {
    
            $filter_name = $this->request->get['filter'] ?? '';
    
            $filter_data = [
                'filter_name' => $filter_name,
                'start'       => 0,
                'limit'       => 10
            ];
    
            $this->load->model("checkout/order");
    
            $results = $this->model_checkout_order->getCategoriesAutocomplete($filter_data);
    
            foreach ($results as $result) {
    
                $json[] = [
                    'category_id' => $result['category_id'],
                    'name'        => $result['name']
                ];
            }
        }

        $sort_order = [];
    
        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }
    
        array_multisort($sort_order, SORT_ASC, $json);
    
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }


    public function Barcodescan() {

    $this->load->model("checkout/order");

    $json = [];

    $barcode = '';
    if (isset($this->request->post['barcode'])) {
        $barcode = trim($this->request->post['barcode']);
    } elseif (isset($this->request->get['barcode'])) {
        $barcode = trim($this->request->get['barcode']);
    }

    if (!$barcode) {
        $json['status'] = 'error';
        $json['message'] = 'Barcode is required!';
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        return;
    }

    if (!preg_match('/^\d{8}$|^\d{15}$/', $barcode)) {
        $json['status'] = 'error';
        $json['message'] = 'Invalid barcode! Only 8 or 15 digits allowed.';
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        return;
    }

    // Get FULL product details from model
    $product_info = $this->model_checkout_order->getFullProductByBarcode($barcode);

    if (!$product_info) {
        $json['status'] = 'error';
        $json['message'] = 'Product not found!';
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        return;
    }

    $json['status'] = 'success';
    $json['product'] = $product_info;

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}
    public function returnorder()
    {
        $json = [];
        $json["success"] = 1;
        $json["message"] = "Return product has been saved successfully!";
        $this->load->model("extension/purpletree_pos/pos/posproduct");
        if (isset($this->request->post["data"])) {
            if (!empty($this->request->post["data"])) {
                foreach ($this->request->post["data"] as $key => $val) {
                    $val["comment"] = $this->request->post["comment"];
                    $val["return_action_id"] = $this->config->get(
                        "module_purpletree_pos_return_action"
                    );
                    $val["return_status_id"] = $this->config->get(
                        "module_purpletree_pos_return_status"
                    );
                    $val["agent_id"] = $this->customer->getId();
                    $return_id = $this->model_extension_purpletree_pos_pos_posproduct->addReturn(
                        $val
                    );
                    if (!$return_id) {
                        $json["success"] = 0;
                        $json["message"] = "Return product is not Succeeded!";
                        break;
                    }
                    if ($return_id) {
                        $returnProductData = $this->model_extension_purpletree_pos_pos_posproduct->getPosReturnProduct(
                            $return_id
                        );
                        $json["returnProductData"][
                            $returnProductData["order_id"]
                        ][] = $returnProductData;
                    }
                }
            }
        }
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    public function addorder()
    {
        $this->response->addHeader("Content-Type: application/json");

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
            $agentId = $this->customer->getId();
            $customer_name = $get($orderDetails, "CustomerName", "");
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


            $invoice_prefix = '';
            $invoice_no     = '';

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
                if ($dueAmountUsed && $dueAmountValue > 0) {
            
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

           // Only run this for specific payment types, not all orders
if (strtolower($paymentThrough) === 'advance') {
    $credit = [
        "customerid" => $customer_id,
        "order_id"   => $order_id,
        "amount"     => $subtotal,
        "description" => $mobile,
        "transactiontype"    => "CREDIT",
        "transactionsubtype" => "TRADE",
        "txtid" => $order_id,
    ];
    $walletUpdate = $this->model_checkout_order->doWalletCredit($credit);
} else {
    $walletUpdate = null;
}

return $this->response->setOutput(
    json_encode([
        "status"       => "success",
        "order_id"     => $order_id,
        "walletUpdate" => $walletUpdate,
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
    
    
    public function addQuoteOrder()
{
    $this->response->addHeader("Content-Type: application/json");

    try {

        $raw = file_get_contents("php://input");
        $payload = json_decode($raw, true);

        if (!is_array($payload) || empty($payload['quotation_details'])) {
            throw new Exception("Invalid quotation payload");
        }

        $q = $payload['quotation_details'];
        $previousQuoteId = (int)($q['quote_id'] ?? 0);
        
        $full_invoice_number = trim($q['invoice_number'] ?? '');
        
        $invoice_prefix = 'SGC-';
        $invoice_no     = '';
        
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

        $agentId = $this->customer->getId();

        $customer_name = trim($q['customer_name'] ?? '');
        $telephone     = trim($q['telephone'] ?? '');
        $email         = trim($q['email'] ?? '');

        if ($customer_name === '' || $telephone === '') {
            throw new Exception("Customer name and phone are required");
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
            throw new Exception("Quote creation failed");
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


    public function cancelOrder()
{
    $this->response->addHeader('Content-Type: application/json');
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

    $agentId = (int)$this->customer->getId();

    $this->load->model("checkout/order");

    $quotes = $this->model_checkout_order->getOrdersByDateRange($agentId,$from_date,$to_date,$order_id,$mobile,$name);

    try {
        $order_id = (int)($this->request->post['order_id'] ?? 0);

        if ($order_id <= 0) {
            throw new Exception('Invalid order id');
        }

        $this->load->model('checkout/order');

        if ($this->model_checkout_order->isOrderCancelled($order_id)) {
            throw new Exception('Order already cancelled');
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


public function cancelQuoteOrder()
{
    $this->response->addHeader("Content-Type: application/json");

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



    public function addNewCustomer() {

    // JSON response
    $this->response->addHeader('Content-Type: application/json');

    // Read raw JSON body
    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);

    if (!is_array($data)) {
        $this->response->setOutput(json_encode([
            "status"  => "error",
            "message" => "Invalid request data"
        ]));
        return;
    }

    $firstname = trim($data['firstname'] ?? '');
    $lastname  = trim($data['lastname'] ?? '');
    $telephone = trim($data['telephone'] ?? '');
    $email     = trim($data['email'] ?? '');

    // Basic validation
    if ($firstname == '') {
        $this->response->setOutput(json_encode([
            "status"  => "error",
            "message" => "Customer name required"
        ]));
        return;
    }

    if ($telephone == '' || strlen($telephone) != 10 || !preg_match('/^[0-9]{10}$/', $telephone)) {
        $this->response->setOutput(json_encode([
            "status"  => "error",
            "message" => "Valid 10-digit mobile number required"
        ]));
        return;
    }

    // Load your model
    $this->load->model("checkout/order");

    // Prepare input arrays like your model expects
    $input = [
        "telephone" => $telephone,
        "email"     => $email
    ];

    $custData = [
        "firstname" => $firstname,
        "lastname"  => ($lastname != ''),
        "email"     => $email
    ];

    try {
        $customerRow = $this->model_checkout_order->addPOSCustomer($input, $custData);

        if (!$customerRow || empty($customerRow['customer_id'])) {
            $this->response->setOutput(json_encode([
                "status"  => "error",
                "message" => "Customer not created"
            ]));
            return;
        }

        $this->response->setOutput(json_encode([
            "status"      => "success",
            "message"     => "Customer created",
            "customer_id" => (int)$customerRow['customer_id'],
            "customer"    => [
                "customer_id" => (int)$customerRow['customer_id'],
                "firstname"   => $customerRow['firstname'] ?? '',
                "lastname"    => $customerRow['lastname'] ?? '',
                "telephone"   => $customerRow['telephone'] ?? '',
                "email"       => $customerRow['email'] ?? '',
                "wallet"      => [
                    "trade" => $customerRow['trade'] ?? '0.00'
                ]
            ]
        ]));

    } catch (\Exception $e) {
        $this->response->setOutput(json_encode([
            "status"  => "error",
            "message" => "Server error: " . $e->getMessage()
        ]));
        return;
    }
}


    public function applycoupon()
    {
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

    public function getOrdersbyId()
    {
        $this->response->addHeader("Content-Type: application/json");

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

    public function getOrdersbyDate()
{
    $this->response->addHeader("Content-Type: application/json");

    $from_date = $this->request->get["from_date"] ?? "";
    $to_date   = $this->request->get["to_date"] ?? "";

    $order_id  = $this->request->get["order_id"] ?? "";
    $mobile    = $this->request->get["mobile"] ?? "";
    $name      = $this->request->get["name"] ?? "";

    if (empty($from_date) || empty($to_date)) {
        $today = date("Y-m-d");
        $from_date = $today;
        $to_date = $today;
    }

    $agentId = (int)$this->customer->getId();
    $this->load->model("checkout/order");

    $orders = $this->model_checkout_order->getOrdersByDateRange($agentId,$from_date,$to_date,$order_id,$mobile,$name);

    $totals = $this->model_checkout_order->getOrderTotalsByDateRange($from_date,$to_date,$agentId);

    return $this->response->setOutput(json_encode([
        "status"       => "success",
        "total_orders" => count($orders),
        "totals"       => $totals,
        "data"         => $orders
    ]));
}




public function getQuotesByDate()
{
    $this->response->addHeader("Content-Type: application/json");

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

    $agentId = (int)$this->customer->getId();

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

public function getQuotationById()
{
    $this->response->addHeader("Content-Type: application/json");

    $order_id = $this->request->get['order_id'] ?? '';
    $agent_id = $this->request->get['agent_id'] ?? '';

    if (!$order_id || !$agent_id) {
        return $this->response->setOutput(json_encode([
            "status" => "error",
            "message" => "order_id and agent_id required"
        ]));
    }

    $this->load->model('checkout/order');

    $quote = $this->model_checkout_order->getQuoteByAgent($order_id, $agent_id);

    if (!$quote) {
        return $this->response->setOutput(json_encode([
            "status" => "error",
            "message" => "Quote not found or not allowed"
        ]));
    }

    return $this->response->setOutput(json_encode([
        "status" => "success",
        "data" => $quote
    ]));
}

public function getEditOrderById()
{
    $this->response->addHeader("Content-Type: application/json");

    $order_id = $this->request->get['order_id'] ?? '';
    $agent_id = $this->request->get['agent_id'] ?? '';

    if (!$order_id || !$agent_id) {
        return $this->response->setOutput(json_encode([
            "status" => "error",
            "message" => "order_id and agent_id required"
        ]));
    }

    $this->load->model('checkout/order');

    $order = $this->model_checkout_order->getEditOrderDetails($order_id, $agent_id);

    if (!$order) {
        return $this->response->setOutput(json_encode([
            "status" => "error",
            "message" => "Order not found or agent not authorized to edit this order"
        ]));
    }

    return $this->response->setOutput(json_encode([
        "status" => "success",
        "data" => $order
    ]));
}

    public function getWalletInfo()
    {
        $this->response->addHeader("Content-Type: application/json");

        $from_date =
            $this->request->get["from_date"] ??
            ($this->request->post["from_date"] ?? "");
        $to_date =
            $this->request->get["to_date"] ??
            ($this->request->post["to_date"] ?? "");

        if (empty($from_date) || empty($to_date)) {
            $today = date("Y-m-d");
            $from_date = $today;
            $to_date = $today;
        }

        $this->load->model("account/customer");
        $customer_info = $this->model_account_customer->getCustomer(
            $this->customer->getId()
        );

        $customer_id = !empty($customer_info)
            ? (int) $customer_info["customer_id"]
            : 0;
        $customer_group_id = !empty($customer_info)
            ? (int) $customer_info["customer_group_id"]
            : 0;

        $this->load->model("checkout/order");
        $wallet_info = $this->model_checkout_order->getWalletInfo($customer_id);
        $raw = [
            "from_date" => $from_date,
            "to_date" => $to_date,
        ];

        $walletHistory = $this->model_checkout_order->walletTradeHistory(
            $customer_info["customer_id"],
            $raw
        );

        $walletHistory["walletAmount"] = $wallet_info["amount"];
        return $this->response->setOutput(json_encode($walletHistory));
    }
    
    public function searchByMobile() {

        $this->response->addHeader('Content-Type: application/json');

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
    
   public function updateTaxExempt() {

    $order_id    = (int)($this->request->get['order_id'] ?? 0);
    $tax_percent = (float)($this->request->get['tax_percentage'] ?? 0);
    $gst_percent = (float)($this->request->get['gst_percentage'] ?? 0);

    if (!$order_id || !$tax_percent) {
        exit('Invalid input');
    }

    $this->load->model("checkout/order");

    $orderData = $this->model_checkout_order
        ->getFullOrderTaxExemptData($order_id, $tax_percent, $gst_percent);

    if (!$orderData) {
        exit('Order not found');
    }

    /* PASS FULL ORDER STRUCTURE AS EXPECTED BY YOUR TWIG */
    $data['order'] = [
        'order_id'     => $orderData['order_id'],
        'date_added'   => $orderData['order_date'],
        'products'     => $orderData['products'],
        'total_qty'    => $orderData['total_qty'],
        'total_taxable'=> $orderData['total_taxable'],
        'gst_percent'  => $orderData['gst_percent'],
        'exempt_percent'=> $orderData['exempt_percent']
    ];

    /* Receiver (Match Your Twig Variable Names EXACTLY) */
    $data['r_name']    = $this->request->get['receiver_name'] ?? '';
    $data['r_address'] = $this->request->get['receiver_address'] ?? '';
    $data['r_state']   = $this->request->get['receiver_state'] ?? '';
    $data['r_mobile']  = $this->request->get['receiver_mobile'] ?? '';
    $data['r_gstin']   = $this->request->get['receiver_gstin'] ?? '';
    $data['r_pan']     = $this->request->get['receiver_pan'] ?? '';

    /* Consignee */
    $data['c_name']    = $this->request->get['consignee_name'] ?? '';
    $data['c_address'] = $this->request->get['consignee_address'] ?? '';
    $data['c_state']   = $this->request->get['consignee_state'] ?? '';
    $data['c_mobile']  = $this->request->get['consignee_mobile'] ?? '';
    $data['c_gstin']   = $this->request->get['consignee_gstin'] ?? '';
    $data['c_pan']     = $this->request->get['consignee_pan'] ?? '';

    $this->response->setOutput(
        $this->load->view(
            "extension/purpletree_pos/pos/tax_invoice",
            $data
        )
    );
}



public function adjustTrade() {

    $this->response->addHeader('Content-Type: application/json');
    $json = [];

    $customer_id = (int)($this->request->post['customer_id'] ?? 0);
    $amount      = (float)($this->request->post['amount'] ?? 0);
    $type        = strtoupper($this->request->post['type'] ?? '');
    $description = trim($this->request->post['description'] ?? '');

    if (!$customer_id || $amount <= 0 || !in_array($type, ['CREDIT','DEBIT'])) {
        $json['message'] = 'Invalid input';
        return $this->response->setOutput(json_encode($json));
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
    $json = [];

    $customer_id = (int)($this->request->post['customer_id'] ?? 0);
    $amount      = (float)($this->request->post['amount'] ?? 0);
    $type        = strtoupper($this->request->post['type'] ?? '');
    $description = trim($this->request->post['description'] ?? '');

    if (!$customer_id || $amount <= 0 || !in_array($type, ['CREDIT','DEBIT'])) {
        $json['message'] = 'Invalid input';
        return $this->response->setOutput(json_encode($json));
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


    
    public function getTables()
    {
        $this->load->model("checkout/order");

        $tables = $this->model_checkout_order->getTables();

        $json = [];

        foreach ($tables as $table) {
            $live = $this->model_checkout_order->getLiveTableData(
                $table["pos_table_id"]
            );

            $orders = [];
            foreach ($live as $o) {
                $orders[] = [
                    "id" => $o["id"],
                    "orderId" => $o["order_id"],
                    "members" => $o["members"],
                ];
            }

            $json[] = [
                "id" => (int) $table["pos_table_id"],
                "name" => $table["table_name"],
                "maxPax" => (int) $table["members"],
                "orders" => $orders,
            ];
        }

        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }

    public function addLiveTable()
    {
        $this->load->model("checkout/order");

        $table_id = $this->request->post["table_id"];
        $order_id = $this->request->post["order_id"];
        $members = $this->request->post["members"];

        // CALL MODEL METHOD
        $this->model_checkout_order->addLiveTableInfo(
            $table_id,
            $order_id,
            $members
        );

        $this->response->setOutput(json_encode(["success" => true]));
    }

    public function clearLiveTable()
    {
        $this->load->model("checkout/order");

        $id = $this->request->post["id"];

        $this->model_checkout_order->clearLiveTableOrder($id);

        $json["success"] = true;

        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }

    public function createHoldOrder(): void
    {
        $this->response->addHeader("Content-Type: application/json");
        $this->load->model("checkout/order");

        $data = [
            "order_id" => (int) ($this->request->post["order_id"] ?? 0), // â­ custom ID
            "table_id" => $this->request->post["table_id"] ?? 0,
            "note" => $this->request->post["message"] ?? "",
            "products" => $this->request->post["cart"] ?? [],
        ];

        $this->model_checkout_order->addHoldOrder($data);

        $this->response->setOutput(
            json_encode([
                "status" => "success",
                "order_id" => $data["order_id"],
            ])
        );
    }

    public function updateHoldOrder(): void
    {
        $this->response->addHeader("Content-Type: application/json");
        $this->load->model("checkout/order");

        $order_id = $this->request->post["order_id"] ?? 0;
        $message = $this->request->post["message"] ?? "";
        $products = $this->request->post["cart"] ?? [];

        if (!$order_id) {
            $this->response->setOutput(
                json_encode([
                    "status" => "failed",
                    "message" => "No Order ID",
                ])
            );
            return;
        }

        $data = [
            "order_id" => $order_id,
            "note" => $message,
            "products" => $products,
        ];

        $this->model_checkout_order->updateHoldOrder($data);

        $this->response->setOutput(
            json_encode([
                "status" => "success",
            ])
        );
    }

    public function invoice(): void
    {
        $this->load->language("sale/order");

        $data["title"] = $this->language->get("text_invoice");

        $data["base"] = HTTP_SERVER;
        $data["direction"] = $this->language->get("direction");
        $data["lang"] = $this->language->get("code");

        // Hard coding css paths so that they can be replaced via the event's system.
        $data["bootstrap_css"] = "view/stylesheet/bootstrap.css";
        $data["icons"] = "view/stylesheet/fonts/fontawesome/css/all.min.css";
        $data["stylesheet"] = "view/stylesheet/stylesheet.css";

        // Hard coding scripts so they can be replaced via the events system.
        $data["jquery"] = "view/javascript/jquery/jquery-3.7.1.min.js";
        $data["bootstrap_js"] =
            "view/javascript/bootstrap/js/bootstrap.bundle.min.js";

        // Order
        $this->load->model("checkout/order");

        // Subscription
        //	$this->load->model('sale/subscription');

        // Setting
        $this->load->model("setting/setting");

        // Upload
        $this->load->model("tool/upload");

        $data["orders"] = [];

        $orders = [];

        if (isset($this->request->post["selected"])) {
            $orders = (array) $this->request->post["selected"];
        }

        if (isset($this->request->get["order_id"])) {
            $orders[] = (int) $this->request->get["order_id"];
        }

        foreach ($orders as $order_id) {
            $order_info = $this->model_checkout_order->getOrderdetails(
                $order_id
            );

            if ($order_info) {
                $store_info = $this->model_setting_setting->getSetting(
                    "config",
                    $order_info["store_id"]
                );

                if ($store_info) {
                    $store_address = $store_info["config_address"];
                    $store_email = $store_info["config_email"];
                    $store_telephone = $store_info["config_telephone"];
                } else {
                    $store_address = $this->config->get("config_address");
                    $store_email = $this->config->get("config_email");
                    $store_telephone = $this->config->get("config_telephone");
                }

                if ($order_info["invoice_no"]) {
                    $invoice_no =
                        $order_info["invoice_prefix"] .
                        $order_info["invoice_no"];
                } else {
                    $invoice_no = "";
                }

                // Payment Address
                if ($order_info["payment_address_format"]) {
                    $format = $order_info["payment_address_format"];
                } else {
                    $format =
                        "{firstname} {lastname}" .
                        "\n" .
                        "{company}" .
                        "\n" .
                        "{address_1}" .
                        "\n" .
                        "{address_2}" .
                        "\n" .
                        "{city} {postcode}" .
                        "\n" .
                        "{zone}" .
                        "\n" .
                        "{country}";
                }

                $find = [
                    "{firstname}",
                    "{lastname}",
                    "{company}",
                    "{address_1}",
                    "{address_2}",
                    "{city}",
                    "{postcode}",
                    "{zone}",
                    "{zone_code}",
                    "{country}",
                ];

                $replace = [
                    "firstname" => $order_info["payment_firstname"],
                    "lastname" => $order_info["payment_lastname"],
                    "company" => $order_info["payment_company"],
                    "address_1" => $order_info["payment_address_1"],
                    "address_2" => $order_info["payment_address_2"],
                    "city" => $order_info["payment_city"],
                    "postcode" => $order_info["payment_postcode"],
                    "zone" => $order_info["payment_zone"],
                    "zone_code" => $order_info["payment_zone_code"],
                    "country" => $order_info["payment_country"],
                ];

                $pattern_1 = ["\r\n", "\r", "\n"];

                $pattern_2 = ["/\\s\\s+/", "/\r\r+/", "/\n\n+/"];

                $payment_address = str_replace(
                    $pattern_1,
                    "<br/>",
                    preg_replace(
                        $pattern_2,
                        "<br/>",
                        trim(str_replace($find, $replace, $format))
                    )
                );

                // Shipping Address
                if ($order_info["shipping_address_format"]) {
                    $format = $order_info["shipping_address_format"];
                } else {
                    $format =
                        "{firstname} {lastname}" .
                        "\n" .
                        "{company}" .
                        "\n" .
                        "{address_1}" .
                        "\n" .
                        "{address_2}" .
                        "\n" .
                        "{city} {postcode}" .
                        "\n" .
                        "{zone}" .
                        "\n" .
                        "{country}";
                }

                $find = [
                    "{firstname}",
                    "{lastname}",
                    "{company}",
                    "{address_1}",
                    "{address_2}",
                    "{city}",
                    "{postcode}",
                    "{zone}",
                    "{zone_code}",
                    "{country}",
                ];

                $replace = [
                    "firstname" => $order_info["shipping_firstname"],
                    "lastname" => $order_info["shipping_lastname"],
                    "company" => $order_info["shipping_company"],
                    "address_1" => $order_info["shipping_address_1"],
                    "address_2" => $order_info["shipping_address_2"],
                    "city" => $order_info["shipping_city"],
                    "postcode" => $order_info["shipping_postcode"],
                    "zone" => $order_info["shipping_zone"],
                    "zone_code" => $order_info["shipping_zone_code"],
                    "country" => $order_info["shipping_country"],
                ];

                $shipping_address = str_replace(
                    $pattern_1,
                    "<br/>",
                    preg_replace(
                        $pattern_2,
                        "<br/>",
                        trim(str_replace($find, $replace, $format))
                    )
                );

                $product_data = [];

                $products = $this->model_checkout_order->getProducts($order_id);

                foreach ($products as $product) {
                    $option_data = [];

                    $options = $this->model_checkout_order->getOptions(
                        $order_id,
                        $product["order_product_id"]
                    );

                    foreach ($options as $option) {
                        if ($option["type"] != "file") {
                            $value = $option["value"];
                        } else {
                            $upload_info = $this->model_tool_upload->getUploadByCode(
                                $option["value"]
                            );

                            if ($upload_info) {
                                $value = $upload_info["name"];
                            } else {
                                $value = "";
                            }
                        }

                        $option_data[] = ["value" => $value] + $option;
                    }

                    // Subscription
                    $description = "";

                    $subscription_info = $this->model_checkout_order->getSubscription(
                        $order_id,
                        $product["order_product_id"]
                    );

                    if ($subscription_info) {
                        if ($subscription_info["trial_status"]) {
                            $trial_price = $this->currency->format(
                                $subscription_info["trial_price"],
                                $this->config->get("config_currency")
                            );
                            $trial_cycle = $subscription_info["trial_cycle"];
                            $trial_frequency = $this->language->get(
                                "text_" . $subscription_info["trial_frequency"]
                            );
                            $trial_duration =
                                $subscription_info["trial_duration"];

                            $description .= sprintf(
                                $this->language->get("text_subscription_trial"),
                                $trial_price,
                                $trial_cycle,
                                $trial_frequency,
                                $trial_duration
                            );
                        }
                        $price = $this->currency->format(
                            $subscription_info["price"],
                            $this->config->get("config_currency")
                        );
                        $cycle = $subscription_info["cycle"];
                        $frequency = $this->language->get(
                            "text_" . $subscription_info["frequency"]
                        );
                        $duration = $subscription_info["duration"];

                        if ($subscription_info["duration"]) {
                            $description .= sprintf(
                                $this->language->get(
                                    "text_subscription_duration"
                                ),
                                $price,
                                $cycle,
                                $frequency,
                                $duration
                            );
                        } else {
                            $description .= sprintf(
                                $this->language->get(
                                    "text_subscription_cancel"
                                ),
                                $price,
                                $cycle,
                                $frequency
                            );
                        }
                    }
                    $product_data[] = [
                        "name" => $product["name"],
                        "model" => $product["model"],
                        "option" => $option_data,
                        "subscription" => $description,
                        "quantity" => $product["quantity"],
                        "price" => $product["price"],
                        "total" => $product["price"] * $product["quantity"],
                        "excluded" => !empty($product["excluded"]) ? 1 : 0
                    ];
                }
                $total_data = [];

                $totals = $this->model_checkout_order->getTotals($order_id);

                foreach ($totals as $total) {
                    $total_data[] =
                        [
                            "text" => $this->currency->format(
                                $total["value"],
                                $order_info["currency_code"],
                                $order_info["currency_value"]
                            ),
                        ] + $total;
                }

                $data["orders"][] = [
                    "order_id" => $order_id,
                    "invoice_no" => $invoice_no,
                    "order_status_id" => $order_info["order_status_id"],
                    "date_added" => date(
                        $this->language->get("date_format_short"),
                        strtotime($order_info["date_added"])
                    ),
                    "store_name" => $order_info["store_name"],

                    "store_address" => nl2br($store_address),
                    "store_email" => $store_email,
                    "store_telephone" => $store_telephone,
                    "email" => $order_info["email"],
                    "telephone" => $order_info["telephone"],
                    "shipping_address" => $shipping_address,
                    "shipping_method" => $order_info["shipping_method"]
                        ? $order_info["shipping_method"]["name"]
                        : "",
                    "payment_address" => $payment_address,
                    "payment_method" => $order_info["payment_method"],
                    "product" => $product_data,
                    "total" => $total_data,
                    "comment" => nl2br($order_info["comment"]),
                    "invoice" => [
                        "sub_total" => $order_info["sub_total"] ?? 0,
                        "discount" => $order_info["discount"] ?? 0,
                        "total_tax" => $order_info["total_tax"] ?? 0,
                        "roundoff_amount" =>
                            $order_info["roundoff_amount"] ?? 0,
                            "coupon" => $order_info["coupon"] ?? "",
                        "total_received" => $order_info["total_received"] ?? 0,
                        "cash_amount" => $order_info["cash_amount"] ?? 0,
                        "upi_amount" => $order_info["upi_amount"] ?? 0,
                        "pending_amount" => $order_info["pending_amount"] ?? 0,
                        "advance_used" => $order_info["advance_used"] ?? 0,
                        "due_amount" => $order_info["balance"] ?? 0,
                        "returnable_balance" =>
                            $order_info["returnable_balance"] ?? 0,
                    ],
                ];
            }
        }
        
        $data['small_print'] = !empty($this->request->get['small_print']);

        $this->response->setOutput($this->load->view("extension/purpletree_pos/pos/order_invoice",$data));
    }

public function mergeBill(): void
{
    $this->load->language("sale/order");

    $data["title"] = "Merged Invoice";
    $data["base"] = HTTP_SERVER;
    $data["direction"] = $this->language->get("direction");
    $data["lang"] = $this->language->get("code");

    $data["bootstrap_css"] = "view/stylesheet/bootstrap.css";
    $data["icons"] = "view/stylesheet/fonts/fontawesome/css/all.min.css";
    $data["stylesheet"] = "view/stylesheet/stylesheet.css";
    $data["jquery"] = "view/javascript/jquery/jquery-3.7.1.min.js";
    $data["bootstrap_js"] = "view/javascript/bootstrap/js/bootstrap.bundle.min.js";

    $this->load->model("checkout/order");
    $this->load->model("setting/setting");

    $data["orders"] = [];

    // ---------------------------
    // Parse order IDs
    // ---------------------------
    if (empty($this->request->get["order_id"])) {
        return;
    }

    $orderIds = array_filter(
        array_map("intval", explode(",", $this->request->get["order_id"]))
    );

    if (!$orderIds) {
        return;
    }

    // ---------------------------
    // Accumulators
    // ---------------------------
    $mergedProducts = [];
    $mergedTotals = [
        "sub_total" => 0,
        "discount" => 0,
        "total_tax" => 0,
        "roundoff_amount" => 0,
        "total_received" => 0,
        "cash_amount" => 0,
        "upi_amount" => 0,
        "pending_amount" => 0,
        "due_amount" => 0,
        "returnable_balance" => 0,
    ];

    $firstOrderInfo = null;

    foreach ($orderIds as $order_id) {

        $order_info = $this->model_checkout_order->getOrderdetails($order_id);
        if (!$order_info) continue;

        if (!$firstOrderInfo) {
            $firstOrderInfo = $order_info;
        }

        // ---------------------------
        // Products
        // ---------------------------
        $products = $this->model_checkout_order->getProducts($order_id);
        foreach ($products as $p) {
            $mergedProducts[] = [
                "name" => $p["name"],
                "model" => $p["model"],
                "option" => [],
                "subscription" => "",
                "quantity" => $p["quantity"],
                "price" => $p["price"],
                "total" => $p["price"] * $p["quantity"],
                "excluded" => !empty($p["excluded"]) ? 1 : 0
            ];
        }

        // ---------------------------
        // Totals
        // ---------------------------
        $mergedTotals["sub_total"] += (float)($order_info["sub_total"] ?? 0);
        $mergedTotals["discount"] += (float)($order_info["discount"] ?? 0);
        $mergedTotals["total_tax"] += (float)($order_info["total_tax"] ?? 0);
        $mergedTotals["roundoff_amount"] += (float)($order_info["roundoff_amount"] ?? 0);
        $mergedTotals["total_received"] += (float)($order_info["total_received"] ?? 0);
        $mergedTotals["cash_amount"] += (float)($order_info["cash_amount"] ?? 0);
        $mergedTotals["upi_amount"] += (float)($order_info["upi_amount"] ?? 0);
        $mergedTotals["pending_amount"] += (float)($order_info["pending_amount"] ?? 0);
        $mergedTotals["due_amount"] += (float)($order_info["balance"] ?? 0);
        $mergedTotals["returnable_balance"] += (float)($order_info["returnable_balance"] ?? 0);
    }

    if (!$firstOrderInfo) return;

    // ---------------------------
    // Store info (from first order)
    // ---------------------------
    $store_info = $this->model_setting_setting->getSetting(
        "config",
        $firstOrderInfo["store_id"]
    );

    $data["orders"][] = [
        "order_id" => implode(", ", $orderIds),
        "invoice_no" => "MERGED-" . implode("-", $orderIds),
        "date_added" => date("d-m-Y H:i"),
        "store_name" => $firstOrderInfo["store_name"],
        "store_address" => nl2br($store_info["config_address"] ?? ""),
        "store_email" => $store_info["config_email"] ?? "",
        "store_telephone" => $store_info["config_telephone"] ?? "",
        "email" => $firstOrderInfo["email"],
        "telephone" => $firstOrderInfo["telephone"],
        "shipping_address" => "",
        "shipping_method" => "Merged Bill",
        "payment_address" => "",
        "payment_method" => "Merged",
        "product" => $mergedProducts,
        "total" => [],
        "comment" => "Merged Bill",
        "invoice" => $mergedTotals,
    ];

    $data['small_print'] = !empty($this->request->get['small_print']);

    $this->response->setOutput(
        $this->load->view("extension/purpletree_pos/pos/order_invoice", $data)
    );
}

    public function quoteInvoice(): void
    {
        $this->load->language("sale/order");
    
        $data["title"] = "Quotation";
    
        $data["base"] = HTTP_SERVER;
        $data["direction"] = $this->language->get("direction");
        $data["lang"] = $this->language->get("code");
    
        $data["bootstrap_css"] = "view/stylesheet/bootstrap.css";
        $data["icons"] = "view/stylesheet/fonts/fontawesome/css/all.min.css";
        $data["stylesheet"] = "view/stylesheet/stylesheet.css";
    
        $data["jquery"] = "view/javascript/jquery/jquery-3.7.1.min.js";
        $data["bootstrap_js"] = "view/javascript/bootstrap/js/bootstrap.bundle.min.js";
    
        $this->load->model("checkout/order");
        $this->load->model("setting/setting");
    
        $data["orders"] = [];
    
        $quote_ids = [];
    
        if (isset($this->request->get["quote_id"])) {
            $quote_ids[] = (int)$this->request->get["quote_id"];
        }
    
        foreach ($quote_ids as $quote_id) {
    
            $order_info = $this->model_checkout_order->getQuoteOrderdetails($quote_id);
    
            if (!$order_info) continue;
    
            $store_info = $this->model_setting_setting->getSetting("config",$order_info["store_id"]);
    
            $store_address   = $store_info["config_address"] ?? $this->config->get("config_address");
            $store_email     = $store_info["config_email"] ?? $this->config->get("config_email");
            $store_telephone = $store_info["config_telephone"] ?? $this->config->get("config_telephone");
            $invoice_prefix = $order_info["invoice_prefix"] ?? '';
            $invoice_no     = $order_info["invoice_no"] ?? '';
    
            $payment_address = $order_info["payment_firstname"] . " " . $order_info["payment_lastname"];
    
            $product_data = [];
    
            $products = $this->model_checkout_order->getQuoteProducts($quote_id);
    
            foreach ($products as $product) {
                $product_data[] = [
                    "name" => $product["name"],
                    "model" => $product["model"],
                    "option" => [],
                    "subscription" => "",
                    "quantity" => $product["quantity"],
                    "price" => $product["price"],
                    "total" => $product["price"] * $product["quantity"],
                    "excluded" => !empty($product["excluded"]) ? 1 : 0
                ];
            }
    
    
            $data["orders"][] = [
                "order_id" => $quote_id,
                "invoice_no" => $invoice_no,
                "date_added" => date(
                    $this->language->get("date_format_short"),
                    strtotime($order_info["date_added"])
                ),
                
                "invoice_prefix" => $invoice_prefix,
                "invoice_no"     => $invoice_no,
                "store_name" => $order_info["store_name"],
    
                "store_address" => nl2br($store_address),
                "store_email" => $store_email,
                "store_telephone" => $store_telephone,
                "email" => $order_info["email"],
                "telephone" => $order_info["telephone"],
                "firstname" => $order_info["firstname"],
                "lastname" => $order_info["lastname"],
    
                "shipping_address" => "",
                "shipping_method" => "",
    
                "payment_address" => $payment_address,
                "payment_method" => "Quotation",
    
                "product" => $product_data,
                "total" => [],
    
                "comment" => nl2br($order_info["comment"]),
    
                "invoice" => [
                    "sub_total" => $order_info["sub_total"],
                    "discount" => $order_info["discount"],
                    "total_tax" => $order_info["total_tax"],
                    "roundoff_amount" => $order_info["roundoff_amount"],
                    "coupon" => $order_info["coupon"],
                    "total_received" => 0,
                    "cash_amount" => 0,
                    "upi_amount" => 0,
                    "pending_amount" => 0,
                    "due_amount" => 0,
                    "returnable_balance" => 0,
                ],
            ];
        }
    
        $this->response->setOutput($this->load->view("extension/purpletree_pos/pos/quote_invoice",$data));
    }
    
    public function printCart(): void 
    {
        $this->response->addHeader('Content-Type: application/json');
    
        $raw = json_decode(file_get_contents('php://input'), true);
    
        if (empty($raw['print_details'])) {
            $this->response->setOutput(json_encode([
                'status' => 'error',
                'message' => 'No print data received'
            ]));
            return;
        }
    
        $p = $raw['print_details'];
        $order_id = $this->request->get['order_id'] ?? '';
        $date     = $this->request->get['date'] ?? '';

        $formatted_date = $date ? date('d-m-Y', strtotime($date)) : date('d-m-Y');
    
        $data = [
            'base' => HTTP_SERVER,
            'direction' => $this->language->get('direction'),
            'lang' => $this->language->get('code'),
            'bootstrap_css' => 'view/stylesheet/bootstrap.css',
            'icons' => 'view/stylesheet/fonts/fontawesome/css/all.min.css',
            'jquery' => 'view/javascript/jquery/jquery-3.7.1.min.js',
            'bootstrap_js' => 'view/javascript/bootstrap/js/bootstrap.bundle.min.js',

            'cart' => [
                'products'    => $p['products'],
                'sub_total'   => $p['net_total'],
                'discount'    => $p['discount'],
                'total_tax'   => $p['total_tax'],
                'final_total' => $p['final_total'],
                'invoice_no'  => 'TEMP-' . time(),
                'order_id'    => $order_id,
                'date'        => $formatted_date
            ]
        ];
    
        $html = $this->load->view('extension/purpletree_pos/pos/product_invoice',$data);
    
        $this->response->setOutput(json_encode([
            'status' => 'success',
            'html' => $html
        ]));
    }
    
    public function processSmallPrint() {
        
        $this->load->language("sale/order");
    
        $data["title"] = "Quotation";
    
        $data["base"] = HTTP_SERVER;
        $data["direction"] = $this->language->get("direction");
        $data["lang"] = $this->language->get("code");
    
        $data["bootstrap_css"] = "view/stylesheet/bootstrap.css";
        $data["icons"] = "view/stylesheet/fonts/fontawesome/css/all.min.css";
        $data["stylesheet"] = "view/stylesheet/stylesheet.css";
    
        $data["jquery"] = "view/javascript/jquery/jquery-3.7.1.min.js";
        $data["bootstrap_js"] = "view/javascript/bootstrap/js/bootstrap.bundle.min.js";

        $data['cash']         = $this->request->post['cash'] ?? '0.00';
        $data['upi']          = $this->request->post['upi'] ?? '0.00';
        $data['ra']           = $this->request->post['ra'] ?? '0.00';
        $data['rc']           = $this->request->post['rc'] ?? '0.00';
        $data['due']          = $this->request->post['due'] ?? '0.00';
        $data['sbt']          = $this->request->post['sbt'] ?? '0.00';
        $data['total_orders'] = $this->request->post['total_orders'] ?? 0;

    $this->response->setOutput($this->load->view("extension/purpletree_pos/pos/smallprint_invoice",$data)
    );
}


public function publicInvoice(): void
{
    $order_id = (int)($this->request->get['order_id'] ?? 0);

    if (!$order_id) {
        echo 'Invalid order';
        return;
    }

    $this->load->model('checkout/order');

    $order_info = $this->model_checkout_order->getOrderdetails($order_id);
    if (!$order_info) {
        echo 'Order not found';
        return;
    }


    $this->request->get['order_id'] = $order_id;
    $this->invoice();
}

public function publicQuoteInvoice(): void
{
    $quote_id = (int)($this->request->get['quote_id'] ?? 0);

    if (!$quote_id) {
        echo 'Invalid quotation';
        return;
    }

    $this->load->model('checkout/order');

    $order_info = $this->model_checkout_order->getQuoteOrderdetails($quote_id);
    if (!$order_info) {
        echo 'Quotation not found';
        return;
    }

    // Reuse existing quoteInvoice logic
    $this->request->get['quote_id'] = $quote_id;
    $this->quoteInvoice();
}


public function generateUpiQr() {
    $this->response->addHeader('Content-Type: application/json');

    try {

        // Get amount from POST
        $amount = isset($this->request->post['amount'])
            ? (float)$this->request->post['amount']
            : 0;

        if ($amount <= 0) {
            throw new \Exception('Invalid amount');
        }

        $upi_id = "7337011206-2@axl";
        $name   = "Saleem Gold Covering";

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


        $folder = $_SERVER['DOCUMENT_ROOT'] . '/pos_qr/';

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }
        
        $file = 'upi_' . time() . '_' . rand(1000,9999) . '.png';
        
        $full_path = $folder . $file;
        
        \QRcode::png($upi_url, $full_path, QR_ECLEVEL_L, 5);
        
        $qr_url = $this->config->get('config_url') . '../pos_qr/' . $file;

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

    try {

        // Get amount from POST
        $amount = isset($this->request->post['amount'])
            ? (float)$this->request->post['amount']
            : 0;

        if ($amount <= 0) {
            throw new \Exception('Invalid amount');
        }

        $upi_id = "7337011206-2@axl";
        $name   = "Saleem Gold Covering";

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


        $folder = $_SERVER['DOCUMENT_ROOT'] . '/pos_qr/';

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }
        
        $file = 'upi_' . time() . '_' . rand(1000,9999) . '.png';
        
        $full_path = $folder . $file;
        
        \QRcode::png($upi_url, $full_path, QR_ECLEVEL_L, 5);
        
        $qr_url = $this->config->get('config_url') . '../pos_qr/' . $file;

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


/*public function getDeviceQr(){

$this->response->addHeader(
'Content-Type: application/json'
);

if(!empty($this->session->data['dqr12_device_send'])){

$data=$this->session->data['dqr12_device_send'];

unset(
$this->session->data['dqr12_device_send']
);

$this->response->setOutput(

json_encode([

'status'=>'send',

'upi_url'=>$data['upi_url'],

'amount'=>$data['amount'],

'vpa'=>$data['vpa']

])

);

}else{

$this->response->setOutput(

json_encode([

'status'=>'idle'

])

);

}

}

public function DQR12() {

$this->response->setOutput(

$this->load->view(

'extension/purpletree_pos/pos/DQR12',

[]

)

);

}*/



    // In class Home extends ... (same file where addorder(), invoice() etc. are)
    public function Order_sendwhatsapp(): void
    {
         $order_id = (int)($this->request->get['order_id'] ?? 0);
    $quote_id = (int)($this->request->get['quote_id'] ?? 0);
    $phone    = trim($this->request->get['phone'] ?? '');

    if ((!$order_id && !$quote_id) || !$phone) {
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode([
            "status" => "error",
            "message" => "order_id or quote_id and phone required",
        ]));
        return;
    }

    $this->load->model("checkout/order");

    // -----------------------------
    // LOAD DATA BASED ON TYPE
    // -----------------------------
    if ($quote_id > 0) {
        // QUOTATION
        $order = $this->model_checkout_order->getQuoteOrderdetails($quote_id);
        if (!$order) {
            $this->response->setOutput(json_encode([
                "status" => "error",
                "message" => "Quotation not found",
            ]));
            return;
        }

        $products = $this->model_checkout_order->getQuoteProducts($quote_id);

        $invoice_no = 'Q-' . $quote_id;
        $download_link = HTTP_SERVER
            . 'index.php?route=extension/purpletree_pos/pos/home|quoteInvoice'
            . '&quote_id=' . $quote_id;

    } else {
        // ORDER
        $order = $this->model_checkout_order->getOrder($order_id);
        if (!$order) {
            $this->response->setOutput(json_encode([
                "status" => "error",
                "message" => "Order not found",
            ]));
            return;
        }

        $products = $order['products'] ?? [];

        $invoice_no = (string)$order_id;
        $download_link = HTTP_SERVER
            . 'index.php?route=extension/purpletree_pos/pos/home|publicInvoice'
            . '&order_id=' . $order_id;
        $download_invoice = HTTP_SERVER
            . 'index.php?route=extension/purpletree_pos/pos/home|publicInvoice'
            . '&order_id=' . $order_id;
    }
        $link = 'https://myteknoland.com/';
    // -----------------------------
    // COMMON TEMPLATE VALUES
    // -----------------------------
    $customer_name = trim(($order['firstname'] ?? '') . ' ' . ($order['lastname'] ?? ''));
    $store_name    = 'Saleem Gold Covering - Wholesale';

    $items_count = (string) max(1, count($products));
    $amount      = number_format((float)($order['total'] ?? 0), 2, '.', '');
    $date        = substr((string)($order['date_added'] ?? date('Y-m-d')), 0, 10);
   
        // Build MSG91 payload for template "order_success"
        $payload = [
            "integrated_number" => "918341711206",
            "content_type" => "template",
            "payload" => [
                "messaging_product" => "whatsapp",
                "type" => "template",
                "template" => [
                    "name" => "download_invoce_order_success",
                    "language" => [
                        "code" => "en",
                        "policy" => "deterministic",
                    ],
                    "namespace" => "f18a3096_c1f2_4aae_8001_f7020abc1c5b",
                    "to_and_components" => [
                        [
                            "to" => [$phone, 917337011206],
                            "components" => [
                                
                                 "header_1"=> [
                            "filename"=> " SGC Invoice",
                            "type"=> "document",
                            "value"=> $download_link
                        ],
                                "body_1" => [
                                    "type" => "text",
                                    "value" => $customer_name,
                                ],
                                "body_2" => [
                                    "type" => "text",
                                    "value" => $store_name,
                                ],
                                "body_3" => [
                                    "type" => "text",
                                    "value" => $invoice_no,
                                ],
                                "body_4" => [
                                    "type" => "text",
                                    "value" => $amount ,
                                ],
                                "body_5" => [
                                    "type" => "text",
                                    "value" => $date ,
                                ],
                                "body_6" => [
                                    "type" => "text",
                                    "value" => (string) $items_count,
                                ],
                                "button_1" => [
                                "subtype" => "url",

                                    "type" => "text",
                                    "value" => $download_invoice
                                ]
                                
                                
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $ch = curl_init(
            "https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/"	
        );
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "authkey: 471465A6FulqId269201b0eP1",
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(
            json_encode([
                "status" => $httpCode,
                "response" => $response,
            ])
        );
        
    }
    
    
    
    public function quote_sendwhatsapp(): void
    {
         $order_id = (int)($this->request->get['order_id'] ?? 0);
    $quote_id = (int)($this->request->get['quote_id'] ?? 0);
    $phone    = trim($this->request->get['phone'] ?? '');

    if ((!$order_id && !$quote_id) || !$phone) {
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode([
            "status" => "error",
            "message" => "order_id or quote_id and phone required",
        ]));
        return;
    }

    $this->load->model("checkout/order");

    if ($quote_id > 0) {
        // QUOTATION
        $order = $this->model_checkout_order->getQuoteOrderdetails($quote_id);
        if (!$order) {
            $this->response->setOutput(json_encode([
                "status" => "error",
                "message" => "Quotation not found",
            ]));
            return;
        }

        $products = $this->model_checkout_order->getQuoteProducts($quote_id);

        $invoice_no = 'Q-' . $quote_id;
        $download_link = HTTP_SERVER
            . 'index.php?route=extension/purpletree_pos/pos/home|quoteInvoice'
            . '&quote_id=' . $quote_id;
         $download_invoice = 
            'JEWELLERY2/index.php?route=extension/purpletree_pos/pos/home|publicInvoice'
            . '&order_id=' . $order_id;

    } else {
        // ORDER
        $order = $this->model_checkout_order->getOrder($order_id);
        if (!$order) {
            $this->response->setOutput(json_encode([
                "status" => "error",
                "message" => "Order not found",
            ]));
            return;
        }

        $products = $order['products'] ?? [];

        $invoice_no = (string)$order_id;
        $download_link = HTTP_SERVER
            . 'index.php?route=extension/purpletree_pos/pos/home|publicInvoice'
            . '&order_id=' . $order_id;
        $download_invoice = 
            'JEWELLERY2/index.php?route=extension/purpletree_pos/pos/home|publicInvoice'
            . '&order_id=' . $order_id;
    }
        $link = 'https://myteknoland.com/';
    // -----------------------------
    // COMMON TEMPLATE VALUES
    // -----------------------------
    $customer_name = trim(($order['firstname'] ?? '') . ' ' . ($order['lastname'] ?? ''));
    $store_name    = 'Saleem Gold Covering - Wholesale';

    $items_count = (string) max(1, count($products));
    $amount      = number_format((float)($order['total'] ?? 0), 2, '.', '');
    $date        = substr((string)($order['date_added'] ?? date('Y-m-d')), 0, 10);

        // Build MSG91 payload for template "order_success"
        $payload = [
            "integrated_number" => "918341711206",
            "content_type" => "template",
            "payload" => [
                "messaging_product" => "whatsapp",
                "type" => "template",
                "template" => [
                    "name" => "download_invoce_quote_success",
                    "language" => [
                        "code" => "en",
                        "policy" => "deterministic",
                    ],
                    "namespace" => "f18a3096_c1f2_4aae_8001_f7020abc1c5b",
                    "to_and_components" => [
                        [
                            "to" => [$phone,'917337011206'],
                            "components" => [
                                
                                 "header_1"=> [
                            "filename"=> " SGC Invoice",
                            "type"=> "document",
                            "value"=> $download_link
                        ],
                                "body_1" => [
                                    "type" => "text",
                                    "value" => $customer_name,
                                ],
                                "body_2" => [
                                    "type" => "text",
                                    "value" => $store_name,
                                ],
                                "body_3" => [
                                    "type" => "text",
                                    "value" => $invoice_no,
                                ],
                                "body_4" => [
                                    "type" => "text",
                                    "value" => $amount ,
                                ],
                                "body_5" => [
                                    "type" => "text",
                                    "value" => $date ,
                                ],
                                "body_6" => [
                                    "type" => "text",
                                    "value" => (string) $items_count,
                                ],
                                "button_1" => [
                                "subtype" => "url",

                                    "type" => "text",
                                    "value" => $download_invoice
                                ]
                                
                                
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $ch = curl_init(
            "https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/"	
        );
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "authkey: 471465A6FulqId269201b0eP1",
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(
            json_encode([
                "status" => $httpCode,
                "response" => $response,
            ])
        );
        
    }

    public function POSProducts1($posData = [])
    {
        $product_info = [];
        if (!empty($posData)) {
            $product_info = $this->model_catalog_product->getProduct(
                $posData["product_id"]
            );
            if ($product_info) {
                $product_info["pos_quantity"] = $posData["pos_quentity"];
                $product_info["pos_status"] = $posData["pos_status"];
                $product_info["manufacturers"] = $this->url->link(
                    "product/manufacturer/info",
                    "manufacturer_id=" . $product_info["manufacturer_id"]
                );
                $product_info["heading_title"] = $product_info["name"];
                $product_info["text_minimum"] = sprintf(
                    $this->language->get("text_minimum"),
                    $product_info["minimum"]
                );

                if ($product_info["description"] != "") {
                    $product_info["description"] =
                        utf8_substr(
                            trim(
                                strip_tags(
                                    html_entity_decode(
                                        $product_info["description"],
                                        ENT_QUOTES,
                                        "UTF-8"
                                    )
                                )
                            ),
                            0,
                            $this->config->get(
                                "theme_" .
                                    $this->config->get("config_theme") .
                                    "_product_description_length"
                            )
                        ) . "..";
                } else {
                    $product_info["description"] = null;
                }

                $this->load->model("catalog/review");

                if ($product_info["quantity"] <= 0) {
                    $product_info["stock"] = $product_info["stock_status"];
                } elseif ($this->config->get("config_stock_display")) {
                    $product_info["stock"] = $product_info["quantity"];
                } else {
                    $product_info["stock"] = $this->language->get(
                        "text_instock"
                    );
                }

                $this->load->model("tool/image");

                if ($product_info["image"]) {
                    $product_info["popup"] = $this->model_tool_image->resize(
                        $product_info["image"],
                        $this->config->get(
                            "theme_" .
                                $this->config->get("config_theme") .
                                "_image_popup_width"
                        ),
                        $this->config->get(
                            "theme_" .
                                $this->config->get("config_theme") .
                                "_image_popup_height"
                        )
                    );
                } else {
                    $product_info["popup"] = "";
                }

                if ($product_info["image"]) {
                    $product_info["thumb"] = $this->model_tool_image->resize(
                        $product_info["image"],
                        80,
                        80
                    );

                    $product_info[
                        "thumb_popup"
                    ] = $this->model_tool_image->resize(
                        $product_info["image"],
                        350,
                        350
                    );
                } else {
                    $product_info["thumb"] = $this->model_tool_image->resize(
                        "placeholder.png",
                        80,
                        80
                    );

                    $product_info[
                        "thumb_popup"
                    ] = $this->model_tool_image->resize(
                        "placeholder.png",
                        350,
                        350
                    );
                }

                $product_info["images"] = [];

                $results = $this->model_catalog_product->getProductImages(
                    $posData["product_id"]
                );

                foreach ($results as $result) {
                    $product_info["images"][] = [
                        "popup" => $this->model_tool_image->resize(
                            $result["image"],
                            $this->config->get(
                                "theme_" .
                                    $this->config->get("config_theme") .
                                    "_image_popup_width"
                            ),
                            $this->config->get(
                                "theme_" .
                                    $this->config->get("config_theme") .
                                    "_image_popup_height"
                            )
                        ),
                        "thumb" => $this->model_tool_image->resize(
                            $result["image"],
                            $this->config->get(
                                "theme_" .
                                    $this->config->get("config_theme") .
                                    "_image_additional_width"
                            ),
                            $this->config->get(
                                "theme_" .
                                    $this->config->get("config_theme") .
                                    "_image_additional_height"
                            )
                        ),
                    ];
                }

                if (
                    $this->customer->isLogged() ||
                    !$this->config->get("config_customer_price")
                ) {
                    $price = $product_info["price"];
                    $product_info["price_without_tax"] = $product_info["price"];
                    $product_info["price_with_tax"] = $this->tax->calculate(
                        $product_info["price"],
                        $product_info["tax_class_id"],
                        $this->config->get("config_tax")
                    );
                    $product_info["price"] = $this->currency->format(
                        $this->tax->calculate(
                            $product_info["price"],
                            $product_info["tax_class_id"],
                            $this->config->get("config_tax")
                        ),
                        $this->config->get("config_currency")
                    );
                } else {
                    $price = false;
                    $product_info["price"] = false;
                    $product_info["price_without_tax"] = false;
                    $product_info["price_with_tax"] = false;
                }
                if (
                    !is_null($product_info["special"]) &&
                    (float) $product_info["special"] >= 0
                ) {
                    $product_info["special_without_tax"] =
                        (float) $product_info["special"];
                    $product_info["special_with_tax"] = $this->tax->calculate(
                        $product_info["special"],
                        $product_info["tax_class_id"],
                        $this->config->get("config_tax")
                    );
                    $special = $this->currency->format(
                        $this->tax->calculate(
                            $product_info["special"],
                            $product_info["tax_class_id"],
                            $this->config->get("config_tax")
                        ),
                        $this->config->get("config_currency")
                    );
                    $price = (float) $product_info["special"];
                    $product_info["special"] = $special;
                } else {
                    $product_info["price"] = $product_info["price"];
                    $product_info["special"] = false;
                    $product_info["special_without_tax"] = false;
                    $product_info["special_with_tax"] = false;
                    $price = (float) $price;
                }
                if ($this->config->get("config_tax")) {
                    $product_info["tax"] = $this->currency->format(
                        $price,
                        $this->config->get("config_currency")
                    );
                } else {
                    $product_info["tax"] = false;
                }
                $product_info["real_price"] = $price;

                $discounts = $this->model_catalog_product->getProductDiscounts(
                    $posData["product_id"]
                );

                $product_info["discounts"] = [];

                foreach ($discounts as $discount) {
                    $product_info["discounts"][] = [
                        "quantity" => $discount["quantity"],
                        "price" => $this->currency->format(
                            $this->tax->calculate(
                                $discount["price"],
                                $product_info["tax_class_id"],
                                $this->config->get("config_tax")
                            ),
                            $this->config->get("config_currency")
                        ),
                        "price_with_tax" => $this->tax->calculate(
                            $discount["price"],
                            $product_info["tax_class_id"],
                            $this->config->get("config_tax")
                        ),
                        "price_without_tax" => $discount["price"],
                    ];
                }

                $product_info["options"] = [];
                foreach (
                    $this->model_catalog_product->getProductOptions(
                        $posData["product_id"]
                    )
                    as $option
                ) {
                    $product_option_value_data = [];

                    foreach ($option["product_option_value"] as $option_value) {
                        if (
                            !$option_value["subtract"] ||
                            $option_value["quantity"] > 0
                        ) {
                            if (
                                (($this->config->get("config_customer_price") &&
                                    $this->customer->isLogged()) ||
                                    !$this->config->get(
                                        "config_customer_price"
                                    )) &&
                                (float) $option_value["price"]
                            ) {
                                $price = $this->currency->format(
                                    $this->tax->calculate(
                                        $option_value["price"],
                                        $product_info["tax_class_id"],
                                        $this->config->get("config_tax")
                                            ? "P"
                                            : false
                                    ),
                                    $this->config->get("config_currency")
                                );
                            } else {
                                $price = false;
                            }

                            $product_option_value_data[] = [
                                "product_option_value_id" =>
                                    $option_value["product_option_value_id"],
                                "option_value_id" =>
                                    $option_value["option_value_id"],
                                "name" => $option_value["name"],
                                "image" => $this->model_tool_image->resize(
                                    $option_value["image"],
                                    50,
                                    50
                                ),
                                "price" => $price,
                                "price_prefix" => $option_value["price_prefix"],
                            ];
                        }
                    }

                    $product_info["options"][] = [
                        "product_option_id" => $option["product_option_id"],
                        "product_option_value" => $product_option_value_data,
                        "option_id" => $option["option_id"],
                        "name" => $option["name"],
                        "type" => $option["type"],
                        "value" => $option["value"],
                        "required" => $option["required"],
                    ];
                }

                if ($product_info["minimum"]) {
                    $product_info["minimum"] = $product_info["minimum"];
                } else {
                    $product_info["minimum"] = 1;
                }

                $product_info["review_status"] = $this->config->get(
                    "config_review_status"
                );

                if (
                    $this->config->get("config_review_guest") ||
                    $this->customer->isLogged()
                ) {
                    $product_info["review_guest"] = true;
                } else {
                    $product_info["review_guest"] = false;
                }

                if ($this->customer->isLogged()) {
                    $product_info["customer_name"] =
                        $this->customer->getFirstName() .
                        "&nbsp;" .
                        $this->customer->getLastName();
                } else {
                    $product_info["customer_name"] = "";
                }

                $product_info["reviews"] = sprintf(
                    $this->language->get("text_reviews"),
                    (int) $product_info["reviews"]
                );
                $product_info["rating"] = (int) $product_info["rating"];

                if (
                    $this->config->get(
                        "captcha_" .
                            $this->config->get("config_captcha") .
                            "_status"
                    ) &&
                    in_array(
                        "review",
                        (array) $this->config->get("config_captcha_page")
                    )
                ) {
                    $product_info["captcha"] = $this->load->controller(
                        "extension/captcha/" .
                            $this->config->get("config_captcha")
                    );
                } else {
                    $product_info["captcha"] = "";
                }

                $product_info["share"] = $this->url->link(
                    "product/product",
                    "product_id=" . (int) $posData["product_id"]
                );

                $product_info[
                    "attribute_groups"
                ] = $this->model_catalog_product->getProductAttributes(
                    $posData["product_id"]
                );

                $product_info["tags"] = [];

                if ($product_info["tag"]) {
                    $tags = explode(",", $product_info["tag"]);

                    foreach ($tags as $tag) {
                        $product_info["tags"][] = [
                            "tag" => trim($tag),
                            "href" => $this->url->link(
                                "product/search",
                                "tag=" . trim($tag)
                            ),
                        ];
                    }
                }

                $product_info[
                    "recurrings"
                ] = $this->model_catalog_product->getProfiles(
                    $posData["product_id"]
                );

                //Tax code

                $tax_data = [];
                $quantity = 1;
                if ($product_info["tax_class_id"]) {
                    $tax_rates = $this->tax->getRates(
                        $product_info["price_without_tax"],
                        $product_info["tax_class_id"]
                    );

                    foreach ($tax_rates as $tax_rate) {
                        if (!isset($tax_data[$tax_rate["tax_rate_id"]])) {
                            $tax_data[$tax_rate["tax_rate_id"]] =
                                $tax_rate["amount"] * $quantity;
                        } else {
                            $tax_data[$tax_rate["tax_rate_id"]] +=
                                $tax_rate["amount"] * $quantity;
                        }
                    }
                }
                $product_info["product_tax"] = $tax_data;
                //Tax code
            }
        }
        return $product_info;
    }

    public function addAddress()
    {
        // echo"<pre>";print_r($this->request->post);die;
        $this->load->model("account/address");
        $json = [];
        if ($this->request->server["REQUEST_METHOD"] == "POST") {
            $data["custom_field"]["address"] = "";
            $address_id = $this->model_account_address->addAddress(
                $this->request->post["customer_id"],
                $this->request->post
            );
        }
        if ($address_id) {
            $this->load->model("extension/purpletree_pos/pos/posproduct");
            $address = $this->model_extension_purpletree_pos_pos_posproduct->getAddresses(
                $this->request->post["customer_id"]
            );
            $json["status"] = "success";
            $json["msg"] = "Your address save successfully.";
            //$json['success'] = 'Your address save successfully';
            $json["setPaymentAddress"] = false;
            $json["setShippingAddress"] = false;
            extract($this->request->post);
            if (isset($setPaymentaddress1)) {
                $json["setPaymentAddress"] = true;
            }
            if (isset($setShippingaddress1)) {
                $json["setShippingAddress"] = true;
            }

            $json["address_id"] = $address_id;
            $json["address"] = $address;
        } else {
            $json["status"] = "error";
            $json["msg"] = "Your address not saved.";
        }

        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }

    public function getPosOrders()
    {
        $json = [];
        $filter_data = $this->request->post;
        $this->load->model("extension/purpletree_pos/pos/posproduct");
        $data[
            "isPosAgent"
        ] = $this->model_extension_purpletree_pos_pos_posproduct->isPosAgent();
        $isPosAdmin = $this->model_extension_purpletree_pos_pos_posproduct->isPosAdmin();
        if ($data["isPosAgent"]) {
            $agent_id = $this->customer->getId();
            $filter_data["agent_id"] = $agent_id;
        }
        if ($isPosAdmin) {
            $agent_id = $filter_data["agent_id"];
        }

        $data["orders"] = [];
        $this->load->model("extension/purpletree_pos/pos/posproduct");
        $results = $this->model_extension_purpletree_pos_pos_posproduct->getPosOrders(
            $filter_data
        );

        $data["return_products"] = [];
        foreach ($results as $result) {
            $productsData = $this->model_extension_purpletree_pos_pos_posproduct->getPosOrderProducts(
                $result["order_id"],
                $agent_id
            );
            $data["products"] = [];

            $data["return_products"][
                $result["order_id"]
            ] = $this->model_extension_purpletree_pos_pos_posproduct->getPosReturnProducts(
                $result["order_id"],
                $agent_id
            );
            if (!empty($productsData)) {
                foreach ($productsData as $key => $product) {
                    $data["products"][] = [
                        "order_product_id" => $product["order_product_id"],
                        "order_id" => $product["order_id"],
                        "product_id" => $product["product_id"],
                        "name" => $product["name"],
                        "model" => $product["model"],
                        "quantity" => $product["quantity"],
                        "price" => $product["price"] + $product["tax"],
                        "total" =>
                            $product["total"] +
                            $product["tax"] * $product["quantity"],
                        "tax" => $product["tax"],
                        "reward" => $product["reward"],
                    ];
                }
            }

            $data["orders"][] = [
                "order_id" => $result["order_id"],
                "firstname" => $result["firstname"],
                "lastname" => $result["lastname"],
                "email" => $result["email"],
                "customer_id" => $result["customer_id"],
                "telephone" => $result["telephone"],
                "customer" => $result["customer"],
                "order_status" => $result["order_status"]
                    ? $result["order_status"]
                    : $this->language->get("text_missing"),
                "total" => $result["total"],
                "date_ordered" => date(
                    "Y-m-d",
                    strtotime($result["date_added"])
                ),
                "date_added" => date(
                    $this->language->get("date_format_short"),
                    strtotime($result["date_added"])
                ),
                "date_modified" => date(
                    $this->language->get("date_format_short"),
                    strtotime($result["date_modified"])
                ),
                "shipping_code" => $result["shipping_method"],
                "payment_method" => $result["payment_method"],
                "products" => $data["products"],
                "agent_id" => $this->customer->getId(),
            ];
        }
        $json["orders"] = $data["orders"];
        $json["return_products"] = $data["return_products"];
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    public function getPosReturnOrders()
    {
        $json = [];
        $returnorders = [];
        $filter_data = $this->request->post;
        $this->load->model("extension/purpletree_pos/pos/posproduct");
        $data[
            "isPosAgent"
        ] = $this->model_extension_purpletree_pos_pos_posproduct->isPosAgent();
        $isPosAdmin = $this->model_extension_purpletree_pos_pos_posproduct->isPosAdmin();
        if ($data["isPosAgent"]) {
            $agent_id = $this->customer->getId();
            $filter_data["agent_id"] = $agent_id;
        }
        if ($isPosAdmin) {
            $agent_id = $filter_data["agent_id"];
        }
        $data["orders"] = [];
        $this->load->model("extension/purpletree_pos/pos/posproduct");
        $returnorders_temp = $this->model_extension_purpletree_pos_pos_posproduct->getPosReturnOrders(
            $filter_data
        );

        if (!empty($returnorders_temp)) {
            foreach ($returnorders_temp as $retrunKey => $returnVal) {
                $returnorders[] = [
                    "return_id" => $returnVal["return_id"],
                    "order_id" => $returnVal["order_id"],
                    "product_id" => $returnVal["product_id"],
                    "customer_id" => $returnVal["customer_id"],
                    "firstname" => $returnVal["firstname"],
                    "lastname" => $returnVal["lastname"],
                    "email" => $returnVal["email"],
                    "telephone" => $returnVal["telephone"],
                    "product" => $returnVal["product"],
                    "model" => $returnVal["model"],
                    "quantity" => $returnVal["quantity"],
                    "opened" => $returnVal["opened"],
                    "return_reason_id" => $returnVal["return_reason_id"],
                    "return_action_id" => $returnVal["return_action_id"],
                    "return_status_id" => $returnVal["return_status_id"],
                    "return_reason" => $this->model_extension_purpletree_pos_pos_posproduct->getReturnReasonById(
                        $returnVal["return_reason_id"]
                    ),
                    "return_action" => $this->model_extension_purpletree_pos_pos_posproduct->getReturnActionById(
                        $returnVal["return_action_id"]
                    ),
                    "return_status" => $this->model_extension_purpletree_pos_pos_posproduct->getReturnStatusById(
                        $returnVal["return_status_id"]
                    ),
                    "comment" => $returnVal["comment"],
                    "date_ordered" => $returnVal["date_ordered"],
                    "date_added" => $returnVal["date_added"],
                    "date_modified" => $returnVal["date_modified"],
                    "customer_name" => $returnVal["customer_name"],
                    "agent_id" => $returnVal["agent_id"],
                ];
            }
        }
        $json["returnorders"] = $returnorders;
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    protected function validateForm()
    {
        if (
            utf8_strlen(trim($this->request->post["firstname"])) < 1 ||
            utf8_strlen(trim($this->request->post["firstname"])) > 32
        ) {
            $this->error["firstname"] = $this->language->get("error_firstname");
        }

        if (
            utf8_strlen(trim($this->request->post["lastname"])) < 1 ||
            utf8_strlen(trim($this->request->post["lastname"])) > 32
        ) {
            $this->error["lastname"] = $this->language->get("error_lastname");
        }

        if (
            utf8_strlen(trim($this->request->post["address_1"])) < 3 ||
            utf8_strlen(trim($this->request->post["address_1"])) > 128
        ) {
            $this->error["address_1"] = $this->language->get("error_address_1");
        }

        if (
            utf8_strlen(trim($this->request->post["city"])) < 2 ||
            utf8_strlen(trim($this->request->post["city"])) > 128
        ) {
            $this->error["city"] = $this->language->get("error_city");
        }

        // $this->load->model('localisation/country');

        // $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

        if (
            utf8_strlen(trim($this->request->post["postcode"])) < 2 ||
            utf8_strlen(trim($this->request->post["postcode"])) > 10
        ) {
            $this->error["postcode"] = $this->language->get("error_postcode");
        }

        if (
            utf8_strlen(trim($this->request->post["postcode"])) < 2 ||
            utf8_strlen(trim($this->request->post["postcode"])) > 10
        ) {
            $this->error["country"] = $this->language->get("error_country");
        }

        if (
            !isset($this->request->post["zone_id"]) ||
            $this->request->post["zone_id"] == "" ||
            !is_numeric($this->request->post["zone_id"])
        ) {
            $this->error["zone"] = $this->language->get("error_zone");
        }
    }
    
    
    public function searchProducts() {
        $json = [];
    
        if (isset($this->request->get['term'])) {
    
            $term = $this->request->get['term'];
    
            $this->load->model('extension/purpletree_pos/pos/posproduct');
    
            // Get matching products (id, quantity, status)
            $results = $this->model_extension_purpletree_pos_pos_posproduct->searchProducts($term);
    
            foreach ($results as $row) {
    
                $posData = $this->model_extension_purpletree_pos_pos_posproduct->getProductByid(
                    $row['product_id']
                );
    
                if ($posData) {
                    $json[] = $this->POSProducts($posData);
                }
            }
        }
    
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    public function updateDailyAmount() {

        $this->response->addHeader("Content-Type: application/json");

        try {

            $this->load->model("checkout/order");

            $affected = $this->model_checkout_order->updateDailyAmount();

            return $this->response->setOutput(json_encode([
                "status" => "success",
                "updated_rows" => $affected
            ]));

        } catch (Throwable $e) {

            return $this->response->setOutput(json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]));
        }
    }
    

   public function getInitialData(): void {

    $this->response->addHeader('Content-Type: application/json');
    
    $this->load->model("checkout/order");
 
    $main_categories = $this->model_checkout_order->getMainCategories();

    $categories = [];

    foreach ($main_categories as $category) {

        $products = $this->model_checkout_order->getProductsOnly($category['category_id'], 0, 5);

        $categories[] = [
            "category_id" => $category['category_id'],
            "name"        => $category['name'],
            "products"    => $products
        ];
    }
    
    $this->response->setOutput(json_encode([
        "status"     => "success",
        "categories" => $categories
    ]));
}
    
    public function getCategoryData(): void {

    $this->response->addHeader('Content-Type: application/json');
    
    
    $category_id=(int)($this->request->get['category_id'] ?? 0);
    
    $this->load->model('groceries/categories');

    $category_products=$this->model_groceries_categories->getProductsOnly($category_id);

    $subcategories=$this->model_groceries_categories->getSubCategories($category_id);
    foreach($subcategories as &$subcategory){
    
    $subcategory['products']=$this->model_groceries_categories->getProductsOnly($subcategory['category_id']);
    
    }

    $this->response->setOutput(
    
        json_encode([
        
        "status"=>"success",
        "subcategories"=>$subcategories,
        "products"=>$category_products
        ])
    
    );

}

} 
?>
