<!--
id: upgrade_v2
tags: ''
-->

# Upgrade Path to Cloudy 2.0.0

Developer's should follow these steps to upgrade Cloudy packages from 1.x to 2.x:

1. Enable Cloudy loggind in package controller.

## Required ReplacEments

| 1.x                                                   | 2.x                                           |
|-------------------------------------------------------|-----------------------------------------------|
| SCRIPT                                                | CLOUDY_PACKAGE_CONTROLLER                     |
| APP_ROOT                                              | CLOUDY_BASEPATH                               |
| CLOUDY_ROOT                                           | CLOUDY_CORE_DIR                               |
| CONFIG                                                | CLOUDY_PACKAGE_CONFIG                         |
| LOGFILE                                               | CLOUDY_LOG                                    |
| {APP_ROOT}                                            | $CLOUDY_BASEPATH                              |
| CLOUDY_NAME                                           | $(path_filename $CLOUDY_PACKAGE_CONTROLLER)"  |
| source "$CLOUDY_ROOT/inc/cloudy.read_local_config.sh" | source "$CLOUDY_CORE_DIR/inc/config/early.sh" |
| path_unresolve                                        | @see CHANGELOG.txt                            |
| path_unresolve "$PWD" ...                             | path_make_pretty ...                          |
| $CLOUDY_PHP                                           | @see CHANGELOG.txt                            |

7. Update the bootstrap in your controllers per changelog

## Optional

1. Replace `get_config_*()` with `get_config_*_as()` functions.
