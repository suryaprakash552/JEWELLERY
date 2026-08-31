<?php
namespace Tests\Library;

use Opencart\System\Library\Document;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase {
    private Document $document;

    protected function setUp(): void {
        $this->document = new Document();
    }

    public function testSetAndGetTitle(): void {
        $this->document->setTitle('Gold Necklace');
        $this->assertEquals('Gold Necklace', $this->document->getTitle());
    }

    public function testGetTitleDefaultsToEmpty(): void {
        $this->assertEquals('', $this->document->getTitle());
    }

    public function testSetAndGetDescription(): void {
        $this->document->setDescription('Beautiful gold necklace');
        $this->assertEquals('Beautiful gold necklace', $this->document->getDescription());
    }

    public function testGetDescriptionDefaultsToEmpty(): void {
        $this->assertEquals('', $this->document->getDescription());
    }

    public function testSetAndGetKeywords(): void {
        $this->document->setKeywords('gold, necklace, jewellery');
        $this->assertEquals('gold, necklace, jewellery', $this->document->getKeywords());
    }

    public function testAddAndGetLinks(): void {
        $this->document->addLink('https://example.com/style.css', 'stylesheet');
        $links = $this->document->getLinks();

        $this->assertCount(1, $links);
        $this->assertArrayHasKey('https://example.com/style.css', $links);
        $this->assertEquals('stylesheet', $links['https://example.com/style.css']['rel']);
    }

    public function testAddLinkDeduplicatesByHref(): void {
        $this->document->addLink('/style.css', 'stylesheet');
        $this->document->addLink('/style.css', 'preload');

        $links = $this->document->getLinks();
        $this->assertCount(1, $links);
        $this->assertEquals('preload', $links['/style.css']['rel']);
    }

    public function testGetLinksDefaultsToEmpty(): void {
        $this->assertEmpty($this->document->getLinks());
    }

    public function testAddAndGetStyles(): void {
        $this->document->addStyle('/css/main.css');
        $styles = $this->document->getStyles();

        $this->assertCount(1, $styles);
        $this->assertEquals('stylesheet', $styles['/css/main.css']['rel']);
        $this->assertEquals('screen', $styles['/css/main.css']['media']);
    }

    public function testAddStyleWithCustomParams(): void {
        $this->document->addStyle('/css/print.css', 'stylesheet', 'print');
        $styles = $this->document->getStyles();

        $this->assertEquals('print', $styles['/css/print.css']['media']);
    }

    public function testAddStyleDeduplicatesByHref(): void {
        $this->document->addStyle('/css/a.css');
        $this->document->addStyle('/css/a.css', 'stylesheet', 'print');

        $this->assertCount(1, $this->document->getStyles());
    }

    public function testAddAndGetHeaderScripts(): void {
        $this->document->addScript('/js/app.js');
        $scripts = $this->document->getScripts('header');

        $this->assertCount(1, $scripts);
        $this->assertEquals('/js/app.js', $scripts['/js/app.js']['href']);
    }

    public function testAddAndGetFooterScripts(): void {
        $this->document->addScript('/js/footer.js', 'footer');
        $headerScripts = $this->document->getScripts('header');
        $footerScripts = $this->document->getScripts('footer');

        $this->assertEmpty($headerScripts);
        $this->assertCount(1, $footerScripts);
    }

    public function testGetScriptsReturnsEmptyForUnknownPosition(): void {
        $this->assertEmpty($this->document->getScripts('sidebar'));
    }

    public function testAddAndGetMeta(): void {
        $this->document->addMeta('<meta charset="UTF-8">');
        $meta = $this->document->getMeta();

        $this->assertCount(1, $meta);
        $this->assertContains('<meta charset="UTF-8">', $meta);
    }

    public function testAddMetaDeduplicates(): void {
        $this->document->addMeta('<meta name="robots" content="index">');
        $this->document->addMeta('<meta name="robots" content="index">');

        $this->assertCount(1, $this->document->getMeta());
    }

    public function testMultipleOperations(): void {
        $this->document->setTitle('Page Title');
        $this->document->setDescription('Desc');
        $this->document->setKeywords('kw1, kw2');
        $this->document->addLink('/canonical', 'canonical');
        $this->document->addStyle('/css/style.css');
        $this->document->addScript('/js/script.js', 'header');
        $this->document->addScript('/js/analytics.js', 'footer');

        $this->assertEquals('Page Title', $this->document->getTitle());
        $this->assertEquals('Desc', $this->document->getDescription());
        $this->assertEquals('kw1, kw2', $this->document->getKeywords());
        $this->assertCount(1, $this->document->getLinks());
        $this->assertCount(1, $this->document->getStyles());
        $this->assertCount(1, $this->document->getScripts('header'));
        $this->assertCount(1, $this->document->getScripts('footer'));
    }
}
