<?php

require_once ('config.php');

/**
 * 
 * TextMagic SMS API wrapper class
 * 
 * @author Fedyashev Nikita <nikita@realitydrivendeveloper.com>
 * @link       http://code.google.com/p/textmagic-sms-api-php/
 */ 

class TextMagicAPI {

	const LOW_BALANCE 				   = 2;
	const INVALID_USERNAME_PASSWORD    = 5;
	const MESSAGE_WAS_NOT_SENT         = 6;
	const TOO_LONG_MESSAGE             = 7; 
	const IP_ADDRESS_IS_NOT_ALLOWED    = 8;
	const WRONG_PHONE_FORMAT           = 9;
	const WRONG_PARAMETER_VALUE        = 10;
	const DAILY_REQUESTS_LIMT_EXCEEDED = 11;
	const TOO_MANY_ITEMS 			   = 12;
	const DISABLED_ACCOUNT             = 13;
	const UNKNOWN_MESSAGE_ID           = 14;
	const UNICODE_SYMBOLS_DETECTED     = 15;

	/**
     * Const for maximum items quantity count in request parameter  
     */
	const MAXIMUM_IDS_PER_REQUEST = 100;

	/**
     * Base config settings
     *
     * @var array $config
     */
	private $config = array(
			'username'	     => '',
			'password'	     => '',
			'gateway'	     => 'https://www.textmagic.com/app/api?',
			'conn_timeout'	 => 10,
			'max_length'     => 3,
			'sending_method' => 'curl'
		);
	/**
     * Connection config values can either be set through constructor's associative array parameter or through @var config
     *
     * @param  array $options Associative array of options
     * @return void
     */
	public function __construct($params = array()){
		foreach($params as $k => $v){
			$this->config[$k] = $v;
		}
	}

    /**
     * Send text
     * 
     * The send command is used to send the SMS message to a mobile phone, or make a scheduled sending.
     * 
     * @param  string $text
     * @param  array $phones
     * @param  boolean $is_unicode
     * @param  integer $scheduled_unix_timestamp Scheduled time for message to be sent in UNIX timestamp format
     * @return mixed
     * @throws WrongPhoneFormatException
     * @throws LowBalanceException
     * @throws TooManyItemsException
     * @throws AuthenticationException
     * @throws IPAddressException
     * @throws RequstsLimitExceededException 
    */
	public function send($text, $phones, $is_unicode, $scheduled_unix_timestamp = false) {
		if (empty($text)) {
			throw new Exception("empty text exception");
		}
		if (!is_array($phones)) {
			throw new Exception("wrong type for phones var");
		}
		if (!is_bool($is_unicode)) {
			throw new Exception("wrong type for is_unicode var");
		}
		$phones = join(',', $phones);
		$params = array(
			'cmd' => 'send',
			'phone' => rawurlencode($phones),
			'text' => rawurlencode($text),
			'unicode' => $is_unicode ? 1: 0 ,
			'max_length' => $this->config['max_length']
		);
		
		if ($scheduled_unix_timestamp)
			$params['send_time'] = $scheduled_unix_timestamp;
		
		
		try {
			$json = $this->doHTTPRequest($params);
			
			$results = array();
			$results['messages'] = $json['message_id']; // asson array in the form message_id|phone_number 
			$results['sent_text'] = $json['sent_text'];
			$results['parts_count'] = $json['parts_count'];
			
			return $results;

		} catch (UnicodeSymbolsDetectedException $e) {
			throw new UnicodeSymbolsDetectedException();			
		} catch (WrongPhoneFormatException $e) {
			throw new WrongPhoneFormatException();			
		} catch (LowBalanceException $e) {
			throw new LowBalanceException();
		} catch (TooManyItemsException $e) {
			throw new TooManyItemsException(); // phones
		} catch (AuthenticationException $e) {
			throw new AuthenticationException();
		} catch (IPAddressException $e) {
			throw new IPAddressException();			
		} catch (RequestsLimitExceededException $e) {
			throw new RequestsLimitExceededException();			
		} catch (TooLongMessageException $e) {
			throw new TooLongMessageException();
		}
		
	}

	/**
     * Get account balance
     * 
     * This command is used to check the current SMS credits balance on your account.
     *
     * @throws AuthenticationException
     * @throws IPAddressException
     * @throws RequestsLimitExceededException
     * @throws DisabledAccountException
     * @return integer
    */
	public function getBalance() {
		
		$params = array(
			'cmd' => 'account',
		);
		
		try {
			$json = $this->doHTTPRequest($params);
			$balance = $json['balance'];
			return $balance;
		} catch (AuthenticationException $e) {
			throw new AuthenticationException();
		} catch (IPAddressException $e) {
			throw new IPAddressException();			
		} catch (RequestsLimitExceededException $e) {
			throw new RequestsLimitExceededException();			
		} catch(DisabledAccountException $e) {
			throw new DisabledAccountException();
		}
	}
	
    /**
     * Get message's status
     * 
     * This command allows you to retrieve the delivery status of any SMS you have already sent. 
     *
     * @param  array $options not associative array of ids
     * @throws Exception
     * @throws TooManyItemsException
     * @throws WrongParameterValueException
     * @throws UnknownMessageIdException
     * @throws AuthenticationException
     * @throws IPAddressException
     * @throws RequestsLimitExceededException
     * @throws DisabledAccountException
     * @return mixed
    */
	public function messageStatus($ids) {
		if (!is_array($ids) || $ids === array()) {
			throw new Exception("ids type mismatch");
		}		
		if (count($ids) > self::MAXIMUM_IDS_PER_REQUEST) {
			throw new TooManyItemsException();
		}
		
		$ids = join(',', $ids);
		$params = array(
			'cmd' => 'message_status',
			'ids' => rawurlencode($ids)
		);
		
		try {
			$json = $this->doHTTPRequest($params);
			return($json);
		} catch (WrongParameterValueException $e) {
			throw new WrongParameterValueException(); // ids			
		} catch (UnknownMessageIdException $e) {
			throw new UnknownMessageIdException();			
		} catch (AuthenticationException $e) {
			throw new AuthenticationException();
		} catch (IPAddressException $e) {
			throw new IPAddressException();
		} catch (RequestsLimitExceededException $e) {
			throw new RequestsLimitExceededException();			
		} catch (DisabledAccountException $e) {
			throw new DisabledAccountException();
			
		}
		
	}
	
	
	/**
     * Check phone number availability, direction cost and destination country code. 
     * 
     * @param  array $options not associative array of ids
     * @throws Exception
     * @throws TooManyItemsException
     * @throws WrongPhoneFormatException
     * @throws AuthenticationException
     * @throws IPAddressException
     * @throws RequestsLimitExceededException
     * @throws DisabledAccountException
     * @return mixed
    */
	public function checkNumber($phones) {
		if (!is_array($phones) || $phones === array()) {
			throw new Exception("$phones type mismatch");
		}
		
		if (count($phones) > self::MAXIMUM_IDS_PER_REQUEST) {
			throw new TooManyItemsException();
		}
		
		$phones = join(',', $phones);
		$params = array(
			'cmd' => 'check_number',
			'phone' => rawurlencode($phones)
		);
		
		try {
			$json = $this->doHTTPRequest($params);
			return($json);
		} catch (WrongPhoneFormatException $e) {
			throw new WrongPhoneFormatException(); // ids			
		} catch (AuthenticationException $e) {
			throw new AuthenticationException();
		} catch (IPAddressException $e) {
			throw new IPAddressException();
		} catch (RequestsLimitExceededException $e) {
			throw new RequestsLimitExceededException();			
		} catch (DisabledAccountException $e) {
			throw new DisabledAccountException();
			
		}
		
	}
	
    /**
     * Receive incoming messages
     * 
     * This command helps you to retrieve the incoming SMS messages from the server. 
     * When SMS is sent to one of our SMS reply numbers you can request these messages using this API.
     * 
     * @param  array $options not ssociative array of last retrieved message's id
     * @throws WrongParameterValueException
     * @throws UnknownMessageIdException
     * @throws AuthenticationException
     * @throws IPAddressException
     * @throws RequestsLimitExceededException
     * @throws DisabledAccountException
     * @return mixed
    */
	public function receive($last_retrieved_id) {
		if (!is_integer($last_retrieved_id)) {
			throw new Exception("wrong type for last_retrieved_id");
		}		
		$params = array(
			'cmd' => 'receive',
			'last_retrieved_id' => $last_retrieved_id
		);
		try {
			$json = $this->doHTTPRequest($params);
			
			return $json;
			
		} catch (WrongParameterValueException $e) {
			throw new WrongParameterValueException(); // last_retrieved_id			
		} catch (UnknownMessageIdException $e) {
			throw new UnknownMessageIdException();			
		} catch (AuthenticationException $e) {
			throw new AuthenticationException();
		} catch (IPAddressException $e) {
			throw new IPAddressException();			
		} catch (RequestsLimitExceededException $e) {
			throw new RequestsLimitExceededException();			
		} catch (DisabledAccountException $e) {
			throw new DisabledAccountException();
		}
	}
	
    /**
     * Delete Incoming message
     * 
     * This command helps you to delete the incoming SMS messages from the server. 
     * After you have read incoming messages sent to one of our SMS reply numbers you can delete them 
     * so they won't be shown in receice function anymore and can decrease unread messages.
     *
     * @param  array $options not associative array of ids
     * @throws UnknownMessageIdException
     * @throws TooManyItemsException
     * @throws AuthenticationException
     * @throws IPAddressException
     * @throws RequestsLimitExceededException
     * @throws DisabledAccountException
     * @return boolean true in case of success
     * 
    */
	public function deleteReply($ids) {
		if (!is_array($ids) || $ids === array()) {
			throw new Exception("ids type mismatch");
		}		
		if (count($ids) > self::MAXIMUM_IDS_PER_REQUEST) {
			throw new TooManyItemsException();
		}
		
		$params = array(
			'cmd' => 'delete_reply',
			'ids' => rawurlencode(implode(',', $ids))
		);
		
		try {
			$json = $this->doHTTPRequest($params);
			
			return true;
		} catch (UnknownMessageIdException $e) {
			throw new UnknownMessageIdException();			
		} catch (TooManyItemsException $e) {
			throw new TooManyItemsException();
		} catch (AuthenticationException $e) {
			throw new AuthenticationException();
		} catch (IPAddressException $e) {
			throw new IPAddressException();			
		} catch (RequestsLimitExceededException $e) {
			throw new RequestsLimitExceededException();			
		} catch(DisabledAccountException $e) {
			throw new DisabledAccountException();
		}
	}
	
	private function isFinalStatus($status) {
		return (in_array($status, array(SMS_DELIVERED, SMS_FAILED, SMS_UNKNOWN, SMS_REJECTED))) ? true : false;		
	}
	
	/**
     * HTTP abstract request wrapper
     *
     * @param  array $params associative array of request parameters
     * @return string
    */
	private function doHTTPRequest($params) {
		$params['username'] = $this->config['username'];
		$params['password'] = $this->config['password'];
		
		if ($this->config['sending_method'] == 'curl') {
			
			$raw_data = $this->executeCURLRequestAndReturn($params);
			
		} elseif($this->config['sending_method'] == 'fopen') {
			
			$raw_data = $this->executeFOpenRequestAndReturn($params);
			
		} else
			throw new Exception("Unsupported sending method");
		
        $json = json_decode($raw_data, true);
        
        if (array_key_exists('error_code', $json)) {
        	switch($json['error_code']) {
        		case self::LOW_BALANCE:
	  				throw new LowBalanceException();
        		break;
        		case self::INVALID_USERNAME_PASSWORD:
	  				throw new AuthenticationException();
        		break;
        		case self::IP_ADDRESS_IS_NOT_ALLOWED:
	  				throw new IPAddressException();
        		break;
        		case self::WRONG_PHONE_FORMAT:
	  				throw new WrongPhoneFormatException();
        		break;
        		case self::DAILY_REQUESTS_LIMT_EXCEEDED:
	  				throw new RequestsLimitExceededException();
        		break;
        		case self::DISABLED_ACCOUNT:
	  				throw new DisabledAccountException();
        		break;
        		case self::UNKNOWN_MESSAGE_ID:
	  				throw new UnknownMessageIdException();
        		break;
        		case self::TOO_MANY_ITEMS:
	  				throw new TooManyItemsException();
        		break;
        		case self::WRONG_PARAMETER_VALUE:
	  				throw new WrongParameterValueException();
        		break;
        		case self::TOO_LONG_MESSAGE:
	  				throw new TooLongMessageException();
        		break;
        		case self::UNICODE_SYMBOLS_DETECTED:
	  				throw new UnicodeSymbolsDetectedException();
        		break;
        		default:
        			throw new Exception($json['error_message']);
  			}
        	
        }

		return $json;		 
	}
	
	/**
     * CURL request wrapper
     *
     * @param  array $params associative array of request parameters
     * @return string
    */
	private function executeCURLRequestAndReturn($params) {
		if (!extension_loaded('curl')) {
            die ("This SMS API class can not work without CURL PHP module! Try using fopen sending method.");
        }
		
		$ch = curl_init($this->config['gateway']);

        curl_setopt_array($ch, array(
        	CURLOPT_POST => true,
        	CURLOPT_RETURNTRANSFER => true,
        	CURLOPT_TIMEOUT => $this->config['conn_timeout'],
        	CURLOPT_POSTFIELDS  => $params,
//        	CURLOPT_VERBOSE => 1
        ));

        $raw_data = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
		if ($http_code != 200){
			if ($http_code){
				
				throw new Exception("Bad response from remote server: HTTP status code $http_code");
			} else {
				throw new Exception("Couldn't connect to remote server");
			}
		}
		return $raw_data;
	}
	
	/**
     * fopen request wrapper
     *
     * @param  array $params associative array of request parameters
     * @return string
    */
	private function executeFOpenRequestAndReturn($params) {
		$raw_data = '';
			
		$url = $this->config['gateway'];
		foreach ($params as $key=>$value) {
			$url .= "&" . $key . "=" . $value ;
		}
		
		$handler = @fopen ($url, 'r');
		if ($handler) {
			while ($line = @fgets($handler,1024)) {
				$raw_data .= $line;
			}
			fclose ($handler);
		} else {
			throw new Exception("Error while executing fopen sending method!<br>Please check does PHP have OpenSSL support.");
		}
		return $raw_data;
	}

}

?>
