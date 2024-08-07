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
call_user_func_array('exit_with_failure', $function_args);
