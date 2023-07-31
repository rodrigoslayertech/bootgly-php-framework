<?php
/*
 * --------------------------------------------------------------------------
 * Bootgly PHP Framework
 * Developed by Rodrigo Vieira (@rodrigoslayertech)
 * Copyright 2023-present
 * Licensed under MIT
 * --------------------------------------------------------------------------
 */

namespace Bootgly\CLI\Terminal\components\Menu\Items\extensions\Headers;


use Bootgly\CLI\Terminal\components\Menu\Item;


final class Header extends Item
{
   // * Config
   // ...

   // * Data
   public string $header;

   // * Meta
   // ...


   public function __construct (string $characters = '')
   {
      parent::__construct();

      // * Data
      $this->header = $characters;
   }
}
