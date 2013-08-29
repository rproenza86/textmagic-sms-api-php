<?php

/**
*
* TextMagic SMS API wrapper
* Environment Checker
* 
* PHP version 5
* 
* @category SMS
* @package  TextMagicSMS
* @author   Petrenko Maxim <mpetrenko@me.com>
* @license  http://www.opensource.org/licenses/bsd-license.php New BSD license
* @link     http://code.google.com/p/textmagic-sms-api-php/
*/




class EnvChecker
{
    // error codes
    const ERROR_NONE    = 1;
    const ERROR_OPENSSL = 2;
    const ERROR_AUF     = 3;
    const ERROR_CURL    = 4;
  
    // parameters
    const PARAM_OPENSSL = 'openssl';
    const PARAM_AUF     = 'allow_url_fopen';
    const PARAM_CURL    = 'curl';

    /** 
     * Method for checking server environment capability for use TextMagicAPI
     *  
     * @return string Error code or No Errors code if all checks are passed
     */ 
    public function check($parameter = false)
    {
        if ($parameter) {
            switch($parameter) {
                case self::PARAM_OPENSSL:
                    if ($this->checkOpenSSL()) {
                        return self::ERROR_OPENSSL;
                    }
                    break;
                case self::PARAM_AUF:
                    if (false == ini_get('allow_url_fopen')) {
                        return self::ERROR_AUF;
                    }
                    break;
                case self::PARAM_CURL:
                    if (false == function_exists('curl_version')) {
                        return self::ERROR_CURL;
                    } 
                    break;
            }
            return self::ERROR_NONE;
        }


        if (ini_get('allow_url_fopen') != 1) {
            // checking fopen dependencies fails, it's bad :(

            // check cURL ext
            if (false == function_exists('curl_version')) {
                return self::ERROR_CURL;
            } else {
                return self::ERROR_AUF;
            }

        }

        // check openssl in php
        if ($this->checkOpenSSL()) {
            return self::ERROR_OPENSSL;
        }

        // return that all is ok if all previous checks are passed
        return self::ERROR_NONE;
    }

    /**
    * Check openSSL support and version
    *
    * @return bool true if error detected
    */
    public function checkOpenSSL()
    {
        return (bool) (OPENSSL_VERSION_NUMBER < 0x009080bf);
    }



    /** 
     * Method for testing server environment capability for use TextMagicAPI
     *  
     * @return string 
     */ 
    public function test()
    {

        switch ($this->check()) {
            case self::ERROR_CURL:
                if ($this->check(self::PARAM_AUF) == self::ERROR_AUF) {
                    print "\033[0;31mERROR: cURL not found and allow_url_fopen is disabled.\033[0m\n";
                    print "One of this issues should be resolwed before You can use TextMagicAPI.\n\n";
                    print "\033[1;33mSolution a:\033[0m is installing cURL from pear or from sources.\n";
                    print "You can use http://php.net/manual/en/book.curl.php for installing cURL.\n\n";
                    print "\033[1;33mSolution b:\033[0m is enable 'allow_url_fopen' in Your php.ini file.\n";
                    print "You can check 'allow_url_fopen' settings doing 'php -i | grep allow_url_fopen' (without quotes) from terminal.\n";
                } else {
                    print "\033[1;33mWARNING: cURL not found.\033[0m\n";
                    print "You will be able to use only 'fopen' sending method.\n";
                    print "Possible solution is installing cURL from pear or from sources.\n";
                    print "You can use http://php.net/manual/en/book.curl.php for installing cURL.\n";
                }
                break;
            case self::ERROR_OPENSSL:
                print "\033[1;33mWARNING: OpenSSL not found or is outdated.\033[0m\n";
                print "HTTP will be used instead of HTTPS.\n";
                print "For installing or updating OpenSSL You can use http://www.openssl.org and http://php.net/manual/en/openssl.installation.php\n";
                break;
            case self::ERROR_AUF:
                print "\033[0;31mERROR: allow_url_fopen is disabled.\033[0m\n";
                print "Possible solution is enable 'allow_url_fopen' in Your php.ini file.\n";
                print "You can check 'allow_url_fopen' settings doing 'php -i | grep allow_url_fopen' (without quotes) from terminal.\n";
                break;
            case self::ERROR_NONE:
                print "\033[0;32mSUCCES: You system is ready to use TextMagicAPI.\033[0m\n";
                break;
        }
    }




}