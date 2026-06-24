<?php
namespace Tests\Library;

use Opencart\System\Library\Encryption;
use PHPUnit\Framework\TestCase;

class EncryptionTest extends TestCase {
    private Encryption $encryption;

    protected function setUp(): void {
        $this->encryption = new Encryption();
    }

    public function testEncryptReturnsString(): void {
        $result = $this->encryption->encrypt('secret_key', 'Hello World');
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testEncryptProducesUrlSafeOutput(): void {
        $result = $this->encryption->encrypt('key123', 'test data');
        $this->assertDoesNotMatchRegularExpression('/[+\/=]/', $result);
    }

    public function testDecryptReversesEncrypt(): void {
        $key = 'my_secret_key';
        $plaintext = 'Sensitive data 123';

        $encrypted = $this->encryption->encrypt($key, $plaintext);
        $decrypted = $this->encryption->decrypt($key, $encrypted);

        $this->assertEquals($plaintext, $decrypted);
    }

    public function testDecryptWithWrongKeyFails(): void {
        $encrypted = $this->encryption->encrypt('correct_key', 'secret');
        $decrypted = $this->encryption->decrypt('wrong_key', $encrypted);

        $this->assertNotEquals('secret', $decrypted);
    }

    public function testEncryptDifferentValuesProduceDifferentResults(): void {
        $key = 'shared_key';
        $enc1 = $this->encryption->encrypt($key, 'value1');
        $enc2 = $this->encryption->encrypt($key, 'value2');

        $this->assertNotEquals($enc1, $enc2);
    }

    public function testEncryptEmptyString(): void {
        $key = 'key';
        $encrypted = $this->encryption->encrypt($key, '');
        $decrypted = $this->encryption->decrypt($key, $encrypted);

        $this->assertEquals('', $decrypted);
    }

    public function testEncryptSpecialCharacters(): void {
        $key = 'key';
        $plaintext = 'Price: $100.00 & 10% off!';

        $encrypted = $this->encryption->encrypt($key, $plaintext);
        $decrypted = $this->encryption->decrypt($key, $encrypted);

        $this->assertEquals($plaintext, $decrypted);
    }

    public function testEncryptMultipleRoundTrips(): void {
        $key = 'consistency_key';
        $plaintext = 'Round trip test';

        for ($i = 0; $i < 5; $i++) {
            $encrypted = $this->encryption->encrypt($key, $plaintext);
            $decrypted = $this->encryption->decrypt($key, $encrypted);
            $this->assertEquals($plaintext, $decrypted);
        }
    }
}
