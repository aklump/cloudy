#!/usr/bin/env bash

## Set a JSON string to be later read by json_get_value().
 #
 # @export string $LOREM will be set and exported.
 # @global string $json_content will by set with the mutated JSON.
 # @param string A JSON string, wrapped by single quotes.
 # @param number The level of cleaning to use.
 # @option --echo
 # @option --style=json|csv Specifiy the output format. Defaults to json
 # @option int --count=N Indicate the number of items.
 # @echo The cleaned JSON string if --echo is used
 # @return 0 If the JSON is valid.
 # @return 1 If the JSON is invalid.
 #
 # Call this once to put your json string into memory, then make unlimited calls
 # to json_get_value as necessary.  You may check the return code to ensure JSON syntax
 # is valid.  If your string contains single quotes, you will need to escape them.
 #
 # @code
 #   json_set '{"foo":{"bar":"baz et al"}}'
 # @endcode
 ##

json_content=''
function json_set()
