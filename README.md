# domaintoolsAPI PHP Wrapper #

## Presentation ##

The domaintoolsAPI PHP Wrapper is a simple connector to access all webservices of [domaintools.com](http://domaintools.com "domaintools.com").

## Getting started ##

1- Clone the project with Git by running:

    $ git clone git://github.com/DomainTools/api-php

2- Rename the **api.ini.default** to **api.ini**

3- Fill **api.ini**  with your domaintools credentials:

    username  = 'your_api_username';
    key       = 'your_api_key';

3-A Create a short PHP file which requires the DomaintoolsAPI.class.php and makes a simple call to a webservice (**whois** for example):

```php
<?php
  // Require domaintoolsAPI PHP wrapper
  require_once 'my/path/to/DomaintoolsAPI.class.php';

  //Make a call to the webservice whois with a xml return
  //type for the domain name : domaintools.com
  $request = new DomaintoolsAPI();

  $request->from("whois")               // Name of the service
          ->withType("xml")             // Return type (JSON or XML or HTML)
          ->domain("domaintools.com");  // Domain name

  $response = $request->execute();
  //Display the response
  echo $response;
?>
```

3-B If required you can also override on the fly your configuration :

```php
<?php
  // Require domaintoolsAPI PHP wrapper
  require_once 'my/path/to/DomaintoolsAPI.class.php';

  // we get the default configuration
  $configuration = new DomaintoolsAPIConfiguration();

  // we change some values
  $configuration->set('username','anotherUsername')
                ->set('password','anotherPassword');

  // Make a call to the webservice whois with a xml return
  // type for the domain name : domaintools.com

  $request = new DomaintoolsAPI($configuration);
  $request->from("whois")
          ->withType("xml")
          ->domain("domaintools.com");

  $response = $request->execute();

  //Display the response
  echo $response;
?>
```

4- Execute the PHP script:

    $ php testDomaintoolsAPI.php

   If everything works fine, you should have a display like this:

```xml
<?xml version="1.0"?>
<whoisapi>
  <response>
    <registrant>DomainTools, LLC</registrant>
    <registration>
      <created>1998-08-02</created>
      <expires>2014-08-01</expires>
      <updated>2010-08-31</updated>
      <registrar>CHEAP-REGISTRAR.COM</registrar>
      <statuses>ok</statuses>
    </registration>
    <name_servers>NS1.P09.DYNECT.NET</name_servers>
    <name_servers>NS2.P09.DYNECT.NET</name_servers>
    <name_servers>NS3.P09.DYNECT.NET</name_servers>
    <name_servers>NS4.P09.DYNECT.NET</name_servers>
    <whois>
      <date>2011-10-17</date>
      <record>Domain name: domaintools.com

      Registrant Contact:
         DomainTools, LLC
         Domain Administrator (memberservices@domaintools.com)
         +1.2068389035
         Fax: +1.2068389056
         2211 5th Avenue
         Suite 201
         Seattle, WA 98121
         US

      Administrative Contact:
         DomainTools, LLC
         Domain Administrator (memberservices@domaintools.com)
         +1.2068389035
         Fax: +1.2068389056
         2211 5th Avenue
         Suite 201
         Seattle, WA 98121
         US

      Technical Contact:
         DomainTools, LLC
         Domain Administrator (memberservices@domaintools.com)
         +1.2068389035
         Fax: +1.2068389056
         2211 5th Avenue
         Suite 201
         Seattle, WA 98121
         US

         Status: Active
         Creation Date: 13-Jul-2002
         Expiration Date: 13-Jul-2016
         Name Server: NS1.P09.DYNECT.NET
         Name Server: NS2.P09.DYNECT.NET
         Name Server: NS3.P09.DYNECT.NET
         Name Server: NS4.P09.DYNECT.NET
      </record>
    </whois>
  </response>
</whoisapi>
```

5- Read the documentation to learn more, and visit [domaintools.com](http://domaintools.com "domaintools.com") to know the list of available services.

## Examples for a quick start ##
In **examples/** directory you will find some typical calls to API :

```php
$ php 1-first-call-to-api.php
$ php 2-different-calls-for-same-result.php
...
```
## Documentation ##

The domaintoolsAPI PHP Wrapper is a fluent API implemented by using method chaining.

After having instanciate your request like this:

```php
<?php $request = new DomaintoolsAPI(); ?>
```

You can combine methods to specify return type, options, etc.:

```php
<?php
$request->from('mark-alert')
        ->where(array("query" => "domaintools"))
        ->withType("xml")
        ->domain("domaintools.com")
        ->execute();
?>
```

### Choose service to call - from ###

```php
<?php $request->from("whois"); ?>
```
If no **from** is found the default service called will be [Domain Profile](http://www.domaintools.com/api/docs/domain-profile/).
You can find the list of available services on [domaintools.com](http://domaintools.com "domaintools.com").

### Specify options - where ###

```php
<?php $request->from("mark-alert")->where(array("query" => "domain tools")); ?>
```

The method **where** allows to specify options of the service. It takes only on parameter, an array of options where the key is the name of the option and value is the value of the option.

The list of options for each service is available on the [domaintoolsAPI documentation](http://domaintools.com/api/docs/ "domaintoolsAPI documentation") .

### Specify return type - withType ###

```php
<?php $request->from("whois")->withType("json"); ?>
```
The method **withType** allows to specify the return type of the response. It takes only one parameter, the **name** of the return type.

The list of return types is available on the [domaintoolsAPI documentation](http://domaintools.com/api/docs/ "domaintoolsAPI documentation").

You can also use **toJson**, **toXml** and **toHtml** as aliases of withType :
```php
<?php
  echo $request->from("whois")->domain('domaintools.com')->toJson()->execute(); // returns Json
  echo $request->from("whois")->domain('domaintools.com')->toXml()->execute(); // returns Xml
  echo $request->from("whois")->domain('domaintools.com')->toHtml()->execute(); // returns Html
?>
```

### If no return type, a DomaintoolsAPIResponse object is returned ###

By default (If you don't call the method withType) the return type used is  a **DomaintoolsAPIResponse** object:

```php
<?php
  $response = $request->from("whois")->domain('domaintools.com')->execute();
?>
```

With this response object, you will be able to access to response properties :

```php
<?php
  $response = $request->from("whois")->domain('domaintools.com')->execute();

  echo $response->registrant; // Domaintools, LLC

  echo $response->whois->date; // 2011-10-17
?>
```

### Call the service - execute ###

```php
<?php
  $response = $request->from("whois")->domain("domaintools.com")->execute();
?>
```

To call the service use the method **execute**, and return the response.

The response is a string with the format of the specify return type (JSON or XML for example).

## Tests with PHPUnit ##

Here is the procedure to test this API

1- Install [PHPUnit](http://www.phpunit.de/)

2- Go into the main directory containing the tests/ directory

2- Call all tests once (in console) :

```php
$ phpunit tests/
```

In **tests/** is include all the tests classes. You should have a similar result :

```php
PHPUnit 3.5.15 by Sebastian Bergmann.

....................

Time: 1 second, Memory: 7.75Mb

OK (20 tests, 31 assertions)
```
## Changelog ##

See the CHANGELOG.md file for details.

## License ##

Copyright (C) 2011 by domaintools.com, DomaintoolsAPI PHP Wrapper is released under the MIT license.
See the LICENSE.md file for details.

