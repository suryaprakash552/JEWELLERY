<?php
namespace Tests\Engine;

use Opencart\System\Engine\Autoloader;
use PHPUnit\Framework\TestCase;

class AutoloaderTest extends TestCase {
    private Autoloader $autoloader;

    protected function setUp(): void {
        $this->autoloader = new Autoloader();
    }

    public function testRegisterAndLoadExistingClass(): void {
        $this->autoloader->register(
            'Opencart\System\Engine',
            dirname(__DIR__, 2) . '/system/engine/'
        );

        $result = $this->autoloader->load('Opencart\System\Engine\Registry');
        $this->assertTrue($result);
    }

    public function testLoadNonExistentClassReturnsFalse(): void {
        $this->autoloader->register(
            'Opencart\System\Engine',
            dirname(__DIR__, 2) . '/system/engine/'
        );

        $result = $this->autoloader->load('Opencart\System\Engine\NonExistentClass');
        $this->assertFalse($result);
    }

    public function testLoadWithUnregisteredNamespace(): void {
        $result = $this->autoloader->load('Unknown\Namespace\SomeClass');
        $this->assertFalse($result);
    }

    public function testRegisterPsr4Namespace(): void {
        $tmpDir = sys_get_temp_dir() . '/oc_autoload_test/';
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0777, true);
        }

        file_put_contents($tmpDir . 'TestWidget.php', '<?php namespace TestNS; class TestWidget {}');

        $this->autoloader->register('TestNS', $tmpDir, true);
        $result = $this->autoloader->load('TestNS\TestWidget');
        $this->assertTrue($result);

        unlink($tmpDir . 'TestWidget.php');
        rmdir($tmpDir);
    }
}
