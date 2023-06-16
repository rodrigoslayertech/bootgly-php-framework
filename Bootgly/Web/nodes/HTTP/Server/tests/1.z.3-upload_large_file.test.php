<?php
use Bootgly\API\Project;
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
   'response.length' => 3101873,
   'separators' => [
      'separator' => true
   ],

   // @ simulate
   // Server API
   'sapi' => function (Request $Request, Response $Response) : Response {
      $Project = new Project;
      $Project->vendor = 'Bootgly/';
      $Project->container = 'Web/';
      $Project->package = 'examples/';
      $Project->version = 'app/';

      $Project->construct();

      return $Response('statics/screenshot.gif')->upload(close: false);
   },
   // Client API
   'capi' => function () {
      return "GET /test/download/large_file/1 HTTP/1.0\r\n\r\n";
   },

   // @ test
   'test' => function ($response) : bool {
      // ! Asserts
      // @ Assert length of response
      $expected = 3101873;

      if (strlen($response) !== $expected) {
         Debugger::$labels = ['HTTP Response length:', 'Expected:'];
         debug(strlen($response), $expected);
         return false;
      }

      return true;
   },
   'except' => function () : string {
      return 'Response length of uploaded file by server is correct?';
   }
];
