<!--
id: php_php_side
tags: ''
-->

# Writing Cloudy PHP: PHP Perspective

* Use [echo](https://www.php.net/manual/en/function.echo.php) as you would in a BASH function or sourced file.
* Return values are always ignored; however [return](https://www.php.net/manual/en/function.return.php) may be used for early exit and/or code flow.
* Never use [exit](https://www.php.net/manual/en/function.exit.php).
* To indicate failure use `fail_because` or `exit_with_failure`. To set exit status pass the number to one of those functions.
* Any exception will be automatically converted to `exit_with_failure` and the script will stop immediately.
* The BASH parent script can be known by looking at `$PHP_FILE_RUN_CONTROLLER`
* Never hardcode PHP into your app codebase, e.g. `php`; instead if you must point to the PHP binary then use the variable `"$CLOUDY_PHP"`
