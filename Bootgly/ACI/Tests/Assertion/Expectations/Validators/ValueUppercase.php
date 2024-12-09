<?php
/*
 * --------------------------------------------------------------------------
 * Bootgly PHP Framework
 * Developed by Rodrigo Vieira (@rodrigoslayertech)
 * Copyright 2023-present
 * Licensed under MIT
 * --------------------------------------------------------------------------
 */

namespace Bootgly\ACI\Tests\Assertion\Expectations\Validators;


use Bootgly\ACI\Tests\Asserting\Fallback;
use Bootgly\ACI\Tests\Assertion\Expectation\Validator;

/**
 * Validate if $actual is an uppercase string.
 */
class ValueUppercase extends Validator
{
   public function assert (mixed &$actual, mixed &$expected): bool
   {
      return is_string($actual) && strtoupper($actual) === $actual;
   }

   public function fail (mixed $actual, mixed $expected, int $verbosity = 0): Fallback
   {
      return new Fallback(
         'Failed asserting that %s is an uppercase string.',
         [
            'actual' => $actual
         ],
         $verbosity
      );
   }
}