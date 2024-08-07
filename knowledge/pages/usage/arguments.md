<!--
id: arguments
tags: usage
-->

# This script "arguments"

Cloudy makes it easy to react to script arguments.  For example:

    ./install.sh dev install --tree=blue -abc

Here we have:

* The script file _install.sh_
* The command `command=$(get_command)`, e.g. `dev`
* One argument `arg=$(get_arg 0)`, e.g., `install`.
* One value option, `tree`, `param=$(get_param "tree")`, e.g., `blue`
* Three boolean options: `a,b,c`, `has_flag "a"`

## Test if an option was used

    has_option b && echo "has b option"
    
## Access an option value

    echo "tree's value is $(get_option tree "not set")"

             
