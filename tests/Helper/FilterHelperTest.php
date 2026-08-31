<?php
namespace Tests\Helper;

use PHPUnit\Framework\TestCase;

class FilterHelperTest extends TestCase {
    public function testFilterDataWithMatchingKeys(): void {
        $filter = ['name' => '', 'email' => ''];
        $data = ['name' => 'John', 'email' => 'john@example.com', 'extra' => 'ignored'];

        $result = oc_filter_data($filter, $data);

        $this->assertEquals('John', $result['name']);
        $this->assertEquals('john@example.com', $result['email']);
        $this->assertArrayNotHasKey('extra', $result);
    }

    public function testFilterDataWithDefaults(): void {
        $filter = ['name' => 'default_name', 'age' => 0];
        $data = ['name' => 'John'];

        $result = oc_filter_data($filter, $data);

        $this->assertEquals('John', $result['name']);
        $this->assertEquals(0, $result['age']);
    }

    public function testFilterDataWithEmptyData(): void {
        $filter = ['name' => 'default', 'active' => true];
        $data = [];

        $result = oc_filter_data($filter, $data);

        $this->assertEquals('default', $result['name']);
        $this->assertTrue($result['active']);
    }

    public function testFilterDataWithNestedArrays(): void {
        $filter = ['address' => ['city' => '', 'zip' => '']];
        $data = ['address' => ['city' => 'NYC', 'zip' => '10001', 'extra' => 'ignored']];

        $result = oc_filter_data($filter, $data);

        $this->assertEquals('NYC', $result['address']['city']);
        $this->assertEquals('10001', $result['address']['zip']);
    }

    public function testFilterDataWithEmptyFilter(): void {
        $result = oc_filter_data([], ['name' => 'John']);
        $this->assertEmpty($result);
    }

    public function testFilterDataTypeMismatch(): void {
        $filter = ['tags' => ['default']];
        $data = ['tags' => 'not_an_array'];

        $result = oc_filter_data($filter, $data);
        $this->assertEquals(['default'], $result['tags']);
    }
}
