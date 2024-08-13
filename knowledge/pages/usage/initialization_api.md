<!--
id: initialization_api
tags: usage
-->

# The Initialize API

The Cloudy Initialization API makes it easy for your application to provide scaffolding (files and directories) to an instance during installation.

**In your app, create a folder called <em>{{ cloudy_init_resources_dir }}</em> in the same directory as your package controller. When users of your app run the `init` command, files will be copied from {{ cloudy_init_resources_dir }} to the location you've specified in your rules file.**

## The Init Rules File

- Create `$(dirname $CLOUDY_PACKAGE_CONTOLLER)/{{ cloudy_init_resources_dir }}/cloudy_init_rules.yml`

```yaml
copy_map:
  - [ ./*, $CLOUDY_BASEPATH/.$CLOUDY_PACKAGE_ID/ ]

```

* The first argument in each line of `copy_map` must be a path relative to {{ cloudy_init_resources_dir }}
* Globs in the first argument will be expanded as expected.
* The second argument must be an absolute path.
* Globs are not supported in the second argument.
* To achieve absolute paths you should [use tokens](@filepaths#tokens) as shown.
* The example above will copy all files in {{ cloudy_init_resources_dir }} to the same destination.

## Another More Complex Example

This example shows how you can copy the resource files to different locations. It also shows how you would handle a _.gitignore_ file.

```text
.
├── init_resources
│   ├── README.md
│   ├── cloudy_init_rules.yml
│   ├── config.local.yml
│   ├── config.yml
│   └── gitignore
└── thunder.sh
```

{{ cloudy_init_resources_dir }}/cloudy_init_rules.yml:

```yaml
copy_map:
  - [ ./README.md, $CLOUDY_BASEPATH/.$CLOUDY_PACKAGE_ID/ ]
  - [ ./gitignore, $CLOUDY_BASEPATH/.$CLOUDY_PACKAGE_ID/.gitignore ]
  - [ ./config.local.yml, $CLOUDY_BASEPATH/bin/config/$CLOUDY_PACKAGE_ID_config.local.yml ]
  - [ ./config.yml, $CLOUDY_BASEPATH/bin/config/$CLOUDY_PACKAGE_ID_config.yml ]
```

The result after initialization:

```text
.
├── .thunder
│   ├── .gitignore
│   └── README.md
└── bin
    └── config
        ├── thunder_config.local.yml
        └── thunder_config.yml
```

## Troubleshooting

### Tokens Not Replaced

This may arise if the `handle_init` function is called too early. It is advisable to assign the `handle_init` function inside the `on_boot` event handler within your package controller. This particular change can resolve your current issue. Please be aware that earlier editions of Cloudy suggested initializing the function during the `on_pre_config` event, but this approach doesn't facilitate token support.
