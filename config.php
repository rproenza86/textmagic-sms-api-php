<?php

define('SMS_QUEUE', 'q');
define('SMS_SCHEDULED_QUEUE', 's');
define('SMS_SENDING_ERROR', 'e');
define('SMS_ENROUTE', 'r');
define('SMS_ACKED', 'a');
define('SMS_DELIVERED', 'd');
define('SMS_BUFFERED', 'b');
define('SMS_FAILED', 'f');
define('SMS_UNKNOWN', 'u');
define('SMS_REJECTED', 'j');

define('FINAL_STATUS', SMS_DELIVERED || SMS_FAILED || SMS_UNKNOWN || SMS_REJECTED); //TODO: test it

//class TextMagicAPIException extends Exception {}

class AuthenticationException extends Exception {}
class TooManyItemsException extends Exception {}
class IPAddressException extends Exception {}
class RequestsLimitExceededException extends Exception {}
class UnknownMessageIdException extends Exception {}
class DisabledAccountException extends Exception {}
class LowBalanceException extends Exception {}
class WrongPhoneFormatException extends Exception {}
class TooLongMessageException extends Exception {}
class WrongParameterValueException extends Exception {}
class UnicodeSymbolsDetectedException extends Exception {}