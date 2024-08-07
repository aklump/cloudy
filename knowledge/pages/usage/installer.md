<!--
id: installer
tags: usage
-->

# The Initialize API

The Cloudy Initialization API makes it easy for your application to provide scaffolding (files and directories) to an instance during installation.

**In your app, create a folder called _init/_. When users of your app run the `init` command, files will be copied from _init/_ to the location you've specified in your files map.**

## The Files Map

In your app, _init/cloudypm.files\_map.txt_ is a text file that tells what files go where. Basically two columns, separated by a space. The first column represents filenames relative to _init/_, where `*` is all files. The second column is either a path relative to `$ROOT` or an absolute path, which represents the installed location.

The contents of the files map must have at least one line that will look something like this; notice the use of the asterix in both columns.

```text
* ../../../bin/config/*
```

### Multiple Destinations

But let's say you want one of the files to go elsewhere. The contents of _cloudypm.files\_map.txt_ might look like this:

```text
* ../../../bin/config/*
_perms.custom.sh ../../../bin/_perms.custom.sh
```

In this case `*` represents all files except for _\_perms.custom.sh_. They are initialized as before, however _\_perms.custom.sh_ is initializeed at _$ROOT/../../../bin/\_perms.custom.sh_.

Renaming the installed file is achieved by indicating a different basename in column two.

### Ignoring Files in _init/_

Finally let's say you want to skip over a file completely; do not include a destination for it, and it will be ignored, like this

```text
* ../../../bin/config/*
_perms.custom.sh ../../../bin/_perms.custom.sh
ignored_file.txt
```

### Tokens

In most cases you should consider using tokens like shown below. If you find your tokens are not get interpolated it's possible `handle_init` is getting called too early. Try moving `handle_init` to the `on_boot` event handler in your controller file to fix this. Early versions of Cloudy recommended calling that from `on_pre_config`, which does not support tokens.

```text
* $CLOUDY_BASEPATH/.live_dev_porter/*
config.gitignore $CLOUDY_BASEPATH/.live_dev_porter/.gitignore
```

#### Supported Tokens

1. `$CLOUDY_BASEPATH`

## Special Filenames

**Special files should not be listed in the files map.**

### _gitignore_

If you create a file at _init/gitignore_ (no leading dot!), it will be automatically be copied to _../../../opt/.gitignore_ and merged with the existing _.gitignore_.  (This is the recommended location by Cloudy Package Manager.)
