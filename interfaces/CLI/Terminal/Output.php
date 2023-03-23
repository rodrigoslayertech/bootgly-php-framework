<?php
/*
 * --------------------------------------------------------------------------
 * Bootgly PHP Framework
 * Developed by Rodrigo Vieira (@rodrigoslayertech)
 * Copyright 2020-present
 * Licensed under MIT
 * --------------------------------------------------------------------------
 */

namespace Bootgly\CLI\Terminal;


use Bootgly\CLI;

use Bootgly\CLI\Terminal\Output\Cursor;
use Bootgly\CLI\Terminal\Output\Text;
use Bootgly\CLI\Terminal\Output\Viewport;


class Output
{
   // * Config
   // @ Delay
   public int $wait;
   public int $waiting;

   // * Data
   public $stream;

   // * Meta
   // @ Stats
   public int|false $written;


   public Cursor $Cursor;
   public Text $Text;
   public Viewport $Viewport;


   public function __construct ($stream = STDOUT)
   {
      // * Config
      // @ Delay
      $this->wait = -1;       // @ to write method
      $this->waiting = 50000; // @ to writing method

      // * Data
      $this->stream = $stream;

      // * Meta
      // @ Stats
      $this->written = 0;


      $this->Cursor = new Cursor($this);
      $this->Text = new Text($this);
      $this->Viewport = new Viewport($this);
   }

   public function reset ()
   {
      $this->__construct();
   }
   public function expand (int $lines) : self
   {
      if ($lines > 0) {
         $this->Viewport->panDown($lines); // @ use EOL instead of pan down?
         $this->Cursor->up($lines);
      }

      return $this;
   }

   public function write (string $text, int $times = 1) : self
   {
      // * Config
      $wait = $this->wait;
      // * Data
      $stream = &$this->stream;

      do {
         $this->written = fwrite($stream, $text);

         if ($wait > 0) {
            usleep($wait);
         }

         $times--;
      } while ($times > 0);

      return $this;
   }
   public function writing (string $text) : self
   {
      // * Config
      $waiting = $this->waiting;
      // * Data
      $stream = $this->stream;
      // * Meta
      $written = 0;

      $parts = str_split($text);
      foreach ($parts as $part) {
         $written += fwrite($stream, $part);

         if ($waiting > 0) {
            usleep($waiting);
         }
      }

      $this->written += $written;

      return $this;
   }
   public function escape (string $code) : self
   {
      fwrite($this->stream, CLI::_START_ESCAPE . $code);

      return $this;
   }
   public function metaescape (string $command) : self
   {
      fwrite($this->stream, escapeshellcmd($command));

      return $this;
   }
   public function append (string $text) : self
   {
      $this->written = fwrite($this->stream, $text . PHP_EOL);

      return $this;
   }
}
