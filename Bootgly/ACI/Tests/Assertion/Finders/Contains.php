<?php
/*
 * --------------------------------------------------------------------------
 * Bootgly PHP Framework
 * Developed by Rodrigo Vieira (@rodrigoslayertech)
 * Copyright 2023-present
 * Licensed under MIT
 * --------------------------------------------------------------------------
 */

namespace Bootgly\ACI\Tests\Assertion\Finders;


use Bootgly\ACI\Tests\Assertion\Finder;


class Contains implements Finder
{
   public function compare (mixed &$actual, mixed &$expected): bool
   {
      return strpos((string) $actual, (string) $expected) !== false;
   }

   public function fail (mixed $actual, mixed $expected, int $verbosity = 0): array
   {
      return [
         'format' => 'Failed asserting that the string "%s" contains "%s".',
         'values' => [
            'actual' => $actual,
            'expected' => $expected
         ]
      ];
   }
}
