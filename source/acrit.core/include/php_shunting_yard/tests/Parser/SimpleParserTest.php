<?php

namespace tests\Parser;

use RR\Shunt\Parser;
use RR\Shunt\Exception\RuntimeError;

class SimpleParserTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @param $equation
     * @param $expected
     *
     * @dataProvider simpleEquations
     */
    public function testParserWithSimpleEquations($equation, $expected)
    {
        $actual = Parser::parse($equation);

        $this->assertEquals($expected, $actual);
    }

    public function simpleEquations()
    {
        return array(
            array(
                '2+3',
                5.0,
            ),
            array(
                '2-3',
                -1.0,
            ),
            array(
                '2*3',
                6.0,
            ),
            array(
                '2/3',
                (2/3),
            ),
            array(
                '3 + 4 * 2 / ( 1 - 5 ) ^ 2 ^ 3',
                3.0001220703125,
            ),
            array(
                '3*(3+4)^(1+2)',
                1029.0,
            ),
            array(
                '4^(-2)',
                0.0625,
            ),
            array(
                '4^-2',
                0.0625,
            ),

            // exclamation / not
            array(
                '!1',
                0,
            ),
            array(
                '!0',
                1,
            ),
        );
    }

    /**
     * @expectedException \RR\Shunt\Exception\RuntimeError
     */
    public function testDivisionFromZero()
    {
        $equation = '100/0';

        Parser::parse($equation);
    }

    /**
     * @expectedException \RR\Shunt\Exception\RuntimeError
     */
    public function testModulusFromZero()
    {
        $equation = '100%0';

        Parser::parse($equation);
    }
}
