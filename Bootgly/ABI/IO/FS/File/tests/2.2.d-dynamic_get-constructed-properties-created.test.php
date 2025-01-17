<?php

use Bootgly\ABI\IO\FS\File;


return [
   // @ configure
   'describe' => '',
   // @ simulate
   // ...
   // @ test
   'test' => function () {
      // @ Valid
      $File1 = new File(__DIR__ . '/1.1-construct-real_file.test.php');
      yield assert(
         assertion: is_int($File1->created),
         description: 'File #1 - should have created value!'
      );

      // @ Neutral
      $File2 = new File('');
      yield assert(
         assertion: $File2->created === null,
         description: 'File #2 - empty path - created should be null'
      );

      // @ Invalid
      $File3 = new File(__DIR__ . '/1.1.3-fake.test.php');
      yield assert(
         assertion: $File3->created === null,
         description: 'File #3 - fake file - created should be null'
      );
   }
];
