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
use Bootgly\CLI\Template;
use Bootgly\CLI\Terminal;
use Bootgly\CLI\Terminal\Output\Cursor;
use Bootgly\CLI\Terminal\Output\Text;
use Bootgly\CLI\Terminal\Output\Viewport;


class Output
{
   // * Config
   public $stream;
   // @ Delay
   public int $wait;
   public int $waiting;

   // * Data
   public string $text;

   // * Meta
   // @ Stats
   public int|false $written;


   public Cursor $Cursor;
   public Text $Text;
   public Viewport $Viewport;


   public function __construct ($stream = STDOUT)
   {
      // * Config
      $this->stream = $stream;
      // @ Delay
      $this->wait = -1;       // @ to write method
      $this->waiting = 30000; // @ to writing method

      // * Data
      $this->text = '';

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
      if ($lines <= 0) {
         return $this;
      }

      // @ Cursor
      // position
      $final = $this->Cursor->position['row'] + $lines;

      if ($final < Terminal::$height) {
         return $this;
      }

      $this->Viewport->panDown($lines);

      $this->Cursor->up($lines);

      return $this;
   }

   // @ Raw
   public function write (string $data, int $times = 1) : self
   {
      // * Config
      $stream = &$this->stream;
      // @ Delay
      $wait = $this->wait;

      // * Data
      // ...


      do {
         $this->written = fwrite($stream, $data);

         if ($wait > 0) {
            usleep($wait);
         }

         $times--;
      } while ($times > 0);

      return $this;
   }
   public function writing (string $data) : self
   {
      // * Config
      $stream = $this->stream;
      // @ Delay
      $waiting = $this->waiting;

      // * Data
      // ...

      // * Meta
      $written = 0;


      $parts = str_split($data);
      foreach ($parts as $part) {
         $written += fwrite($stream, $part);

         if ($waiting > 0) {
            usleep($waiting);
         }
      }

      $this->written += $written;

      return $this;
   }

   public function append (string $data) : self
   {
      $this->written = fwrite($this->stream, $data . PHP_EOL);

      return $this;
   }

   // @ ANSI Code
   public function escape (string $data) : self
   {
      fwrite($this->stream, CLI::_START_ESCAPE . $data);

      return $this;
   }
   public function metaescape (string $data) : self
   {
      fwrite($this->stream, escapeshellcmd($data));

      return $this;
   }

   public function render (string $data) : self
   {
      $text = Template::render($data);

      fwrite($this->stream, $text);

      return $this;
   }
}
