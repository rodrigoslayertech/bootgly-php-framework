<?php
/*
 * --------------------------------------------------------------------------
 * Bootgly PHP Framework
 * Developed by Rodrigo Vieira (@rodrigoslayertech)
 * Copyright 2020-present
 * Licensed under MIT
 * --------------------------------------------------------------------------
 */

namespace Bootgly;


use Generator;


class Pipe
{
   // * Config
   // ! Stream
   public int $timeout;

   // * Data
   private array $pair;

   // * Meta
   #public string $data;


   public function __construct (? int $timeout = null, ? bool $blocking = false, ? array $pair = null)
   {
      // * Config
      // ! Stream
      $this->timeout = $timeout;

      // * Data
      $this->pair = $pair ?? stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);

      // * Meta
      #$this->data = '';


      // @ Set non-blocking to pipes
      // Read pipe
      stream_set_blocking($this->pair[0], $blocking);
      // Write pipe
      stream_set_blocking($this->pair[1], $blocking);
   }

   public function reading (int $length = 1024) : Generator
   {
      $read = [$this->pair[0]];
      $write = null;
      $except = null;

      // @ Read output from pair
      while (true) {
         try {
            $streams = @stream_select($read, $write, $except, $this->timeout);
         } catch (\Throwable) {
            $streams = false;
         }

         // @ Check result
         if ($streams === false) {
            break;
         } elseif ($streams === 0) {
            yield null;

            continue;
         }

         yield $this->read(length: $length);
      }

      yield false;
   }
   public function read (int $length = 1024) : string|false
   {
      try {
         $chunk = @fread($this->pair[0], $length);
      } catch (\Throwable) {
         $chunk = false;
      }

      if ($chunk === false) {
         // TODO check errors
      }

      return $chunk;
   }

   public function write (string $data, ? int $length = null) : int|false
   {
      try {
         $written = @fwrite($this->pair[1], $data, $length);
      } catch (\Throwable) {
         $written = false;
      }

      if ($written === false) {
         // TODO check errors
      }

      return $written;
   }

   public function __destruct ()
   {
      // @ Close the ends of the communication channel
      fclose($this->pair[0]);
      fclose($this->pair[1]);
   }
}
