# Introduction #

This tutorial is designed to provide a basic overview of how to use TextMagic PHP api. When you have completed the tutorial you will have written a simple web-application that sends and receives sms messages through TextMagic SMS Gateway. You can also download working web application package at the end of the tutorial.


## Instantiating TextMagicAPI class ##

You can instantiate the class in 2 ways:

```
$api = new TextMagicAPI(array(
    "username" => "your_user_name",
    "password" => "your_API_password", 
));
```

or

```
$api = new TextMagicAPI(); // in that case you will need to set all your settings(username, password, timeout limits) in the config.php file
```

## Sending Messages ##

### Unicode message ###

```
$text = "Unicode symbols here allowed";
$phones = array(9991234567); // one of the test number. Replace it with your destination phone number

try {

     $results = $api->send($text, $phones, true);

     $sentText = $results['sent_text'];
     $partsCount = $results['parts_count'];

     foreach($results['messages'] as $msgID => $phoneNumber) {
          // do whatever you need with this info
     }

} catch (UnicodeSymbolsDetectedException $e) {
    //your code
} catch (WrongPhoneFormatException $e) {
    //your code
} catch (LowBalanceException $e) {
    //your code
} catch (TooManyItemsException $e) {
    //your code
} catch (AuthenticationException $e) {
    //your code
} catch (IPAddressException $e) {
    //your code
} catch (RequestsLimitExceededException $e) {
    //your code
} catch (TooLongMessageException $e) {
    //your code
} catch (Exception $e) {
    echo "Catched Exception '".__CLASS__ ."' with message '".$e->getMessage()."' in ".$e->getFile().":".$e->getLine();
}


```



---



### Non-Unicode message ###

```
$text = "latin symbols here only";
$phones = array(9991234567);

try {

     $results = $api->send($text, $phones, false);

     $sentText = $results['sent_text'];
     $partsCount = $results['parts_count'];

     foreach($results['messages'] as $msgID => $phoneNumber) {
          // do whatever you need with this info
     }

} catch (UnicodeSymbolsDetectedException $e) {
    //your code
} catch (WrongPhoneFormatException $e) {
    //your code
} catch (LowBalanceException $e) {
    //your code
} catch (TooManyItemsException $e) {
    //your code
} catch (AuthenticationException $e) {
    //your code
} catch (IPAddressException $e) {
    //your code
} catch (RequestsLimitExceededException $e) {
    //your code
} catch (TooLongMessageException $e) {
    //your code
} catch (Exception $e) {
    echo "Catched Exception '".__CLASS__ ."' with message '".$e->getMessage()."' in ".$e->getFile().":".$e->getLine();
}

```



---




## Check your balance ##

Sometimes it is required to know beforehand whether you can send messages or can not. For example, if you don't have enough credits on your balance, you can assume that your sending requests would fail.

TextMagic API provides convenient way to check your balance:

```
try {

    $results = $api->getBalance();

} catch (AuthenticationException $e) {
    //your code
} catch (IPAddressException $e) {
    //your code
} catch (RequestsLimitExceededException $e) {
    //your code
} catch (DisabledAccountException $e) {
    //your code
} catch (Exception $e) {
    echo "Catched Exception '".__CLASS__ ."' with message '".$e->getMessage()."' in ".$e->getFile().":".$e->getLine();
}

```




---



## Check sent message delivery status ##

After you sent message to TextMagic server, the message sometimes can be sent in the same second. Some operators server can be overloaded and delivering can take some time, the recipients handset can be turned off or even recipients phone number can be wrong and not-existing one.

```

$messageId = 2;

try {

    $results = $api->messageStatus(array($messageId));

    foreach($results as $msgId => $msgInfo) {
        /*$msgInfo['text']
        $msgInfo['status']
        $msgInfo['reply_number']
    
        // this parameter exists for final statuses
        if (array_key_exists('completed_time', $msgInfo)
            $msgInfo['completed_time']

        // this parameter exists for final statuses
        if (array_key_exists('credits_cost', $msgInfo)
            $msgInfo['credits_cost']

        */

} catch (WrongParameterValueException $e) {
    //your code
} catch (TooManyItemsException $e) {
    //your code
} catch (UnknownMessageIdException $e) {
    //your code
} catch (AuthenticationException $e) {
    //your code
} catch (IPAddressException $e) {
    //your code
} catch (RequestsLimitExceededException $e) {
    //your code
} catch (DisabledAccountException $e) {
    //your code
} catch (Exception $e) {
    echo "Catched Exception '".__CLASS__ ."' with message '".$e->getMessage()."' in ".$e->getFile().":".$e->getLine();
}

```



---




## Retrieving incoming sms messages ##

Once you start using TextMagic for sending messages, you may realize that in some cases you need feedback from your recipients. TextMagic allows your recipients to reply to the messages they get. And even to send messages to your <a href='http://api.textmagic.com/https-api/sender-id'>sender id</a>. This inbound messages are stored on TextMagic server and can be accessed in your account administration page or with help of PHP api.

```

$lastRetrievedId = 0; // for the start, request with 0(zero), later use your latest retrived message, by his ID
try {
    
    $results = $api->receive($lastRetrievedId);

    $messages = $results['messages'];
    foreach($messages as $message_id => $message) {
        $text = $message['text'];
        $from = $message['from'];
        $timestamp = $message['timestamp'];
    }

    $unread = $results['unread']; // if it is greater than zero, rerequst this function with latest message ID retrived in that response

} catch (WrongParameterValueException $e) {
    //your code
} catch (UnknownMessageIdException $e) {
    //your code
} catch (AuthenticationException $e) {
    //your code
} catch (IPAddressException $e) {
    //your code
} catch (RequestsLimitExceededException $e) {
    //your code
} catch (DisabledAccountException $e) {
    //your code
} catch (Exception $e) {
    echo "Catched Exception '".__CLASS__ ."' with message '".$e->getMessage()."' in ".$e->getFile().":".$e->getLine();
}

```



---




## Delete incoming sms messages ##

This command helps you to delete the incoming SMS messages from the server.
After you have read incoming messages sent to one of our SMS reply numbers you can delete them so they won't be shown in receice function anymore and can decrease unread messages.

```

$messageIDs = array(8624390);
//$messageIDs = array(8624390, 8624391);  - for multiple messages at once
try {

    $api->deleteReply($messageIDs);

} catch (UnknownMessageIdException $e) {
    //your code
} catch (TooManyItemsException $e) {
    //your code
} catch (AuthenticationException $e) {
    //your code
} catch (IPAddressException $e) {
    //your code
} catch (RequestsLimitExceededException $e) {
    //your code
} catch(DisabledAccountException $e) {
    //your code
} catch (Exception $e) {
    echo "Catched Exception '".__CLASS__ ."' with message '".$e->getMessage()."' in ".$e->getFile().":".$e->getLine();
}

```



---



## Check phone number validity and destination price ##

This command helps you to validate phone number format and check message price to this destination.
This command also tells about phone number's country.
```

$phones = array(447123456789);
//$phones = array(447123456789, 447123456790);  - for multiple numbers at once
try {

    $results = $api->checkNumber($phones);

    foreach($results as $number => $info) {
        $price = $info['price'];
        $country = $info['country'];
    }

} catch (WrongPhoneFormatException $e) {
    //your code
} catch (TooManyItemsException $e) {
    //your code
} catch (AuthenticationException $e) {
    //your code
} catch (IPAddressException $e) {
    //your code
} catch (RequestsLimitExceededException $e) {
    //your code
} catch (DisabledAccountException $e) {
    //your code
} catch (Exception $e) {
    echo "Catched Exception '".__CLASS__ ."' with message '".$e->getMessage()."' in ".$e->getFile().":".$e->getLine();
}

```