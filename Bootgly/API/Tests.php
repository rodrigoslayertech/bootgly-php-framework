<?php
/*
 * --------------------------------------------------------------------------
 * Bootgly PHP Framework
 * Developed by Rodrigo Vieira (@rodrigoslayertech)
 * Copyright 2023-present
 * Licensed under MIT
 * --------------------------------------------------------------------------
 */

namespace Bootgly\API;


use Bootgly\API\Logs\Escaped\Loggable;


abstract class Tests
{
   use Loggable;


   // * Config
   // ...

   // * Data
   public array $tests;
   public array $specifications;

   // * Meta
   public int $failed;
   public int $passed;
   public int $skipped;
   // @ Stats
   public int $total;
   // @ Time
   public float $started;
   public float $finished;
   // @ Screen?
   public int $width;


   abstract public function __construct (array &$tests);

   abstract public function test (? array &$specifications) : object|false;

   public function summarize ()
   {
      $failed = $this->failed;
      $passed = $this->passed;
      $skipped = $this->skipped;
      // @ Stats
      $total = $this->total;
      // @ Time
      $started = $this->started;
      $finished = $this->finished = microtime(true);

      // @ Benchmark Tests time
      $duration = number_format(round($finished - $started, 5), 6);

      $this->log(<<<TESTS
      
      Tests: @:e:{$failed} failed @;, @:n:{$skipped} skipped @;, @:s:{$passed} passed @;, {$total} total
      Duration: \033[1;35m{$duration}s \033[0m
      \033[90mRan all tests.\033[0m
      \n
      TESTS);
   }
}
