<?php declare(strict_types=1);
namespace test\froq\pagination;
use froq\pagination\{Paginator, HtmlPaginator};

class HtmlPaginatorTest extends \TestCase
{
    function testGenerateLinks() {
        $paginator = new HtmlPaginator();
        $paginator->paginate(10);

        $this->assertSame(
            '<ul class="pagination"><li><a class="current" href="#">1</a></li></ul>',
            $paginator->generateLinks(),
        );

        $paginator->paginate(100);

        $this->assertSame(
            '<ul class="pagination"><li><a class="current" href="#">1</a></li><li><a rel="next" href="?s=2">2</a></li>'.
            '<li><a href="?s=3">3</a></li><li><a href="?s=4">4</a></li><li><a href="?s=5">5</a></li><li><a class="next"'.
            ' rel="next" href="?s=2">&rsaquo;</a></li><li><a class="last" rel="last" href="?s=10">&raquo;</a></li></ul>',
            $paginator->generateLinks()
        );

        $this->assertSame(
            '<ul class="pagination"><li><a class="current" href="#">1</a></li><li><a rel="next" href="?s=2">2</a></li>'.
            '<li><a href="?s=3">3</a></li><li><a class="next" rel="next" href="?s=2">&rsaquo;</a></li><li><a class="last"'.
            ' rel="last" href="?s=10">&raquo;</a></li></ul>',
            $paginator->generateLinks(limit: 3)
        );
    }

    function testGenerateCenteredLinks() {
        $paginator = new HtmlPaginator();
        $paginator->paginate(10);

        $this->assertSame(
            '<ul class="pagination center"><li><a class="current" href="#">Page 1</a></li></ul>',
            $paginator->generateCenteredLinks(),
        );

        $paginator->paginate(100);

        $this->assertSame(
            '<ul class="pagination center"><li><a class="current" href="#">Page 1</a></li><li><a class="next" rel="next"'.
            ' href="?s=2">&rsaquo;</a></li><li><a class="last" rel="last" href="?s=10">&raquo;</a></li></ul>',
            $paginator->generateCenteredLinks()
        );
    }
}
