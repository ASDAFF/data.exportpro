<?php

namespace tests\Context;

use RR\Shunt\Context;
use RR\Shunt\Exception\RuntimeError;
use Exception;

class FunctionDefinitionTest extends \PHPUnit\Framework\TestCase
{

    public function testFunctionDefinitionAndCall()
    {
        $context = new Context();

        $context->def('func', function ($param1) {
            return $param1;
        });

        $actual = $context->fn('func', array(3));

        $this->assertEquals(3.0, $actual);

    }

    public function testSystemFunctionDefinition()
    {
        $context = new Context();

        $context->def('abs');

        $actual = $context->fn('abs', array(-3));

        $this->assertEquals(3.0, $actual);

    }

    /**
     * @expectedException \Exception
     */
    public function testNonCallableFunctionDefinition()
    {
        $context = new Context();

        $context->def('abs', 'Just a String That Causes Error #$#$%#@');
    }

    /**
     * @expectedException \RR\Shunt\Exception\RuntimeError
     */
    public function testCallNotsetFunctionCausesException()
    {
        $context = new Context();

        $context->fn('notdefinedfunction', array(-3));
    }

    public function testFunctionDefinitionWithOptionalParams()
    {
        $context = new Context();

        $context->def('func', function ($param1, $param2 = 100) {
                return ($param1 + $param2);
            });

        $actual = $context->fn('func', array(3));

        $this->assertEquals(103.0, $actual);

        $actual = $context->fn('func', array(3, 200));

        $this->assertEquals(203.0, $actual);

    }

}
