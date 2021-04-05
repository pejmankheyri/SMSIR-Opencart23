<?php

/**
 * Modules Gateway class page
 * 
 * PHP version 5.6.x | 7.x
 * 
 * @category  Modules
 * @package   OpenCart 2.3
 * @author    Pejman Kheyri <pejmankheyri@gmail.com>
 * @copyright 2021 All rights reserved.
 */

/**
 * Modules Gateway class
 * 
 * @category  Modules
 * @package   OpenCart 2.3
 * @author    Pejman Kheyri <pejmankheyri@gmail.com>
 * @copyright 2021 All rights reserved.
 */
class SmsIr
{
    /**
     * Gets API Message Send Url.
     *
     * @return string Indicates the Url
     */
    private static function _getAPIMessageSendUrl()
    {
        return "api/MessageSend";
    }

    /**
     * Gets Api Token Url.
     *
     * @return string Indicates the Url
     */
    private static function _getApiTokenUrl()
    {
        return "api/Token";
    }

    /**
     * Gets Api Credit Url.
     *
     * @return string Indicates the Url
     */
    private static function _getApiCreditUrl()
    {
        return "api/credit";
    }

    /**
     * Gets API Customer Club Add Contact And Send Url.
     *
     * @return string Indicates the Url
     */
    private static function _getAPICustomerClubAddContactAndSendUrl()
    {
        return "api/CustomerClub/AddContactAndSend";
    }

    /**
     * Gets API Customer Club Send To Categories Url.
     *
     * @return string Indicates the Url
     */
    private static function _getAPICustomerClubSendToCategoriesUrl()
    {
        return "api/CustomerClub/SendToCategories";
    }

    /**
     * Send sms with simple web service.
     *
     * @param string          $apidomain     api domain
     * @param string          $APIKey        API Key
     * @param string          $SecretKey     Secret Key
     * @param string          $LineNumber    Line Number
     * @param MobileNumbers[] $MobileNumbers array structure of mobile numbers
     * @param Messages[]      $Messages      array structure of messages
     * 
     * @return string Indicates the sent sms result
     */
    public static function sendSingle($apidomain, $APIKey, $SecretKey, $LineNumber, $MobileNumbers, $Messages)
    {
        $token = SmsIr::_getToken($apidomain, $APIKey, $SecretKey);

        $result = false;
        if ($token != false) {
            $postData = array(
                'Messages' => $Messages,
                'MobileNumbers' => $MobileNumbers,
                'LineNumber' => $LineNumber,
                'SendDateTime' => '',
                'CanContinueInCaseOfError' => 'false'
            );

            $url = $apidomain.SmsIr::_getAPIMessageSendUrl();
            $result = SmsIr::_execute($postData, $url, $token);

        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Send sms with customer club web service.
     *
     * @param string          $apidomain     api domain
     * @param string          $APIKey        API Key
     * @param string          $SecretKey     Secret Key
     * @param MobileNumbers[] $MobileNumbers array structure of mobile numbers
     * @param Messages[]      $Messages      array structure of message
     * 
     * @return string Indicates the sent sms result
     */
    public static function sendSingleCustomerClub($apidomain, $APIKey, $SecretKey, $MobileNumbers, $Messages)
    {
        $token = SmsIr::_getToken($apidomain, $APIKey, $SecretKey);

        $result = false;
        if ($token != false) {
            foreach ($MobileNumbers as $key => $value) {
                $postData[] = array(
                    'Prefix' => '',
                    'FirstName' => '',
                    'LastName' => '',
                    'Mobile' => $value,
                    'BirthDay' => '',
                    'CategoryId' => '',
                    'MessageText' => $Messages[0],
                );
            }
            $url = $apidomain.SmsIr::_getAPICustomerClubAddContactAndSendUrl();
            $result = SmsIr::_execute($postData, $url, $token);
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Send sms with customer club to all customer club contacts.
     *
     * @param string     $apidomain api domain
     * @param string     $APIKey    API Key
     * @param string     $SecretKey Secret Key
     * @param Messages[] $Messages  array structure of message
     * 
     * @return string Indicates the sent sms result
     */
    public static function sendToAllCustomerClub($apidomain, $APIKey, $SecretKey, $Messages)
    {
        $token = SmsIr::_getToken($apidomain, $APIKey, $SecretKey);

        $result = false;
        if ($token != false) {
            $postData = array(
                'Messages' => $Messages[0],
                'contactsCustomerClubCategoryIds' => '',
                'SendDateTime' => '',
                'CanContinueInCaseOfError' => 'false'
            );
            $url = $apidomain.SmsIr::_getAPICustomerClubSendToCategoriesUrl();
            $result = SmsIr::_execute($postData, $url, $token);
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Executes the sms send methods.
     *
     * @param postData[] $postData array of json data
     * @param string     $url      url
     * @param string     $token    token string
     * 
     * @return string Indicates the curl execute result
     */
    private static function _execute($postData, $url, $token)
    {
        $postString = json_encode($postData);
        $ch = curl_init($url);
        curl_setopt(
            $ch, 
            CURLOPT_HTTPHEADER, 
            array(
                'Content-Type: application/json',
                'x-sms-ir-secure-token: '.$token
            )
        );
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * Gets token key for all web service requests.
     *
     * @param string $apidomain api domain
     * @param string $APIKey    API Key
     * @param string $SecretKey Secret Key
     * 
     * @return string Indicates the token key
     */
    private static function _getToken($apidomain, $APIKey, $SecretKey)
    {
        $postData = array(
            'UserApiKey' => $APIKey,
            'SecretKey' => $SecretKey,
            'System' => 'opencart_2_3_v_2_1'
        );
        $postString = json_encode($postData);

        $ch = curl_init($apidomain.SmsIr::_getApiTokenUrl());
        curl_setopt(
            $ch, 
            CURLOPT_HTTPHEADER, 
            array(
                'Content-Type: application/json'
            )
        );
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);

        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result);
        $resp = false;

        if (is_object($response)) {
            @$IsSuccessful = $response->IsSuccessful;
            if ($IsSuccessful == true) {
                @$TokenKey = $response->TokenKey;
                $resp = $TokenKey;
            } else {
                $resp = false;
            }
        }
        return $resp;
    }

    /**
     * Gets credit.
     *
     * @param string $apidomain api domain
     * @param string $APIKey    API Key
     * @param string $SecretKey Secret Key
     * 
     * @return int Indicates the credit
     */
    public static function getCredit($apidomain, $APIKey, $SecretKey)
    {
        $token = SmsIr::_getToken($apidomain, $APIKey, $SecretKey);

        $resp = false;
        if ($token != false) {
            $ch = curl_init($apidomain.SmsIr::_getApiCreditUrl());
            curl_setopt(
                $ch, 
                CURLOPT_HTTPHEADER, 
                array(
                    'x-sms-ir-secure-token: '.$token
                )
            );
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            $result = curl_exec($ch);
            curl_close($ch);

            $response = json_decode($result);

            if (is_object($response)) {
                @$IsSuccessful = $response->IsSuccessful;
                if ($IsSuccessful == true) {
                    @$Credit = $response->Credit;
                    $resp = $Credit;
                } else {
                    $resp = false;
                }
            }
        } else {
            $resp = false;
        }
        return $resp;
    }

    /**
     * Check if mobile number is valid.
     *
     * @param string $mobile mobile number
     * 
     * @return boolean Indicates the mobile validation
     */
    public static function isMobile($mobile)
    {
        if (preg_match('/^09(0[1-5]|1[0-9]|3[0-9]|2[0-2]|9[0-1])-?[0-9]{3}-?[0-9]{4}$/', $mobile)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if mobile with zero number is valid.
     *
     * @param string $mobile mobile with zero number
     * 
     * @return boolean Indicates the mobile with zero validation
     */
    public static function isMobileWithouthZero($mobile)
    {
        if (preg_match('/^9(0[1-5]|1[0-9]|3[0-9]|2[0-2]|9[0-1])-?[0-9]{3}-?[0-9]{4}$/', $mobile)) {
            return true;
        } else {
            return false;
        }
    }
}