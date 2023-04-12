<?php
/*
 * --------------------------------------------------------------------------
 * Bootgly PHP Framework
 * Developed by Rodrigo Vieira (@rodrigoslayertech)
 * Copyright 2020-present
 * Licensed under MIT
 * --------------------------------------------------------------------------
 */

namespace Bootgly\CLI\Terminal\components\Menu\Items;


use Bootgly\CLI\Terminal\components\Menu\ {
   Menu
};


class Items
{
   private Menu $Menu;

   // * Config
   // @ Selection
   /**
    * Items Selection is Unique or Multiple?
    */
   public Selection $Selection;
   /**
    * Items are selectable?
    */
   public bool $selectable;
   /**
    * Items are deselectable?
    */
   public bool $deselectable;
   // @ Display
   /**
    * Items Orientation is Vertical or Horizontal?
    */
   public Orientation $Orientation;

   // * Data
   #public array $deselectables;
   #public array $selectables;
   public array $items;

   // * Meta
   // @ Pointer
   public int $aimed;
   // @ Selection
   public array $selected;


   public function __construct (Menu &$Menu)
   {
      $this->Menu = $Menu;

      // * Config
      // @ Selection
      $this->selectable = true;
      $this->deselectable = true;
      $this->Selection = Selection::Multiple->set();
      // @ Display
      $this->Orientation = Orientation::Horizontal->set();


      // * Data
      $this->items = [];

      // * Meta
      $this->aimed = 0;
      $this->selected = [];
   }
   public function __get ($name)
   {
      return match ($name) {
         'count' => count($this->items),
         default => null
      };
   }

   public function set (array $items)
   {
      $this->items = $items;
   }
   public function add (string $label)
   {
      $this->items[] = $label;

      return $this;
   }

   // @ Aiming
   public function regress ()
   {
      if ($this->aimed > 0) {
         $this->aimed--;
      }
   }
   public function advance ()
   {
      if ($this->aimed < count($this->items) - 1) {
         $this->aimed++;
      }
   }

   // @ Selecting
   private function select ($index)
   {
      if ($this->selectable) {
         $this->selected[] = $index;
      }
   }
   private function deselect ($index)
   {
      if ($this->deselectable) {
         $this->selected = array_diff($this->selected, [$index]);
      }
   }
   private function toggle ($index)
   {
      if ( in_array($index, $this->selected) ) {
         $this->deselect($index);
      } else {
         $this->select($index);
      }
   }
   private function iterate ()
   {
      // @ Select / Unselect current item
      $index = 0;
      foreach ($this->items as $key => $value) {
         if ($this->aimed === $index) {
            $this->toggle($index);
         } else if ($this->Selection->get() === $this->Selection::Unique) {
            $this->deselect($index);
         }

         $index++;
      }
   }

   public function control (string $char) : bool
   {
      $continue = true;

      switch ($char) {
         // \x1b \e \033
         // @ Aiming
         case "\e[D": // Left Key
         case "\e[A": // Up Key
            $this->regress();
            break;
         case "\e[C": // Right Key
         case "\e[B": // Down Key
            $this->advance();
            break;

         // @ Selecting
         case ' ': // Space Key
            $this->iterate();
            break;

         case PHP_EOL: // Enter Key
            $continue = false;
            break;

         default:
            break;
      }

      return $continue;
   }

   public function render ()
   {
      $index = 0;

      // @ Write each Menu item
      foreach ($this->items as $key => $value) {
         // * Config
         // @ Display
         $Orientation = $this->Orientation->get();
         if ($Orientation === $Orientation::Vertical) {
            $divisor = "\n";
         } else {
            $divisor = ' ';
         }

         // * Data
         // item
         $prepend = '';
         $append = '';
         $item = '';
         if ( is_array($value) ) {
            $item = $key;
            $prepend = $value['prepend'] ?? '';
            $append = $value['append'] ?? '';
         } else {
            $item = $value;
         }

         // * Meta
         // @ Pointer
         // Aimed prepend / append
         $aimed = [];
         if ($this->aimed === $index) {
            $aimed[0] = '=>';
            $aimed[1] = '';
         } else {
            $aimed[0] = '  ';
            $aimed[1] = '';
         }
         // @ Selection
         // Selected prepend / append
         $selected = [];
         if ( in_array($index, $this->selected) ) {
            $selected[0] = '[X]';
            $selected[1] = '';
         } else {
            $selected[0] = '[ ]';
            $selected[1] = '';
         }

         // @
         $this->Menu->Output->render(<<<OUTPUT
         {$aimed[0]} {$selected[0]} {$prepend}$item{$append}{$selected[1]} {$aimed[1]} {$divisor}
         OUTPUT);

         // ...
         $index++;
      }
   }
}


// * Configs
// @ Selection
enum Selection
{
   use \Bootgly\Set;


   case Unique;
   case Multiple;
}
// @ Display
enum Orientation
{
   use \Bootgly\Set;


   case Vertical;
   case Horizontal;
}
