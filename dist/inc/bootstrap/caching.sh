#!/usr/bin/env bash

##
 # @file Bootstrap the caching layer.
 #
 # @global string $CACHED_CONFIG_FILEPATH
 # @global string $CACHED_CONFIG_JSON_FILEPATH
 # @global string $CACHED_CONFIG_MTIME_FILEPATH
 # @global string $CACHED_CONFIG_HASH_FILEPATH
 # @export string $CLOUDY_CACHE_DIR The absolute path to Cloudy's cached files.
 #
 ##

declare -rx CLOUDY_CACHE_DIR="$CLOUDY_CORE_DIR/cache"

CACHED_CONFIG_FILEPATH="$CLOUDY_CACHE_DIR/_cached.$(path_filename $CLOUDY_PACKAGE_CONTROLLER).config.sh"
CACHED_CONFIG_JSON_FILEPATH="$CLOUDY_CACHE_DIR/_cached.$(path_filename $CLOUDY_PACKAGE_CONTROLLER).config.json"
CACHED_CONFIG_MTIME_FILEPATH="${CACHED_CONFIG_FILEPATH/.sh/.modified.txt}"
CACHED_CONFIG_HASH_FILEPATH="${CACHED_CONFIG_FILEPATH/.sh/.hash.txt}"

# Ensure the configuration cache environment is present and writeable.
if [ ! -d "$CLOUDY_CACHE_DIR" ]; then
  mkdir -p "$CLOUDY_CACHE_DIR" || fail_because "Unable to create cache folder: $CLOUDY_CACHE_DIR"
fi

has_failed && return 1
return 0
