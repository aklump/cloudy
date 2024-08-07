<!--
id: php_errors
tags: ''
-->

# Error Handling in Cloudy PHP

You should use the Cloudy error handling strategy and functions, which have been mirrored to PHP:

* `fail_because`
* `exit_with_failure`

### How Exceptions are Handled

If your PHP code throws an exception, the exception message will automatically be passed to `fail_because` and the exception code will be passed to `$CLOUDY_EXIT_STATUS`
