#!/usr/bin/env bash

#
# @file
# Demonstrate how one might write a Cloudy script.
#

# Define the configuration file relative to this script.
CLOUDY_PACKAGE_CONFIG="script.example.yml";

# Leave this blank and vendor will be detected automatically, only change it if
# you know what you're doing.  Search the codebase for more info.
# You may want to define an alternative location for the Composer vendor
# directory relative to this script.  See documentation on installing Cloudy
# using composer.  This is a step you will probably take if you are using Cloudy
# inside of a PHP application that uses other Composer dependencies.
#CLOUDY_COMPOSER_VENDOR=""

# Leave this blank and the app_root will be auto-detected.  If necessary you may
# set this to a path relative to this script, e.g '../../../'.  Absolute paths
# are not allowed.
#CLOUDY_BASEPATH=""

# Uncomment this line to enable file logging.
#[[ ! "$CLOUDY_LOG" ]] && CLOUDY_LOG="script.example.log"
# Or, set for a terminal session using `export CLOUDY_LOG="script.example.log"`.

# TODO: Event handlers and other functions go here or register one or more includes in "additional_bootstrap".

# Begin Cloudy Bootstrap
s="${BASH_SOURCE[0]}";while [ -h "$s" ];do dir="$(cd -P "$(dirname "$s")" && pwd)";s="$(readlink "$s")";[[ $s != /* ]] && s="$dir/$s";done;r="$(cd -P "$(dirname "$s")" && pwd)";CLOUDY_CORE_DIR="$r/cloudy";source "$CLOUDY_CORE_DIR/cloudy.sh"[[ "$ROOT" != "$r" ]] && echo "$(tput setaf 7)$(tput setab 1)Bootstrap failure, cannot load cloudy.sh$(tput sgr0)" && exit 1
# End Cloudy Bootstrap

# Input validation.
validate_input || exit_with_failure "Input validation failed."

# Cloudy basic is a bare-bones set of command command that you may want to
# share across all your scripts.  This line can be removed.  See the function
# comments for more information.
implement_cloudy_basic

eval $(get_config "some.thing")

# This is a short way to validate your configuration before moving on.
exit_with_failure_if_empty_config "some.thing"

# Handle other commands.
command=$(get_command)
case $command in

    "alpha")
        has_param "pass" || fail_because "Alpha does nothing yet"
        has_param "pass" && succeed_because "You passed the correct parameter \"pass\"."
        ;;

    "examples")

        echo_title "associative array values"
        echo_key_value 'Usage' 'eval $(get_config -a "user.images.types");Usage' && echo
        echo $LIL $(echo_green " $(get_config -a 'user.images.types')")
        echo; echo
        ;;

esac

throw "Unhandled command \"$command\"."
