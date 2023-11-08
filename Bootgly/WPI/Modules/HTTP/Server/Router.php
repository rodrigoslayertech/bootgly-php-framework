<?php
/*
 * --------------------------------------------------------------------------
 * Bootgly PHP Framework
 * Developed by Rodrigo Vieira (@rodrigoslayertech)
 * Copyright 2023-present
 * Licensed under MIT
 * --------------------------------------------------------------------------
 */

namespace Bootgly\WPI\Modules\HTTP\Server;


use Bootgly;

use Bootgly\ABI\IO\FS\File;

use Bootgly\WPI\Modules\HTTP\Server\Route;


class Router
{
   public static string $Server;

   // * Config
   // ...

   // * Data
   // @ Status
   protected bool $active;

   // * Meta
   public ? Route $Route;
   // @ Stats
   private int $routes;
   private int $matched; // 0 -> none; 1 = path; 2 = path and method(s)
   // @ History
   private array $routeds;


   public function __construct (string $Server)
   {
      self::$Server = $Server;

      // * Config
      // ...

      // * Data
      // @ Status
      $this->active = true;

      // * Meta
      $this->Route = new Route;
      // @ Stats
      $this->routes = 0;
      #$this->matched = 0;
      // @ History
      $this->routeds = [];
   }

   public function boot (string|array $instances = ['routes'])
   {
      $Request = self::$Server::$Request;

      $Route = &$this->Route;
      $Router = &$this;

      $boot = Bootgly::$Project->path . 'router/';

      $Index = new File($boot . 'index.php');
      if ($Index->exists) {
         (static function (string $__default__)
            use ($Request, $Router, $Route) {
            include_once $__default__;
         })($Index->file);
      }

      $instances = (array) $instances;
      foreach ($instances as $instance) {
         $Instance = new File($boot . $instance . '.php');

         if ($Instance->exists) {
            (static function (string $__routes__)
               use ($Request, $Router, $Route) {
               @include_once $__routes__;
            })($Instance->file);
         }
      }
   }
   public function pause ()
   {
      $this->active = false;
   }
   public function continue ()
   {
      $this->active = true;
   }

   // @ default
   public function route (
      string $route,
      callable $handler,
      null|string|array $methods = null
   ) : false|object
   {
      $Route = &$this->Route;

      $routed = 0;

      // @ Check
      if ($this->active === false) {
         return false;
      }
      if ($Route->nested && $route[0] === '/') {
         throw new \Exception('Nested route path must be relative!');
      }

      // ! Route Methods
      // @ Match
      if (empty($methods) || \in_array(self::$Server::$Request->method, (array) $methods)) {
         $routed = 1;
      }

      // ! Route Route
      // @ Boot
      if ($routed === 1) {
         $route = ($route === '/'
            ? ''
            : \rtrim($route, '/')
         );
         $Route->path = $route;

         // @ Match
         $routed = match (true) {
            $route === self::$Server::$Request->URL,
            // Not Matched Route (nested level)
            $Route->nested && $route === '*',
            // Not Matched Route (root level)
            $route === '/*',
               => 2,
            default => $this->match($route)
         };
      }

      // ! Route Callback
      if ($routed === 2) {
         // @ Prepare
         // Route Params values
         if ($Route->parameterized) {
            // @ HTTP Server Request
            // ->Path
            $parts = self::$Server::$Request->Path->parts;
            // @ Router Route
            $Params = &$Route->Params;

            foreach ($Params as $param => $value) {
               if ( \is_int($value) ) {
                  $Params->$param = @$parts[$value - 1];
               }
               else if ( \is_array($value) ) {
                  foreach ($value as $index => $location) {
                     $Params->$param[$index] = @$parts[$location - 1];
                  }
               }
            }
         }

         // @ Call
         // Closure
         if ($handler instanceof \Closure) {
            $handler = $handler->bindTo($Route, $Route);

            $Response = $handler(
               self::$Server::$Request,
               self::$Server::$Response
            );
         }
         else {
            // callable
            $Response = \call_user_func_array(
               callback: $handler,
               args: [
                  self::$Server::$Request,
                  self::$Server::$Response,
                  $Route
               ]
            );
         }

         // @ Log
         $this->routes++;
         $Route::$level++;
         $this->routeds[$route] = [
            $handler
         ];

         // @ Reset
         if ($Response instanceof \Generator) {
            // Route nested
         }
         else if ($Response && $Response !== self::$Server::$Response) {
            self::$Server::$Response = $Response;
         }
         $Route->nested = false;

         return $Response;
      }

      return false;
   }
   public function routing (\Generator $Routes)
   {
      foreach ($Routes as $Response) {
         if ($Response instanceof \Generator) {
            $this->Route->nested = true;
            yield from $this->routing($Response);
            break;
         } else if ($Response !== false) {
            #$this->Route->nested = false;
            $this->Route = new Route;
            yield $Response;
            break;
         } else {
            #$this->Route->nested = false;
            #$this->Route = new Route;
            yield $Response;
         }
      }
   }

   private function match (string $route) : int
   {
      $Route = &$this->Route;

      if ($Route->parameterized) {
         $this->parse($route); // @ Set $Route->parsed and $Route->catched

         if ($Route->parsed) {
            // $pattern
            $pattern = '/^' . $Route->parsed . '$/m';
            // $subject
            if ($Route->catched && $Route->catched !== '(.*)') {
               $subject = $Route->catched;
               $Route->catched = '';
            }
            else {
               $subject = self::$Server::$Request->URL;
            }
            // @
            \preg_match($pattern, $subject, $matches);

            if ($Route->catched === '(.*)') {
               // TODO check if matches[1] is null
               $Route->catched = @$matches[1];
            }

            if (@$matches[0]) {
               return 2;
            }
         }

         return 0;
      }

      if ($Route->nested) {
         if ($route === $Route->catched) {
            return 2;
         }

         return 0;
      }

      return 0;
   }
   public function parse (string $route) // @ Parse Route Path (Parameterized)
   {
      if ($this->active === false) return '';

      $Route = &$this->Route;

      // ! Prepare Route Path
      // ? Route path
      $paths = \explode('/', \str_replace("/", "\/", $Route->path));
      // ? Request path (full | relative)
      // Get catched path instead of Request path
      if ($Route->catched) {
         $locations = \explode('/', \str_replace("/", "\/", $Route->catched));
      }
      else {
         $locations = \explode('/', \str_replace("/", "\/", self::$Server::$Request->URL));
      }
      // ? Route Path Node replaced by Regex
      $regex_replaced = [];

      // ! Reset Route->parsed
      if ($Route->nested) {
         $Route->parsed = '';
      }
      else {
         $Route->parsed = '\\';
      }

      foreach ($paths as $index => $node) {
         if ($index > 0 || $node !== '\\') {
            if (@$node[-1] === '*' || @$node[-2] === '*') { //? Catch-All Param
               $node = \str_replace(':*', '(.*)', $node); //? Replace with (...) capture everything enclosed
               $Route->catched = '(.*)';
               // TODO error if detected next node after Catch-All param?
            }
            else if (@$node[0] === ':') { //? Param
               $param = \trim($node, ':\\'); //? Get Param name
               // TODO get param name with In-URL Regex
               // TODO validate all $param name 😓 - only accept a-z, A-Z, 0-9 and "-"?

               //? In-URL Regex
               // ! BAD IDEA?! 🤔
               if (@$node[-1] === ')') {
                  // TODO validate In-URL Regex 🥵 - only accept this characters -> "azdDw-[]^|" ???
                  $params = \explode('(', \rtrim($param, ')'));
                  $param = $params[0];

                  $Route->Params->$param = $params[1]; // @ Set Param Regex

                  $Route->path = \str_replace('(' . $params[1] . ')', '', $Route->path);
               }

               // ? Param without Regex
               if ($Route->Params->$param === null) {
                  // TODO Review Regex to all cases to found the best Regex
                  $Route->Params->$param = '(.*)\/?(.*)';
               }

               if (\is_string($Route->Params->$param)) {
                  $node = \str_replace(':' . $param, $Route->Params->$param, $node);
                  // @ Store values to use in equals params
                  $regex_replaced[$param] = $Route->Params->$param;

                  // @ Replace param by $index + nodes parsed
                  $Route->Params->$param = $index + $Route->nodes;
               } else {
                  $node = \str_replace(':' . $param, $regex_replaced[$param], $node);
                  $Route->Params->$param = (array) $Route->Params->$param;
                  $Route->Params->$param[] = $index + $Route->nodes;
               }
            }
            else if (@$locations[$index] !== $node) {
               $Route->parsed = false;
               break;
            }

            if ($index > 0) {
               $Route->parsed .= '/';
            }

            $Route->parsed .= $node;

            if ($Route->catched === '(.*)') break;

            // TODO FIX BUG
            if ($Route->path[-1] === '*' || $Route->path[-2] === '*' || $Route->catched) {
               $Route->nodes++;
            }
         }
      }

      return $Route->parsed;
   }
}
