# Introduction #

This is a class to give you an easy solution to send SMS and receive replies with this simple API wrapper class. The wrapper provides a way to integrate <a href='http://www.textmagic.com'>TextMagic</a> SMS Gateway to your PHP application.

# Quick Start Guide #

  1. Create TextMagic account on http://www.textmagic.com/
  1. Get wrapper's source code by executing the command `svn checkout http://textmagic-sms-api-php.googlecode.cour om/svn/trunk/ textmagic-sms-api-php-read-only`
  1. Include TextMagicAPI.php file to your application
  1. Set your account's settings
  1. Use it!


# Features/supported API functions #

  * **send**           - send SMS, scheduled sending

  * **account**        - check account's balance

  * **message\_status** - check message's cost and delivery status

  * **receive**        - receive incoming messages

  * **delete\_reply**   - delete specified incoming messages

  * **check\_number**   - check phone number validity and destination price


# Example #

```

$api = new TextMagicAPI(array(
    "username" => "your_user_name",
    "password" => "your_API_password", 
));

$text = "Hello world!";
$phones = array(9991234567);
$is_unicode = true;

$api->send($text, $phones, $is_unicode);

```

This is simplified version. For production code using, read real examples first.


# Notes #

  * Exceptions thrown by the server are turned into Exceptions in the library through the response parsers. all functions have explicit exceptions.

  * There is phpdoc's commenting throughout the code.


# Requirements #

  * PHP 5.0.0+ (tested on 5.2.6 Ubuntu 9.04)

  * JSON extension(tested on 1.2.1)


# Changelog #

  * June 5, 2009 - first API wrapper version has been released.

  * June 27, 2009 - fopen request method has been added.

  * November 11, 2010 - fopen is default sending method (curl is no longer supported).

# <a href='http://www.textmagic.com/affiliate/fordevelopers.html'>SMS Gateway Affiliate Programme For Developers</a> #

Here’s what you’ll get:

<li>A 10% share of the lifetime value of the customer. If a customer spends £5,000 on SMS credit during his or her membership of TextMagic, you’ll earn £500.</li>

<li>Bonus: we’ll pay you a £15 flat fee for each new paying customer referral. You’ll still get your 10% revenue share from their initial order, too.</li>
