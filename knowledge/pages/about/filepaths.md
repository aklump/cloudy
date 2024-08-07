<!--
id: filepaths
tags: ''
-->

# Filepaths

## `realpath` vs. `pwd`

The difference between these two is made apparent when you deal with a symlink. Using `realpath` will resolve the symlink to the actual path. Whereas, `pwd` is going to give you the apparent filepath, that is an unresolved symlink.

```text
.
├── bar
│   └── foo -> ../foo
└── foo

3 directories, 0 files
☁  $  echo $(realpath "bar/foo")
/Users/aklump/foo
☁  $  echo $(cd "bar/foo" && pwd)
/Users/aklump/bar/foo
```

## How do relative paths resolve?

* Use `path_make_absolute`.
* Use a path token (see below).
* <s>All relative paths in configuration will resolve to CLOUDY_BASEPATH by default.</s>
* For greater clarity, you may use path tokens instead of relative paths in your configuration.

## Path Tokens

@todo Auto generate this in book.php

1. `CLOUDY_BASEPATH`
1. `CLOUDY_CORE_DIR`

? what are the path tokens?

1. `$CLOUDY_BASEPATH`
1. `$CLOUDY_CORE_DIR`
