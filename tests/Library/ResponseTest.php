<?php
namespace Tests\Library;

use Opencart\System\Library\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase {
    private Response $response;

    protected function setUp(): void {
        $this->response = new Response();
    }

    public function testAddAndGetHeaders(): void {
        $this->response->addHeader('Content-Type: text/html');
        $this->response->addHeader('X-Custom: value');

        $headers = $this->response->getHeaders();
        $this->assertCount(2, $headers);
        $this->assertEquals('Content-Type: text/html', $headers[0]);
        $this->assertEquals('X-Custom: value', $headers[1]);
    }

    public function testGetHeadersDefaultsToEmpty(): void {
        $this->assertEmpty($this->response->getHeaders());
    }

    public function testSetAndGetOutput(): void {
        $this->response->setOutput('<html><body>Hello</body></html>');
        $this->assertEquals('<html><body>Hello</body></html>', $this->response->getOutput());
    }

    public function testGetOutputDefaultsToEmpty(): void {
        $this->assertEquals('', $this->response->getOutput());
    }

    public function testSetOutputOverwritesPrevious(): void {
        $this->response->setOutput('First');
        $this->response->setOutput('Second');
        $this->assertEquals('Second', $this->response->getOutput());
    }

    public function testSetCompression(): void {
        $this->response->setCompression(5);
        // Compression level is stored internally; verify no exception is thrown
        $this->assertTrue(true);
    }

    public function testMultipleHeaders(): void {
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Cache-Control: no-cache');
        $this->response->addHeader('X-Request-Id: abc123');

        $this->assertCount(3, $this->response->getHeaders());
    }

    public function testSetOutputWithHtml(): void {
        $html = '<html><head><title>Test</title></head><body><h1>Hello</h1></body></html>';
        $this->response->setOutput($html);
        $this->assertEquals($html, $this->response->getOutput());
    }

    public function testSetOutputWithEmptyString(): void {
        $this->response->setOutput('');
        $this->assertEquals('', $this->response->getOutput());
    }
}
