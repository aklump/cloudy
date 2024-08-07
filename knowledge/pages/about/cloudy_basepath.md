<!--
id: cloudy_basepath
tags: ''
-->

# Cloudy Basepath

The variable `$CLOUDY_BASEPATH` is used to resolve relative paths.

## Auto-detected

Generally speaking you should allow it to be set automatically. In that case it will be set to one of the following:

* The App root directory when the Cloudy Package is installed using Cloudy Package Manager.
* The App root directory when the Cloudy Package is installed using Composer.
* Otherwise, the directory containing the Cloudy Package Configuration file, e.g. `dirname "$CLOUDY_PACKAGE_CONFIG"`

## Assigned in Cloudy Package Controller (Script)

You may include something like the following in your Cloudy Package Controller (Script). If the value is a relative path as in the first example, it will be resolved relative to the directory containing the Controller.

```shell
CLOUDY_BASEPATH=.
```

```shell
CLOUDY_BASEPATH=/some/file/path/
```
