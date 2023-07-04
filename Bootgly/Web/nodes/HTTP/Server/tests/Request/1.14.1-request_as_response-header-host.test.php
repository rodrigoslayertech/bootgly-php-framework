<?php

use Bootgly\ACI\Debugger;
// SAPI
use Bootgly\Web\nodes\HTTP\Server\Request;
use Bootgly\Web\nodes\HTTP\Server\Response;
// CAPI?
#use Bootgly\Web\nodes\HTTP\Client\Request;
#use Bootgly\Web\nodes\HTTP\Client\Response;
// TODO ?

return [
   // @ configure

   // @ simulate
   // Client API
   'capi' => function () {
      // ...
      return
      <<<HTTP
      GET / HTTP/1.1\r
      Host: lab.bootgly.com\r
      \r
      
      HTTP;
   },
   // Server API
   'sapi' => function (Request $Request, Response $Response): Response {
      $host = $Request->host;
      return $Response(content: $host);
   },

   // @ test
   'test' => function ($response): bool {
      $expected = <<<HTML_RAW
      HTTP/1.1 200 OK\r
      Server: Bootgly\r
      Content-Length: 15\r
      Content-Type: text/html; charset=UTF-8\r
      \r
      lab.bootgly.com
      HTML_RAW;

      // @ Assert
      if ($response !== $expected) {
         Debugger::$labels = ['HTTP Response:', 'Expected:'];
         debug(json_encode($response), json_encode($expected));
         return false;
      }

      return true;
   },
   'except' => function (): string {
      return 'Request not matched';
   }
];
