<?php
/*
 * --------------------------------------------------------------------------
 * Bootgly PHP Framework
 * Developed by Rodrigo Vieira (@rodrigoslayertech)
 * Copyright 2023-present
 * Licensed under MIT
 * --------------------------------------------------------------------------
 */

namespace Bootgly\WPI\Nodes\HTTP_Server_CLI\Decoders;


use Bootgly\WPI\Interfaces\TCP_Server_CLI\Packages;
use Bootgly\WPI\Nodes\HTTP_Server_CLI as Server;
use Bootgly\WPI\Nodes\HTTP_Server_CLI\Decoders;


class Decoder_Waiting extends Decoders
{
   // * Metadata
   private static int $decoded;
   // @ Request Body
   private static int $read;


   public static function decode (Packages $Package, string $buffer, int $size): int
   {
      // @ Get callbacks
      $Request = Server::$Request;
      $Body = $Request->Raw->Body;

      // @ Check if Request Body is waiting data
      if ($Body->waiting) {
         // * Metadata
         self::$decoded ??= time();
         self::$read ??= 0;

         // ? Valid HTTP Client Body Timeout
         /**
         * Validate if the client is sending the rest of the Content data 
         * within a 60-second interval.
         */
         if ((time() - self::$decoded) >= 60 && self::$read === $Body->downloaded) {
            Server::$Decoder = new Decoder_;
            return Server::$Decoder::decode($Package, $buffer, $size);
         }

         // @
         if ($Body->downloaded === null) {
            $Body->raw = \substr($buffer, $Body->position, $Body->length);
         }
         else {
            $Body->raw .= $buffer;
         }

         $Body->downloaded += $size;
         self::$read = $Body->downloaded;

         if ($Body->length > $Body->downloaded) {
            return 0;
         }

         $Body->waiting = false;

         return $Body->length;
      }

      Server::$Decoder = new Decoder_;
      return Server::$Decoder::decode($Package, $buffer, $size);
   }
}
