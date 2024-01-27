# Math Implies 

This package facilitates the solution of discrete mathematical problems.

## How to install

```bash
composer require noorani-mm/math-implies
```

## How to use

Provide your desired logical statement as the parameter to the Implies class.

```php
$implies = new \Math\Implies\Implies('p->q');
```
**From now on, you can retrieve the required outputs.**

<hr />

## Table

It constructs a truth table by placing the column headers in the first row and sequentially adding rows to represent logical conditions.

```php
$result = $implies->table
/**
[
  ["p",     "q",  "(p -> q)"], This is columns and others are value
  ["0",     "0",     "1"    ],
  ["0",     "1",     "1"    ],
  ["1",     "0",     "0"    ],
  ["1",     "1",     "1"    ],
]
 */
```

## Columns
It displays the computed logical expressions.

```php
$test1 = $implies->columns(); // ['p', 'q', '(p -> q)']
$test2 = $implies->columns;   // ['p', 'q', '(p -> q)']
```

## Rows
It returns the result of the truth table computation.

```php
$test1 = $implies->rows(); // ["001", "011", "100", "111"]
$test2 = $implies->rows;   // ["001", "011", "100", "111"]
```

## PDNF
The `pdnf` function returns a string representing the sum of products of the logical statement, obtained by multiplying the components of the logical expression.

Meanwhile, the `pdnf` property returns an array representing individual product terms of the logical expression, where each element corresponds to a specific term obtained by multiplying the components of the logical statement.
```php
$string = $implies->pdnf(); // "(~p^~q)v(~p^q)v(p^q)"
$array  = $implies->pdnf;   // ["(~p^~q)", "(~p^q)", "(p^q)"]
```

## PCNF
The `pcnf` function returns a string representing the Product of Sums Normalized (PCNF) for a logical statement by adding its components.

Meanwhile, the `pcnf` property returns an array representing individual sum terms of the logical expression, with each element corresponding to a specific term obtained by adding the components of the logical statement.

```php
$string = $implies->pcnf(); // "(~pv~qv~r)v(pv~qv~r)v(pv~qvr)"
$array  = $implies->pcnf;   // ["(~pv~qv~r)", "(pv~qv~r)", "(pv~qvr)"]
```

## minterm
The minterm function computes minterms for a logical statement, providing a summarized string representation.
The minterm property returns an array of individual minterms, enhancing clarity on the logical conditions.

```php
$string = $implies->minterm(); // "Σ(1,2,3,6,7)"
$array  = $implies->minterm;   // [1,2,3,6,7];
```

## maxterm
The maxterm function calculates maxterms for a logical statement, presenting a concise string representation.
The maxterm property returns an array of individual maxterms, contributing to a clear understanding of the logical conditions.

```php
$string = $implies->maxterm(); // "Σ(0,4,5)"
$array  = $implies->maxterm;   // [0,4,5]
```
