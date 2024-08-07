<!--
id: globals
tags: 'config,installation'
-->

# Global Variables

Beyond the [internal variables](https://www.tldp.org/LDP/abs/html/internalvariables.html#BASHSUBSHELLREF) the following variables are available to your Cloudy Package:

```shell
{{ bash_variables }}
```

## $CLOUDY_BASEPATH

An absolute path, which is used to resolve relative paths. This can be set automatically or it will be detected automatically [see this page](@cloudy_basepath) for more info.

## $CLOUDY_CACHE_DIR

The absolute path the directory containing Cloudy Core

## $CLOUDY_START_DIR

The working directory when $CLOUDY_PACKAGE_CONTROLLER was called.

## $CLOUDY_CORE_DIR

The absolute path the directory containing Cloudy Core.  Developers can [control where this is located](@relocating_cloudy).

## $CLOUDY_PACKAGE_CONTROLLER

The absolute path to the Cloudy Package controller script.

## $CLOUDY_PACKAGE_CONFIG

The absolute path to the main configuration file for your Cloudy package.

## $CLOUDY_LOG

Absolute path to a log file, if enabled.

## $CLOUDY_RUNTIME_UUID

This will change every time the controller is executed.

---

* Determine your version of BASH with `echo $BASH_VERSION`
