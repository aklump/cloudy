<?php

/**
 * @file
 * This file is meant to test Cloudy PHP functions.
 *
 * @param $argv [1] The function to call.
 * @param... Any parameters to be passed to the function.
 *
 * @code
 *
 * @endcode
 */

$function_args = $argv;
array_shift($function_args);
$exception_code = array_shift($function_args);
throw new RuntimeException('An unknown problem occurred.', $exception_code);
