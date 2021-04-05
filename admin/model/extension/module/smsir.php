<?php 

/**
 * Model File Of Admin 
 * 
 * PHP version 5.6.x | 7.x
 * 
 * @category  Modules
 * @package   OpenCart 2.3
 * @author    Pejman Kheyri <pejmankheyri@gmail.com>
 * @copyright 2021 All rights reserved.
 */

/**
 * Model File Of Admin Class
 * 
 * @category  Modules
 * @package   OpenCart 2.3
 * @author    Pejman Kheyri <pejmankheyri@gmail.com>
 * @copyright 2021 All rights reserved.
 */
class ModelExtensionModuleSmsir extends Model
{
    private $_module_path = 'module/smsir';

    /**
     * Class Init Method
     * 
     * @return void
     */
    private function _init()
    {
        if (version_compare(VERSION, '2.3.0.0', '>=')) {
            $this->_module_path = 'extension/module/smsir';
        }
    }

    /**
     * Get Setting Method
     * 
     * @param integer $group    group id
     * @param integer $store_id store id
     * 
     * @return array Setting data array
     */
    public function getSetting($group, $store_id = 0)
    {
        $this->_init();
        $data = array();
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `group` = '" . $this->db->escape($group) . "'");
        foreach ($query->rows as $result) {
            if (!$result['serialized']) {
                $data[$result['key']] = $result['value'];
            } else {
                $data[$result['key']] = unserialize($result['value']);
            }
        }
        return $data;
    }

    /**
     * Edit Setting Method
     * 
     * @param integer $group    group id
     * @param array   $data     data
     * @param integer $store_id store id
     * 
     * @return void
     */
    public function editSetting($group, $data, $store_id = 0)
    {
        $this->_init();
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `group` = '" . $this->db->escape($group) . "'");
        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `group` = '" . $this->db->escape($group) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
            } else {
                $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `group` = '" . $this->db->escape($group) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape(serialize($value)) . "', serialized = '1'");
            }
        }
    }

    /**
     * Checking send number
     * 
     * @param string $number number
     * 
     * @return integer filtered number
     */
    public function sendCheck($number = '')
    {
        $this->_init();
        $this->load->model('setting/setting');
        $SMSIr = $this->model_setting_setting->getSetting('SMSIr', $this->config->get('config_store_id'));

        $number = str_replace(' ', '', $number);
        $number = str_replace('-', '', $number);
        $number = str_replace('(', '', $number);
        $number = str_replace(')', '', $number);

        return $number;
    }

    /**
     * Install Method
     * 
     * @return void
     */
    public function install()
    {
        // Install Code
        $this->_init();
        $this->load->model('extension/event');

        if (version_compare(VERSION, '2.2.0.0', ">=")) {
            $this->model_extension_event->addEvent("smsir", 'catalog/controller/checkout/success/before', $this->_module_path . '/onCheckout');
            $this->model_extension_event->addEvent("smsir", 'catalog/model/checkout/order/addOrderHistory/after', $this->_module_path . '/onHistoryChange');
            $this->model_extension_event->addEvent("smsir", 'catalog/model/account/customer/addCustomer/after', $this->_module_path . '/onRegister');
        } else {
            //$this->model_extension_event->addEvent('smsir', 'post.order.add', 'module/smsir/onCheckout');
            $this->model_extension_event->addEvent('smsir', 'post.order.history.add', $this->_module_path . '/onHistoryChange');
            $this->model_extension_event->addEvent('smsir', 'post.customer.add', $this->_module_path . '/onRegister');
        }
    }

    /**
     * Uninstall Method
     * 
     * @return void
     */
    public function uninstall()
    {
        // Uninstall Code
        $this->load->model('setting/setting');
        $this->load->model('setting/store');
        $this->model_setting_setting->deleteSetting('smsir_module', 0);
        $stores = $this->model_setting_store->getStores();
        foreach ($stores as $store) {
            $this->model_setting_setting->deleteSetting('smsir_module', $store['store_id']);
        }
        $this->load->model('extension/event');
        $this->model_extension_event->deleteEvent('smsir');
    }

    /**
     * Get Total Customers Method
     * 
     * @param array $data data
     * 
     * @return integer Indicates total customers count
     */
    public function getTotalCustomers($data = array())
    {
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer";
        $implode = array();
        if (!empty($data['filter_name'])) {
            $implode[] = "CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }
        if (!empty($data['filter_email'])) {
            $implode[] = "email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
        }
        if (isset($data['filter_newsletter']) && !is_null($data['filter_newsletter'])) {
            $implode[] = "newsletter = '" . (int)$data['filter_newsletter'] . "'";
        }
        if (!empty($data['filter_customer_group_id'])) {
            $implode[] = "customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
        }
        if (!empty($data['filter_ip'])) {
            $implode[] = "customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
        }
        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $implode[] = "status = '" . (int)$data['filter_status'] . "'";
        }
        if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
            $implode[] = "approved = '" . (int)$data['filter_approved'] . "'";
        }
        if (!empty($data['filter_date_added'])) {
            $implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }
        if ($implode) {
            $sql .= " WHERE " . implode(" AND ", $implode);
        }
        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    /**
     * Get Customers
     * 
     * @param array $data data
     * 
     * @return array customers detail
     */
    public function getCustomers($data = array())
    {
        $sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name, cgd.name AS customer_group FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
        $implode = array();
        if (!empty($data['filter_name'])) {
            $implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }
        if (!empty($data['filter_email'])) {
            $implode[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
        }
        if (isset($data['filter_newsletter']) && !is_null($data['filter_newsletter'])) {
            $implode[] = "c.newsletter = '" . (int)$data['filter_newsletter'] . "'";
        }
        if (!empty($data['filter_customer_group_id'])) {
            $implode[] = "c.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
        }
        if (!empty($data['filter_ip'])) {
            $implode[] = "c.customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
        }
        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $implode[] = "c.status = '" . (int)$data['filter_status'] . "'";
        }
        if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
            $implode[] = "c.approved = '" . (int)$data['filter_approved'] . "'";
        }
        if (!empty($data['filter_date_added'])) {
            $implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }
        if ($implode) {
            $sql .= " AND " . implode(" AND ", $implode);
        }
        $sort_data = array(
            'name',
            'c.email',
            'customer_group',
            'c.status',
            'c.approved',
            'c.ip',
            'c.date_added'
        );
        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY name";
        }
        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }
        $query = $this->db->query($sql);
        return $query->rows;
    }

    /**
     * Get Customer Detail
     * 
     * @param integer $customer_id customer id
     * 
     * @return array customer detail
     */
    public function getCustomer($customer_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
        return $query->row;
    }

    /**
     * Get Total Affiliates Amount
     * 
     * @param array $data data
     * 
     * @return integer Affiliate count
     */
    public function getTotalAffiliates($data = array())
    {
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "affiliate";
        $implode = array();
        if (!empty($data['filter_name'])) {
            $implode[] = "CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }
        if (!empty($data['filter_email'])) {
            $implode[] = "LCASE(email) = '" . $this->db->escape(utf8_strtolower($data['filter_email'])) . "'";
        }
        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $implode[] = "status = '" . (int)$data['filter_status'] . "'";
        }
        if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
            $implode[] = "approved = '" . (int)$data['filter_approved'] . "'";
        }
        if (!empty($data['filter_date_added'])) {
            $implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }
        if ($implode) {
            $sql .= " WHERE " . implode(" AND ", $implode);
        }
        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    /**
     * Get Affiliates
     * 
     * @param array $data data
     * 
     * @return array Affiliates detail
     */
    public function getAffiliates($data = array())
    {
        $sql = "SELECT *, CONCAT(a.firstname, ' ', a.lastname) AS name, (SELECT SUM(at.amount) FROM " . DB_PREFIX . "affiliate_transaction at WHERE at.affiliate_id = a.affiliate_id GROUP BY at.affiliate_id) AS balance FROM " . DB_PREFIX . "affiliate a";
        $implode = array();
        if (!empty($data['filter_name'])) {
            $implode[] = "CONCAT(a.firstname, ' ', a.lastname) LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }
        if (!empty($data['filter_email'])) {
            $implode[] = "LCASE(a.email) = '" . $this->db->escape(utf8_strtolower($data['filter_email'])) . "'";
        }
        if (!empty($data['filter_code'])) {
            $implode[] = "a.code = '" . $this->db->escape($data['filter_code']) . "'";
        }
        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $implode[] = "a.status = '" . (int)$data['filter_status'] . "'";
        }
        if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
            $implode[] = "a.approved = '" . (int)$data['filter_approved'] . "'";
        }
        if (!empty($data['filter_date_added'])) {
            $implode[] = "DATE(a.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }
        if ($implode) {
            $sql .= " WHERE " . implode(" AND ", $implode);
        }
        $sort_data = array(
            'name',
            'a.email',
            'a.code',
            'a.status',
            'a.approved',
            'a.date_added'
        );
        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY name";
        }
        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }
        $query = $this->db->query($sql);
        return $query->rows;
    }

    /**
     * Get Affiliate
     * 
     * @param array $affiliate_id affiliate_id
     * 
     * @return array Affiliate detail
     */
    public function getAffiliate($affiliate_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "affiliate WHERE affiliate_id = '" . (int)$affiliate_id . "'");
        return $query->row;
    }

    /**
     * Get Total Telephones By Products Ordered
     * 
     * @param array $products products list
     * 
     * @return integer phones count
     */
    public function getTotalTelephonesByProductsOrdered($products)
    {
        $implode = array();
        foreach ($products as $product_id) {
            $implode[] = "op.product_id = '" . (int)$product_id . "'";
        }

        $query = $this->db->query("SELECT DISTINCT telephone FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) WHERE (" . implode(" OR ", $implode) . ") AND o.order_status_id <> '0'");

        if (is_object($query)) {
            $resultVars = get_object_vars($query);
            $count = $resultVars['num_rows'];
        }

        return $count;
    }

    /**
     * Get Telephones By Products Ordered
     * 
     * @param array   $products products list
     * @param integer $start    start
     * @param integer $end      end
     * 
     * @return integer phones detail
     */
    public function getTelephonesByProductsOrdered($products, $start, $end)
    {
        $implode = array();
        foreach ($products as $product_id) {
            $implode[] = "op.product_id = '" . (int)$product_id . "'";
        }
        $query = $this->db->query("SELECT DISTINCT telephone FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) WHERE (" . implode(" OR ", $implode) . ") AND o.order_status_id <> '0' LIMIT " . (int)$start . "," . (int)$end);
        return $query->rows;
    }
}
 