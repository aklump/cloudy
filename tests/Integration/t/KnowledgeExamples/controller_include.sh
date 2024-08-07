#!/usr/bin/env bash

# User input
command='json-decode'
json_user_input='{"color":"red"}'

# Snippet taken from controller
case $command in
    "json-decode")
      . "$PHP_FILE_RUNNER" "$CLOUDY_BASEPATH/json_decode.php" "$json_user_input"
      has_failed && exit_with_failure
      exit_with_success
      ;;
esac
