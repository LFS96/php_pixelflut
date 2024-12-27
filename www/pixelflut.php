<?php

spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.php';
});

$ImageService = new ImageService();

$file = "/var/www/html/img/php.webp";
$threads = 4;
$run_thread = $argv[1] ?? 0;

$offsetX = 0;
$offsetY = 250;
$mtu = 500;

$ip = "pixelflut";

$image = $ImageService->loadImage($file);
if (!$image) {
    throw new Exception("Failed to load image from $file");
}

$slice = [];
$ImageService->splitImage($image, $threads, $offsetX, $offsetY, $slice);

$tcpClass = new TCPClass();
if (!$tcpClass->createSocket($ip, 1234)) {
    throw new Exception("Error creating socket");
}

echo "Thread $run_thread started\n";
echo "Sending " . count($slice[$run_thread]) . " commands\n";

$commands = $slice[$run_thread];
$totalCommands = count($commands);
$completePrints = 0;

while (true) {
    $packet = '';
    $packetSize = 0;

    foreach ($commands as [$command, $commandSize]) {
        $nextPacketSize = $packetSize + $commandSize;
        if ($nextPacketSize > $mtu) {
            if (!$tcpClass->sendCommand($packet, $packetSize)) {
                echo "Error sending packet\n";
                exit(1);
            }
            $packet = '';
            $packetSize = 0;
        }
        $packet .= $command;
        $packetSize += $commandSize;
    }

    if ($packet !== '' && !$tcpClass->sendCommand($packet, $packetSize)) {
        echo "Error sending final packet\n";
        exit(1);
    }

    $completePrints++;
    echo "Thread $run_thread completed iteration $completePrints\n";
}
