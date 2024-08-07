<?php

/**
 * @file This will test: changes to cloudy vars and cloudy_export()
 */

/** @var $CLOUDY_FAILURES */

// Changes to this variable (and any other of our defined globals) are
// automatically picked up in php_file_runner.php and bubbled up to the controller

$CLOUDY_FAILURES[] = 'Alpha Bravo';

// New variable that we pass to the controller using cloudy_putenv().
$putenv_test_value = 'Lorem ipsum dolar sit';

cloudy_putenv('putenv_test_value', $putenv_test_value);
cloudy_putenv('COLOR', 'aqua');
cloudy_putenv('NAMES', ['Adam', 'Eve']);
