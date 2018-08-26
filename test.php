<?php
function fire()
{
	echo "Fire!";
	yield 3;
	yield 4;
}

function compute($x, $y)
{
	$operations = ['sum', 'difference', 'product', 'division'];
	yield $operations => $x + $y;

	yield $operations => $x - $y;

	yield $operations => $x * $y;

	yield $operations => $x / $y;
}

$sum = compute(2, 5);
//print_r(iterator_to_array($sum));


echo fire()->next();
echo fire()->next();

//foreach ($sum as $key => $value) {
//	var_dump($key);
//}