<?php
/*
 * --------------------------------------------------------------------------
 * Bootgly PHP Framework
 * Developed by Rodrigo Vieira (@rodrigoslayertech)
 * Copyright 2023-present
 * Licensed under MIT
 * --------------------------------------------------------------------------
 */

namespace Bootgly\WPI\Nodes;


use Bootgly\ABI\Debugging\Data\Throwables\Exceptions;
use Bootgly\ABI\IO\FS\File;

use Bootgly\ACI\Logs\Logger;

use Bootgly\ACI\Tests;
use Bootgly\ACI\Tests\Tester;

use Bootgly\API\Environments;
use Bootgly\API\Projects;
use Bootgly\API\Server as SAPI;

use const Bootgly\WPI;
use Bootgly\WPI\Endpoints\Servers\Modes;
use Bootgly\WPI\Interfaces\TCP_Client_CLI;
use Bootgly\WPI\Interfaces\TCP_Server_CLI;
use Bootgly\WPI\Modules\HTTP;
use Bootgly\WPI\Modules\HTTP\Server;
use Bootgly\WPI\Modules\HTTP\Server\Router;
use Bootgly\WPI\Nodes\HTTP_Server_CLI\Decoders\Decoder_;
use Bootgly\WPI\Nodes\HTTP_Server_CLI\Encoders\Encoder_;
use Bootgly\WPI\Nodes\HTTP_Server_CLI\Encoders\Encoder_Testing;
use Bootgly\WPI\Nodes\HTTP_Server_CLI\Request;
use Bootgly\WPI\Nodes\HTTP_Server_CLI\Response;


class HTTP_Server_CLI extends TCP_Server_CLI implements HTTP, Server
{
   // * Config
   // ...inherited from TCP_Server_CLI

   // * Data
   // ...inherited from TCP_Server_CLI

   // * Metadata
   // ...inherited from TCP_Server_CLI

   public static Request $Request;
   public static Response $Response;
   public static Router $Router;


   public function __construct (Modes $Mode = Modes::Monitor)
   {
      // * Config
      // ...inherited from TCP_Server_CLI

      // * Data
      // ...inherited from TCP_Server_CLI

      // * Metadata
      // ...inherited from TCP_Server_CLI


      // \
      parent::__construct();
      // * Config
      $this->socket = ($this->ssl !== null
         ? 'https'
         : 'http'
      );
      // @ Configure Logger
      $this->Logger = new Logger(channel: 'HTTP.Server.CLI');

      // . Request,Response,Router
      self::$Request = new Request;
      self::$Response ??= new Response;
      self::$Router = new Router(static::class);

      // . Decoders,Encoders
      self::$Decoder = new Decoder_;
      $this->Mode = $Mode;
      switch ($Mode) {
         case Modes::Test:
            self::$Encoder = new Encoder_Testing;
            break;
         default:
            self::$Encoder = new Encoder_;
      }

      $WPI = WPI;
      // # HTTP
      $WPI->Server = $this;
      $WPI->Response = &self::$Response;
      $WPI->Request = &self::$Request;
      $WPI->Router = &self::$Router;
   }

   /**
    * Configure the HTTP Server.
    *
    * @param string $host The host to bind the server to.
    * @param int $port Port to bind the server to.
    * @param int $workers Number of workers to spawn.
    * @param null|array<string> $ssl SSL configuration.
    *
    * @return self The HTTP Server instance, for chaining 
    */
   public function configure (
      string $host, int $port, int $workers, ?array $ssl = null
   ): self
   {
      parent::configure($host, $port, $workers, $ssl);

      if ($host === '0.0.0.0') {
         $this->domain ??= 'localhost';
      }

      // * Config
      $this->socket = ($this->ssl !== null
         ? 'https://'
         : 'http://'
      );

      return $this;
   }

   public static function boot (Environments $Environment): void
   {
      switch ($Environment) {
         case Environments::Test:
            try {
               self::$Encoder = new Encoder_Testing;

               // * Config
               $Suite_Bootstrap_File = new File(
                  BOOTGLY_ROOT_DIR . __CLASS__ . '/tests/@.php'
               );

               // ? Validate the existence of the bootstrap file
               if ($Suite_Bootstrap_File->exists === false) {
                  throw new \Exception('Validate the existence of the bootstrap file!');
               }

               // @ Reset Cache of Test boot file
               if (\function_exists('opcache_invalidate')) {
                  \opcache_invalidate($Suite_Bootstrap_File, true);
               }
               \clearstatcache(false, $Suite_Bootstrap_File);

               $files = (@require $Suite_Bootstrap_File)['tests'];

               SAPI::$tests[self::class] = Tests::list($files);

               // * Metadata
               SAPI::$Tests[self::class] = [];
               foreach (SAPI::$tests[self::class] as $index => $case) {
                  $Test_Case_File = new File(
                     BOOTGLY_ROOT_DIR . __CLASS__ . '/tests/' . $case . '.test.php'
                  );

                  // ?
                  if ($Test_Case_File->exists === false) {
                     continue;
                  }

                  // @ Reset Cache of Test case file
                  if (\function_exists('opcache_invalidate')) {
                     \opcache_invalidate($Test_Case_File, true);
                  }
                  \clearstatcache(false, $Test_Case_File);

                  // @ Load Test case from file
                  try {
                     $spec = require $Test_Case_File;
                  }
                  catch (\Throwable) {
                     $spec = null;
                  }

                  // @ Set Closure to SAPI Tests
                  SAPI::$Tests[self::class][] = $spec;
               }
            }
            catch (\Throwable $Throwable) {
               Exceptions::report($Throwable);
            }

            break;
         default:
            SAPI::$production = Projects::CONSUMER_DIR . 'Bootgly/WPI/HTTP_Server_CLI-1.SAPI.php';
            self::$Encoder = new Encoder_;

            SAPI::boot(reset: true, key: 'on.Request');
      }
   }

   protected static function test (TCP_Server_CLI $TCP_Server_CLI): bool
   {
      Logger::$display = Logger::DISPLAY_NONE;

      self::boot(Environments::Test);

      $TCP_Client_CLI = new TCP_Client_CLI;
      $TCP_Client_CLI->configure(
         host: ($TCP_Server_CLI->host === '0.0.0.0')
            ? '127.0.0.1'
            : $TCP_Server_CLI->host,
         port: $TCP_Server_CLI->port
      );
      $TCP_Client_CLI->on(
         // on Connection connect
         connect: static function ($Socket, $Connection) use ($TCP_Client_CLI) {
            Logger::$display = Logger::DISPLAY_MESSAGE;

            // @ Get test files
            $testFiles = SAPI::$tests[self::class] ?? [];

            $Tests = new Tester($testFiles);
            $Tests->separate('HTTP Server');

            // @ Run test cases
            foreach ($testFiles as $index => $value) {
               /**
                * @var array<string>|null $spec
                */
               $spec = SAPI::$Tests[self::class][$index] ?? null;

               // @ Init Test
               $Test = $Tests->test($spec);
               if ($Test === false) {
                  continue;
               }

               if ($spec === null || count($spec) < 3) {
                  $Tests->skip();

                  continue;
               }

               // ! Server
               $responseLength = @$spec['response.length'] ?? null;
               // ! Client
               // ? Request
               $requestData = $spec['request']($TCP_Client_CLI->host . ':' . $TCP_Client_CLI->port);
               $requestLength = strlen($requestData);
               // @ Send Request to Server
               $Connection::$output = $requestData;

               if ( ! $Connection->writing($Socket, $requestLength) ) {
                  $Test->fail();
                  break;
               }

               // ? Response
               $timeout = 2;
               $input = '';
               // @ Get Response from Server
               if ( $Connection->reading($Socket, $responseLength, $timeout) ) {
                  $input = $Connection::$input;
               }

               // @ Execute Test
               $Test->test($input);
               // @ Output Test result
               if (! $Connection->expired && $Test->passed) {
                  $Test->pass();
               }
               else {
                  $Test->fail();
                  break;
               }
            }

            $Tests->summarize();

            // @ Reset CLI Logger
            Logger::$display = Logger::DISPLAY_MESSAGE;

            // @ Destroy Client Event Loop
            $TCP_Client_CLI::$Event->destroy();
         }
      );
      $TCP_Client_CLI->start();

      return true;
   }
}
