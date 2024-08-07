<!--
id: php_version
tags: ''
-->

# Setting the PHP Version Used by Cloudy

The value of the [environment variable](https://www.howtogeek.com/668503/how-to-set-environment-variables-in-bash-on-linux/) `CLOUDY_PHP` will be used to locate the PHP binary. You may set that explicitly (see below) or let Cloudy do it automatically.

### Globally

Add the following line to _~/.bashrc_ or _~/.bash\_profile_ as appropriate to your case.  [Learn more...](https://www.howtogeek.com/668503/how-to-set-environment-variables-in-bash-on-linux/)

```bash
export CLOUDY_PHP="/Applications/MAMP/bin/php/php7.2.20/bin/php"
```

### At Runtime

Coincidentally, if you run the following, the test will actually fail, as it asserts that `$CLOUDY_PHP` is set to the default PHP binary, which it will not be in this case.

```bash
 export CLOUDY_PHP="/Applications/MAMP/bin/php/php7.2.20/bin/php"; ./cloudy_tools.sh tests
```

### Defined in Cloudy Package Controller

You may set the value in the `on_pre_config` [event handler](@events):

```bash
function on_pre_config() {
  CLOUDY_PHP="/some/path/to/php"
}
```

### In Configuration Files

To enable this feature **you must add the following** to the Cloudy Package Controller, which does an early configuration read.

_Cloudy Package Controller File: foo.sh_

```bash
function on_pre_config() {
  source "$CLOUDY_CORE_DIR/inc/config/early.sh"
}
```

_Base configuration File: foo.core.yml_

```yaml
additional_config:
  - $CLOUDY_BASEPATH/.foo/local.yml
```

_Additional Local Config: local.yml_

```yaml
shell_commands:
  php: /usr/local/bin/php84
```

