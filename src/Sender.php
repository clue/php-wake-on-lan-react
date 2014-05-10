<?php

namespace Clue\Wol;

use Socket\React\Datagram\Socket;
use InvalidArgumentException;

class Sender
{
    private $socket;

    public function __construct(Socket $socket)
    {
        $this->socket = $socket;
        $this->socket->pause();
    }

    public function send($mac)
    {
        $mac = $this->coerceMac($mac);

        $message = "\xFF\xFF\xFF\xFF\xFF\xFF" . str_repeat($this->formatMac($mac), 16);

        $this->socket->send($message);
    }

    /**
     *
     * @param string $mac
     *            mixed case mac address with colon, hyphen or no separators
     * @return string uppercase mac address with colon separators (e.g. 00:11:22:33:44:55)
     * @throws InvalidArgumentException
     */
    public function coerceMac($mac)
    {
        if (strlen($mac) === 12) {
            // no separators => add colons in between
            $mac = implode(':', str_split($mac, 2));
        } elseif (strpos($mac, '-') !== false) {
            // hyphen separators => replace with colons
            $mac = str_replace('-', ':', $mac);
        }
        $mac = strtoupper($mac);

        if (!preg_match('/(?:[A-F0-9]{2}\:){5}[A-F0-9]{2}/', $mac)) {
            throw new InvalidArgumentException('Invalid mac address given');
        }

        return $mac;
    }

    private function formatMac($mac)
    {
        $address = '';

        foreach (explode(':', $mac) as $part) {
            $address .= chr(hexdec($part));
        }

        return $address;
    }
}
