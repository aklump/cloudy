<!--
id: error_handling
tags: usage
-->

# Exit Status

When you call `exit_with_success` and `exit_with_success_elapsed` the exit status is set to 0 and the script exits.  With the latter, the elapsed time is also printed.

When you call `exit_with_failure` the exit status is set to 1 by default.  To change the exit status to something other than 1, then pass the `--status={code}` option, like the following, which will return a 2.  Valid exit codes are from 0-255. [Learn more](https://www.tldp.org/LDP/abs/html/exit-status.html).

    exit_with_failure --status=2 "Missing $ROOT/_perms.local.sh."

You can use `throw` kind of like an exception.
