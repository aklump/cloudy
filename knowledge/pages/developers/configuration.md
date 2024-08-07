<!--
id: dev_configuration
tags: ''
-->

## Understanding Configuration

### Serialized Configuration Files

* YAML Configuration files get normalized then converted to BASH and cached.
* This process happens early in _cloudy.core.sh_
* Changes in the serialized files trigger an automatic flush and rebuild of the config cache.
* Cached files are stored in `$CLOUDY_CACHE_DIR`

### Cached Configuration

* Relative paths are no yet resolved at this point.


