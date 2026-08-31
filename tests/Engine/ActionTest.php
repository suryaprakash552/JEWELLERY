<?php
namespace Tests\Engine;

use Opencart\System\Engine\Action;
use PHPUnit\Framework\TestCase;

class ActionTest extends TestCase {
    public function testConstructorSetsRouteAndDefaultMethod(): void {
        $action = new Action('catalog/product');
        $this->assertEquals('catalog/product', $action->getId());
    }

    public function testConstructorParsesMethodFromRoute(): void {
        $action = new Action('catalog/product.list');
        $this->assertEquals('catalog/product.list', $action->getId());
    }

    public function testGetIdReturnsRoute(): void {
        $action = new Action('account/login');
        $this->assertEquals('account/login', $action->getId());
    }

    public function testRouteStripsInvalidCharacters(): void {
        $action = new Action('catalog/product<script>');
        // Invalid chars should be stripped, leaving 'catalog/productscript'
        $this->assertStringNotContainsString('<', $action->getId());
        $this->assertStringNotContainsString('>', $action->getId());
    }

    public function testRouteWithPipeCharacter(): void {
        $action = new Action('extension/module|featured');
        $this->assertEquals('extension/module|featured', $action->getId());
    }

    public function testRouteWithUnderscores(): void {
        $action = new Action('catalog/product_category');
        $this->assertEquals('catalog/product_category', $action->getId());
    }

    public function testRouteWithDotSeparatedMethod(): void {
        $action = new Action('api/cart.add');
        $this->assertEquals('api/cart.add', $action->getId());
    }

    public function testEmptyRoute(): void {
        $action = new Action('');
        $this->assertEquals('', $action->getId());
    }
}
