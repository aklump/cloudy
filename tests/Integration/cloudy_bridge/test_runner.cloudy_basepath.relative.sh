#!/usr/bin/env bash

#
# @file
# Lorem ipsum dolar sit amet consectador.
#

# Define the configuration file relative to this script.
CLOUDY_PACKAGE_CONFIG="$1";
test_runner_include_file="$2"
shift
shift

CLOUDY_BASEPATH=.

# This has been altered to facilitate testing; it is not standard initial code.
s="${BASH_SOURCE[0]}";while [ -h "$s" ];do dir="$(cd -P "$(dirname "$s")" && pwd)";s="$(readlink "$s")";[[ $s != /* ]] && s="$dir/$s";done;r="$(cd -P "$(dirname "$s")" && pwd)";CLOUDY_CORE_DIR="$r/../../../dist/";source "$CLOUDY_CORE_DIR/cloudy.sh";[[ "$ROOT" != "$r" ]] && echo "$(tput setaf 7)$(tput setab 1)Bootstrap failure, cannot load cloudy.sh$(tput sgr0)" && exit 1
# End Cloudy Bootstrap

source "$test_runner_include_file"
