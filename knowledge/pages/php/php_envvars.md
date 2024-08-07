<!--
id: php_envvars
tags: ''
-->

# Sharing Data Between BASH and PHP

## From BASH to PHP

If you want to pass custom variables from BASH to PHP, then you should use the native BASH `export` and the PHP `getenv()`, which is not special to Cloudy.

_In file.sh_

```shell
export MY_VAR='foo bar'
. "$PHP_FILE_RUNNER" file.php 
```

_In file.php_

```php
{{ php_file_runner_variables|raw }}

$MY_VAR=getenv('MY_VAR');
```

When you use `$PHP_FILE_RUNNER` the BASH variables listed above are mirrored in the PHP include file.  _Hint: Add the varable declarations to the top of your included PHP files._

## From PHP to BASH

It is in this direction where Cloudy works behind the scenes to do the unusual, thereby allowing you to send data from PHP back to your BASH script. You must be using `$PHP_FILE_RUNNER` to take advantage of this feature.

_In file.sh_

```shell
. "$PHP_FILE_RUNNER" file.php
# See below for how $MY_VAR is set in file.php...
echo "$MY_VAR" 
```

_In file.php_

```php
$MY_VAR='lorem ipsum'
cloudy_putenv('MY_VAR', $MY_VAR);
```

## Accessing Configuration in PHP

The complete configuration will be written to an environment variable `$CLOUDY_CONFIG_JSON`, which can be decoded by PHP.

```php
$config = json_decode(getenv('CLOUDY_CONFIG_JSON'), TRUE);
```
