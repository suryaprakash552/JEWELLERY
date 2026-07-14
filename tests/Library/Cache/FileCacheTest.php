<?php
namespace Tests\Library\Cache;

use Opencart\System\Library\Cache\File;
use PHPUnit\Framework\TestCase;

class FileCacheTest extends TestCase {
    private File $cache;

    protected function setUp(): void {
        // Clean up cache directory before each test
        $files = glob(DIR_CACHE . 'cache.*');
        if ($files) {
            foreach ($files as $file) {
                @unlink($file);
            }
        }
        $this->cache = new File(3600);
    }

    protected function tearDown(): void {
        $files = glob(DIR_CACHE . 'cache.*');
        if ($files) {
            foreach ($files as $file) {
                @unlink($file);
            }
        }
    }

    public function testSetAndGet(): void {
        $this->cache->set('test_key', 'test_value');
        $result = $this->cache->get('test_key');
        $this->assertEquals('test_value', $result);
    }

    public function testGetReturnsEmptyArrayForMissingKey(): void {
        $result = $this->cache->get('nonexistent_key');
        $this->assertEquals([], $result);
    }

    public function testSetOverwritesPreviousValue(): void {
        $this->cache->set('key', 'value1');
        $this->cache->set('key', 'value2');
        $this->assertEquals('value2', $this->cache->get('key'));
    }

    public function testDelete(): void {
        $this->cache->set('to_delete', 'data');
        $this->assertEquals('data', $this->cache->get('to_delete'));

        $this->cache->delete('to_delete');
        $this->assertEquals([], $this->cache->get('to_delete'));
    }

    public function testSetWithArrayValue(): void {
        $data = ['name' => 'Gold Ring', 'price' => 599.99];
        $this->cache->set('product', $data);

        $result = $this->cache->get('product');
        $this->assertEquals($data, $result);
    }

    public function testSetWithCustomExpiry(): void {
        $this->cache->set('custom_expiry', 'data', 7200);
        $this->assertEquals('data', $this->cache->get('custom_expiry'));
    }

    public function testSetWithIntegerValue(): void {
        $this->cache->set('count', 42);
        $this->assertEquals(42, $this->cache->get('count'));
    }

    public function testSetWithBooleanValue(): void {
        $this->cache->set('active', true);
        $this->assertTrue($this->cache->get('active'));
    }

    public function testSetWithNestedArray(): void {
        $data = [
            'categories' => [
                ['id' => 1, 'name' => 'Rings'],
                ['id' => 2, 'name' => 'Necklaces']
            ]
        ];
        $this->cache->set('categories', $data);
        $this->assertEquals($data, $this->cache->get('categories'));
    }

    public function testDeleteNonExistentKey(): void {
        // Should not throw any errors
        $this->cache->delete('does_not_exist');
        $this->assertEquals([], $this->cache->get('does_not_exist'));
    }

    public function testMultipleKeysIndependent(): void {
        $this->cache->set('key1', 'val1');
        $this->cache->set('key2', 'val2');

        $this->assertEquals('val1', $this->cache->get('key1'));
        $this->assertEquals('val2', $this->cache->get('key2'));

        $this->cache->delete('key1');
        $this->assertEquals([], $this->cache->get('key1'));
        $this->assertEquals('val2', $this->cache->get('key2'));
    }

    public function testDefaultExpiry(): void {
        $cache = new File(3600);
        $cache->set('default_exp', 'data');
        $this->assertEquals('data', $cache->get('default_exp'));
    }
}
