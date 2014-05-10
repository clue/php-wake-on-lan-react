<?php

namespace Clue\React\Wol;

use React\EventLoop\LoopInterface;
use Socket\React\Datagram\Factory as DatagramFactory;
use Socket\React\Datagram\Socket;

class Factory
{
    const DEFAULT_ADDRESS = '255.255.255.255:7';

    protected $loop;

    public function __construct(LoopInterface $loop, DatagramFactory $datagramFactory = null)
    {
        if ($datagramFactory === null) {
            $datagramFactory = new DatagramFactory($loop);
        }
        $this->loop = $loop;
        $this->datagramFactory = $datagramFactory;
    }

    public function createSender($address = self::DEFAULT_ADDRESS)
    {
        return $this->datagramFactory->createClient($address, array('broadcast' => true))->then(function (Socket $socket) {
            return new Sender($socket);
        });
    }
}