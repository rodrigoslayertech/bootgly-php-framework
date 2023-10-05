<?php
/*
 * --------------------------------------------------------------------------
 * Bootgly PHP Framework
 * Developed by Rodrigo Vieira (@rodrigoslayertech)
 * Copyright 2023-present
 * Licensed under MIT
 * --------------------------------------------------------------------------
 */

namespace Bootgly\ABI\Debugging;


abstract class Exceptions
{
   // * Data
   protected static array $exceptions = [];


   public static function collect (\Error|\Exception $E)
   {
      self::$exceptions[] = $E;
   }

   public static function dump (...$data)
   {
      // ...
   }
}
