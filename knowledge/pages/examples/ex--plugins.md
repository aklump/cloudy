<!--
id: plugins
tags: examples
-->

# Single File Plugins Pattern

Here is a pattern that can be used if you want to allow "plugin" type files, PHP or BASH, to be used by your project:

_Implementing controller snippet:_

```shell
call_plugin "$CONFIG_DIR/plugins/$plugin" || exit_with_failure
```

_Function definition for `call_plugin()`_
```shell
function call_plugin() {
  local plugin_path="$1"

  if [[ ! -f "$plugin_path" ]]; then
    fail_because "$plugin_path does not exist."
    return 1
  elif [[ "$(path_extension "$plugin_path")" == "php" ]]; then
    # TODO It may be more appropriate to be explicite about the arguments.
    plugin_output=$($CLOUDY_PHP  $@)
  else
    plugin_output=$(.  $@)
  fi
  
  if [[ $? -ne 0 ]]; then
    [[ "$plugin_output" ]] && fail_because "$plugin_output"
    fail_because "\"$plugin\" has failed."
    return 1
  fi
  
  [[ "$plugin_output" ]] && succeed_because "$plugin_output"
  return 0
}
```

_The plugin, PHP version:_

```php
<?php
$filepath = $argv[1];

$contents = file_get_contents($filepath);

if ('' == $contents) {
  echo "$filepath is an empty file.";
  exit(1);
}
echo "Contents approved in $filepath";
```

## Notes

* The echo string should not contain any line breaks.
* The plugin must exit with non-zero if it fails.
* When existing non-zero, a default failure message will always be displayed. If the plugin echos a message, this default will appear after the message.
* If the plugin exists with a zero, there is no default message.
