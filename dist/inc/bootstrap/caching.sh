#!/usr/bin/env bash

# SPDX-License-Identifier: BSD-3-Clause

##
 # @file Bootstrap the caching layer.
 #
 # @global string $CACHED_CONFIG_FILEPATH
 # @global string $CACHED_CONFIG_JSON_FILEPATH
 # @global string $CACHED_CONFIG_MTIME_FILEPATH
 # @global string $CACHED_CONFIG_HASH_FILEPATH
 # @export string $CLOUDY_CACHE_DIR The absolute path to Cloudy's cached files.
 #
 # @return 0 If all worked
 # @return 1 If failed; see fail messages.
 ##

_cache_basedir="${TMPDIR%%/}"
_cache_subdir="/cloudy/cache"
if [[ ! "$_cache_basedir" ]]; then
  _cache_basedir="${HOME%%/}"
  [[ ! "$_cache_basedir" ]] && fail_because "Missing $HOME directory" && return 1
  _cache_subdir="/.cloudy/cache"
fi
declare -rx CLOUDY_CACHE_DIR="$_cache_basedir$_cache_subdir"
unset _cache_basedir
unset _cache_subdir

# Ensure the configuration cache environment is present and writeable.
if [ ! -d "$CLOUDY_CACHE_DIR" ]; then
  mkdir -p "$CLOUDY_CACHE_DIR" || fail_because "Unable to create cache folder: $CLOUDY_CACHE_DIR"
fi

CACHED_CONFIG_FILEPATH="$CLOUDY_CACHE_DIR/_cached.$(path_filename $CLOUDY_PACKAGE_CONTROLLER).config.sh"
CACHED_CONFIG_JSON_FILEPATH="$CLOUDY_CACHE_DIR/_cached.$(path_filename $CLOUDY_PACKAGE_CONTROLLER).config.json"
CACHED_CONFIG_MTIME_FILEPATH="${CACHED_CONFIG_FILEPATH/.sh/.modified.txt}"
CACHED_CONFIG_HASH_FILEPATH="${CACHED_CONFIG_FILEPATH/.sh/.hash.txt}"

has_failed && return 1
return 0
