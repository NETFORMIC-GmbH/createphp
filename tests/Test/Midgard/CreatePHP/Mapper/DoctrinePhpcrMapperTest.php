<?php

namespace Test\Midgard\CreatePHP\Mapper;


use Midgard\CreatePHP\Mapper\DoctrinePhpcrOdmMapper;
use PHPUnit\Framework\TestCase;

/**
 * @author Uwe Jäger <uwej711@googlemail.com>
 */
class DoctrinePhpcrMapperTest extends TestCase
{
    protected $mapper;

    public function setUp(): void
    {
        $om = $this->createMock('Doctrine\Common\Persistence\ObjectManager');

        $registry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry
          ->expects($this->once())
          ->method('getManager')
          ->will($this->returnValue($om))
        ;

        $this->mapper = new DoctrinePhpcrOdmMapper(array(), $registry);
    }

    /**
     * @dataProvider sortData
     */
    public function testSort($input, $reference, $expected)
    {
        $this->assertSame($this->mapper->sort($input, $reference), $expected);
    }

    public function sortData()
    {
        return array(
            array(
                array(),
                array(),
                array(),
            ),
            array(
                array('block'),
                array('block3', 'block1', 'block2'),
                array('block'),
            ),
            array(
                array('block1', 'block2', 'block3'),
                array('block3', 'block1', 'block2'),
                array('block3', 'block1', 'block2'),
            ),
            array(
                array('block1', 'block2', 'block3'),
                array('block3', 'block1', 'test', 'block2'),
                array('block3', 'block1', 'block2'),
            ),
            array(
                array('header', 'block1', 'block2', 'block3', 'block4'),
                array('block4', 'block1', 'test', 'block2'),
                array('header', 'block3', 'block4', 'block1', 'block2'),
            ),
            array(
                array('block1', 'block2', 'block3', 'block4', 'footer'),
                array('block4', 'block1', 'test', 'block2'),
                array('block3', 'block4', 'block1', 'block2', 'footer'),
            ),
            array(
                array('header', 'block1', 'block2', 'block3', 'block4', 'footer'),
                array('block4', 'block1', 'test', 'block2'),
                array('header', 'block3', 'block4', 'block1', 'block2', 'footer'),
            ),
            array(
                array('header1', 'header2', 'block1', 'block2', 'block3', 'block4', 'footer'),
                array('block4', 'block1', 'test', 'block2'),
                array('header1', 'header2', 'block3', 'block4', 'block1', 'block2', 'footer'),
            ),

        );
    }
}
