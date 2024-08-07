<!--
id: php_example
tags: ''
-->

# Writing Cloudy PHP (Complete Example)

Let's say your Cloudy app defines the command  `json-decode`. Because PHP has a native function for this we will use PHP to do the work instead of BASH.

This excerpt, taken from the _Cloudy Package Controller_, shows how to reference the PHP file that handles the `json-decode` command provided by the user.

{{ php_usage_controller|raw }}

Here are the contents of a PHP file, which will do the work for the command `json-decode`. Notice the use of the functions that you have been using while writing Cloudy BASH code.

{{ php_usage_php_file_runner|raw }}
