<!--
id: route_access
tags: examples
-->

# Command Access

This describes a pattern for controlling command access.

Create a method such as this:

```shell
function implement_route_access() {
  command=$(get_command)
  eval $(get_config_as 'allowed_routes' "commands.$command.access_by_env")
  [[ "" == "$allowed_routes" ]] && return 0

  local csv
  for i in "${allowed_routes[@]}"; do
     [ "$i" == "$LOCAL_ENV_ID" ] && return 0
     eval $(get_config_as env_alias "environments.$i.id")
     csv="$csv, \"$env_alias\""
  done

  fail_because "\"$command\" can be used only in ${csv#, } environments."
  fail_because "Current environment is \"$LOCAL_ENV\"."
  exit_with_failure "Command not allowed"
}
```

Add to your configuration, e.g., 

```yaml
commands:
  fetch:
    access_by_env:
      - dev
      - staging
    help: 'Fetch remote assets to local.'
    ...
```

Call the function before your command switch:

```bash
...
implement_cloudy_basic
implement_route_access

# Handle other commands.
command=$(get_command)
case $command in
...
```
