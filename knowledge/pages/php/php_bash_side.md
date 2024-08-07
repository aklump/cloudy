<!--
id: php_bash_side
tags: ''
-->

# Writing Cloudy PHP: BASH Perspective

Mixing PHP with BASH using Cloudy is quite simple.

## Basic PHP File Inclusion Syntax

```php
. "$PHP_FILE_RUNNER" <php_file> <args...>
```

The above pattern allows your PHP file to execute within the current cloudy context and configuration. Cloudy also provides [functions](@api_functions_php) in PHP you will recognize from Cloudy's [BASH API](@api_functions).

## Capturing the PHP Output

This is just like you'd do sourcing a BASH file or function.

```php
php_file_output=$(. "$PHP_FILE_RUNNER" <php_file> <args...>)
```

## Get the PHP File's Exit Status

The exit status of `$PHP_FILE_RUNNER` will reflect if `fail_because` was used by php_file, indicated by a 1. However, a number > 1 will result if `fail_because` was passed that number as the explicit exit status. An exit status of 0 represents success, as usual.  ** Be careful here, and make sure you read the documentation on how to trigger errors from Cloudy PHP.**

```shell
. "$PHP_FILE_RUNNER" file.php
exit_status=$?
if [[ $exit_status -ne 0 ]]; then
  # Respond to the failure.
fi 
```

The following can be used as an alternative to the above pattern, with the subtle difference that it will take into account the failure state, i.e. `$CLOUDY_EXIT_STATUS -ne 0`, that may have existed before file.php was run. Therefore it does not check ONLY the result of file.php, but the failure state of the entire app at that point.

```shell
. "$PHP_FILE_RUNNER" file.php
if has_failed; then
  # Respond to the failure.
fi 
```
