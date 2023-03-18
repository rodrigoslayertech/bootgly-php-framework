<?php
/*
 * --------------------------------------------------------------------------
 * Bootgly PHP Framework
 * Developed by Rodrigo Vieira (@rodrigoslayertech)
 * Copyright 2020-present
 * Licensed under MIT
 * --------------------------------------------------------------------------
 */

namespace Bootgly\CLI\Terminal\components;


use Bootgly\CLI;

use Bootgly\CLI\Escaping;
use Bootgly\CLI\Escaping\cursor;
use Bootgly\CLI\Escaping\text;

use Bootgly\CLI\Terminal;
use Bootgly\CLI\Terminal\Output;
use Bootgly\CLI\Terminal\components\Progress\Bar;


class Progress
{
   use Escaping;
   use cursor\Positioning;
   use cursor\Visualizing;
   use text\Modifying;


   private Output $Output;

   // * Config
   // @ Tick
   public float $ticks;
   public float $throttle;
   // @ Templating
   public string $template;
   // @ Precision
   public int $secondPrecision;
   public int $percentPrecision;

   // * Data
   // @ Tick
   public float $ticked;

   // * Meta
   // @ Cursor
   private int $row;
   // @ Timing
   private float $started;
   private float $rendered;
   private bool $finished;
   // @ Display
   private string $description; // TODO use CLI templating
   private float|string $percent;
   private float|string $elapsed;
   private float|string $eta;
   private float|string $rate;
   // @ Templating
   private array $tokens;

   public Bar $Bar;


   public function __construct (Output &$Output)
   {
      $this->Output = $Output;

      // * Config
      // @ Tick
      $this->ticks = 100;
      $this->throttle = 0.1;
      // @ Templating
      $this->template = <<<'TEMPLATE'
      @described;
      @ticked;/@ticks; [@bar;] @percent;%
      ⏱️ @elapsed;s - 🏁 @eta;s - 📈 @rate; loops/s
      TEMPLATE;
      // @ Precision
      $this->secondPrecision = 2;
      $this->percentPrecision = 1;

      // * Data
      // @ Tick
      $this->ticked = 0.0;

      // * Meta
      // @ Cursor
      $this->row = 0;
      // @ Timing
      $this->started = 0.0;
      $this->rendered = 0.0;
      $this->finished = false;
      // @ Display
      $this->description = ''; // TODO use CLI templating
      $this->percent = 0.0;
      $this->elapsed = 0.0;
      $this->eta = 0.0;
      $this->rate = 0.0;
      // @ Templating
      $this->tokens = ['@description;', '@ticked;', '@ticks;', '@bar;', '@percent;', '@elapsed;', '@eta;', '@rate;'];

      $this->Bar = new Bar($this);
   }
   public function __get ($name)
   {
      return $this->$name;
   }

   private function render ()
   {
      // @ Timing
      $this->rendered = microtime(true);

      // ! Templating
      // @ Prepare values
      // description
      // TODO use CLI templating
      if (strpos($this->template, '@description;') !== false) {
         $description = $this->description;
      }
      // ticked
      $ticked = $this->ticked;
      // ticks
      $ticks = (int) $this->ticks;
      // bar
      if (strpos($this->template, '@bar;') !== false) {
         $bar = $this->Bar->render();
      }

      $percent = number_format($this->percent, $this->percentPrecision, '.', '');
      $elapsed = number_format($this->elapsed, $this->secondPrecision, '.', '');
      $eta = number_format($this->eta, $this->secondPrecision, '.', '');
      $rate = number_format($this->rate, 0, '.', '');

      // @ Replace tokens by strings
      $output = strtr($this->template, [
         '@description;' => $description ?? '',
         '@ticked;' => $ticked,
         '@ticks;' => $ticks,
         '@bar;' => $bar ?? '',
         '@percent;' => $percent,
         '@elapsed;' => $elapsed,
         '@eta;' => $eta,
         '@rate;' => $rate
      ]);

      // @ Move cursor to line
      $this->Output->Cursor->moveTo(line: $this->row, column: 1);

      // @ Write to output
      $this->Output->write($output);
   }

   public function start ()
   {
      // * Meta
      $this->started = microtime(true);

      // @ Make vertical space for writing
      $lines = substr_count($this->template, "\n") + 1;
      $this->Output->write(str_repeat("\n", $lines + 1));
      $this->Output->Cursor->up($lines + 1);

      // @ Point cursor to the initial position to write
      $this->Output->Cursor->hide();
      $this->Output->Cursor->moveTo(column: 1);

      // @ Set the start time
      $this->rendered = microtime(true);

      // @ Format Template EOL
      $this->template = str_replace("\n", "   \n", $this->template);
      $this->template .= "   \n\n";

      // @ Get/Set the current Cursor position row
      $this->row = ($this->Output->Cursor->position['row'] ?? 0);

      $this->render();
   }

   public function tick (int $amount = 1)
   {
      $ticked = $this->ticked += $amount;
      $ticks = $this->ticks;

      if (microtime(true) - $this->rendered < $this->throttle) {
         return;
      }

      // @ Calculate
      // elapsed
      $elapsed = microtime(true) - $this->started;
      // percent
      if ($ticks > 0) {
         $percent = ($ticked / $ticks) * 100;
      } else {
         $percent = $ticked;
      }
      // eta
      if ($ticked > 0) {
         $eta = (($elapsed / $ticked) * $ticks) - $elapsed;
      } else {
         $eta = 0.0;
      }
      // rate
      if ($ticked > 0) {
         $rate = $ticked / $elapsed;
      } else {
         $rate = 0.0;
      }

      // @ Set
      $this->elapsed = $elapsed;
      $this->percent = $percent;
      $this->eta = $eta;
      $this->rate = $rate;

      $this->render();
   }

   public function describe (string $description)
   {
      if ($this->description === $description) {
         return;
      }

      $describedLength = strlen($this->description);

      if (strlen($description) < $describedLength) {
         $description = str_pad($description, $describedLength, ' ', STR_PAD_RIGHT);
      }

      $this->description = $description;
   }

   public function finish ()
   {
      if ($this->finished) {
         return;
      }

      $this->finished = true;

      // TODO Check whether the last rendered showed the completed progress (when using throttle).

      $this->Output->Cursor->show();
   }

   public function __destruct()
   {
      $this->finish();
   }
}
