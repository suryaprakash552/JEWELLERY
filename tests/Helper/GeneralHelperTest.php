<?php
namespace Tests\Helper;

use PHPUnit\Framework\TestCase;

class GeneralHelperTest extends TestCase {
    public function testOcTokenDefaultLength(): void {
        $token = oc_token();
        $this->assertEquals(32, strlen($token));
        $this->assertMatchesRegularExpression('/^[a-f0-9]+$/', $token);
    }

    public function testOcTokenCustomLength(): void {
        $token = oc_token(16);
        $this->assertEquals(16, strlen($token));
    }

    public function testOcTokenUniqueness(): void {
        $token1 = oc_token();
        $token2 = oc_token();
        $this->assertNotEquals($token1, $token2);
    }

    public function testOcStrlen(): void {
        $this->assertEquals(5, oc_strlen('Hello'));
        $this->assertEquals(0, oc_strlen(''));
    }

    public function testOcStrlenMultibyte(): void {
        // été = 3 multibyte characters: é, t, é
        $this->assertEquals(3, oc_strlen("\xC3\xA9t\xC3\xA9"));
    }

    public function testOcStrpos(): void {
        $this->assertEquals(7, oc_strpos('Hello, World!', 'World'));
        $this->assertFalse(oc_strpos('Hello', 'xyz'));
    }

    public function testOcStrposWithOffset(): void {
        $this->assertEquals(8, oc_strpos('foo bar foo', 'foo', 1));
    }

    public function testOcStrrpos(): void {
        $this->assertEquals(8, oc_strrpos('foo bar foo', 'foo'));
    }

    public function testOcSubstr(): void {
        $this->assertEquals('World', oc_substr('Hello World', 6));
        $this->assertEquals('Hel', oc_substr('Hello', 0, 3));
    }

    public function testOcStrtoupper(): void {
        $this->assertEquals('HELLO', oc_strtoupper('hello'));
        $this->assertEquals('HELLO', oc_strtoupper('Hello'));
    }

    public function testOcStrtolower(): void {
        $this->assertEquals('hello', oc_strtolower('HELLO'));
        $this->assertEquals('hello', oc_strtolower('Hello'));
    }

    public function testStrStartsWith(): void {
        $this->assertTrue(str_starts_with('Hello World', 'Hello'));
        $this->assertFalse(str_starts_with('Hello World', 'World'));
        $this->assertTrue(str_starts_with('Hello', ''));
    }

    public function testStrEndsWith(): void {
        $this->assertTrue(str_ends_with('Hello World', 'World'));
        $this->assertFalse(str_ends_with('Hello World', 'Hello'));
        $this->assertTrue(str_ends_with('Hello', ''));
    }

    public function testStrContains(): void {
        $this->assertTrue(str_contains('Hello World', 'lo Wo'));
        $this->assertFalse(str_contains('Hello World', 'xyz'));
        $this->assertTrue(str_contains('Hello', ''));
    }
}
