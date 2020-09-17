## PHP Shunting Yard Implementation

### Example

Simple equation parsing
```php
use RR\Shunt\Parser;

$equation = '3 + 4 * 2 / ( 1 - 5 ) ^ 2 ^ 3';
$result = Parser::parse($equation);
echo $result; //3.0001220703125
```

Equation with constants and functions
```php
use RR\Shunt\Parser;
use RR\Shunt\Context;

$ctx = new Context();
$ctx->def('abs'); // wrapper for PHP "abs" function
$ctx->def('foo', 5); // constant "foo" with value "5"
$ctx->def('bar', function($a, $b) { return $a * $b; }); // define function

$equation = '3 + bar(4, 2) / (abs(-1) - foo) ^ 2 ^ 3';
$result = Parser::parse($equation, $ctx);
echo $result; //3.0001220703125
```

Test a condition
```php
use RR\Shunt\Parser;
use RR\Shunt\Context;

$ctx = new Context();
$ctx->def('foo', 5); // constant "foo" with value "5"

$equation = '(foo > 3) & (foo < 6)';
$result = Parser::parse($equation, $ctx);
echo $result; //true
```

Re-run parsed expression on multiple inputs
```php
use RR\Shunt\Parser;
use RR\Shunt\Context;

$counter = 1;
$ctx = new Context();
$ctx->def('data', function() { global $counter; return $counter++; }); // define function
$ctx->def('bar', function($a) { return 2*$a; }); // define function

$equation = 'bar(data())';
$parser = new Parser(new Scanner($equation));

$result = $parser->reduce($this->ctx); // first result
echo $result; // 2
$result = $parser->reduce($this->ctx); // second result
echo $result; // 4
```

### Installation

Define the following requirement in your composer.json file:

```json
{
    "require": {
        "andig/php-shunting-yard": "dev-master"
    }
}
```

### Authors

Originally source code taken from https://github.com/droptable/php-shunting-yard, some changes from:

  - https://github.com/andig/php-shunting-yard
  - https://github.com/pmishev/php-shunting-yard
  - https://github.com/falahati/php-shunting-yard

Test cases and refactoring for composer/packagist by https://github.com/sergej-kurakin/php-shunting-yard.

