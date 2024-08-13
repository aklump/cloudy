<!--
id: legacy_installations
tags: ''
-->

# Legacy Installations

## Cloudy Core

1. Install using composer create-project

```shell
mv cloudy _cloudy
mv _cloudy/dist cloudy
```

1. Add `autoload.psr-4` from _cloudy/composer.json to composer.json, replacing `dist` with `cloudy`
2. Copy repositories section from _cloudy/composer.json_ to composer.json.
3. Delete _\_cloudy_ directory when finished.
