<!--
id: upgrade_v2
tags: ''
-->

# Upgrade Path to Cloudy 2.0.0

Developer's should follow these steps to upgrade Cloudy packages from 1.x to 2.x:

1. Replace `get_config_*()` with `get_config_*_as()` functions.
2. Replace `$SCRIPT` with `$CLOUDY_PACKAGE_CONTROLLER`
3. Replace `$APP_ROOT` with `$CLOUDY_BASEPATH`
5. Replace `$CLOUDY_ROOT` with `$CLOUDY_CORE_DIR`
6. Replace `$CONFIG` with `$CLOUDY_PACKAGE_CONFIG`
7. Replace `$LOGFILE` with `$CLOUDY_LOG`
8. Update the bootstrap in your controllers per changelog
