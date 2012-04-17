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

To see more samples, check the tests (until I write more documentation).

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

If you want a special feature or if you find a bug, please, let me know.

If you want to contribute to the project, also, please let me know :).
