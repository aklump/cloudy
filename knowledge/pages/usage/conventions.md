<!--
id: conventions
tags: usage
-->

# Cloudy Conventions

_Cloudy_ has some opinions about how to write code, this page reveals them.

## General

* Functions that begin with `_cloudy` should be considered private and never called by your script. They may change in future versions.
* All functions listed in _cloudy.sh_ comprise the public API and will not change across minor version changes.

## Naming

* Function names should be readable and intuitive, e.g., `exit_with_success` is better than `success_exit`
* Function names should start with a verb if applicable, unless they are grouped by category, e.g. `array_join` is fine because _stack_ is the common group; `join` is the verb.

## Boolean

Even though BASH doesn't distinguish between (bool) "true" and (string) "true", you should indicate your intention by not using quotes for boolean value.

    my_var=true
    my_var=false

Do not do the following when your intention is boolean:

    my_var="true"
    my_var="false"

Likewise to test for `true` do like this, which again, omits any quotes around `true`.

    [[ "$my_var" = true ]]

## Functions

* For getters when echoing a _default value_, return 2

### Functions that operate on arrays

When a function needs to manipulate an array, the array should be assigned to a global variable, the name of which is the function with `__array` added to the end, e.g.,

    function array_join() {
        local glue=$1
        local string
        string=$(printf "%s$glue" "${array_join__array[@]}") && string=${string%$glue} || return 1
        echo $string
        return 0
    }

And here is the usage

    array_join__array=("${_config_values[@]}")
    local options="-$(array_join ", -"), --${option}"    

However, if a single function operates on more than one array, then the suffix should be modified as necessary. Look at `_cloudy_parse_option_arguments` for a good example. You still want the suffix to begin with two underscores.

### Functions that return an array

The same naming conventions apply, if the function has to "return" an array, which, in BASH means it _sets_ or _mutates_ a global array.

Beware of a scope issue for functions that use `eval` to set or mutate. This first example does not work, because `eval` doesn't affect the value of a global variable, even if said variable was already defined outside of the function. My understanding is that  `eval` creates [local variables by definition](https://stackoverflow.com/questions/40079054/eval-variable-assignment-in-a-bash-function-causes-variable-to-be-local) when called within a function.

```shell
function array_sort_by_item_length() {
    local eval=$(. "$PHP_FILE_RUNNER" "$CLOUDY_CORE_DIR/php/functions/invoke.php" "array_sort_by_item_length" "${array_sort_by_item_length__array[@]}")

    # note: [ $eval = 'declare -a array_sort_by_item_length__array=("on" "five" "three" "september")' ]
    # Notice the eval code aims to mutate $array_sort_by_item_length__array
    # Even though $array_sort_by_item_length__array was already global, the eval doesn't not mutate the global value.
    
    eval $eval
    ...
}
```

Here is the fix to make it work:

```shell
declare -a array_sort_by_item_length__array=()
function array_sort_by_item_length() {
    local eval=$(. "$PHP_FILE_RUNNER" "$CLOUDY_CORE_DIR/php/functions/invoke.php" "array_sort_by_item_length" "${array_sort_by_item_length__array[@]}")
    
    eval $eval
    
    # note: [ $eval = 'declare -a sorted=("on" "five" "three" "september")' ]
    # It's the following assignment here that makes it work.
    array_sort_by_item_length__array=("${sorted[@]}")
    
    ...
}    
```

### Name your function arguments

To make your code more readible, the first line(s) of a function should name the function arguments. Declare them as `local`. Then follow with a blank space, after which you begin the process of the function.

    function get_config() {
        local config_key_path="$1"
        local default_value="$2"
        
        # Now do the function stuff...

### Next, declare local variables just below that

Group all local variable names below arguments and declare them there rather than deeper in the function where they are used. Even if no default values, declare them here anyway.

    function get_config() {
        local config_key_path=$1
        local default_value="$2"
        
        local name
        local type="string"
        
        ...    
