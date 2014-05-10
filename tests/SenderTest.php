<?php

use Clue\React\Wol\Sender;

class SenderTest extends TestCase
{
    public function setUp()
    {
        $this->socket = $this->getMockBuilder('Socket\React\Datagram\Socket')
                             ->disableOriginalConstructor()
                             ->getMock();

        $this->sender = new Sender($this->socket);
    }

    public function testSend()
    {
        $this->socket
             ->expects($this->once())
             ->method('send');

        $this->sender->send('aa:bb:cc:dd:ee:ff');
    }

    /**
     * @dataProvider provideValidAddress
     * @param string $address
     * @param string $mac
     */
    public function testValidAddress($address, $mac)
    {
        $this->assertEquals($mac, $this->sender->coerceMac($address));
    }

    public function provideValidAddress()
    {
        return array(
            array(
                'Aa:bB:12:EF:34:56',
                'AA:BB:12:EF:34:56'
            ),
            array(
                'Aa-bB-12-EF-34-56',
                'AA:BB:12:EF:34:56'
            ),
            array(
                'AabB12EF3456',
                'AA:BB:12:EF:34:56'
            ),
        );
    }

    /**
     * @dataProvider provideInvalidAddress
     * @expectedException InvalidArgumentException
     * @param unknown $address
     */
    public function testInvalidAddress($address)
    {
       $this->sender->coerceMac($address);
    }

    public function provideInvalidAddress($address)
    {
        return array(
            array('aa:bb:cc'),
            array('aa:aa:aa:aa:aa:aa:aa'),
            array('gg:gg:gg:gg:gg:gg'),
        );
    }
}
