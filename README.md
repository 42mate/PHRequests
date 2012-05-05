PHRequests
========

 PHRequests is an API to make HTTP requests using Curl.

 Is pronounced  Free-Quests (original Idea by @mgi1982)

## Why use PHRequests ?

- Its built on Curl.
- Simplifies your live by making CURL actually usable.
- Inspired by the Requests API for Python, this pretends to be a port for PHP
  https://github.com/kennethreitz/requests/

## Usage
If you need to make a Request to get the content of some Url using Curl a clasic
code will look like this.

``` php
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
  curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
  curl_setopt($ch, CURLOPT_URL, 'http://www.google.com');

  $result = curl_exec($ch);

  if (curl_errno($ch) > 0) {
    //Handle Error
  }

  curl_close($ch);
```

PHRequests wraps all this awful code to make our lives easier.

In order to make a GET Request you should do this

``` php
$response = \PHRequests\PHRequests::get('http://www.google.com');
```

Yes, only that. Looks nice, take a look to a POST Request.

To make a POST you can do this

``` php
$opt = array (
  'param1' => 'Some Value',
  'param2' => 'Some other value',
);

$response = \PHRequests\PHRequests::post('http://www.httpbin.org/post', $opt);
```

and that's all folks!

The Response object will hold the result of the request. Also it has a lot
of important data of the request.

 - $response->content : The Content of the Request
 - $response->headers : The Response Headers
 - $response->status_code : The Response Code

And more.

PHRequests uses the PSR-0 standard for class autoloading so you will need a
compatible defined autoload function to start using by itself (versus inside a
compatible framework like Symfony). You can find one such a function in the
Tests\bootstrap.php file.

To see more samples, check the tests (until I write more documentation).

## Proxy Support

If your are behind a proxy you need to define the Url of the proxy in order to
make the Request. Here is an example.

``` php
$options = array(
 'proxy' => array(
    'url' => 'http://prx_name_or_ip:3128'         
  ),
);
$response = \PHRequests\PHRequests::options(BASE_GET_URL, $options);
``` 

If your proxy uses auth, try with this

``` php
$options = array(
 'proxy' => array(
    'url' => 'http://prx_name_or_ip:3128',
    'auth' => 'username:password',
    'auth_method' => Auth::BASIC //Optional, BASIC By default, NTLM is the second option. 
  ),
);

$response = \PHRequests\PHRequests::options(BASE_GET_URL, $options);
``` 

## HTTPS support

In order to make HTTPs Requests against a valid HTTPs Server. You need
download and save the Certificate of the site, save it into a reachable
folder. After, you need to define the option ssl_ca with the full path to the 
certificate in order to setup PHRequest. Here is an example.

``` php
$options = array (
  'ssl_ca' => '../certs/mycert.pem';
)
$response = PHRequests::get('https://www.mysite.com', $options);
``` 

If you don't set the PEM certificate, the HTTPs request will be made anyway but 
without a proper certificate validation, the connection will be still an SSL 
connection but can be a security issue accept any certificate without validation.

IMPORTANT : The certificate must be a PEM certificate.

Here you have more detailed explanation about HTTPs with curl.

  http://unitstep.net/blog/2009/05/05/using-curl-in-php-to-access-https-ssltls-protected-sites/

## Supported methods.

 - GET
 - POST
 - PUT
 - DELETE
 - HEAD
 - OPTIONS

## Todo

 - Session Supports
 - Cookies Supports
 - Auth Mecanism

## Support

If you want a special feature or if you find a bug, please, let me know.

If you want to contribute to the project, also, please let me know :).
