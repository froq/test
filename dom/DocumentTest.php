<?php declare(strict_types=1);
namespace test\froq\dom;
use froq\dom\{DomException, DomDocument, DomElement, DomElementList, DomNodeList};

class DocumentTest extends \TestCase
{
    function test_loaderMethods() {
        $doc = new DomDocument();
        $doc->loadSource('xml', $source = $this->getMockSource());

        $this->assertSame($doc, $doc->loadXmlSource($source));
        $this->assertInstanceOf(DomElement::class, $doc->firstElementChild);
        $this->assertInstanceOf(\DOMElement::class, $doc->firstElementChild);
        $this->assertInstanceOf(\DOMNode::class, $doc->firstChild);

        $doc = new DomDocument();
        $doc->loadSource('html', $source);

        $this->assertSame($doc, $doc->loadHtmlSource($source));
        $this->assertInstanceOf(DomElement::class, $doc->firstElementChild);
        $this->assertInstanceOf(\DOMElement::class, $doc->firstElementChild);
        $this->assertInstanceOf(\DOMNode::class, $doc->firstChild);
        $this->assertInstanceOf(\DOMProcessingInstruction::class, $doc->firstChild);

        try {
            $doc = new DomDocument();
            $doc->loadSource('xml', '<invalid');
        } catch (DomException $e) {
            $this->assertStringContains('Parse error', $e->getMessage());
        }
    }

    function test_findMethods() {
        $doc = new DomDocument();
        $doc->loadSource('xml', $this->getMockSource());

        $el = $doc->find('//br');

        $this->assertSame('br', $el->tag());
        $this->assertSame('br', $el->tagName);
        $this->assertSame('br', $el->nodeName);
        $this->assertSame('1', $el->attribute('s'));

        $this->assertInstanceOf(DomElement::class, $el);
        $this->assertInstanceOf(\DOMElement::class, $el);
        $this->assertInstanceOf(\DOMNode::class, $el);

        $els = $doc->findAll('//br');

        $this->assertCount(2, $els);
        $this->assertSame(2, count($els));
        $this->assertSame(2, $els->count());
        $this->assertSame(2, $els->length());

        $this->assertInstanceOf(DomElementList::class, $els);
        $this->assertInstanceOf(DomNodeList::class, $els);
        $this->assertInstanceOf(\ItemList::class, $els);

        $this->assertEquals($els, $doc->findByTag('br'));
        $this->assertNotNull($doc->findById('foo'));
        $this->assertNotNull($doc->findByName('bar'));
        $this->assertNotNull($doc->findByClass('baz'));
        $this->assertNotNull($doc->findByAttribute('custom'));
        $this->assertNotNull($doc->findByAttribute('custom', 'bat'));
        $this->assertNull($doc->findByAttribute('custom', 'none'));
    }

    function test_utilMethods() {
        $doc = new DomDocument();
        $doc->loadSource('xml', $this->getMockSource());

        $el = $doc->find('//y');

        $this->assertSame('y', $el->tag());
        $this->assertSame('/root/p/y', $el->path());
        $this->assertSame('baz', $el->attribute('class'));
        $this->assertSame(['class' => 'baz'], $el->attributes());

        $this->assertNull($el->text());
        $this->assertNull($el->html());
        $this->assertNull($el->value());

        $this->assertSame('x', $el->prev()->tag());
        $this->assertSame('z', $el->next()->tag());
        $this->assertNull($el->next()->next()?->tag());

        $this->assertSame(1, $el->prevAll()->length());
        $this->assertSame(1, $el->nextAll()->length());

        $el = $doc->find('//p');

        $this->assertSame($el->child(0), $el->find('//x'));
        $this->assertSame($el->children()->first(), $el->find('//x'));
        $this->assertCount(3, $el->children());

        $this->assertSame($doc->root(), $el->parent());
        $this->assertCount(2, $el->parents());
    }

    private function getMockSource() {
        return <<<XML
        <root>
          <br s="1" />
          <br s="2" />
          <p id="foo">
            <x name="bar" />
            <y class="baz" />
            <z custom="bat" />
          </p>
        </root>
        XML;
    }
}
