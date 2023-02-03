<?php
/*
 * --------------------------------------------------------------------------
 * Bootgly PHP Framework
 * Developed by Rodrigo Vieira (@rodrigoslayertech)
 * Copyright 2020-present
 * Licensed under MIT
 * --------------------------------------------------------------------------
 */

namespace Bootgly\Web\HTTP\Server\Request\_;


class Content
{
   // * Config
   // -
   // * Data
   public string $raw;
   public string $input;
   // * Meta
   public ? int $length;
   public null|int|false $position;
   public ? int $downloaded;
   public bool $waiting;


   public function __construct ()
   {
      // * Config
      // -
      // * Data
      $this->raw = '';
      $this->input = PHP_SAPI === 'cli' ? '' : file_get_contents('php://input');
      // * Meta
      $this->length = null;
      $this->position = null;
      $this->downloaded = null;
      $this->waiting = false;
   }

   public function parse (string $content = 'raw', string $type) : bool|string
   {
      switch ($content) {
         case 'Form-data':
            // @ Parse Form-data (boundary)
            $matched = preg_match('/boundary="?(\S+)"?/', $type, $match);

            if ($matched === 1) {
               $boundary = trim('--' . $match[1], '"');

               return $boundary;
            }

            return false;
         case 'raw':
            // @ Check if Content downloaded length is minor than Content length
            if ($this->downloaded < $this->length) {
               $this->waiting = true;
               return 0;
            }

            $this->waiting = false;

            switch ($type) {
               // @ Parse Raw - JSON
               case 'application/json':
                  $_POST = (array) json_decode($this->raw, true);

                  return true;
               // @ Parse Raw - URL Encoded (x-www-form-urlencoded)
               case 'application/x-www-form-urlencoded':
                  $this->input = $this->raw;

                  parse_str($this->raw, $_POST);

                  return true;
               default: // @ Set Input Raw: text, binary, etc.
                  $this->input = $this->raw;
            }
      }

      return false;
   }
}
