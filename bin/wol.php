#!/usr/bin/env php
<?php

(@include_once __DIR__ . '/../vendor/autoload.php') or (print('ERROR: Installation incomplete, please see README' . PHP_EOL) and exit(1));

$loop = React\EventLoop\Factory::create();

$wol = new Clue\Wol\Sender($loop);
$do = function ($mac) use ($loop, $wol) {
    try {
        $mac = $wol->coerceMac($mac);
    } catch (InvalidArgumentException $e) {
        echo 'ERROR: invalid mac given' . PHP_EOL;
        return false;
    }
    echo 'Sending magic wake on lan (WOL) packet to ' . $mac . PHP_EOL;
    $wol->send($mac);
    return true;
};

$prompt = '> ';

if ($_SERVER['argc'] > 1) {
    if (!$do($_SERVER['argv'][1])) {
        exit(1);
    }
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
