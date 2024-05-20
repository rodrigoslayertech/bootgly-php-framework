<?php

namespace Bootgly\WPI\Interfaces\TCP_Server_CLI\Commands;


use Bootgly\ABI\Data\__String\Path;

use Bootgly\API\Server as SAPI;

use const Bootgly\CLI;
use Bootgly\CLI\Command;
use Bootgly\CLI\UI\Fieldset\Fieldset;
use Bootgly\CLI\UI\Progress\Progress;
use Bootgly\WPI\Endpoints\Servers\Modes;
use Bootgly\WPI\Interfaces\TCP_Server_CLI as Server;


return new class extends Command
{
   // * Config
   public string $name = 'status';
   public string $description = 'Show server status';
   // * Metadata
   private static int $stat = -1;
   private static array $stats = [];


   public function run (array $arguments = [], array $options = []) : bool
   {
      // !
      // ** @var \Closure $context
      $context = $this->context;

      // * Metadata
      $stat = self::$stat;
      $stats = self::$stats;

      // @
      $context(function ()
      use ($stat, $stats) {
         /** @var Server $Server */
         $Server = $this; /** @phpstan-ignore-line */

         $Output = CLI->Terminal->Output;
         if ($Server->Mode === Modes::Monitor) {
            $Output->clear();
            $Output->render('>_ Type `@#Green:CTRL + Z@;` to enter in Interactive mode or `@#Green:CTRL + C@;` to stop the Server.@..;');
         }
   
         // ! Server
         // @
         $server = (new \ReflectionClass($Server))->getName();
         $version = $Server::VERSION;
         $php = PHP_VERSION;
         // Runtime
         $runtime = [];
         $uptime = time() - $Server->started;
         $runtime['started'] = date('Y-m-d H:i:s', $Server->started);
         // @ uptime (d = days, h = hours, m = minutes)
         if ($uptime > 60) {
            $uptime += 30;
         }
         $runtime['d'] = (int) ($uptime / (24 * 60 * 60)) . 'd ';
         $uptime %= (24 * 60 * 60);
         $runtime['h'] = (int) ($uptime / (60 * 60)) . 'h ';
         $uptime %= (60 * 60);
         $runtime['m'] = (int) ($uptime / 60) . 'm ';
         $uptime %= 60;
         $runtime['s'] = (int) ($uptime) . 's ';
         $uptimes = $runtime['d'] . $runtime['h'] . $runtime['m'] . $runtime['s'];
   
         // @ System
         // Load Average
         $load = ['-', '-', '-'];
         if ( function_exists('sys_getloadavg') ) {
            $load = array_map('round', sys_getloadavg(), [2, 2, 2]);
         }
         $load = "{$load[0]}, {$load[1]}, {$load[2]}";
   
         // @ Workers
         // count
         $workers = $Server->workers;
   
         // @ Socket
         // address
         $address = $Server->socket . ($Server->domain ?? $Server->host) . ':' . $Server->port;
   
         // Event-loop
         $event = (new \ReflectionClass($Server::$Event))->getName();
   
         // Script
         // TODO
   
         // SAPI
         $SAPI = Path::relativize(SAPI::$production, BOOTGLY_ROOT_DIR);
         $Decoder = (Server::$Decoder
            ? Server::$Decoder::class
            : 'N/A'
         );
         $Encoder = (Server::$Encoder
            ? Server::$Encoder::class
            : 'N/A'
         );
   
         // @ Server Status
         $Fieldset = new Fieldset($Output);
         // * Config
         $Fieldset->width = 80;
         // * Data
         $Fieldset->title = '@#Black: Server Status @;';
         $Fieldset->content = <<<OUTPUT
   
         @#Cyan:  Bootgly Server: @; {$server}
         @#Cyan:  PHP version: @; {$php}\t\t\t@:i: Server version: @; {$version}
   
         @#Cyan:  Started time: @; {$runtime['started']}\t@:i: Uptime: @; {$uptimes}
         @#Cyan:  Workers count: @; {$workers}\t\t\t@:i: Load average: @; {$load}
         @#Cyan:  Socket address: @; {$address}
   
         @#cyan:  Event-loop: @; {$event}
   
         @#yellow:  Server API: @; {$SAPI}
         @#yellow:  Server Decoder: @; {$Decoder}
         @#yellow:  Server Encoder: @; {$Encoder}
   
         OUTPUT;
         $Fieldset->render();
   
         // @ Workers Load
         $Fieldset2 = new Fieldset($Output);
         // * Config
         $Fieldset2->width = 80;
         // * Data
         $Fieldset2->title = '@#Black: Workers Load (CPU usage) @;';
         $Fieldset2->content = PHP_EOL;
   
         // TODO use only Progress\Bar
         $Progress = [];
         $Progress[0] = new Progress($Output);
         // * Config
         $Progress[0]->throttle = 0.0;
         $Progress[0]->Precision->percent = 0;
         // @ render
         $Progress[0]->render = Progress::RETURN_OUTPUT;
         // * Data
         $Progress[0]->total = 100;
         // ! Templating
         $Progress[0]->template = "[@bar;] @percent;%\n";
         // _ Bar
         $Bar = $Progress[0]->Bar;
         // * Config
         $Bar->units = 50;
         // * Data
         $Bar->Symbols->incomplete = '▁';
         $Bar->Symbols->current = '';
         $Bar->Symbols->complete = '▉';
   
         $pids = $Server->Process->pids;
         foreach ($pids as $i => $pid) {
            // @ Worker
            $id = sprintf('%02d', $i + 1);
            // @ System
            $procPath = "/proc/$pid";
   
            if ( is_dir($procPath) ) {
               $process_stat = file_get_contents("$procPath/stat");
               $process_stats = explode(' ', $process_stat);
   
               $stats[$i] ??= [];
   
               switch ($stat) {
                  case 0:
                     $stats[$i][0] = $process_stats;
                     break;
                  case 1:
                     $stats[$i][1] = $process_stats;
                     break;
                  default:
                     $stats[$i][0] = $process_stats;
                     $stats[$i][1] = $process_stats;
               }
   
               // CPU time spent in user code
               $utime1 = $stats[$i][0][13];
               // CPU time spent in kernel code
               $stime1 = $stats[$i][0][14];
   
               // CPU time spent in user code
               $utime2 = $stats[$i][1][13];
               // CPU time spent in kernel code
               $stime2 = $stats[$i][1][14];
   
               $userDiff = $utime2 - $utime1;
               $sysDiff = $stime2 - $stime1;
   
               $workerLoad = (int) abs($userDiff + $sysDiff);
   
               // @ Output
               $Progress[$i]->start();
               $Progress[$i]->advance($workerLoad);
   
               $CPU_usage = $Progress[$i]->output;
   
               $Fieldset2->content .= " Worker #{$id}: {$CPU_usage}";
   
               // new Progress
               $Progress[$i + 1] = clone $Progress[0];
            }
            else {
               $Fieldset2->content .= <<<OUTPUT
                Worker #{$id} with PID $pid not found. \n
               OUTPUT;
            }
         }
         $Fieldset2->render();
   
         $stat = match ($stat) {
            0 => 1,
            1 => 0,
            default => 0
         };
      });

      return true;
   }
};
