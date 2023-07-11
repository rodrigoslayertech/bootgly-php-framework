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
   'request' => function () {
      // ...
      return <<<HTTP
      GET / HTTP/1.1\r
      Accept-Language: en-US, fr, es;q=0.8, de;q=0.5, pt-BR;q=0.2\r
      \r

      HTTP;
   },
   // Server API
   'response' => function (Request $Request, Response $Response) : Response {
      $language = $Request->language;

      return $Response(content: $language);
   },

   // @ test
   'test' => function ($response) : bool {
      $expected = <<<HTML_RAW
      HTTP/1.1 200 OK\r
      Server: Bootgly\r
      Content-Length: 5\r
      Content-Type: text/html; charset=UTF-8\r
      \r
      en-US
      HTML_RAW;

      // @ Assert
      if ($response !== $expected) {
         Debugger::$labels = ['HTTP Response:', 'Expected:'];
         debug(json_encode($response), json_encode($expected));
         return false;
      }

      return true;
   },
   'except' => function () : string {
      return 'Request not matched';
   }
];
