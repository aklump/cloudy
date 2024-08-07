#!/usr/bin/env bash

# SPDX-License-Identifier: BSD-3-Clause

source "$CLOUDY_CORE_DIR/inc/cloudy.define_variables.sh"

##
 # @file Processes variables during the bootstrapping.
 #
 # @export string $CLOUDY_PACKAGE_CONFIG
 # @export string $CLOUDY_RUNTIME_UUID
 # @global string $CLOUDY_BASEPATH
 # @global string $CLOUDY_INSTALLED_AS
 # @global string $CLOUDY_LOG
 ##

# Expand some vars from our controlling script.
if [[ "$CLOUDY_PACKAGE_CONFIG" ]] && ! path_is_absolute "$CLOUDY_PACKAGE_CONFIG"; then
  CLOUDY_PACKAGE_CONFIG="$(cd $(dirname "$r/$CLOUDY_PACKAGE_CONFIG") && pwd)/$(basename $CLOUDY_PACKAGE_CONFIG)"
fi
if [[ "$CLOUDY_PACKAGE_CONFIG" ]] && [ -f "$CLOUDY_PACKAGE_CONFIG" ]; then
  CLOUDY_PACKAGE_CONFIG="$(realpath "$CLOUDY_PACKAGE_CONFIG")"
fi
declare -rx CLOUDY_PACKAGE_CONFIG="$CLOUDY_PACKAGE_CONFIG"

if [[ "$CLOUDY_LOG" ]]; then
  p="$(path_make_absolute "$CLOUDY_LOG" "$r")" && CLOUDY_LOG="$p"
  log_dir="$(dirname "$CLOUDY_LOG")"
  if ! mkdir -p "$log_dir"; then
    fail_because "Please manually create \"$log_dir\" and ensure it is writeable."
    return 2
  fi
  declare -rx CLOUDY_LOG="$(cd "$log_dir" && pwd)/$(basename $CLOUDY_LOG)"
fi

declare -rx CLOUDY_PACKAGE_CONTROLLER="$(realpath "$CLOUDY_PACKAGE_CONTROLLER")"

# Detect installation type
declare -rx CLOUDY_INSTALLED_AS=$(_cloudy_detect_installation_type "$CLOUDY_PACKAGE_CONTROLLER")
write_log_debug "\$CLOUDY_INSTALLED_AS autodetected as \"$CLOUDY_INSTALLED_AS\""

declare -rx CLOUDY_RUNTIME_UUID=$(create_uuid)

# Holds the path of the controlling script that is executing $PHP_FILE_RUNNER;
# can be read by the PHP file to know it's parent script.
declare -x PHP_FILE_RUN_CONTROLLER=''
