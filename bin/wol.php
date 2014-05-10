#!/usr/bin/env php
<?php

(@include_once __DIR__ . '/../vendor/autoload.php') or (print('ERROR: Installation incomplete, please see README' . PHP_EOL) and exit(1));

$loop = new Socket\React\EventLoop\SocketSelectLoop();
$wolFactory = new Clue\React\Wol\Factory($loop);

$do = function ($mac, $address = Clue\React\Wol\Factory::DEFAULT_ADDRESS) use ($loop, $wolFactory) {
    $wolFactory->createSender($address)->then(function(Clue\React\Wol\Sender $wol) use($mac) {
        $wol->send($mac);
        echo 'Sending magic wake on lan (WOL) packet to ' . $mac . PHP_EOL;
    })->then(null, function (Exception $error) {
        echo 'Error: ' . $error->getMessage() . PHP_EOL;
        exit(1);
    });
};

$prompt = '> ';

if ($_SERVER['argc'] > 2) {
    $do($_SERVER['argv'][1], $_SERVER['argv'][2]);
} else if ($_SERVER['argc'] > 1) {
    $do($_SERVER['argv'][1]);
} else {
    echo 'No target MAC address given as argument, reading from STDIN: ' . PHP_EOL . $prompt;

    $loop->addReadStream(STDIN, function () use ($wol, $loop, $do, $prompt) {
        $line = fread(STDIN, 8192);
        if ($line === '') {
            // EOF: CRTL+D
            echo PHP_EOL;
            $loop->removeReadStream(STDIN);
        } else {
            $line = trim($line);
            if ($line === '') {
                // empty line
                echo $prompt;
                return;
            }

            $do($line);
            echo $prompt;
        }
    });
}

$loop->run();
