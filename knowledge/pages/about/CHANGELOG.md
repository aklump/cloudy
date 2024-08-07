<!--
id: changelog
tags: about
-->

# Cloudy Changelog

## [2.0.0] - 2024-07-??

### Added

- CLOUDY_BASEPATH To point to the basepath of the application. In some cases this will be the same as `$(dirname "$CLOUDY_PACKAGE_CONTROLLER")`, in other cases this will be a parent directory such as a website that leverages Cloudy apps installed with Cloudy Package Manager. Relative configuration paths are ALWAYS resolved with this path.
- CLOUDY_CORE_DIR To point to the directory where Cloudy core is installed.
- CLOUDY_PACKAGE_CONTROLLER To point to the directory where Cloudy core is installed.
- CLOUDY_CACHE_DIR To point to the directory where Cloudy stores it's cache files.
- CLOUDY_START_DIR used to be WDIR

### Changed

- Absolute paths can now be set on CONFIG; previously only relative paths worked.
- In the bootstrap portion of the Cloudy Package Controller, replace `source "$r/cloudy/cloudy.sh"` with `CLOUDY_CORE_DIR="$r/cloudy";source "$CLOUDY_CORE_DIR/cloudy.sh"`; the path may be slightly different, e.g. `source "$r/../../cloudy/cloudy/cloudy.sh"` so ensure you maintain the correct path. The point here is to set the variable to the directory and then source _cloudy.sh_ using `$CLOUDY_CORE_DIR`.
- Replace `path_relative_to_root` with `path_make_absolute`

  ```shell
  ## Cloudy 1.x
  relative_path=$(path_relative_to_root "$relative_path")
  
  ## Cloudy 2.x
  ! path_is_absolute "$relative_path" && relative_path=$(path_make_absolute "$relative_path" "$ROOT")
  ```
- Replace `path_relative_to_pwd` with `path_make_relative`.

  ```shell
  path_relative_to_pwd "$absolute"
  path_make_relative "$absolute" "$PWD"
  ```

- Replace `path_resolve` with `path_make_absolute` using the following pattern:

  ```shell
  path_resolve "$absolute_prefix" "$path"
  a=$(path_make_absolute "$path" "$absolute_prefix") && path="$a"
  ``` 
- Replace `path_unresolve` with `path_make_relative` using the following pattern:

  ```shell
  path_unresolve "$absolute_prefix" "$path"
  a=$(path_make_relative "$path" "$absolute_prefix") && path="$a"
  ```

### Deprecated

- `get_config()` is too brittle. Use get_config_as instead, e.g. `get_config 'title'` -> `get_config_as 'title' 'title'`
- `get_config_keys()`
- `get_config_path()`

### Removed

- SCRIPT Use CLOUDY_PACKAGE_CONTROLLER instead.
- LOGFILE Use CLOUDY_LOG instead.
- CLOUDY_ROOT instead use CLOUDY_CORE_DIR
- JSON configuration files; all configuration must now be written in YAML.
- APP_ROOT Use CLOUDY_BASEPATH instead.
- The token `${config_path_base}` has been replaced by `$CLOUDY_BASEPATH` for consistency. It can no longer be used in cloudypm.files_map.txt. Replace with `$CLOUDY_BASEPATH` in all cloudy pm packages.
- `CLOUDY_NAME`; Add the following snippet to your package controller if you want to continue using this according to the legacy value: `export CLOUDY_NAME="$(path_filename $SCRIPT)"`
- `path_relative_to_config_base`

### Fixed

- Merge of cloudy package gitignore into cloudy pm .gitignore on install of package.

### Security

- lorem

## [1.7.11] - 2024-06-29

### Changed

- Allow $APP_ROOT to be set with an absolute path.

## [1.7.9] - 2024-06-25

### Fixed

- Bug introduced in 1.7.8 with {APP_ROOT} tokens.

## [1.7.7] - 2024-05-10

### Changed

- APP_ROOT is no longer influenced by symlinking.

### Added

- CLOUDY_COMPOSER_VENDOR can now handle absolute paths.
- PHP functions to match write_log_*().
- PHP errors are now logged to the Cloudy logfile.

### Removed

- Composer vendor and composer.lock are no longer shipped.

## [1.7.1] - 2024-05-08

### Fixed

- Swapped hardcoded `php` for `$CLOUDY_PHP`

## [1.7.0] - 2024-05-07

### Added

- Add functionality to read PHP override from local config in Cloudy. See docs for code examples.

## [1.6.0] - 2024-05-07

### Added

- The initialization API now supports directories as well as files.

## [1.5.16] - 2024-03-14

### Fixed

- Bug with JSON methods when JSON contains unescaped single quotes.

## [1.5.5] - 2023-09-21

### Changed

- Split _cloudy.sh_ into _inc/cloudy.api.sh_ and _cloudy.sh_ to allow cherry-picking the api functions without bootstrapping the app.

## [1.5] - 2023-04-25

### Changed

- POTENTIAL BREAKING CHANGES: The return value for fail_because(), warn_because(), and succeed_because() is now always `0`. Previously it was `1` if the message was empty AND the default was empty.

## [1.4.9] - 2022-12-07

### Fixed

- If you see an error like "line 833: cd: ... cloudy/cloudy/dist: No such file or directory", try adding `CLOUDY_COMPOSER_VENDOR=""` to the top of your script file, e.g. _framework/script.sh_.

## [1.4.0] - 2022-07-19

### Added

- Added functions `echo_task`, `clear_task`, `echo_task_completed`, `echo_task_failed`, `choose`
- Configurable Composer vendor direct using `$CLOUDY_COMPOSER_VENDOR`
- Added `$CLOUDY_CONFIG_HAS_CHANGED` which will be `true` if the configuration was rebuilt during boot.
- Other small improvements and bug fixes.

### Changed

- Moved Composer vendor directory from _cloudy/dist/php/vendor_ to _cloudy/dist/vendor_.

## [1.3.7] - 2022-05-02

### Added

- `array_dedupe` function

### Fixed

- `assert_contains` and `assert_not_contains` with space-containing values now works.

## [1.3.6] - 2021-09-17

### Changed

- Configuration merge algorithm to address deep merge problem.

## [1.3.0] - 2021-09-11

### Fixed

- _.gitignore_ no longer includes duplicate lines when updating a cloudy package.

### Added

- `json_set`, `json_load_file`, `json_get_value` and `json_get` for easily working with JSON.
- `echo_pass` and `echo_fail` for a consistent in test scenarios.
- A pattern for writing command access to the documentation.

### Deprecated

- `--as=ALIAS` flag has been deprecated in `exit_with_failure_if_empty_config` and `exit_with_failure_if_config_is_not_path`. Replace `exit_with_failure_if_empty_config "database.host" --as=host` with `exit_with_failure_if_empty_config "host" "database.host"` to switch to the new syntax. This was done to match `get_config_as` and `get_config` argument patterns.

## [1.2.0] - 2019-12-16

### Added

- md5_string function

### Changed

* Changed internal variable names, which should not affect your scripts.
    * `CLOUDY_OPTION__*` are now hashed variable names.
    * Configuration cached variable names now use hashes rather than plain config keys.

### Fixed

* Config variable problems with special characters.

## [1.1.0] - 2019-07-13

### Fixed

A bug when using `additional_config` together with `config_path_base`. The issue was that `additional_config` should have been relative to `config_path_base`, however it was not working correctly. After you update to 1.1.0 your app will be broken. To fix this you will need to remove the value of `config_path_base` from each of the items in `additional_config`. As an example, if before this update, your configuration was:

    config_path_base: ../../..
    additional_config:
      - ../../../bin/config/website_benchmark.yml

Your configuration should now look like this:

    config_path_base: ../../..
    additional_config:
      - bin/config/website_benchmark.yml

## [1.0.6]

### Changed

* Renamed `$parse_args__option__*` to `parse_args__options__*`.
