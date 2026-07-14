<?php
namespace Tests\Library;

use Opencart\System\Library\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase {
    public function testCleanString(): void {
        $request = new Request();
        $result = $request->clean('Hello World');
        $this->assertEquals('Hello World', $result);
    }

    public function testCleanStripsHtml(): void {
        $request = new Request();
        $result = $request->clean('<script>alert("xss")</script>');
        $this->assertStringNotContainsString('<script>', $result);
    }

    public function testCleanTrimsWhitespace(): void {
        $request = new Request();
        $result = $request->clean('  Hello  ');
        $this->assertEquals('Hello', $result);
    }

    public function testCleanArray(): void {
        $request = new Request();
        $result = $request->clean(['key' => 'value', 'html' => '<b>bold</b>']);

        $this->assertEquals('value', $result['key']);
        $this->assertStringNotContainsString('<b>', $result['html']);
    }

    public function testCleanNestedArray(): void {
        $request = new Request();
        $result = $request->clean([
            'level1' => [
                'level2' => '<script>hack</script>'
            ]
        ]);

        $this->assertStringNotContainsString('<script>', $result['level1']['level2']);
    }

    public function testCleanSpecialCharacters(): void {
        $request = new Request();
        $result = $request->clean('Price: $100 & 10% "discount"');
        $this->assertStringContainsString('&amp;', $result);
        $this->assertStringContainsString('&quot;', $result);
    }

    public function testCleanEmptyString(): void {
        $request = new Request();
        $result = $request->clean('');
        $this->assertEquals('', $result);
    }

    public function testCleanEmptyArray(): void {
        $request = new Request();
        $result = $request->clean([]);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testCleanPreservesArrayKeys(): void {
        $request = new Request();
        $result = $request->clean(['name' => 'John', 'age' => '30']);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('age', $result);
    }
}
