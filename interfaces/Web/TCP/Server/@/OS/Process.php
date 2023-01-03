<?php
/*
 * --------------------------------------------------------------------------
 * Bootgly PHP Framework
 * Developed by Rodrigo Vieira (@rodrigoslayertech)
 * Copyright 2020-present
 * Licensed under MIT
 * --------------------------------------------------------------------------
 */

namespace Bootgly\Web\TCP\_\OS;


use Bootgly\CLI\_\ {
   Logger\Logging
};
use Bootgly\Web\TCP\ {
   Server
};


class Process
{
   use Logging;


   public Server $Server;

   // * Meta
   // @ Id
   public static int $index;
   public static int $master;
   public static array $children = [];

   public static $pidFile = '/workspace/server.pid';


   public function __construct (Server &$Server)
   {
      $this->Server = $Server;

      // * Meta
      self::$master = posix_getpid();

      static::lock();
      static::saveMasterPid();
   }
   public function __get ($name)
   {
      switch ($name) {
         case 'id':
            return posix_getpid();

         case 'master':
            return $this->id === self::$master;
         case 'child':
            return $this->id !== self::$master;

         case 'level':
            return $this->master ? 'master' : 'child';

         case 'children':
            return count(self::$children);
      }
   }

   protected static function lock ($flag = \LOCK_EX)
   {
      static $file;

      $lock_file = $_SERVER['PWD'] . static::$pidFile . '.lock';

      $file = $file ? : \fopen($lock_file, 'a+');

      if ($file) {
         flock($file, $flag);

         if ($flag === \LOCK_UN) {
            fclose($file);

            $file = null;

            clearstatcache();

            if ( is_file($lock_file) ) {
               unlink($lock_file);
            }
         }
      }
   }

   // @ Signal
   public function installSignal ()
   {
      $signalHandler = [$this, 'handleSignal'];

      // ! Server
      // @ stop()
      pcntl_signal(SIGHUP, $signalHandler, false);  // 1
      pcntl_signal(SIGINT, $signalHandler, false);  // 2 (CTRL + C)
      pcntl_signal(SIGQUIT, $signalHandler, false); // 3
      pcntl_signal(SIGTERM, $signalHandler, false); // 15
      // @ pause()
      pcntl_signal(SIGTSTP, $signalHandler, false); // 20 (CTRL + Z)
      // @ resume()
      pcntl_signal(SIGUSR1, $signalHandler, false); // 10

      // ? @Info
      // @ $status
      pcntl_signal(SIGUSR2, $signalHandler, false); // 12

      // ! \Connection
      // @ $stats
      pcntl_signal(SIGIOT, $signalHandler, false);  // 6
      // @ $peers
      pcntl_signal(SIGIO, $signalHandler, false);   // 29

      // ignore
      pcntl_signal(SIGPIPE, SIG_IGN, false);
   }
   public function handleSignal ($signal)
   {
      #$this->log($signal . PHP_EOL);

      switch ($signal) {
         // ! Server
         // @ stop()
         case SIGHUP:  // 1
         case SIGINT:  // 2 (CTRL + C)
         case SIGQUIT: // 3
         case SIGTERM: // 15
            $this->Server->stop();
            break;
         // @ pause()
         case SIGTSTP: // 20 (CTRL + Z)
            $this->Server->pause();
            break;
         // @ resume()
         case SIGUSR1: // 10
            $this->Server->resume();
            break;
         // ? @Info
         // @ $status
         // Show info about status of server (uptime, ...)
         case SIGUSR2: // 12
            $this->Server->{'@status'};
            break;

         // ! \Connection
         // ? @Info
         // @ $peers
         // Show info of active remote accepted connections (remote ip + remote port, ...)
         case SIGIOT:  // 6
            $this->Server->Connection->{'@peers'};
            break;
         // @ $stats
         // Show stats of server socket connections (reads, writes, errors...)
         case SIGIO:   // 29
            $this->Server->Connection->{'@stats'};
            break;
      }
   }
   public function sendSignal (int $signal, bool $master = true, bool $children = true)
   {
      if ($master) {
         // Send signal to master process
         posix_kill(static::$master, $signal);
      }

      if ($children) {
         // Send signal to children process
         foreach (self::$children as $id) {
            posix_kill($id, $signal);
            usleep(100000); // Wait 0,1 s
         }
      }

      return true;
   }

   public function fork (int $workers)
   {
      $this->log("forking $workers workers... ", self::LOG_INFO_LEVEL);

      $script = $_SERVER['PWD'] . '/' . $_SERVER['PHP_SELF'];

      for ($i = 0; $i < $workers; $i++) {
         $pid = pcntl_fork();

         self::$children[$i] = $pid;

         if ($pid === 0) {
            // Child process
            self::$index = $i + 1; // Set child index

            cli_set_process_title('BootglyWebServer: child process (Worker #' . self::$index . ')');

            $this->Server->instance();

            $this->Server::$Event->add($this->Server->Socket, $this->Server::$Event::EVENT_READ, 'accept');
            $this->Server::$Event->loop();

            $this->Server->stop();
            #exit(1);
         } else if ($pid > 0) {
            // Master process
            cli_set_process_title("BootglyWebServer: master process ($script)");
         } else if ($pid === -1) {
            die("Could not fork process!"); 
         }
      }
   }

   // @ User
   protected static function getCurrentUser ()
   {
      $user_info = posix_getpwuid(posix_getuid());

      return $user_info['name'];
   }

   protected static function saveMasterPid () // Save process master id to file
   {
      if (file_put_contents($_SERVER['PWD'] . static::$pidFile, static::$master) === false) {
         throw new \Exception('Can not save pid to ' . $_SERVER['PWD'] . static::$pidFile);
      }
   }
}
