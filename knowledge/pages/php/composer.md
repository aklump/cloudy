<!--
id: composer
tags: usage, php, composer, dependencies
-->

# Building a Cloudy-based App w/ Composer

Cloudy uses Composer for it's PHP dependencies. By default these dependencies are located within the _cloudy_ folder and managed by _cloudy/composer.json_. Cloudy will try to install these dependencies using composer when you run `cloudy new`.

```text
./cloudy/
  ├── LICENSE
  ├── cloudy.sh
  ├── cloudy_config.schema.json
  ├── cloudypm_info.schema.json
  ├── composer.json
  ├── composer.lock
  ├── vendor/
```

It's possible however, to implement Cloudy into an existing app that uses Composer. That is to say, Cloudy itself becomes a composer dependency. In such case you should make a few changes as described below:

## Tell Composer About Cloudy

1. Add a repository reference to Cloudy in your application's _composer.json_...

   ```php
   "repositories": [
      {
         "type": "path",
         "url": "./cloudy/"
      }
   ]
   ```   
1. `composer require aklump/cloudy:@dev`
1. Delete `cloudy/vendor` and `cloudy/composer.lock`; these will no longer be used.
1. Set those same files to be ignored by source control, e.g.,

   ```gitignore
   /cloudy/vendor/
   /cloudy/composer.lock
   ```

## Tell Cloudy About Composer

_This step is usually unnecessary, as it will be detected automatically in most cases._

1. Let's say your Cloudy-based controller is called _foo.sh_.
1. Open _foo.sh_ and add the path (relative to _foo.sh_) to the vendor directory as `CLOUDY_COMPOSER_VENDOR`. See _script.example.sh_ for code example.
