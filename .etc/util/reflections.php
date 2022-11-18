<?php
namespace foo\bar;

/**
 * The interface.
 *
 * @package foo\bar
 * @class   foo\bar\ITest
 * @author  Jon Doo
 * @since   1.0, 2.0
 * @internal
 */
interface ITest {}

/**
 * The trait.
 *
 * @package foo\bar
 * @class   foo\bar\TTest
 * @author  Jon Doo
 * @since   1.0, 2.0
 * @internal
 */
trait TTest {}


/**
 * The class.
 *
 * @package foo\bar
 * @class   foo\bar\Test
 * @author  Jon Doo
 * @since   1.0, 2.0
 * @internal
 */
class Test {
    /**
     * The const.
     *
     * @const int
     */
    const A = 1;

    /**
     * The var.
     *
     * @var bool
     */
    var $a = true;

    /**
     * The method.
     *
     * @param  bool $a The a.
     * @return void
     */
    function foo(bool $a): void {}
}
