<?php

/**
 * Modules Catalog controller page
 * 
 * PHP version 5.6.x | 7.x
 * 
 * @category  Modules
 * @package   OpenCart 2.3
 * @author    Pejman Kheyri <pejmankheyri@gmail.com>
 * @copyright 2021 All rights reserved.
 */

/**
 * Modules Catalog controller Class
 * 
 * @category  Modules
 * @package   OpenCart 2.3
 * @author    Pejman Kheyri <pejmankheyri@gmail.com>
 * @copyright 2021 All rights reserved.
 */
class ControllerExtensionModuleSmsir extends Controller
{
    private $_call_model = 'model_module_smsir';
    private $_module_path = 'module/smsir';
    private $_model_class = 'ModelModuleSmsir';
    private $_smsir_model;

    /**
     * Class Construct method
     * 
     * @param string $registry registry
     * 
     * @return void
     */
    public function __construct($registry)
    {
        parent::__construct($registry);
        //cross version check and module specific declarations
        if (version_compare(VERSION, '2.3.0.0', '>=')) {
            $this->_call_model = 'model_extension_module_smsir';
            $this->_module_path = 'extension/module/smsir';
            $this->_model_class = 'ModelExtensionModuleSmsir';
        }
        //SMSIr model
        $this->load->model($this->_module_path);
        //Settings model
        $this->load->model('setting/setting');

        $this->_smsir_model = $this->{$this->_call_model};
    }

    /**
     * On Checkout method
     * 
     * @param array $data data
     * 
     * @return void
     */
    public function onCheckout($data = 0)
    {
        if (isset($this->session->data['order_id'])) {
            $order_id = $this->session->data['order_id'];
        } else {
            $order_id = 0;
        }
        if ($order_id != 0 && version_compare(VERSION, '2.3.0.1', '>')) {
            $this->_smsir_model->SMSIrOnCheckout($order_id);
        }
    }

    /**
     * On History Change method
     * 
     * @param integer $order_id order id
     * @param array   $route    route
     * @param array   $data     data
     * 
     * @return void
     */
    public function onHistoryChange($order_id, $route = '', $data = '')
    {
        if (version_compare(VERSION, '2.2.0.0', ">=") && version_compare(VERSION, '2.3.0.0', "<")) {
            $order_id = $data;
        } else if (version_compare(VERSION, '2.3.0.0', ">=")) {
            $order_id = $route[0];
        }

        //Send SMS when the status order is changed
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($order_id);
        $SMSIr = $this->model_setting_setting->getSetting('SMSIr', $order_info['store_id']);

        if (strcmp(VERSION, "2.1.0.1") < 0) {
            $this->load->library('smsir');
        }

        $LineNumber = $SMSIr['SMSIr']['linenumber'];
        $APIKey = $SMSIr['SMSIr']['apiKey'];
        $SecretKey = $SMSIr['SMSIr']['SecretKey'];
        $apidomain = $SMSIr['SMSIr']['apidomain'];
        @$IsCustomerClubNum = $SMSIr['SMSIr']['IsCustomerClubNum'];

        if (isset($SMSIr) && ($SMSIr['SMSIr']['Enabled'] == 'yes') && ($SMSIr['SMSIr']['OrderStatusChange']['Enabled'] == 'yes')) {
            $result = $this->db->query("SELECT count(*) as counter FROM " . DB_PREFIX ."order_history WHERE order_id = ". $order_id);
            if ($order_info['order_status_id'] && $result->row['counter'] > 1 && (!empty($SMSIr['SMSIr']['OrderStatusChange']['OrderStatus']) && (in_array($order_info['order_status_id'], $SMSIr['SMSIr']['OrderStatusChange']['OrderStatus'])))) {
                if (isset($order_info['order_status']))
                    $Status = $order_info['order_status'];
                else
                    $Status = "";

                $language = $order_info['language_id'];
                $last_order_status = $this->_smsir_model->getLastOrderStatuses($order_id, $language);
                $Status1 = !empty($last_order_status[1]['name']) ? $last_order_status[1]['name'] : '';
                $Status2 = $Status;
                $original = array("{SiteName}","{OrderID}","{Status}","{Status1}","{Status2}","{StatusFrom}","{StatusTo}");
                $replace = array($this->config->get('config_name'), $order_id, $Status,$Status1,$Status2,$Status1, $Status2);
                $Message[] = str_replace($original, $replace, $SMSIr['SMSIr']['OrderStatusChangeText'][$language]);
                $phone = $order_info['telephone'];
                $sendCheck[] = $this->_smsir_model->sendCheck($phone);
                $Mobiles = array();
                $Mobile = array();

                foreach ($sendCheck as $keys => $values) {
                    if ((SmsIr::isMobile($values)) || (SmsIr::isMobileWithouthZero($values))) {
                        $Mobile[] = doubleval($values);
                    }
                }
                $Mobiles = array_unique($Mobile);

                if ($Mobiles && $Message) {
                    if ((!empty($IsCustomerClubNum)) && ($IsCustomerClubNum == 'on')) {
                        $SendSingle = SmsIr::sendSingleCustomerClub($apidomain, $APIKey, $SecretKey, $Mobiles, $Message);
                    } else {
                        $SendSingle = SmsIr::sendSingle($apidomain, $APIKey, $SecretKey, $LineNumber, $Mobiles, $Message);
                    }
                }
            }
        }
    }

    /**
     * On Register method
     * 
     * @return void
     */
    public function onRegister()
    {
        if (func_num_args() > 1) {
            $temp_id = !is_array(func_get_arg(1)) ? func_get_arg(1) : func_get_arg(2);
        } else {
            $temp_id = func_get_arg(0);
        }
        $customer_id = $temp_id;
        $this->load->model('setting/setting');
        $SMSIr = $this->model_setting_setting->getSetting('SMSIr', $this->config->get('store_id'));
        if (strcmp(VERSION, "2.1.0.1") < 0) {
            $this->load->library('smsir');
        }

        $LineNumber = $SMSIr['SMSIr']['linenumber'];
        $APIKey = $SMSIr['SMSIr']['apiKey'];
        $SecretKey = $SMSIr['SMSIr']['SecretKey'];
        $apidomain = $SMSIr['SMSIr']['apidomain'];
        @$IsCustomerClubNum = $SMSIr['SMSIr']['IsCustomerClubNum'];

        //Send SMS to the admin when new user is registered
        if (isset($SMSIr) && ($SMSIr['SMSIr']['Enabled'] == 'yes') && ($SMSIr['SMSIr']['AdminRegister']['Enabled'] == 'yes')) {
            $customer = $this->db->query("SELECT firstname,lastname,telephone FROM `" . DB_PREFIX ."customer` WHERE customer_id = ".(int)$customer_id);

            if ($customer->row) {
                $nameCustomer = $customer->row['firstname']." ".$customer->row['lastname'];
            } else {
                $nameCustomer = '';
            }

            $original = array("{SiteName}","{CustomerName}");
            $replace = array($this->config->get('config_name'), $nameCustomer);
            $AdminMessage[] = str_replace($original, $replace, $SMSIr['SMSIr']['AdminRegisterText']);
            $adminNumbers = isset($SMSIr['SMSIr']['StoreOwnerPhoneNumber']) ? $SMSIr['SMSIr']['StoreOwnerPhoneNumber'] : array();
            $AdminMobiles = array();
            $AdminMobile = array();

            foreach ($adminNumbers as $key => $value) {
                if ((SmsIr::isMobile($value)) || (SmsIr::isMobileWithouthZero($value))) {
                    $AdminMobile[] = doubleval($value);
                }
            }
            $AdminMobiles = array_unique($AdminMobile);

            if ($AdminMobiles && $AdminMessage) {
                if ((!empty($IsCustomerClubNum)) && ($IsCustomerClubNum == 'on')) {
                    $AdminSendSingle = SmsIr::sendSingleCustomerClub($apidomain, $APIKey, $SecretKey, $AdminMobiles, $AdminMessage);
                } else {
                    $AdminSendSingle = SmsIr::sendSingle($apidomain, $APIKey, $SecretKey, $LineNumber, $AdminMobiles, $AdminMessage);
                }
            }
        }

        //Send SMS to the user when the registration is successful
        if (isset($SMSIr) && ($SMSIr['SMSIr']['Enabled'] == 'yes') && ($SMSIr['SMSIr']['CustomerRegister']['Enabled'] == 'yes')) {
            $customer = $this->db->query("SELECT firstname,lastname,telephone FROM `" . DB_PREFIX ."customer` WHERE customer_id = ".(int)$customer_id);

            if ($customer->row) {
                $phone = $customer->row['telephone'];
                $nameCustomer = $customer->row['firstname']." ".$customer->row['lastname'];
            } else {
                $phone = '';
                $nameCustomer = '';
            }

            $language = $this->config->get('config_language_id');
            $original = array("{SiteName}","{CustomerName}");
            $replace = array($this->config->get('config_name'), $nameCustomer);
            $UserMessage[] = str_replace($original, $replace, $SMSIr['SMSIr']['CustomerRegisterText'][$language]);
            $sendCheck[] = $this->_smsir_model->sendCheck($phone);
            $UserMobiles = array();
            $UserMobile = array();

            foreach ($sendCheck as $keys => $values) {
                if ((SmsIr::isMobile($values)) || (SmsIr::isMobileWithouthZero($values))) {
                    $UserMobile[] = doubleval($values);
                }
            }
            $UserMobiles = array_unique($UserMobile);

            if ($UserMobiles && $UserMessage) {
                if ((!empty($IsCustomerClubNum)) && ($IsCustomerClubNum == 'on')) {
                    $UserSendSingle = SmsIr::sendSingleCustomerClub($apidomain, $APIKey, $SecretKey, $UserMobiles, $UserMessage);
                } else {
                    $UserSendSingle = SmsIr::sendSingle($apidomain, $APIKey, $SecretKey, $LineNumber, $UserMobiles, $UserMessage);
                }
            }
        }
    }

    /**
     * Log method
     * 
     * @param string $text text
     * 
     * @return void
     */
    private function _log($text)
    {
        $log = new Log("smsir_error_log.txt");
        $log->write($text);
    }
}