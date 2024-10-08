<!--
id: readme
tags: about
-->

# Cloudy

> A BASH Framework for PHP-Minded Developers

![cloudy](../../images/hero_image.jpg)

## Summary

* Clean and simple YAML configuration
* Easy integration with PHP
* BASH Unit Testing framework inspired by [PHPUnit](https://phpunit.de)
* Auto-generated help output
* Support for multilingual localization

**Visit <https://aklump.github.io/cloudy> for full documentation.**

## Quick Start

Let's consider a project called _Thunder_:

1. Create a new directory to hold your project, e.g. _thunder/_
2. Change into that directory.
3. Install Cloudy using one of the methods described below.
5. Open _thunder.sh_ and _thunder.yml_
7. Replace `__package_name` with appropriate values, e.g. `thunder`, `Thunder`, etc.
8. Execute your script using `./thunder.sh` to ensure things are working.
   
   ![All Good](../../images/thunder_installed.png)
1. Now write code referring to [the documentation](https://aklump.github.io/cloudy/README.html) for guidance.
1. Also refer to _cloudy/examples/_ for more code examples.

{{ composer.install|raw }}

4. Copy and rename scaffold files:

   ```shell
   cp vendor/aklump/cloudy/install/__package_name.sh thunder.sh
   cp vendor/aklump/cloudy/install/__package_name.yml thunder.yml
   ```
5. Set the assignment in _thunder.sh_ to  `CLOUDY_CORE_DIR="$r/vendor/aklump/cloudy/dist"`

## Choose a Scaffold

```shell
mkdir -p init_resources
touch init_resource/cloudy_init_rules.yml
mkdir -p inc/commands
mkdir -p inc/snippets
mkdir -p inc/bootstrap
mkdir -p php/src
```
## Install with Composer Create Project

Use this method to create a _cloudy/_ folder in the root of your project.

1. Change to the root of your project and execute:
   ```shell
   {{ composer.create_project|raw }}
   ```   
2. Replace the _.gitignore file_:

    ```bash
    rm cloudy/.gitignore
    cp cloudy/install/composer_create_project/gitignore cloudy/.gitignore
    ```
4. Copy and rename scaffold files:

   ```shell
   cp cloudy/install/__package_name.sh thunder.sh
   cp cloudy/install/__package_name.yml thunder.yml
   ```

## Requirements

* BASH >=3
* PHP
* Composer

## Contributing

If you find this project useful... please consider [making a donation](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4E5KZHDQCEUV8&item_name=Gratitude%20for%20aklump%2Fcloudy).

## Learn More

* Learn more about BASH with the [Advanced Bash-Scripting Guide](https://www.tldp.org/LDP/abs/html/).
* Checkout [The Bash Guide](https://guide.bash.academy/) by Maarten Billemont.
