#!/usr/bin/env bash

# SPDX-License-Identifier: BSD-3-Clause

##
# @file
# Examples of hooks to be used in Cloudy scripts.
#

##
# Fires when the caches are cleared.
#
# @return non-zero to block the cache clear.
#
function on_clear_cache() {
  local cloudy_root=$1

  fail_because "The weather is cloudy." && return 1
}

##
# Fires before configuration has been read in.
#
# You will want to use this for any installation commands that setup configuration files.
#
# @return a non zero will exit with failure.
#
function on_pre_config() {

}

##
# Fires as soon as the minimum bootstrap has been executed.
#
# @return a non zero will exit with failure.
#
function on_boot() {
  # Run the test command before the bootstrap to avoid conflicts.
  [[ "$(get_command)" == "tests" ]] || return 0
  source "$CLOUDY_CORE_DIR/inc/cloudy.testing.sh"
  do_tests_in "tests/cloudy.tests.sh"
  exit_with_test_results
}

##
# Fires when the configuration is about to be converted from YAML/JSON
# Used to add additional configuration files to merge in.
#
# @return ignored
#
function on_compile_config() {
  echo "$config_dir/config.yml"
  echo "$config_dir/config2.yml"
  echo "$config_dir/config3.yml"
}

# Fires on exit.
#
# $1 - int The exit status of the app.
function on_exit() {
  local exit_status=$1
}
