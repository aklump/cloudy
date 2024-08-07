<!--
id: lexicon
tags: ''
-->

# Cloudy Lexicon

## Cloudy Core

Cloudy core is used to build cloudy Packages. It is a BASH framework.

## Cloudy Package

General reference to any project built upon Cloudy Core. Identifying constituents are: a controller script, one or more separate configuration file(s), and one or more commands, all of which help users accomplish things via the CLI.

### Cloudy Package Controller (Script)

A single BASH file (as part of a Cloudy Package) that boostraps Cloudy Core and then handles user commands.

### Cloudy Package Configuration

A single YAML file that defines your Cloudy Package. A key responsibility is that it may define other configuration files, allowing the end user to affect the package.

## Cloudy Package Manager

Provides a simply process for installing Cloudy Packages into projects. It will install packages at: _CLOUDY\_BASEPATH/opt/VENDOR/PACKAGE\_NAME_

## App

In some cases, Cloudy Packages will be combined or used within a larger project, referred to as the "app". The app usually has some limited configuration for each Cloudy Package, which the end user will modify as desired for app implementation. When the Cloudy Package Manager is used to facilitate inclusion, the configuration files will generally be located at _CLOUDY\_BASEPATH/bin/config/PACKAGE\_NAME.yml_; see specific, individual Cloudy Packages for further details.  
