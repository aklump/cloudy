<?php

/**
 * @file This will test that cloudy_putenv also sets the PHP env variables.
 */

$test_value = 'Evergreen trees are nice!';
cloudy_putenv('putenv_test_value', $test_value);
unset($test_value);
echo getenv('putenv_test_value');
