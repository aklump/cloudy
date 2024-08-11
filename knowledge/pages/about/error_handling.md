<!--
id: error_handling
tags: usage
-->

# Error Handling

## Using fail_because

You are encouraged to use `fail_because` in all code contexts (package controller, include files, functions, etc) whenever something goes wrong, and to explain why it failed.

## Using exit_with_failure

You should be much more conservative in your use of `exit_with_failure` as it is like throwing an uncaught exception in PHP, and execution stops immediately.

In terms of functions still use `fail_because`, however prefer using non-zero return codes over `exit_with_failure`. This maintains control with the caller and promotes the single responsibility of functions. It enhances the predictability of the program flow, simplifies debugging, and enables better error handling. The caller may then use the function in a test, check the return code `$? -ne 0`, or use `has_failed` in a test.

Here is an example of a function with the suggested error handling strategy, followed by three ways to respond to function errors.

```shell
function environment_path_resolve() {
  local environment_id="$1"
  local relative_path="$2"

  path_is_absolute "$relative_path" && fail_because "Second argument may only be a relative path or omitted." && return 1
  [[ "$relative_path" ]] && p="$(path_make_absolute "$relative_path" "$path")" && path="$p"
  echo "$path"
}
```

### Use the Function in a Test

```shell
resolved_path=$(environment_path_resolve "local") || exit_with_failure
```

### Check the Return Code

```shell
resolved_path=$(environment_path_resolve "local")
[[ $? -ne 0 ]] && fail_because "Can't do it" && exit_with_failure
```

### Use `has_failed` in a Test

```shell
resolved_path=$(environment_path_resolve "local")
echo "The result was: $resolved_path"
has_failed && exit_with_failure
```

## Controlling Package Controller Exit Status

When you call `exit_with_success` and `exit_with_success_elapsed` the exit status is set to 0 and the script exits. With the latter, the elapsed time is also printed.

When you call `exit_with_failure` the exit status is set to 1 by default. To change the exit status to something other than 1, then pass the `--status={code}` option, like the following, which will return a 2. Valid exit codes are from 0-255. [Learn more](https://www.tldp.org/LDP/abs/html/exit-status.html).

```shell
exit_with_failure --status=2 "Missing $ROOT/_perms.local.sh."
```

## Other

You can use `throw` kind of like an exception.

