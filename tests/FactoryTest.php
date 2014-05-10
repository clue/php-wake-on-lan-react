<?php

use Clue\Wol\Factory;
use Clue\Wol\Sender;

class FactoryTest extends TestCase
{
    public function setUp()
    {
        $this->loop = React\EventLoop\Factory::create();
        $this->factory = new Factory($this->loop);
    }

    public function testSender()
    {
        $ret = $this->factory->createSender();

        $this->assertInstanceOf('React\Promise\PromiseInterface', $ret);

        $ret->then($this->expectCallableOnce());
        $ret->then(function (Sender $sender) {

        });

        $this->loop->run();
    }

    public function testInvalidSender()
    {
        $ret = $this->factory->createSender('some.host.invalid');

        $this->assertInstanceOf('React\Promise\PromiseInterface', $ret);

        $ret->then(null, $this->expectCallableOnce());
        $ret->then(function (Exception $exception) {

        });

        $this->loop->run();
    }
}
