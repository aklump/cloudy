<!--
id: php_failures_in_functions
tags: ''
-->

* IF you use `$PHP_FILE_RUNNER` inside a bash function
* AND the php code calls `fail_because`
* AND you try to return from the function to the caller
* AND you try to exit_with_failure from the caller
* THEN the failure messages added by PHP will not appear.

```shell
function my_function() {
  . "$PHP_FILE_RUNNER" "$CLOUDY_CORE_DIR/php/functions/handle_foo.php"
  return $?
}
```

## Hack #1

Write the failures to the log only.

```shell
function my_function() {
  . "$PHP_FILE_RUNNER" "$CLOUDY_CORE_DIR/php/functions/handle_foo.php"
  local _result=$?
  if has_failed; then
    for reason in "${CLOUDY_FAILURES[@]}" ; do
      write_log_error "$reason"
    done
    return 1
  fi
  return $_result
}
```

## Get Around This Bug #1

```shell
function my_function() {
  . "$PHP_FILE_RUNNER" "$CLOUDY_CORE_DIR/php/functions/handle_foo.php"
  local _result=$?
  if has_failed; then
    list_clear
    for reason in "${CLOUDY_FAILURES[@]}" ; do
      write_log_error "$reason"
      list_add_item "$reason"
    done
    echo_red_highlight "Some problems occurred"
    echo_red_list
  fi
  return $_result
}
```

## Get Around This Bug #1

Use an source-able file instead of a function.
