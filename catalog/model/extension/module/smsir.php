<?php

/**
 * Modules Catalog model page
 * 
 * PHP version 5.6.x | 7.x
 * 
 * @category  Modules
 * @package   OpenCart 2.3
 * @author    Pejman Kheyri <pejmankheyri@gmail.com>
 * @copyright 2021 All rights reserved.
 */

/**
 * Modules Catalog model Class
 * 
 * @category  Modules
 * @package   OpenCart 2.3
 * @author    Pejman Kheyri <pejmankheyri@gmail.com>
 * @copyright 2021 All rights reserved.
 */
class ModelExtensionModuleSmsir extends Model
{
    /**
     * Get Setting method
     * 
     * @param integer $group    group id
     * @param integer $store_id store id
     * 
     * @return array setting data
     */
    public function getSetting($group, $store_id)
    {
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
     * On Checkout method
     * 
     * @param integer $order_id order id
     * 
     * @return void
     */
    public function SMSIrOnCheckout($order_id)
    {
        //Get order info
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($order_id);
        //Get SMSIr settings
        $this->load->model('setting/setting');
        $SMSIr = $this->model_setting_setting->getSetting('SMSIr', $order_info['store_id']);

        if (strcmp(VERSION, "2.1.0.1") < 0) {
            //load SMSIr library
            $this->load->library('smsir');
        }

        $LineNumber = $SMSIr['SMSIr']['linenumber'];
        $APIKey = $SMSIr['SMSIr']['apiKey'];
        $SecretKey = $SMSIr['SMSIr']['SecretKey'];
        $apidomain = $SMSIr['SMSIr']['apidomain'];
        @$IsCustomerClubNum = $SMSIr['SMSIr']['IsCustomerClubNum'];

        //Send SMS to the customer
        if (isset($SMSIr) && ($SMSIr['SMSIr']['Enabled'] == 'yes') && ($SMSIr['SMSIr']['CustomerPlaceOrder']['Enabled'] == 'yes')) {

            if (!empty($order_info['telephone'])) {
                $phone = $order_info['telephone'];
            } else {
                $phone = '';
            }
            $language = $this->config->get('config_language_id');
            if ($order_info['payment_address_format']) {
                $format = $order_info['payment_address_format'];
            } else {
                $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
            }
            $find = array(
                '{firstname}',
                '{lastname}',
                '{company}',
                '{address_1}',
                '{address_2}',
                '{city}',
                '{postcode}',
                '{zone}',
                '{zone_code}',
                '{country}'
            );
            $replace = array(
                'firstname' => $order_info['payment_firstname'],
                'lastname' => $order_info['payment_lastname'],
                'company' => $order_info['payment_company'],
                'address_1' => $order_info['payment_address_1'],
                'address_2' => $order_info['payment_address_2'],
                'city' => $order_info['payment_city'],
                'postcode' => $order_info['payment_postcode'],
                'zone' => $order_info['payment_zone'],
                'zone_code' => $order_info['payment_zone_code'],
                'country' => $order_info['payment_country']
            );

            $payment_address = str_replace($find, $replace, $format);
            if ($order_info['shipping_address_format']) {
                $format = $order_info['shipping_address_format'];
            } else {
                $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
            }

            $find = array(
                '{firstname}',
                '{lastname}',
                '{company}',
                '{address_1}',
                '{address_2}',
                '{city}',
                '{postcode}',
                '{zone}',
                '{zone_code}',
                '{country}'
            );

            $replace = array(
                'firstname' => $order_info['shipping_firstname'],
                'lastname' => $order_info['shipping_lastname'],
                'company' => $order_info['shipping_company'],
                'address_1' => $order_info['shipping_address_1'],
                'address_2' => $order_info['shipping_address_2'],
                'city' => $order_info['shipping_city'],
                'postcode' => $order_info['shipping_postcode'],
                'zone' => $order_info['shipping_zone'],
                'zone_code' => $order_info['shipping_zone_code'],
                'country' => $order_info['shipping_country']
            );

            $shipping_address = str_replace($find, $replace, $format);

            $original = array(
                "{OrderID}",
                "{SiteName}",
                "{CartTotal}",
                "{ShippingAddress}",
                "{ShippingMethod}",
                "{PaymentAddress}",
                "{PaymentMethod}"
            );

            $replace = array(
                $order_id,
                $this->config->get('config_name'),
                $order_info['total'],
                $shipping_address,
                $order_info['shipping_method'],
                $payment_address,
                $order_info['payment_method']
            );

            $UserMessage[] = str_replace($original, $replace, $SMSIr['SMSIr']['CustomerPlaceOrderText'][$language]);

            $sendCheck[] = $this->sendCheck($phone);

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
                $this->session->data["smsir_lastorder"]['price'] = $order_info['total'];
                $this->session->data["smsir_lastorder"]['time'] = date('m/d/Y h:i:s a', time());
            }
        }

        //Send SMS to the admin
        if (isset($SMSIr) && ($SMSIr['SMSIr']['Enabled'] == 'yes') && ($SMSIr['SMSIr']['AdminPlaceOrder']['Enabled'] == 'yes')) {
            if ($order_info['order_status_id'] != 0) {
                if (!empty($order_info['telephone'])) {
                    $phone = $order_info['telephone'];
                } else {
                    $phone = '';
                }
                $language = $this->config->get('config_language_id');
                $original = array("{OrderID}","{SiteName}","{CartTotal}");
                $replace = array($order_id, $this->config->get('config_name'),$order_info['total']);
                $AdminMessage[] = str_replace($original, $replace, $SMSIr['SMSIr']['AdminPlaceOrderText']);
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
        }
    }

    /**
     * Get Last Order Statuses
     * 
     * @param integer $order_id    order id
     * @param integer $language_id language id
     * 
     * @return integer order statuses
     */
    public function getLastOrderStatuses($order_id, $language_id)
    {
        $order_statuses = array();
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order_id . "' ORDER BY `order_history_id` DESC LIMIT 0, 2");
        foreach ($query->rows as $result) {
            $order_statuses[] = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" .(int)$result['order_status_id']."' AND `language_id` = '" . $language_id . "'")->row;
        }
        return  $order_statuses;
    }

    /**
     * Send Check
     * 
     * @param integer $number number
     * 
     * @return integer filtered number
     */
    public function sendCheck($number = '')
    {
        $this->load->model('setting/setting');
        $SMSIr = $this->model_setting_setting->getSetting('SMSIr', $this->config->get('config_store_id'));
        $number = str_replace(' ', '', $number);
        $number = str_replace('-', '', $number);
        $number = str_replace('(', '', $number);
        $number = str_replace(')', '', $number);
        return $number;
    }
}
?>