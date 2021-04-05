<?php

/**
 * Controller File Of Admin 
 * 
 * PHP version 5.6.x | 7.x
 * 
 * @category  Modules
 * @package   OpenCart 2.3
 * @author    Pejman Kheyri <pejmankheyri@gmail.com>
 * @copyright 2021 All rights reserved.
 */

/**
 * Controller File Of Admin Class
 * 
 * @category  Modules
 * @package   OpenCart 2.3
 * @author    Pejman Kheyri <pejmankheyri@gmail.com>
 * @copyright 2021 All rights reserved.
 */
class ControllerExtensionModuleSmsir extends Controller
{
    private $data = array();
    private $_version = '1.0.0';
    private $_call_model = 'model_extension_module_smsir';
    private $_module_path = 'module/smsir';
    private $_smsir_model;

    /**
     * SMSIr Controller Constructor
     * 
     * @param string $registry registry string
     * 
     * @return void Initialize necessary dependencies from the OpenCart framework.
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
        $this->load->model($this->_module_path);
        $this->_smsir_model = $this->{$this->_call_model};
        $this->load->language($this->_module_path);
        //Loading framework models
        $this->load->model('setting/store');
        $this->load->model('localisation/language');
        $this->load->model('design/layout');
        $this->load->model('tool/image');
        $this->load->model('setting/setting');
        //Module specific resources
        $this->document->addStyle('view/stylesheet/smsir/smsir.css');
        $this->document->addStyle('view/stylesheet/smsir/select2.css');
        $this->document->addScript('view/javascript/smsir/smsir.js');
        $this->document->addScript('view/javascript/smsir/select2.min.js');
        $this->document->addScript('view/javascript/smsir/charactercounter.js');
        //global module variables
        $this->data['_module_path'] = $this->_module_path;
        $this->data['catalogURL'] = $this->_getCatalogURL();
    }

    /**
     * SMSIR Controller index
     * 
     * @return void 
     */
    public function index()
    { 
        if (!isset($this->request->get['store_id'])) {
            $this->request->get['store_id'] = 0; 
        }
        $this->document->setTitle($this->language->get('heading_title'));
        $store = $this->_getCurrentStore($this->request->get['store_id']);

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            if (!$this->user->hasPermission('modify', $this->_module_path)) {
                $this->error['warning'] = $this->language->get('error_permission');
            }
            if (!empty($_POST['OaXRyb1BhY2sgLSBDb21'])) {
                $this->request->post['SMSIr']['LicensedOn'] = $_POST['OaXRyb1BhY2sgLSBDb21'];
            }
            if (!empty($_POST['cHRpbWl6YXRpb24ef4fe'])) {
                $this->request->post['SMSIr']['License'] = json_decode(base64_decode($_POST['cHRpbWl6YXRpb24ef4fe']), true);
            }
            if (!$this->user->hasPermission('modify', $this->_module_path)) {
                $this->session->data['error'] = 'You do not have permissions to edit this module!';
            } else {
                $this->model_setting_setting->editSetting('SMSIr', $this->request->post, $this->request->post['store_id']);
                $this->session->data['success'] = $this->language->get('text_success');
            }
            $this->response->redirect($this->url->link($this->_module_path, 'token=' . $this->session->data['token'].'&store_id='.$store['store_id'], 'SSL'));
        }

        $this->data['image'] = 'no_image.jpg';
        $this->data['thumb'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
        $this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        $this->load->model('localisation/order_status');
        $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $this->data['breadcrumbs']   = array();
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
        );
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'].'&type=module', 'SSL'),
        );
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link($this->_module_path, 'token=' . $this->session->data['token'].'$type=module', 'SSL'),
        );

        $languageVariables = array(
            'entry_code',
            'smsir_panel_credit',
            'smsir_panel_credit_desc',
            'smsir_sms',
            'smsir_ApiDomain',
            'smsir_ApiKey',
            'smsir_SecretKey',
            'smsir_Keys_link',
            'smsir_Keys_link_desc',
            'smsir_SecretKey_link_desc',
            'smsir_linenumber',
            'smsir_linenumber_desc',
            'smsir_linenumber_link',
            'smsir_send_to',
            'smsir_choose_users_for_send_sms',
            'smsir_all_customers',
            'smsir_specific_customers',
            'smsir_specific_mobiles',
            'smsir_customers_ordered_specific_products',
            'smsir_customer_groups',
            'smsir_newsletter_users',
            'smsir_all_affiliates',
            'smsir_specific_affiliates',
            'smsir_number',
            'smsir_add',
            'smsir_customer',
            'smsir_auto_complete',
            'smsir_affiliate',
            'smsir_products',
            'smsir_products_desc',
            'smsir_message',
            'smsir_message_desc',
            'smsir_send_message',
            'smsir_sending_messages',
            'smsir_dont_close_windows',
            'smsir_last_sent_to',
            'smsir_sent_messages',
            'smsir_errors',
            'smsir_close',
            'smsir_written_chars',
            'smsir_error_all_fiels_requied',
            'smsir_message_sent_successfuly',
            'smsir_message_sent_with_some_errors',
            'smsir_general',
            'smsir_transactional_sms',
            'smsir_settings',
            'smsir_confirm',
            'smsir_send_sms_to_customers',
            'smsir_send_sms_to_admins',
            'smsir_on_new_order',
            'smsir_on_order_status_change',
            'smsir_on_new_registration',
            'smsir_status',
            'smsir_short_codes',
            'smsir_short_codes_desc',
            'smsir_store_name',
            'smsir_order_id',
            'smsir_order_total',
            'smsir_shipping_address',
            'smsir_shipping_method',
            'smsir_payment_address',
            'smsir_payment_method',
            'smsir_customer_new_order_default_text',
            'smsir_order_status',
            'smsir_select_all',
            'smsir_deselect_all',
            'smsir_which_order_status_send_sms',
            'smsir_status_changed_from',
            'smsir_status_changed_to',
            'smsir_order_status_change_default_text',
            'smsir_customer_name',
            'smsir_new_registration_default_text',
            'smsir_new_order_admin_default_text',
            'smsir_new_registration_admin_default_text',
            'smsir_admins_mobiles',
            'smsir_admins_mobiles_desc',
            'smsir_ifcustomerclubdesc',
            'smsir_all_customer_club',
            'smsir_version',
            'heading_title',
            'error_input_form',
            'entry_yes',
            'entry_no',
            'text_default',
            'text_enabled',
            'text_disabled',
            'text_text',
            'save_changes',
            'button_cancel',
            'text_settings',
            'button_add',
            'button_edit',            
            'button_remove',
            'text_special_duration',
            'smsir_no_number_to_send'
          );
       
        foreach ($languageVariables as $languageVariable) {
            $this->data[$languageVariable] = $this->language->get($languageVariable);
        }
        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['stores'] = array_merge(array(0 => array('store_id' => '0', 'name' => $this->config->get('config_name') . ' ' . $this->data['text_default'], 'url' => HTTP_SERVER, 'ssl' => HTTPS_SERVER)), $this->model_setting_store->getStores());
        $this->data['error_warning'] = '';  
        $this->data['languages'] = $this->model_localisation_language->getLanguages();
        foreach ($this->data['languages'] as $key => $value) {
            if (version_compare(VERSION, '2.2.0.0', "<")) {
                $this->data['languages'][$key]['flag_url'] = 'view/image/flags/'.$this->data['languages'][$key]['image'];
            } else {
                $this->data['languages'][$key]['flag_url'] = 'language/'.$this->data['languages'][$key]['code'].'/'.$this->data['languages'][$key]['code'].'.png"';
            }
        }
        $this->data['_version'] = $this->_version;
        $this->data['store'] = $store;
        $this->data['token'] = $this->session->data['token'];
        $this->data['action'] = $this->url->link('extension/module/smsir', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['saveApiKey'] = $this->url->link($this->_module_path.'/saveApiKey', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['validatePhoneNumberUrl'] = $this->url->link($this->_module_path.'/validatePhone', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'].'&type=module', 'SSL');
        $this->data['data'] = $this->model_setting_setting->getSetting('SMSIr', $store['store_id']);

        $this->document->addStyle('view/javascript/smsir/jquery/css/ui-lightness/jquery-ui.min.css');
        $this->document->addScript('view/javascript/smsir/jquery/js/jquery-ui.min.js');
        $this->data['status'] = true;

        @$apiKey = $this->data['data']['SMSIr']['apiKey'];
        @$this->data['linenumber'] = $this->data['data']['SMSIr']['linenumber'];
        $this->data['_getcredit'] = $this->_getCredit();

        // SMS Bulk Start
        if (strcmp(VERSION, "2.1.0.1") < 0) {
            $this->load->model('sale/customer_group');
            $this->data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups(0);
            $this->data['customer_autocomplete_url'] = $this->url->link('sale/customer/autocomplete', '', 'SSL');
        } else {
            $this->load->model('customer/customer_group');
            $this->data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups(0);
            $this->data['customer_autocomplete_url'] = $this->url->link('customer/customer/autocomplete', '', 'SSL');
        }
        // SMS Bulk End

        if (!empty($_SERVER['HTTP_REFERER'])) {
            $referer = $_SERVER['HTTP_REFERER'];
            $url = parse_url($referer);
        }

        $this->data['header'] = $this->load->controller('common/header');
        $this->data['column_left'] = $this->load->controller('common/column_left');
        $this->data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view($this->_module_path.'.tpl', $this->data));
    }

    /**
     * Saving Api Key
     * 
     * @return void 
     */
    public function saveApiKey()
    {
        header("Content-Type: application/json", true);
        if (isset($this->request->get['store_id']) && !empty($this->request->get['api_key'])) {
            $data = array(
                'store_id' => $this->request->get['store_id'],
                'SMSIr' => array (
                    'apiKey' => $this->request->get['apiKey'],
                    'linenumber' => $this->request->get['linenumber'],
                    'SecretKey' => $this->request->get['SecretKey'],
                    'apidomain' => $this->request->get['apidomain'],
                    'IsCustomerClubNum' => $this->request->get['IsCustomerClubNum']
                )
            );
            $this->model_setting_setting->editSetting('SMSIr', $data, $data['store_id']);
            $result = array(
                'status' => 'success',
                'redirect_url' => $this->url->link($this->_module_path, 'token=' . $this->session->data['token'].'&store_id='.$data['store_id'], 'SSL')
            );
            $this->response->setOutput(json_encode($result));
        } else {
            $result = array(
                'status' => 'error'
            );
            $this->response->setOutput(json_encode($result));
        }
    }

    /**
     * Get Credit Amount
     * 
     * @return float Credit Amount 
     */
    private function _getCredit()
    {
        $this->load->library('smsir');
        $this->load->model('setting/setting');
        $SMSIr = $this->model_setting_setting->getSetting('SMSIr', $this->config->get('store_id'));

        @$APIKey = $SMSIr['SMSIr']['apiKey'];
        @$SecretKey = $SMSIr['SMSIr']['SecretKey'];
        @$apidomain = $SMSIr['SMSIr']['apidomain'];

        $Credit = SmsIr::getCredit($apidomain, $APIKey, $SecretKey);
        return $Credit;
    }

    /**
     * Get Catalog URL
     * 
     * @return string Indicates The Store URL
     */
    private function _getCatalogURL()
    {
        if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
            $storeURL = HTTPS_CATALOG;
        } else {
            $storeURL = HTTP_CATALOG;
        } 
        return $storeURL;
    }

    /**
     * Get Server URL
     * 
     * @return string Indicates The Store URL
     */
    private function _getServerURL()
    {
        if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
            $storeURL = HTTPS_SERVER;
        } else {
            $storeURL = HTTP_SERVER;
        } 
        return $storeURL;
    }

    /**
     * Get Current Store
     * 
     * @param integer $store_id store id
     * 
     * @return array Indicates The Store Details
     */
    private function _getCurrentStore($store_id)
    {    
        if ($store_id && $store_id != 0) {
            $store = $this->model_setting_store->getStore($store_id);
        } else {
            $store['store_id'] = 0;
            $store['name'] = $this->config->get('config_name');
            $store['url'] = $this->_getCatalogURL(); 
        }
        return $store;
    }
    
    /**
     * Install Method
     * 
     * @return void
     */
    public function install()
    {
        $this->_smsir_model->install();
    }

    /**
     * Uninstall Method
     * 
     * @return void
     */
    public function uninstall()
    {
        $this->_smsir_model->uninstall();
    }

    /**
     * Send SMS Method
     * 
     * @return void
     */
    public function send()
    {
        $json = array();
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if (!$this->user->hasPermission('modify', $this->_module_path)) {
                $json['error']['warning'] = $this->language->get('smsir_permission_deniy_action');
            }
            if (!$this->request->post['message']) {
                $json['error']['message'] = $this->language->get('smsir_fill_message_field');
            }
            if (!$json) {
                $store_info = $this->model_setting_store->getStore($this->request->post['store_id']);
                if ($store_info) {
                    $store_name = $store_info['name'];
                } else {
                    $store_name = $this->config->get('config_name');
                }
                if (isset($this->request->get['page'])) {
                    $page = $this->request->get['page'];
                } else {
                    $page = 1;
                }

                $telephones_total = 0;
                $json['telephones'] = array();
                $telephones = array();
                $json_telephones = array();
                $AllCustomerClub = '';

                switch ($this->request->post['to']) {
                case 'telephones':
                    $phones = isset($this->request->post['phones']) ? $this->request->post['phones'] : array();
                    foreach ($phones as $result) {
                        $telephones[] = $result;
                    }
                    break;
                case 'newsletter':
                    $customer_data = array(
                        'filter_newsletter' => 1,
                        'start' => ($page - 1) * 10
                    );
                    $telephones_total = $this->_smsir_model->getTotalCustomers($customer_data);
                    $results = $this->_smsir_model->getCustomers($customer_data);
                    foreach ($results as $result) {
                        $validPhone = $this->_smsir_model->sendCheck($result['telephone']);
                        if ($validPhone) {
                            $telephones[] = $validPhone;
                        }
                    }
                    break;
                case 'customer_all':
                    $customer_data = array(
                        'start'  => ($page - 1) * 10
                    );
                    $telephones_total = $this->_smsir_model->getTotalCustomers($customer_data);
                    $results = $this->_smsir_model->getCustomers($customer_data);
                    foreach ($results as $result) {
                        $validPhone = $this->_smsir_model->sendCheck($result['telephone']);
                        if ($validPhone) {
                            $telephones[] = $validPhone;
                        }
                    }
                    break;
                case 'customer_group':
                    $customer_data = array(
                        'filter_customer_group_id' => $this->request->post['customer_group_id'],
                        'start' => ($page - 1) * 10
                    );
                    $telephones_total = $this->_smsir_model->getTotalCustomers($customer_data);
                    $results = $this->_smsir_model->getCustomers($customer_data);
                    foreach ($results as $result) {
                        $validPhone = $this->_smsir_model->sendCheck($result['telephone']);
                        if ($validPhone) {
                            $telephones[] = $validPhone;
                        }
                    }
                    break;
                case 'customer':
                    if (!empty($this->request->post['customer'])) {
                        foreach ($this->request->post['customer'] as $customer_id) {
                            $customer_info = $this->_smsir_model->getCustomer($customer_id);
                            if ($customer_info) {
                                $validPhone = $this->_smsir_model->sendCheck($customer_info['telephone']);
                                if ($validPhone) {
                                    $telephones[] = $validPhone;
                                }
                            }
                        }
                    }
                    break;
                case 'affiliate_all':
                    $affiliate_data = array(
                        'start'  => ($page - 1) * 10
                    );
                    $telephones_total = $this->_smsir_model->getTotalAffiliates($affiliate_data);
                    $results = $this->_smsir_model->getAffiliates($affiliate_data);
                    foreach ($results as $result) {
                        $validPhone = $this->_smsir_model->sendCheck($result['telephone']);
                        if ($validPhone) {
                            $telephones[] = $validPhone;
                        }
                    }
                    break;
                case 'affiliate':
                    if (!empty($this->request->post['affiliate'])) {
                        foreach ($this->request->post['affiliate'] as $affiliate_id) {
                            $affiliate_info = $this->_smsir_model->getAffiliate($affiliate_id);
                            if ($affiliate_info) {
                                $validPhone = $this->_smsir_model->sendCheck($affiliate_info['telephone']);
                                if ($validPhone) {
                                    $telephones[] = $validPhone;
                                }
                            }
                        }
                    }
                    break;
                case 'product':
                    if (isset($this->request->post['product'])) {
                        $telephones_total = $this->_smsir_model->getTotalTelephonesByProductsOrdered($this->request->post['product']);
                        $results = $this->_smsir_model->getTelephonesByProductsOrdered($this->request->post['product'], ($page - 1) * 10, 10);
                        foreach ($results as $result) {
                            $validPhone = $this->_smsir_model->sendCheck($result['telephone']);
                            if ($validPhone) {
                                $telephones[] = $validPhone;
                            }
                        }
                    }
                    break;
                case 'AllCustomerClub':
                    $AllCustomerClub = 'ON';
                    break;
                }
                $this->load->library('smsir');
                foreach ($telephones as $key=>$value) {
                    if ((SmsIr::isMobile($value)) || (SmsIr::isMobileWithouthZero($value))) {
                        $json_telephones[] = doubleval($value);
                    }
                }

                $json['telephones'] = array_unique($json_telephones);
                $json['telephonesTotal'] = $telephones_total;

                if (($json['telephones']) || ($AllCustomerClub == 'ON')) {
                    $json['success'] = $this->language->get('text_success');
                    if ($AllCustomerClub == 'ON') {
                        $json['AllCustomerClub'] = 'ON';
                    }
                } else {
                    $json['error']['message'] = $this->language->get('smsir_no_number_to_send');
                }
            }
        }
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Send Request Method
     * 
     * @return void
     */
    public function sendRequest()
    {
        $this->load->library('smsir');
        $this->load->model('setting/setting');
        $SMSIr = $this->model_setting_setting->getSetting('SMSIr', $this->config->get('store_id'));

        $Mobiles = array();
        $Mobile = array();

        $Messages[] = $_POST['Messages'];
        $type = $_POST['type'];

        $LineNumber = $SMSIr['SMSIr']['linenumber'];
        $APIKey = $SMSIr['SMSIr']['apiKey'];
        $SecretKey = $SMSIr['SMSIr']['SecretKey'];
        $apidomain = $SMSIr['SMSIr']['apidomain'];

        if ($type == "AllCustomerClub") {
            $sendToAllCustomerClub = SmsIr::sendToAllCustomerClub($apidomain, $APIKey, $SecretKey, $Messages);

            $result = json_decode($sendToAllCustomerClub);
            if ($result) {
                if (is_object($result)) {
                    $resultVars = get_object_vars($result);
                    if (is_array($resultVars)) {
                        @$result_IsSuccessful = $resultVars['IsSuccessful'];
                        @$result_Message = $resultVars['Message'];
                    }
                }

                if ($result_IsSuccessful) {
                    if ($result_IsSuccessful == true) {
                        $json['success'] = $result_Message;
                    } else {
                        $json['error'] = $result_Message;
                    }
                } else {
                    $json['error'] = $result_Message;
                }
            } else {
                $json['error'] = $this->language->get('smsir_send_request_error');
            }
        } else {
            $MobileNumbers = $_POST['MobileNumbers'];
            @$IsCustomerClubNum = $SMSIr['SMSIr']['IsCustomerClubNum'];

            if ($MobileNumbers) {
                foreach ($MobileNumbers as $key=>$value) {
                    if ((SmsIr::isMobile($value)) || (SmsIr::isMobileWithouthZero($value))) {
                        $Mobile[] = doubleval($value);
                    }
                }
                $Mobiles = array_unique($Mobile);

                if ((!empty($IsCustomerClubNum)) && ($IsCustomerClubNum == 'on')) {
                    $sendSingle = SmsIr::sendSingleCustomerClub($apidomain, $APIKey, $SecretKey, $Mobiles, $Messages);
                } else {
                    $sendSingle = SmsIr::sendSingle($apidomain, $APIKey, $SecretKey, $LineNumber, $Mobiles, $Messages);
                }

                $result = json_decode($sendSingle);
                if ($result) {
                    if (is_object($result)) {
                        $resultVars = get_object_vars($result);
                        if (is_array($resultVars)) {
                            @$result_IsSuccessful = $resultVars['IsSuccessful'];
                            @$result_Message = $resultVars['Message'];
                        }
                    }
                    if ($result_IsSuccessful) {
                        if ($result_IsSuccessful == true) {
                            $json['to'] = $Mobiles;
                            $json['success'] = $result_Message;
                        } else {
                            $json['error'] = $result_Message;
                        }
                    } else {
                        $json['error'] = $result_Message;
                    }
                } else {
                    $json['error'] = $this->language->get('smsir_send_request_error');
                }
            } else {
                $json['error'] = $this->language->get('smsir_no_mobile');
            }
        }
        $this->response->setOutput(json_encode($json));
    }
}
?>