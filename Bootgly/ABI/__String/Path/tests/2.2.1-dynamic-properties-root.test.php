<?php


use Bootgly\ABI\__String\Path;


return [
   // @ configure
   'describe' => 'It should return root dir',
   // @ simulate
   // ...
   // @ test
   'test' => function () {
      // @
      // ! Dir
      // ? Absolute
      // Valid - absolute dir (2 parts)
      $Path = new Path('/etc/php/');
      assert(
         assertion: $Path->root === '/',
         description: 'Path #11 - root: ' . $Path->root
      );
      // Valid - absolute dir (1 part)
      $Path = new Path('/etc/');
      assert(
         assertion: $Path->root === '/',
         description: 'Path #12 - root: ' . $Path->root
      );
      // Valid - absolute dir (0 part)
      $Path = new Path('/');
      assert(
         assertion: $Path->root === '/',
         description: 'Path #13 - root: ' . $Path->root
      );

      // ? Relative
      // Valid - relative dir (2 parts)
      $Path = new Path('etc/php/');
      assert(
         assertion: $Path->root === '',
         description: 'Path #21 - root: ' . $Path->root
      );
      // Valid - relative dir (1 part)
      $Path = new Path('etc/');
      assert(
         assertion: $Path->root === '',
         description: 'Path #22 - root: ' . $Path->root
      );
      // Valid - relative dir (0 part)
      $Path = new Path('');
      assert(
         assertion: $Path->root === '',
         description: 'Path #23 - root: ' . $Path->root
      );


      // ! File
      // ? Absolute
      // Valid - absolute file (2 parts)
      $Path = new Path('/var/test.php');
      assert(
         assertion: $Path->root === '/',
         description: 'Path #31 - root: ' . $Path->root
      );
      // Valid - absolute file (1 part)
      $Path = new Path('/test.php');
      assert(
         assertion: $Path->root === '/',
         description: 'Path #32 - root: ' . $Path->root
      );
      // Valid - absolute file without extension (2 parts)
      $Path = new Path('/var/test');
      assert(
         assertion: $Path->root === '/',
         description: 'Path #33 - root: ' . $Path->root
      );
      // Valid - absolute file without extension (1 part)
      $Path = new Path('/test');
      assert(
         assertion: $Path->root === '/',
         description: 'Path #34 - root: ' . $Path->root
      );

      return true;
   }
];
