<?php
error_reporting(E_ALL);


$ram =  shell_exec('wmic OS get FreePhysicalMemory');
$cpu = shell_exec('wmic cpu get LoadPercentage');


file_put_contents("lol.txt",$cpu);
echo shell_exec('whoami');

