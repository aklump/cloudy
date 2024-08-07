<!--
id: version
tags: usage
-->

# Script Version

To provide a version for your script other than the default `1.0`, you have two options.

## Version Indicated in YAML

You may hard-code the version in a YAML config file like this:

    title: Cloudy Installer
    version: 1.5
    ...
    
## Version Indicated Dynamically

If you would rather provide your version dynamically with BASH, you can override the default `get_version` by adding it to your script file anytime after the bootstrap; this allows you to provide it via PHP, `cat` or whatever, e.g.,

    ...
    done;r="$(cd -P "$(dirname "$s")" && pwd)";source "$r/cloudy/dist/cloudy.sh"
    # End Cloudy Bootstrap
    
    function get_version() {
        local version=$(grep "version = " "$ROOT/web_package.info")
        echo ${version/version = / }
    }
