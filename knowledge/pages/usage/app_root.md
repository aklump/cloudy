<!--
id: app_root
tags: usage
-->
# Application Root

The application root is by default the same as `$ROOT`, which is the directory containing your cloudy entry script file.  However you may want to alter that by the use of `config_path_base` to point to a higher directory, such as the case with [Cloudy Packages](@packages).

`$CLOUDY_BASEPATH` should point to the most logical top-level directory in the context where the cloudy script is used.

## What does it do?

* Relative paths read in with `get_config_path` are made absolute using `$CLOUDY_BASEPATH`.
* Paths shortened via `path_shorten` use `$CLOUDY_BASEPATH` as the basis for prefix removal.


## Functions

`cloudy_resolve_to_app`
`cloudy_resolve_to_script`
