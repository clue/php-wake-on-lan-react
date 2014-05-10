<?php

namespace Clue\Wol;

use React\EventLoop\LoopInterface;
use Socket\React\Datagram\Factory as DatagramFactory;
use Socket\React\Datagram\Socket;

class Factory
{
    const DEFAULT_ADDRESS = '255.255.255.255:7';

    protected $loop;

    public function __construct(LoopInterface $loop, DatagramFactory $datagramFactory = null) {
        $this->loop = $loop;
        $this->datagramFactory = $datagramFactory;

        if (!($this->datagramFactory instanceof DatagramFactory)) {
            $this->datagramFactory = new DatagramFactory($this->loop);
        }
    }

    public function createWol($address = self::DEFAULT_ADDRESS)
    {
        return $this->datagramFactory->createClient($address, array('broadcast' => true))->then(function (Socket $socket) {
            return new Sender($socket);
        });
    }
}