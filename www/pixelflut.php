<?php
//autoload classes
spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.php';
});

$ImageService = new ImageService();

$file = "/var/www/html/img/php.webp";
$threads = 4;
$run_thread = $argv[1] ?? 0;

$offsetX = 0;// 1550;
$offsetY = 250;// 800;
$mtu = 500;

$ip = "pixelflut"; //192.168.16.2
//$ip = "192.168.16.2";

$image = $ImageService->loadImage($file);
$ImageService->splitImage($image, $threads, $offsetX, $offsetY, $slice);


$pixelThreads = [];

$tcpClass = new TCPClass();
if (!$tcpClass->createSocket($ip, 1234)) {
    throw new Exception("Error creating socket");
}

echo "Thread $run_thread started\n";
echo "Sending " . count($slice[$run_thread]) . " commands\n";

$c = count($slice[$run_thread]);
$completePrints = 0;

while (true) {
    $size = 0;
    $packet = "";
    for ($i = 0; $i < $c; $i++) {
        // $command_arr = $slice[$run_thread][$i];
        $command = $slice[$run_thread][$i][0];
        $str_size = $slice[$run_thread][$i][1];
        $size_n = $size + $str_size;
        //$tcpClass->sendCommand($command, $str_size);
        if ($size_n > $mtu) {
           // echo $packet;
            $tcpClass->sendCommand($packet, strlen($packet));
            $packet = "";
            $size = $str_size;
        } else {
            $size = $size_n;
        }
        $packet .= $command;
    }
    if (!$tcpClass->sendCommand($packet, $size)) {
        echo "Error sending packet";
        exit;
    }
    $completePrints++;
   // echo "Thread $run_thread: $completePrints\n";
}




