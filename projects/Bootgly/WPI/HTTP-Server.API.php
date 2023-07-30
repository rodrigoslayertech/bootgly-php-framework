<?php

namespace projects\Bootgly\WPI;


use Bootgly;
use Bootgly\WPI\modules\HTTP\Server\Router;
use Bootgly\WPI\nodes\HTTP\Server\Request;
use Bootgly\WPI\nodes\HTTP\Server\Response;


Bootgly::$Project->vendor    = 'Bootgly/';
Bootgly::$Project->container = 'WPI/';
Bootgly::$Project->package   = 'examples/';
Bootgly::$Project->version   = 'app/';
Bootgly::$Project->construct();


return static function
(Request $Request, Response $Response, Router $Router)
{
   // ! Request examples
   // ? Request Meta (first line of HTTP Request Header)
   #$Request->method;    // GET
   #$Request->uri;       // /path/to?query1=value2...
   #$Request->protocol;  // HTTP/1.1
   // ? Request Header
   #$host = $Request->Header->get('Host');
   // ? Request Content
   // @ download
   // Form-data ($_POST, $_FILES)
   #$files = $Request->download('file1'); // $_FILES and $Request->files available too
   // @ receive
   // Raw - JSON, URL Encoded, Text, etc.
   #$Request->receive();

   #debug($_POST, $_FILES, $Request->input); // $Request->input ↔ file_get_contents('php://input')


   // ! Response examples
   // ? Response Meta (first line of HTTP Response Header)
   #return $Response(status: 302); // 302 Not Found

   // ? Response Header
   #$Response->Header->set('Content-Type', 'text/plain');

   // Cookies
   #$Response->Header->Cookie->append('Test', 'value1');
   #$Response->Header->Cookie->append('Test2', 'value2');

   // ? Response Content
   $Router->route('/', function ($Response) {
      return $Response(content: 'Hello World!');
   }, GET);

   // @ send
   #return $Response->Json->send(['Hello' => 'World!']); // JSON

   // @ upload
   // Small files
   #return $Response('statics/image1.jpg')->upload();
   #return $Response('statics/alphanumeric.txt')->upload(offset: 0, length: 2);
   // Medium files
   #return $Response('statics/screenshot.gif')->upload();

   // @ authenticate
   #return $Response->authenticate(realm: 'Protected area');

   // @ redirect
   #return $Response->redirect(uri: 'https://docs.bootgly.com/', code: 302);
};
