Requests
========

 Requests is an API to make HTTP requests using Curl.

## Why use Requests ?

- Is builded over Curl
- Simplifies our lives making CURL usable by humans
- It's inspired in Requests API for Python, this pretends to be a port for PHP
  https://github.com/kennethreitz/requests/

## Usage

The usage is very easy

In order to make a GET Request you should do this

``` php
$response = \Requests\Requests::get('http://www.google.com');
```

Yes, only that.

To make a POST you can do this

``` php
$opt = array (
  'param1' => 'Some Value',
  'param2' => 'Some other value',
);

$response = \Requests\Requests::get('http://www.httpbin.org/post', $opt);
```

and that's all

The Response object will hold the result of the request. Also it has a lot
of important data of the request.

 - $response->content : The Content of the Request
 - $response->headers : The Response Headers
 - $response->status_code : The Response Code

And more.

To see more samples, check your tests (until I write more documentation).

## Supported methods.

 - GET
 - POST
 - PUT
 - DELETE
 - HEAD
 - OPTIONS

## Todo

 - HTTPs support
 - Session Supports
 - Cookies Supports
 - Auth Mecanism

## Support

If you want a special feature or if you detect some bug, please, let me know.

If you wanna contribute to the project, also, please let me know :).