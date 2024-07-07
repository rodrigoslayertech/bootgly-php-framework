<?php
/*
 * --------------------------------------------------------------------------
 * Bootgly PHP Framework
 * Developed by Rodrigo Vieira (@rodrigoslayertech)
 * Copyright 2023-present
 * Licensed under MIT
 * --------------------------------------------------------------------------
 */

namespace Bootgly\WPI\Modules\HTTP\Server\Response\Raw;


/**
 * @property array $preset
 * @property array $prepared
 * 
 * @property array $fields
 * @property string $raw
 * 
 * @property bool $sent
 * @property array $queued
 * @property int $built
 */
abstract class Header
{
   // * Config
   // Fields
   /** @var array<string> */
   protected array $preset;
   /** @var array<string> */
   protected array $prepared;

   // * Data
   /** @var array<string> */
   protected array $fields;
   protected string $raw;

   // * Metadata
   protected bool $sent;
   // Fields
   /** @var array<string> */
   protected array $queued;
   protected int $built;


   abstract public function clean (): void;

   /**
    * @param array<string> $fields
    */
   abstract public function prepare (array $fields): void;

   abstract public function get (string $name): string;

   abstract public function set (string $field, string $value): bool;
   abstract public function append (string $field, string $value = '', ?string $separator = ', '): void;
   abstract public function queue (string $field, string $value = ''): bool;

   abstract public function build (): bool;
}
