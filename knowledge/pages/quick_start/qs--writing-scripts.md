<!--
id: writing_scripts
tags: quick_start
-->

# Writing Your First Cloudy Script

Let's say you want to create a script called _imagine.sh_.


## Defining Your Script's Commands 

To understand how to define commands, let's look at the following example.  This is taken from the script's master configuration file.

    commands:
      mountain:
        help: Indicate the the user wants to imagine a mountain setting.
        aliases:
          - mt
        arguments:
          mountain_name:
            help: The name to give to the imagined item.
            required: true
        options:
          peaks:
            help: The numbe of peaks.
            aliases:
              - p
            type: integer

Let's break the above down into parts, but first take note that it is not enough merely to define your commands in configuration, you have to handle each command in _imagine.sh_ as well, e.g., 

    if [[ "$(get_command)" == "mountain" ]]; then ...

... or with a case statment, e.g.,

    case $(get_command) in
      "mountain")
        ...
      ;;   

Back to the configuration...
                  
### Indicate the command name

The most basic definition consists fo a command and a help topic:

    commands:
      mountain:
        help: Install a Cloudy package from the Cloudy Package Registry.

If this was all that was defined in the configuration, users would only be able to enter:

    ./imagine.sh mountain
    
### Indicate command aliases

By adding one or more command aliases, you give the user options, usually for brevity in typing.
    
    commands:
      mountain:
        help: Indicate the the user wants to imagine a mountain setting.
        aliases:
          - mt
          
With this configuration the user can enter either of these two and get the same effect.

    ./imagine.sh mountain
    ./imagine.sh mt

### Indicate command arguments

Let's say you want to collect a mountain name, you would indicate a command argument `<mountain_name>`.

    commands:
      mountain:
        ...
        arguments:
          mountain_name:
            help: The name to give to the imagined mountain.

Now the user may enter any of the following, however the user who provides the mountain name will have a different response--presumably--than the one who omits it.

    ./imagine.sh mountain
    ./imagine.sh mt
    ./imagine.sh mountain Everest
    ./imagine.sh mt Everest

#### Make an argument required

But what if the name is to be requried?

    commands:
      mountain:
        ...
        arguments:
          mountain_name:
            ...
            required: true
            
Now the user can no longer omit the `<mountain_name>` argument.            

### Indicate command options

    commands:
      mountain:
        ...
        options:
          peaks:
            help: The numbe of peaks.
            aliases:
              - p
            type: integer
          yes:
            help: Answer yes to all questions.
            aliases:
              - y
            type: boolean
            
The user will now be entering any of the following:

    ./imagine.sh mt "Three Sisters" --peaks=3            
    ./imagine.sh mt "Three Sisters" -p=3            
    ./imagine.sh mt "Three Sisters" --yes -p=3            
    ./imagine.sh mt "Three Sisters" -y -p=3            
