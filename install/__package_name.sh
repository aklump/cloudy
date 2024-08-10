#!/usr/bin/env bash

#
# @file
# Controller for __package_name
#

# Define the configuration file relative to this script.
CLOUDY_PACKAGE_CONFIG="__package_name.yml";

# Comment this next line to disable file logging.
[[ "$CLOUDY_LOG" ]] || controller_log="__package_name.log"

# TODO: Event handlers and other functions go here or register one or more includes in "additional_bootstrap".

# Begin Cloudy Bootstrap
s="${BASH_SOURCE[0]}";while [ -h "$s" ];do dir="$(cd -P "$(dirname "$s")" && pwd)";s="$(readlink "$s")";[[ $s != /* ]] && s="$dir/$s";done;r="$(cd -P "$(dirname "$s")" && pwd)";CLOUDY_CORE_DIR="$r/cloudy/dist";source "$CLOUDY_CORE_DIR/cloudy.sh";[[ "$ROOT" != "$r" ]] && echo "$(tput setaf 7)$(tput setab 1)Bootstrap failure, cannot load cloudy.sh$(tput sgr0)" && exit 1
# End Cloudy Bootstrap

validate_input

implement_cloudy_basic

command=$(get_command)
case $command in

    "command")
      # TODO: Write the code to handle this command here.
      has_failed && exit_with_failure
      exit_with_success
      ;;

esac

throw "Unhandled command \"$command\"."
