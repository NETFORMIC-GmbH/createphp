<?php
namespace Midgard\CreatePHP\tests;

use Midgard\CreatePHP\Widget;
use Midgard\CreatePHP\Manager;
use PHPUnit\Framework\TestCase;

class WidgetTest extends TestCase {

    public function testMethods() {
        $this->factoryMock = $this->getMockBuilder('Midgard\\CreatePHP\\Metadata\\RdfTypeFactory')->disableOriginalConstructor()->getMock();
        $manager = new Manager(new MockMapper, $this->factoryMock);

        $wgt = new Widget($manager);
        $this->assertTrue(method_exists($wgt, 'render'));
    }
}
