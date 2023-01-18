<?php
/*
 * --------------------------------------------------------------------------
 * Bootgly PHP Framework
 * Developed by Rodrigo Vieira (@rodrigoslayertech)
 * Copyright 2020-present
 * Licensed under MIT
 * --------------------------------------------------------------------------
 */

namespace Bootgly\Web\TCP\Server\Connections;


use Bootgly\OS\Process\Timer;
use Bootgly\Web\TCP\Server;
use Bootgly\Web\TCP\Server\Connections;


class Connection
{
   public $Socket;

   // * Config
   public array $timers;
   public int $expiration;
   // * Data
   public string $ip;
   public int $port;
   // * Meta
   public int $id;
   // @ Status
   const STATUS_INITIAL = 0;
   const STATUS_CONNECTING = 1;
   const STATUS_ESTABLISHED = 2;
   const STATUS_CLOSING = 4;
   const STATUS_CLOSED = 8;
   public int $status;
   // @ Handler
   public int $started;
   public int $used;
   // @ Stats
   #public int $reads;
   public int $writes;


   public function __construct (&$Socket)
   {
      $this->Socket = $Socket;

      $peer = stream_socket_get_name($Socket, true);
      @[$ip, $port] = explode(':', $peer, 2); // TODO IPv6

      // * Config
      $this->timers = [];
      $this->expiration = 15;
      // * Data
      $this->ip = $ip;
      $this->port = $port;
      // * Meta
      $this->id = (int) $Socket;
      // @ Status
      $this->status = self::STATUS_ESTABLISHED;
      // @ Handler
      $this->started = time();
      $this->used = time();
      // @ Stats
      #$this->reads = 0;
      $this->writes = 0;

      #$context = stream_context_get_options($Socket);
      #if ( isSet($context['ssl']) && $this->handshake() === false)
      #   return false;

      // @ Set Connection timeout expiration
      $this->timers[] = Timer::add(
         interval: $this->expiration,
         handler: [$this, 'expire'],
         args: [$this->expiration]
      );
      /*
      // @ Set Connection limit
      $this->timers[] = Timer::add(
         interval: 5,
         handler: [$this, 'limit'],
         args: [1000]
      );
      */
   }

   public function handshake ()
   {
      try {
         $negotiation = @stream_socket_enable_crypto(
            $this->Socket,
            true,
            STREAM_CRYPTO_METHOD_SSLv2_SERVER |
            STREAM_CRYPTO_METHOD_SSLv23_SERVER |
            STREAM_CRYPTO_METHOD_TLSv1_1_SERVER |
            STREAM_CRYPTO_METHOD_TLSv1_2_SERVER |
            STREAM_CRYPTO_METHOD_TLSv1_3_SERVER
         );
      } catch (\Throwable) {
         $negotiation = -1;
      }

      // @ Check negotiation
      if ($negotiation === false) {
         $this->close();
         return false;
      } elseif ($negotiation === 0) {
         // TODO Need try again
         return 0;
      }

      return true;
   }

   public function check () : bool
   {
      // @ Check blacklist
      // Blocked IP
      if ( isSet(Connections::$blacklist[$this->ip]) ) {
         // TODO add timer to unblock
         return false;
      }

      return true;
   }
   public function expire (int $timeout = 5) 
   {
      static $writes = 0;

      if ($this->status === self::STATUS_CLOSED) {
         return true;
      }

      if ($writes < $this->writes) {
         $this->used = time();
      }

      if (time() - $this->used >= $timeout) {
         return $this->close();
      }

      $writes = $this->writes;

      return false;
   }
   public function limit (int $packages)
   {
      static $writes = 0;

      if ($this->status === self::STATUS_CLOSED) {
         return true;
      }

      if (($this->writes - $writes) >= $packages) {
         Connections::$blacklist[$this->ip] = true;
         return $this->close();
      }

      $writes = $this->writes;

      return false;
   }

   public function close ()
   {
      Server::$Event->del($this->Socket, Server::$Event::EVENT_READ);
      Server::$Event->del($this->Socket, Server::$Event::EVENT_WRITE);

      if ($this->Socket === null || $this->Socket === false) {
         #$this->log('$Socket is false or null on close!');
         return false;
      }

      $closed = false;
      try {
         $closed = @fclose($this->Socket);
      } catch (\Throwable) {}

      if ($closed === false) {
         #$this->log('Connection failed to close!' . PHP_EOL);
         return false;
      }

      // @ On success
      $this->status = self::STATUS_CLOSED;
      // Delete timers
      foreach ($this->timers as $id) {
         Timer::del($id);
      }
      // Destroy itself
      unset(Connections::$Connections[$this->id]);

      return true;
   }

   public function __destruct ()
   {
      // Delete timers
      foreach ($this->timers as $id) {
         Timer::del($id);
      }
   }
}