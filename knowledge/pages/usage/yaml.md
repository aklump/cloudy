<!--
id: yaml
tags: usage
-->

# YAML in BASH

There are some helper functions to facilitate JSON and YAML data manipulation.  Here is some real-world code to illustrate this:
    
    # Begin a new YAML string.
    yaml_clear
    
    # Add a root-level value.
    yaml_add_line "base_url: $base_url"
    
    # Begin a new hash/array.
    yaml_add_line "results:"
    for path in "${pages[@]}"; do
      
      # Notice the indent on this hash key.
      yaml_add_line "  \"$path\":"
      for (( i = 0; i < 5; ++i )); do
        time=$(curl -w '%{time_total}' -o /dev/null -s ${base_url%/}/${path%/} -L)
        
        # Add a hash element, notice the double indent.
        yaml_add_line "    - $time"
      done
    done
    
    # Send the YAML off to be processed by PHP, but first convert it to JSON for
    # easier PHP consumption.  functions/invoke.php will process the raw json and add
    # some values to it and echo augmented JSON string which we can then do
    # something with...
    processed_json=$(. "$PHP_FILE_RUNNER" "$ROOT/functions/invoke.php" "$(yaml_get_json)") || fail_because "Could not process raw results."
    
    ...
