<!--
id: packages
tags: usage
-->

# Cloudy Packages

Cloudy packages are modular scripts that are meant to share a single cloudy instance across all of them.  There is a defined directory structure that must be followed for this to work.

## Create a package info file

    name: aklump/perms
    clone_from: https://github.com/aklump/website-perms
    entry_script: perms.sh
    entry_symlink: perms
    on_install: init
    on_update: update

1. Create a file, convention names it _cloudypm.yml_.
1. The name must follow `vendor/name` format.
1. The schema of the file can be seen [here](https://github.com/aklump/cloudy/blob/master/cloudy/dist/cloudypm_info.schema.json).
1. The `on_*` are commands that will be fired at the end of that event.  They are optional.
1. `entry_symlink` is optional and can be used to indicate a symlink value other than the filename of `entry_script` without extension.  In the above example `entry_symlink` is shown only for illustration.  The default value if it was omitted is `perms`.

## Add Package to the Registry

1. The registry file is located [here](https://github.com/aklump/cloudy/blob/master/cloudy_package_registry.txt).
1. Add the _vendor/name_ and _a link to cloudypm.yml or cloudypm.json_ for your package.  These must be separated by a single space, e.g.

        aklump/perms https://raw.githubusercontent.com/aklump/website-perms/master/cloudypm.yml?token=AAZ_CajHqKkAfvJNQT5WKrtqwcYZivzGks5btjkEwA%3D%3D

1. You do this by cloning and creating a pull request with your addition.
