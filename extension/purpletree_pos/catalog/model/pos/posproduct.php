<?php
namespace Opencart\Catalog\Model\Extension\PurpletreePos\Pos;
class Posproduct extends \Opencart\System\Engine\Model
{
    public function getProductid()
    {
        $query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "pts_pos_product LIMIT 10"
        );
        return $query->rows;
    }

    public function getAllCategory()
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category");
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return null;
        }
    }
    public function getAllCategoryDescription()
    {
        $query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "category_description"
        );
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return null;
        }
    }

    public function getProducttoCategory()
    {
        $query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "product_to_category"
        );
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return null;
        }
    }

    public function getAllCoupon()
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "coupon");
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return null;
        }
    }

    public function getPosProducts()
    {
        $query = $this->db->query(
            "SELECT p.* ,ppd.* FROM " .
                DB_PREFIX .
                "product p LEFT JOIN " .
                DB_PREFIX .
                "pts_pos_product ppd ON (p.product_id = ppd.product_id) WHERE ppd.pos_status=1 AND p.status=1 LIMIT 10"
        );
        if ($query->num_rows) {
            $products = [];
            foreach ($query->rows as $key => $product) {
                $products[$product["product_id"]] = $product;
            }
            return $products;
        } else {
            return null;
        }
    }

    public function getPosProductDescription()
    {
        $query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "product_description"
        );
        if ($query->num_rows) {
            $productDesc = [];
            foreach ($query->rows as $key => $product) {
                $productDesc[$product["product_id"]] = $product;
            }
            return $productDesc;
        } else {
            return null;
        }
    }

    public function getPosProductOption()
    {
        $query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "product_option"
        );
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return null;
        }
    }
    public function getPosOption()
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "option");
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return null;
        }
    }
    public function getPosOptionDescription()
    {
        $query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "option_description"
        );
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return null;
        }
    }

    public function getPosProductOptionValue()
    {
        $query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "product_option_value"
        );
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return null;
        }
    }

    public function getPosOptionValue()
    {
        $query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "option_value"
        );
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return null;
        }
    }
    public function getPosOptionValueDescription()
    {
        $query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "option_value_description"
        );
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return null;
        }
    }
    public function getPosProductDiscount()
    {
        $query = $this->db->query(
            "SELECT * FROM " .
                DB_PREFIX .
                "product_discount ORDER BY product_discount_id ASC"
        );
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return null;
        }
    }
    public function getPosProductSpecial()
    {
        $query = $this->db->query(
            "SELECT * FROM " .
                DB_PREFIX .
                "product_special ORDER BY product_special_id ASC"
        );
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return null;
        }
    }
    public function getPosProductReward()
    {
        $query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "product_reward"
        );
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return null;
        }
    }

    public function getCustomersList()
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer");
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return null;
        }
    }
    public function getCustomerGroupname()
    {
        $query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "customer_group_description"
        );
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return null;
        }
    }
    public function getCustomerEmails($data = [])
    {
        $query = $this->db->query(
            "SELECT * FROM " .
                DB_PREFIX .
                "customer WHERE email = '" .
                $this->db->escape($data["email"]) .
                "' OR telephone = '" .
                $this->db->escape($data["telephone"]) .
                "'"
        );
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return null;
        }
    }
    public function getCustomerFullData($customer_id)
    {
        $query = $this->db->query(
            "SELECT * FROM " .
                DB_PREFIX .
                "customer WHERE customer_id = '" .
                $customer_id .
                "'"
        );
        if ($query->num_rows) {
            return $query->row;
        } else {
            return null;
        }
    }

    public function getCustomers($data = [])
    {
        if (!empty($data["filter_name"])) {
            $sql =
                "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name, cgd.name AS customer_group FROM " .
                DB_PREFIX .
                "customer c LEFT JOIN " .
                DB_PREFIX .
                "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id)";

            $sql .=
                " WHERE cgd.language_id = '" .
                (int) $this->config->get("config_language_id") .
                "'";

            $implode = [];

            if (!empty($data["filter_name"])) {
                $implode[] =
                    "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" .
                    $this->db->escape($data["filter_name"]) .
                    "%'";
            }

            if (!empty($data["filter_name"])) {
                $implode[] =
                    "c.email LIKE '%" .
                    $this->db->escape($data["filter_name"]) .
                    "%'";
            }

            if (!empty($data["filter_name"])) {
                $implode[] =
                    "c.telephone LIKE '%" .
                    $this->db->escape($data["filter_name"]) .
                    "%'";
            }

            if ($implode) {
                $sql .= " AND " . implode(" OR ", $implode);
            }

            $sort_data = [
                "name",
                "c.email",
                "customer_group",
                "c.status",
                "c.ip",
                "c.date_added",
            ];

            if (isset($data["sort"]) && in_array($data["sort"], $sort_data)) {
                $sql .= " ORDER BY " . $data["sort"];
            } else {
                $sql .= " ORDER BY name";
            }

            if (isset($data["order"]) && $data["order"] == "DESC") {
                $sql .= " DESC";
            } else {
                $sql .= " ASC";
            }

            if (isset($data["start"]) || isset($data["limit"])) {
                if ($data["start"] < 0) {
                    $data["start"] = 0;
                }

                if ($data["limit"] < 1) {
                    $data["limit"] = 5;
                }

                $sql .=
                    " LIMIT " .
                    (int) $data["start"] .
                    "," .
                    (int) $data["limit"];
            }

            $query = $this->db->query($sql);

            return $query->rows;
        } else {
            return null;
        }
    }
    public function getWeightUnit()
    {
        $query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "weight_class_description"
        );
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return null;
        }
    }
    public function getLengthUnit()
    {
        $query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "length_class_description"
        );
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return null;
        }
    }

    public function getCurrency()
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency");
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return null;
        }
    }
    public function rateNameData()
    {
        $tax_query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "tax_rate "
        );

        if ($tax_query->num_rows) {
            return $tax_query->rows;
        } else {
            return null;
        }
    }

    public function shippingAddressData()
    {
        $tax_query = $this->db->query(
            "SELECT z2gz.country_id, z2gz.zone_id, tr1.tax_class_id, tr2.tax_rate_id, tr2.name, tr2.rate, tr2.type, tr1.priority FROM " .
                DB_PREFIX .
                "tax_rule tr1 LEFT JOIN " .
                DB_PREFIX .
                "tax_rate tr2 ON (tr1.tax_rate_id = tr2.tax_rate_id) INNER JOIN " .
                DB_PREFIX .
                "tax_rate_to_customer_group tr2cg ON (tr2.tax_rate_id = tr2cg.tax_rate_id) LEFT JOIN " .
                DB_PREFIX .
                "zone_to_geo_zone z2gz ON (tr2.geo_zone_id = z2gz.geo_zone_id) LEFT JOIN " .
                DB_PREFIX .
                "geo_zone gz ON (tr2.geo_zone_id = gz.geo_zone_id) WHERE tr1.based = 'shipping' AND tr2cg.customer_group_id = '" .
                (int) $this->config->get("config_customer_group_id") .
                "' ORDER BY tr1.priority ASC"
        );

        if ($tax_query->num_rows) {
            return $tax_query->rows;
        } else {
            return null;
        }
    }

    public function paymentAddressData()
    {
        $tax_query = $this->db->query(
            "SELECT z2gz.country_id, z2gz.zone_id, tr1.tax_class_id, tr2.tax_rate_id, tr2.name, tr2.rate, tr2.type, tr1.priority FROM " .
                DB_PREFIX .
                "tax_rule tr1 LEFT JOIN " .
                DB_PREFIX .
                "tax_rate tr2 ON (tr1.tax_rate_id = tr2.tax_rate_id) INNER JOIN " .
                DB_PREFIX .
                "tax_rate_to_customer_group tr2cg ON (tr2.tax_rate_id = tr2cg.tax_rate_id) LEFT JOIN " .
                DB_PREFIX .
                "zone_to_geo_zone z2gz ON (tr2.geo_zone_id = z2gz.geo_zone_id) LEFT JOIN " .
                DB_PREFIX .
                "geo_zone gz ON (tr2.geo_zone_id = gz.geo_zone_id) WHERE tr1.based = 'payment' AND tr2cg.customer_group_id = '" .
                (int) $this->config->get("config_customer_group_id") .
                "' ORDER BY tr1.priority ASC"
        );

        if ($tax_query->num_rows) {
            return $tax_query->rows;
        } else {
            return null;
        }
    }

    public function storeAddressData()
    {
        $tax_query = $this->db->query(
            "SELECT z2gz.country_id, z2gz.zone_id, tr1.tax_class_id, tr2.tax_rate_id, tr2.name, tr2.rate, tr2.type, tr1.priority FROM " .
                DB_PREFIX .
                "tax_rule tr1 LEFT JOIN " .
                DB_PREFIX .
                "tax_rate tr2 ON (tr1.tax_rate_id = tr2.tax_rate_id) INNER JOIN " .
                DB_PREFIX .
                "tax_rate_to_customer_group tr2cg ON (tr2.tax_rate_id = tr2cg.tax_rate_id) LEFT JOIN " .
                DB_PREFIX .
                "zone_to_geo_zone z2gz ON (tr2.geo_zone_id = z2gz.geo_zone_id) LEFT JOIN " .
                DB_PREFIX .
                "geo_zone gz ON (tr2.geo_zone_id = gz.geo_zone_id) WHERE tr1.based = 'store' AND tr2cg.customer_group_id = '" .
                (int) $this->config->get("config_customer_group_id") .
                "' ORDER BY tr1.priority ASC"
        );

        if ($tax_query->num_rows) {
            return $tax_query->rows;
        } else {
            return null;
        }
    }

    public function getAddressId($customer_id)
    {
        $customer_query = $this->db->query(
            "SELECT * FROM " .
                DB_PREFIX .
                "customer WHERE customer_id = '" .
                (int) $customer_id .
                "' AND status = '1'"
        );
        if ($customer_query->num_rows) {
            return $customer_query->row;
        } else {
            return null;
        }
        // if ($customer_query->num_rows) {
        // return $customer_query->row['address_id'];
        // } else {
        // return null;
        // }
    }

    public function getProductForCart()
    {
        $product_query = $this->db->query(
            "SELECT * FROM " .
                DB_PREFIX .
                "product_to_store p2s LEFT JOIN " .
                DB_PREFIX .
                "product p ON (p2s.product_id = p.product_id) LEFT JOIN " .
                DB_PREFIX .
                "product_description pd ON (p.product_id = pd.product_id) WHERE p2s.store_id = '" .
                (int) $this->config->get("config_store_id") .
                "' AND pd.language_id = '" .
                (int) $this->config->get("config_language_id") .
                "' AND p.date_available <= NOW() AND p.status = '1'"
        );

        if ($product_query->num_rows) {
            return $product_query->rows;
        } else {
            return null;
        }
    }

    public function getAddress($address_id)
    {
        $address_query = $this->db->query(
            "SELECT DISTINCT * FROM " .
                DB_PREFIX .
                "address WHERE address_id = '" .
                (int) $address_id .
                "'"
        );

        if ($address_query->num_rows) {
            if (version_compare(VERSION, "4.1.0.3", ">=")) {
                $country_query = $this->db->query(
                    "SELECT * FROM `" .
                        DB_PREFIX .
                        "country` `c` LEFT JOIN `" .
                        DB_PREFIX .
                        "country_description` `cd` ON (`c`.`country_id` = `cd`.`country_id`) WHERE `c`.`country_id` = '" .
                        (int) $address_query->row["country_id"] .
                        "' AND `cd`.`language_id` = '" .
                        (int) $this->config->get("config_language_id") .
                        "' AND `c`.`status` = '1'"
                );
            } else {
                $country_query = $this->db->query(
                    "SELECT * FROM `" .
                        DB_PREFIX .
                        "country` WHERE country_id = '" .
                        (int) $address_query->row["country_id"] .
                        "'"
                );
            }

            if ($country_query->num_rows) {
                $country = $country_query->row["name"];
                $iso_code_2 = $country_query->row["iso_code_2"];
                $iso_code_3 = $country_query->row["iso_code_3"];
                $address_format = $country_query->row["address_format_id"];
            } else {
                $country = "";
                $iso_code_2 = "";
                $iso_code_3 = "";
                $address_format = "";
            }

            if (version_compare(VERSION, "4.1.0.3", ">=")) {
                $zone_query = $this->db->query(
                    "SELECT * FROM `" .
                        DB_PREFIX .
                        "zone` `z` LEFT JOIN `" .
                        DB_PREFIX .
                        "zone_description` `zd` ON (`z`.`zone_id` = `zd`.`zone_id`) WHERE `z`.`zone_id` = '" .
                        (int) $address_query->row["zone_id"] .
                        "' AND `zd`.`language_id` = '" .
                        (int) $this->config->get("config_language_id") .
                        "' AND `z`.`status` = '1'"
                );
            } else {
                $zone_query = $this->db->query(
                    "SELECT * FROM `" .
                        DB_PREFIX .
                        "zone` WHERE zone_id = '" .
                        (int) $address_query->row["zone_id"] .
                        "'"
                );
            }

            if ($zone_query->num_rows) {
                $zone = $zone_query->row["name"];
                $zone_code = $zone_query->row["code"];
            } else {
                $zone = "";
                $zone_code = "";
            }

            $address_data = [
                "address_id" => $address_query->row["address_id"],
                "firstname" => $address_query->row["firstname"],
                "lastname" => $address_query->row["lastname"],
                "company" => $address_query->row["company"],
                "address_1" => $address_query->row["address_1"],
                "address_2" => $address_query->row["address_2"],
                "postcode" => $address_query->row["postcode"],
                "city" => $address_query->row["city"],
                "zone_id" => $address_query->row["zone_id"],
                "zone" => $zone,
                "zone_code" => $zone_code,
                "country_id" => $address_query->row["country_id"],
                "country" => $country,
                "iso_code_2" => $iso_code_2,
                "iso_code_3" => $iso_code_3,
                "address_format" => $address_format,
                "custom_field" => json_decode(
                    $address_query->row["custom_field"],
                    true
                ),
            ];

            return $address_data;
        } else {
            return false;
        }
    }

    public function getCoupon()
    {
        $coupon_query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "coupon WHERE status = '1'"
        );

        if ($coupon_query->num_rows) {
            return $coupon_query->rows;
        } else {
            return null;
        }
    }

    public function getCouponCategory()
    {
        $coupon_category_query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "coupon_category"
        );

        if ($coupon_category_query->num_rows) {
            return $coupon_category_query->rows;
        } else {
            return null;
        }
    }

    public function getCouponProduct()
    {
        $coupon_product_query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "coupon_product"
        );

        if ($coupon_product_query->num_rows) {
            return $coupon_product_query->rows;
        } else {
            return null;
        }
    }
    public function getCouponHistory()
    {
        $coupon_history_query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "coupon_history"
        );

        if ($coupon_history_query->num_rows) {
            return $coupon_history_query->rows;
        } else {
            return null;
        }
    }

    public function getCategoryPath()
    {
        $category_path_query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "category_path"
        );

        if ($category_path_query->num_rows) {
            return $category_path_query->rows;
        } else {
            return null;
        }
    }

    public function getAddresses($customer_id)
    {
        $address_data = [];

        $query = $this->db->query(
            "SELECT address_id FROM " .
                DB_PREFIX .
                "address WHERE customer_id = '" .
                (int) $customer_id .
                "'"
        );

        foreach ($query->rows as $result) {
            $address_info = $this->getAddress($result["address_id"]);

            if ($address_info) {
                $address_data[$result["address_id"]] = $address_info;
            }
        }

        return $address_data;
    }

    public function getCustmerAddress($address_id, $customer_id)
    {
        $address_query = $this->db->query(
            "SELECT DISTINCT * FROM " .
                DB_PREFIX .
                "address WHERE address_id = '" .
                (int) $address_id .
                "' AND customer_id = '" .
                (int) $customer_id .
                "'"
        );

        if ($address_query->num_rows) {
            if (version_compare(VERSION, "4.1.0.3", ">=")) {
                $country_query = $this->db->query(
                    "SELECT * FROM `" .
                        DB_PREFIX .
                        "country` `c` LEFT JOIN `" .
                        DB_PREFIX .
                        "country_description` `cd` ON (`c`.`country_id` = `cd`.`country_id`) WHERE `c`.`country_id` = '" .
                        (int) $address_query->row["country_id"] .
                        "' AND `cd`.`language_id` = '" .
                        (int) $this->config->get("config_language_id") .
                        "' AND `c`.`status` = '1'"
                );
            } else {
                $country_query = $this->db->query(
                    "SELECT * FROM `" .
                        DB_PREFIX .
                        "country` WHERE country_id = '" .
                        (int) $address_query->row["country_id"] .
                        "'"
                );
            }
            if ($country_query->num_rows) {
                $country = $country_query->row["name"];
                $iso_code_2 = $country_query->row["iso_code_2"];
                $iso_code_3 = $country_query->row["iso_code_3"];
                $address_format = $country_query->row["address_format_id"];
            } else {
                $country = "";
                $iso_code_2 = "";
                $iso_code_3 = "";
                $address_format = "";
            }
            if (version_compare(VERSION, "4.1.0.3", ">=")) {
                $zone_query = $this->db->query(
                    "SELECT * FROM `" .
                        DB_PREFIX .
                        "zone` `z` LEFT JOIN `" .
                        DB_PREFIX .
                        "zone_description` `zd` ON (`z`.`zone_id` = `zd`.`zone_id`) WHERE `z`.`zone_id` = '" .
                        (int) $address_query->row["zone_id"] .
                        "' AND `zd`.`language_id` = '" .
                        (int) $this->config->get("config_language_id") .
                        "' AND `z`.`status` = '1'"
                );
            } else {
                $zone_query = $this->db->query(
                    "SELECT * FROM `" .
                        DB_PREFIX .
                        "zone` WHERE zone_id = '" .
                        (int) $address_query->row["zone_id"] .
                        "'"
                );
            }
            if ($zone_query->num_rows) {
                $zone = $zone_query->row["name"];
                $zone_code = $zone_query->row["code"];
            } else {
                $zone = "";
                $zone_code = "";
            }

            $address_data = [
                "address_id" => $address_query->row["address_id"],
                "firstname" => $address_query->row["firstname"],
                "lastname" => $address_query->row["lastname"],
                "company" => $address_query->row["company"],
                "address_1" => $address_query->row["address_1"],
                "address_2" => $address_query->row["address_2"],
                "postcode" => $address_query->row["postcode"],
                "city" => $address_query->row["city"],
                "zone_id" => $address_query->row["zone_id"],
                "zone" => $zone,
                "zone_code" => $zone_code,
                "country_id" => $address_query->row["country_id"],
                "country" => $country,
                "iso_code_2" => $iso_code_2,
                "iso_code_3" => $iso_code_3,
                "address_format" => $address_format,
                "custom_field" => json_decode(
                    $address_query->row["custom_field"],
                    true
                ),
            ];

            return $address_data;
        } else {
            return false;
        }
    }

    public function getZone()
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone");
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return null;
        }
    }

    public function getPosOrders($data)
    {
        if (version_compare(VERSION, "4.0.2.0", ">=")) {
            $sql =
                "SELECT o.order_id, o.firstname, o.lastname,o.customer_id, o.email, o.telephone, CONCAT(o.firstname, ' ', o.lastname) AS customer, (SELECT os.name FROM " .
                DB_PREFIX .
                "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" .
                (int) $this->config->get("config_language_id") .
                "') AS order_status, o.shipping_method, o.payment_method, o.total, o.currency_code, o.currency_value, o.date_added, o.date_modified FROM `" .
                DB_PREFIX .
                "order` o LEFT JOIN `" .
                DB_PREFIX .
                "pts_pos_order` ppo ON (o.order_id = ppo.order_id)";
        } else {
            $sql =
                "SELECT o.order_id, o.firstname, o.lastname,o.customer_id, o.email, o.telephone, CONCAT(o.firstname, ' ', o.lastname) AS customer, (SELECT os.name FROM " .
                DB_PREFIX .
                "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" .
                (int) $this->config->get("config_language_id") .
                "') AS order_status, o.shipping_code, o.shipping_method, o.payment_method, o.total, o.currency_code, o.currency_value, o.date_added, o.date_modified FROM `" .
                DB_PREFIX .
                "order` o LEFT JOIN `" .
                DB_PREFIX .
                "pts_pos_order` ppo ON (o.order_id = ppo.order_id)";
        }
        $implode = [];
        if (!empty($data["agent_id"])) {
            $implode[] = "ppo.agent_id = '" . (int) $data["agent_id"] . "'";
        }

        if (!empty($data["filter_date_start"])) {
            $implode[] =
                "DATE(o.date_added) >= DATE('" .
                $this->db->escape($data["filter_date_start"]) .
                "')";
        }

        if (!empty($data["filter_date_end"])) {
            $implode[] =
                "DATE(o.date_added) <= DATE('" .
                $this->db->escape($data["filter_date_end"]) .
                "')";
        }
        if (!empty($implode)) {
            $sql .= " WHERE " . implode(" AND ", $implode);
        }
        $query = $this->db->query($sql);

        return $query->rows;
    }
    public function getPosReturnOrders($data)
    {
        $sql =
            "SELECT r.*, CONCAT(r.firstname,' ',r.lastname) AS customer_name, ppro.agent_id FROM `" .
            DB_PREFIX .
            "return` r LEFT JOIN `" .
            DB_PREFIX .
            "pts_pos_return_order` ppro ON (r.return_id = ppro.return_id) ";
        $implode = [];
        if (!empty($data["agent_id"])) {
            $implode[] = "ppro.agent_id = '" . (int) $data["agent_id"] . "'";
        }

        if (!empty($data["filter_date_start"])) {
            $implode[] =
                "DATE(r.date_added) >= DATE('" .
                $this->db->escape($data["filter_date_start"]) .
                "')";
        }

        if (!empty($data["filter_date_end"])) {
            $implode[] =
                "DATE(r.date_added) <= DATE('" .
                $this->db->escape($data["filter_date_end"]) .
                "')";
        }
        if (!empty($implode)) {
            $sql .= " WHERE " . implode(" AND ", $implode);
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getReturnActionById($return_action_id)
    {
        $sql =
            "SELECT * FROM `" .
            DB_PREFIX .
            "return_action` WHERE  	return_action_id= '" .
            $return_action_id .
            "' AND  language_id='" .
            $this->config->get("config_language_id") .
            "'";
        $query = $this->db->query($sql);
        return $query->row["name"];
    }

    public function getReturnReasonById($return_reason_id)
    {
        $sql =
            "SELECT * FROM `" .
            DB_PREFIX .
            "return_reason` WHERE  	return_reason_id= '" .
            $return_reason_id .
            "' AND  language_id='" .
            $this->config->get("config_language_id") .
            "'";
        $query = $this->db->query($sql);
        return $query->row["name"];
    }

    public function getReturnStatusById($return_status_id)
    {
        $sql =
            "SELECT * FROM `" .
            DB_PREFIX .
            "return_status` WHERE  	return_status_id= '" .
            $return_status_id .
            "' AND  language_id='" .
            $this->config->get("config_language_id") .
            "'";
        $query = $this->db->query($sql);
        return $query->row["name"];
    }

    public function getPosOrderProducts($order_id, $agent_id)
    {
        $sql =
            "SELECT * FROM " .
            DB_PREFIX .
            "order_product op LEFT JOIN `" .
            DB_PREFIX .
            "pts_pos_order` ppo ON (op.order_id = ppo.order_id) WHERE ppo.order_id= '" .
            $order_id .
            "'";

        if ($agent_id) {
            $sql .= " AND ppo.agent_id = '" . (int) $agent_id . "'";
        }

        $query = $this->db->query($sql);

        if ($query->num_rows) {
            return $query->rows;
        }
        return null;
    }

    public function getReturnReason($language_id)
    {
        $query = $this->db->query(
            "SELECT * FROM `" .
                DB_PREFIX .
                "return_reason` WHERE language_id = '" .
                (int) $language_id .
                "'"
        );
        if ($query->num_rows) {
            return $query->rows;
        }
        return null;
    }

    public function getReturnAction($language_id)
    {
        $query = $this->db->query(
            "SELECT * FROM `" .
                DB_PREFIX .
                "return_action` WHERE language_id = '" .
                (int) $language_id .
                "'"
        );
        if ($query->num_rows) {
            return $query->rows;
        }
        return null;
    }

    public function getReturnStatus($language_id)
    {
        $query = $this->db->query(
            "SELECT * FROM `" .
                DB_PREFIX .
                "return_status` WHERE language_id = '" .
                (int) $language_id .
                "'"
        );
        if ($query->num_rows) {
            return $query->rows;
        }
        return null;
    }

    public function getTaxClass()
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "tax_class`");
        if ($query->num_rows) {
            return $query->rows;
        }
        return null;
    }

    public function addReturn($data)
    {
        $this->db->query(
            "INSERT INTO `" .
                DB_PREFIX .
                "return` SET order_id = '" .
                (int) $data["order_id"] .
                "', product_id = '" .
                (int) $data["product_id"] .
                "', customer_id = '" .
                (int) $data["customer_id"] .
                "', firstname = '" .
                $this->db->escape($data["firstname"]) .
                "', lastname = '" .
                $this->db->escape($data["lastname"]) .
                "', email = '" .
                $this->db->escape($data["email"]) .
                "', telephone = '" .
                $this->db->escape($data["telephone"]) .
                "', product = '" .
                $this->db->escape($data["product"]) .
                "', model = '" .
                $this->db->escape($data["model"]) .
                "', quantity = '" .
                (int) $data["quantity"] .
                "', opened = '" .
                (int) $data["opened"] .
                "', return_reason_id = '" .
                (int) $data["return_reason_id"] .
                "', return_status_id = '" .
                (int) $data["return_status_id"] .
                "', return_action_id = '" .
                (int) $data["return_action_id"] .
                "', comment = '" .
                $this->db->escape($data["comment"]) .
                "', date_ordered = '" .
                $this->db->escape($data["date_ordered"]) .
                "', date_added = NOW(), date_modified = NOW()"
        );

        $return_id = $this->db->getLastId();

        $query = $this->db->query(
            "INSERT INTO " .
                DB_PREFIX .
                "pts_pos_return_order SET return_id = '" .
                (int) $return_id .
                "',order_id = '" .
                (int) $data["order_id"] .
                "',agent_id = '" .
                (int) $data["agent_id"] .
                "'"
        );

        $query = $this->db->query(
            "INSERT INTO " .
                DB_PREFIX .
                "return_history SET return_id = '" .
                (int) $return_id .
                "',return_status_id = '" .
                (int) $data["return_status_id"] .
                "',date_added = NOW() "
        );

        return $return_id;
    }

    public function getPosReturnProduct($return_id)
    {
        $query = $this->db->query(
            "SELECT r.return_id, r.order_id, r.product_id, r.quantity, ppro.agent_id FROM `" .
                DB_PREFIX .
                "return` r LEFT JOIN `" .
                DB_PREFIX .
                "pts_pos_return_order` ppro ON (r.return_id = ppro.return_id) WHERE ppro.return_id= '" .
                $return_id .
                "'"
        );
        if ($query->num_rows) {
            return $query->row;
        }
        return null;
    }
    public function getPosReturnProducts($order_id, $agent_id)
    {
        $sql =
            "SELECT r.return_id, r.order_id, r.product_id, r.quantity, ppro.agent_id FROM `" .
            DB_PREFIX .
            "return` r LEFT JOIN `" .
            DB_PREFIX .
            "pts_pos_return_order` ppro ON (r.return_id = ppro.return_id) WHERE ppro.order_id= '" .
            $order_id .
            "'";
        if ($agent_id) {
            $sql .= "AND ppro.agent_id= '" . (int) $agent_id . "'";
        }
        $query = $this->db->query($sql);
        if ($query->num_rows) {
            return $query->rows;
        }
        return null;
    }

    public function getOrderTotal()
    {
        $query = $this->db->query(
            "SELECT * FROM `" . DB_PREFIX . "order_total`"
        );
        if ($query->num_rows) {
            return $query->rows;
        }
        return null;
    }

    public function getAgents()
    {
        $query = $this->db->query(
            "SELECT c.*, CONCAT(c.firstname,' ',c.lastname) AS agent_name FROM `" .
                DB_PREFIX .
                "pts_pos_agent` ppa LEFT JOIN `" .
                DB_PREFIX .
                "customer` c ON(ppa.customer_id=c.customer_id) WHERE c.status=1 AND (agent_status = 1 OR agent_status = 2)"
        );
        if ($query->num_rows) {
            return $query->rows;
        }
        return null;
    }

    public function getBestSeller(int $limit): array
    {
        $product_data = (array) $this->cache->get(
            "product.bestseller." .
                (int) $this->config->get("config_language_id") .
                "." .
                (int) $this->config->get("config_store_id") .
                "." .
                $this->config->get("config_customer_group_id") .
                "." .
                (int) $limit
        );

        if (!$product_data) {
            $query = $this->db->query(
                "SELECT op.`product_id`, SUM(op.`quantity`) AS `total` FROM `" .
                    DB_PREFIX .
                    "order_product` op LEFT JOIN `" .
                    DB_PREFIX .
                    "order` o ON (op.`order_id` = o.`order_id`) LEFT JOIN `" .
                    DB_PREFIX .
                    "product` p ON (op.`product_id` = p.`product_id`) LEFT JOIN `" .
                    DB_PREFIX .
                    "product_to_store` p2s ON (p.`product_id` = p2s.`product_id`) WHERE o.`order_status_id` > '0' AND p.`status` = '1' AND p.`date_available` <= NOW() AND p2s.`store_id` = '" .
                    (int) $this->config->get("config_store_id") .
                    "' GROUP BY op.`product_id` ORDER BY `total` DESC LIMIT " .
                    (int) $limit
            );

            foreach ($query->rows as $result) {
                $product_data[
                    $result["product_id"]
                ] = $this->model_catalog_product->getProduct(
                    $result["product_id"]
                );
            }

            $this->cache->set(
                "product.bestseller." .
                    (int) $this->config->get("config_language_id") .
                    "." .
                    (int) $this->config->get("config_store_id") .
                    "." .
                    $this->config->get("config_customer_group_id") .
                    "." .
                    (int) $limit,
                $product_data
            );
        }

        return $product_data;
    }

    public function isPosAdmin()
    {
        $query = $this->db->query(
            "SELECT * FROM " .
                DB_PREFIX .
                "pts_pos_agent WHERE customer_id = '" .
                (int) $this->session->data["customer_id"] .
                "' AND agent_status = 1"
        );

        if ($query->num_rows) {
            return true;
        }
        return false;
    }

    public function isPosAgent()
    {
        $query = $this->db->query(
            "SELECT * FROM " .
                DB_PREFIX .
                "pts_pos_agent WHERE customer_id = '" .
                (int) $this->session->data["customer_id"] .
                "' AND agent_status = 2"
        );

        if ($query->num_rows) {
            return true;
        }
        return false;
    }

    public function getProductTax(float $amount, int $tax_class_id)
    {
        $tax_rates = [];
        //echo "select ra.* from " . DB_PREFIX . "tax_rule ru LEFT JOIN " . DB_PREFIX . "tax_rate ra ON (ru.tax_rate_id=ra.tax_rate_id) where tax_class_id='". $tax_class_id ."'";
        $query = $this->db->query(
            "select ra.* from " .
                DB_PREFIX .
                "tax_rule ru LEFT JOIN " .
                DB_PREFIX .
                "tax_rate ra ON (ru.tax_rate_id=ra.tax_rate_id) where tax_class_id='" .
                $tax_class_id .
                "'"
        );
        if ($query->num_rows) {
            return $query->rows;
        }
        return null;
    }

    public function getPosProductsByNameOrSku($data)
    {
        $sql =
            "SELECT product_id, name, sku, price, image 
            FROM " .
            DB_PREFIX .
            "product 
            WHERE name LIKE '%" .
            $this->db->escape($data["filter_name"]) .
            "%'
               OR sku LIKE '%" .
            $this->db->escape($data["filter_name"]) .
            "%'
            LIMIT " .
            (int) $data["limit"];

        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getProductByid($product_id)
    {
        $query = $this->db->query(
            "SELECT * FROM " .
                DB_PREFIX .
                "pts_pos_product WHERE product_id='" .
                $product_id .
                "' AND pos_status=1"
        );
        return $query->row;
    }

    public function getPosProductBySku($sku) {

    $term = $this->db->escape($sku);

    $sql = "SELECT p.product_id, p.quantity AS pos_quentity, p.status AS pos_status
            FROM " . DB_PREFIX . "product p
            WHERE p.upc = '" . $term . "'
               OR p.sku = '" . $term . "'
            ORDER BY 
                CASE 
                    WHEN p.upc = '" . $term . "' THEN 1
                    WHEN p.sku = '" . $term . "' THEN 2
                    ELSE 3
                END
            LIMIT 20";

    $query = $this->db->query($sql);
    return $query->rows;
}

    
 public function searchProducts($term) {

    $term = $this->db->escape($term);

    $sql = "SELECT p.product_id,
                   CASE
                     WHEN p.upc = '" . $term . "' THEN 1
                     WHEN p.sku = '" . $term . "' THEN 2
                     WHEN pd.name LIKE '%" . $term . "%' THEN 3
                     ELSE 4
                   END AS match_priority
            FROM " . DB_PREFIX . "product p
            LEFT JOIN " . DB_PREFIX . "product_description pd 
                ON (p.product_id = pd.product_id)
            WHERE 
                p.upc = '" . $term . "'
                OR p.sku = '" . $term . "'
                OR pd.name LIKE '%" . $term . "%'
            GROUP BY p.product_id
            ORDER BY match_priority ASC, p.product_id DESC
            LIMIT 20";

    $query = $this->db->query($sql);
    return $query->rows;
}


}
?>
