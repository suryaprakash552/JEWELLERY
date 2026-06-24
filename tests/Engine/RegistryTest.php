<?php
namespace Tests\Engine;

use Opencart\System\Engine\Registry;
use PHPUnit\Framework\TestCase;

class RegistryTest extends TestCase {
    private Registry $registry;

    protected function setUp(): void {
        $this->registry = new Registry();
    }

    public function testSetAndGet(): void {
        $obj = new \stdClass();
        $obj->name = 'test';

        $this->registry->set('config', $obj);
        $this->assertSame($obj, $this->registry->get('config'));
    }

    public function testGetReturnsNullForMissingKey(): void {
        $this->assertNull($this->registry->get('nonexistent'));
    }

    public function testHasReturnsTrueWhenSet(): void {
        $this->registry->set('db', new \stdClass());
        $this->assertTrue($this->registry->has('db'));
    }

    public function testHasReturnsFalseWhenNotSet(): void {
        $this->assertFalse($this->registry->has('missing'));
    }

    public function testUnset(): void {
        $this->registry->set('temp', new \stdClass());
        $this->assertTrue($this->registry->has('temp'));

        $this->registry->unset('temp');
        $this->assertFalse($this->registry->has('temp'));
        $this->assertNull($this->registry->get('temp'));
    }

    public function testMagicGet(): void {
        $obj = new \stdClass();
        $this->registry->set('service', $obj);
        $this->assertSame($obj, $this->registry->__get('service'));
    }

    public function testMagicSet(): void {
        $obj = new \stdClass();
        $this->registry->__set('service', $obj);
        $this->assertSame($obj, $this->registry->get('service'));
    }

    public function testMagicIsset(): void {
        $this->assertFalse($this->registry->__isset('key'));
        $this->registry->set('key', new \stdClass());
        $this->assertTrue($this->registry->__isset('key'));
    }

    public function testOverwriteValue(): void {
        $obj1 = new \stdClass();
        $obj1->val = 1;
        $obj2 = new \stdClass();
        $obj2->val = 2;

        $this->registry->set('item', $obj1);
        $this->registry->set('item', $obj2);
        $this->assertSame($obj2, $this->registry->get('item'));
    }
}
