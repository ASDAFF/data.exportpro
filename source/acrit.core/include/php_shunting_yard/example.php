<?php

// Composer autoloading
if (file_exists('vendor/autoload.php')) {
    $loader = include 'vendor/autoload.php';
}

use RR\Shunt\Parser;
use RR\Shunt\Context;

// einfach
$trm = '3 + 4 * 2 / ( 1 - 5 ) ^ 2 ^ 3';
print Parser::parse($trm)."\n"; // 3.0001220703125

// mit eigenen konstanten und funktionen
$ctx = new Context;
$ctx->def('abs'); // wrapper
$ctx->def('foo', 5);
$ctx->def('bar', function($a, $b) { return $a * $b; });

$trm = '3 + bar(4, 2) / (abs(-1) - foo) ^ 2 ^ 3';
print Parser::parse($trm, $ctx)."\n"; // 3.0001220703125

// mit string konstanten
$ctx = new Context;
$ctx->def('groupA', 'A', 'string'); // string constant
$ctx->def('isgroupA', function($g) { return ($g == 'A') ? 1 : 0; });

$trm = 'isgroupA(groupA)';
print Parser::parse($trm, $ctx)."\n"; // 1
