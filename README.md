# Cloudy

> A BASH Framework for PHP-Minded Developers

![cloudy](images/screenshot.jpg)

## Summary

* Clean and simple YAML configuration
* Easy integration with PHP  
* BASH Unit Testing framework inspired by [PHPUnit](https://phpunit.de)
* Auto-generated help output
* Support for multilingual localization

**Visit <https://aklump.github.io/cloudy> for full documentation.**

## Install with Composer

1. Because this is an unpublished package, you must define it's repository in
   your project's _composer.json_ file. Add the following to _composer.json_ in
   the `repositories` array:
   
    ```json
    {
        "type": "github",
        "url": "https://github.com/aklump/cloudy"
    }
    ```
1. Require this package:
   
    ```
    composer require aklump/cloudy:@dev
    ```

```shell
composer create-project aklump/cloudy:@dev --repository="{\"type\":\"github\",\"url\": \"https://github.com/aklump/cloudy\"}"
```

## Quick Start

After installing Cloudy, to write a new script called _thunder.sh_ ...

1. `cd` to the directory where you want the script to be created.
1. Type `cloudy new thunder.sh` and the necessary files/directories will be created in the current directory.
1. Open _thunder.sh_, enable logging, and write your code.
1. Open _thunder.yml_ and add some configuration.
1. To learn about the options to use with `new` type `cloudy help new`.
1. Refer to [the documentation](https://aklump.github.io/cloudy/README.html) for to learn more.

### Source Control

1. You may omit the cloudy framework from your repository with something like the following, then use `cloudy install` to put the files in place when necessary.

```gitignore
cloudy/**
!cloudy/version.sh
```

## Requirements

* Works with BASH 3
* PHP (Used invisibly on the backend for configuration processing; no PHP knowledge is required to use Cloudy.)

## Contributing

If you find this project useful... please consider [making a donation](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4E5KZHDQCEUV8&item_name=Gratitude%20for%20aklump%2Fcloudy).

## Learn More

* Learn more about BASH with the [Advanced Bash-Scripting Guide](https://www.tldp.org/LDP/abs/html/).
* Checkout [The Bash Guide](https://guide.bash.academy/) by Maarten Billemont.
