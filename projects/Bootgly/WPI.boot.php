<?php

use Bootgly\API\Project;

if (self::count() === 0) {
   self::autoboot(self::AUTHOR_DIR);

   // @ Select default WPI Project
   $Project = self::select('Bootgly.WPI.demo');

   if ($Project instanceof Project) {
      require $Project->path . 'index.php';
   }
}
