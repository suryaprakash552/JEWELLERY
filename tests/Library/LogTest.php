<?php
namespace Tests\Library;

use Opencart\System\Library\Log;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase {
    private string $logFile;

    protected function setUp(): void {
        $this->logFile = DIR_LOGS . 'test_' . uniqid() . '.log';
    }

    protected function tearDown(): void {
        if (is_file($this->logFile)) {
            unlink($this->logFile);
        }
    }

    public function testConstructorCreatesLogFile(): void {
        $filename = 'test_' . uniqid() . '.log';
        $log = new Log($filename);
        $this->assertFileExists(DIR_LOGS . $filename);
        unlink(DIR_LOGS . $filename);
    }

    public function testWriteAppendsToFile(): void {
        $filename = basename($this->logFile);
        $log = new Log($filename);

        $log->write('Test message');

        $contents = file_get_contents($this->logFile);
        $this->assertStringContainsString('Test message', $contents);
    }

    public function testWriteIncludesTimestamp(): void {
        $filename = basename($this->logFile);
        $log = new Log($filename);

        $log->write('Timestamp test');

        $contents = file_get_contents($this->logFile);
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $contents);
    }

    public function testWriteMultipleMessages(): void {
        $filename = basename($this->logFile);
        $log = new Log($filename);

        $log->write('Message 1');
        $log->write('Message 2');
        $log->write('Message 3');

        $contents = file_get_contents($this->logFile);
        $this->assertStringContainsString('Message 1', $contents);
        $this->assertStringContainsString('Message 2', $contents);
        $this->assertStringContainsString('Message 3', $contents);
    }

    public function testWriteArray(): void {
        $filename = basename($this->logFile);
        $log = new Log($filename);

        $log->write(['key' => 'value']);

        $contents = file_get_contents($this->logFile);
        $this->assertStringContainsString('key', $contents);
        $this->assertStringContainsString('value', $contents);
    }

    public function testWriteHandlesExistingFile(): void {
        $filename = basename($this->logFile);
        // Create file first
        file_put_contents($this->logFile, 'existing content');

        $log = new Log($filename);
        $log->write('New message');

        $contents = file_get_contents($this->logFile);
        $this->assertStringContainsString('existing content', $contents);
        $this->assertStringContainsString('New message', $contents);
    }
}
