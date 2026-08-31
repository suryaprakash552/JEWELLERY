<?php
namespace Tests\Helper;

use PHPUnit\Framework\TestCase;

class ValidationHelperTest extends TestCase {
    public function testValidateLengthWithinRange(): void {
        $this->assertTrue(oc_validate_length('Hello', 1, 10));
        $this->assertTrue(oc_validate_length('Hi', 2, 2));
    }

    public function testValidateLengthTooShort(): void {
        $this->assertFalse(oc_validate_length('Hi', 5, 10));
    }

    public function testValidateLengthTooLong(): void {
        $this->assertFalse(oc_validate_length('Hello World', 1, 5));
    }

    public function testValidateLengthTrimsWhitespace(): void {
        $this->assertTrue(oc_validate_length('  Hi  ', 2, 2));
    }

    public function testValidateEmailValid(): void {
        $this->assertTrue((bool)oc_validate_email('user@example.com'));
        $this->assertTrue((bool)oc_validate_email('test.user@domain.org'));
    }

    public function testValidateEmailInvalid(): void {
        $this->assertFalse((bool)oc_validate_email('not-an-email'));
        $this->assertFalse((bool)oc_validate_email(''));
    }

    public function testValidateEmailTooLong(): void {
        $long_email = str_repeat('a', 90) . '@test.com';
        $this->assertFalse(oc_validate_email($long_email));
    }

    public function testValidateEmailNoAtSign(): void {
        $this->assertFalse(oc_validate_email('invalidemail.com'));
    }

    public function testValidateIpValidV4(): void {
        $this->assertTrue((bool)oc_validate_ip('192.168.1.1'));
        $this->assertTrue((bool)oc_validate_ip('127.0.0.1'));
    }

    public function testValidateIpInvalid(): void {
        $this->assertFalse((bool)oc_validate_ip('999.999.999.999'));
        $this->assertFalse((bool)oc_validate_ip('not-an-ip'));
    }

    public function testValidateFilenameValid(): void {
        $this->assertTrue(oc_validate_filename('image.jpg'));
        $this->assertTrue(oc_validate_filename('my-file_2.png'));
        $this->assertTrue(oc_validate_filename('document'));
    }

    public function testValidateFilenameInvalid(): void {
        $this->assertFalse(oc_validate_filename('file name.jpg'));
        $this->assertFalse(oc_validate_filename('../etc/passwd'));
        $this->assertFalse(oc_validate_filename('file@name.txt'));
    }

    public function testValidateUrlValid(): void {
        $this->assertTrue((bool)oc_validate_url('https://example.com'));
        $this->assertTrue((bool)oc_validate_url('http://www.test.org/path?q=1'));
    }

    public function testValidateUrlInvalid(): void {
        $this->assertFalse((bool)oc_validate_url('not-a-url'));
        $this->assertFalse((bool)oc_validate_url(''));
    }

    public function testValidatePathValid(): void {
        $this->assertTrue(oc_validate_path('products/gold-ring'));
        $this->assertTrue(oc_validate_path('category/sub_category'));
    }

    public function testValidatePathInvalid(): void {
        $this->assertFalse(oc_validate_path('path with spaces'));
        $this->assertFalse(oc_validate_path('path@invalid'));
    }
}
