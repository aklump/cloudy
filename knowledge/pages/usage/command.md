<!--
id: command
tags: usage
-->

# The Script "Command"

The script operation is defined as the first _argument_ passed.  In all of the following examples it is `alpha`.

    ./script.example.sh alpha bravo charlie
    ./script.example.sh -f alpha
    ./script.example.sh --test alpha -f

## No Script Arguments (Default Command)

If no argument is passed then the YAML configuration file may define a default operation like this:

    default_command: get
    
If the configuration file is as above, then these two are identical:

    $ ./script.example.sh    
    $ ./script.example.sh get        

## An Assumed Command
    
Somewhat related to the default command is `assume_command`.  It is used in a case where you want to insert (assume) a command that is not typed.  For example, if you want your users to type,  `./script.example.sh <arg>` instead of `./script.example.sh <command> <arg>`, you should use `assume_command` in your config, which will cause the command to be inserted.  Any command that is registered will be respected as you would expect, but if the immediate first script argument is not registered as a command, then the `assume_command` will be used as if it had been typed.  This effectively inserts the `assume_command` value between the script name and the first argument.

    assume_command get
    
When configured as show above, the following are the same (so long as `file` is not registered as a command).

    $ ./script.example.sh file file2
    $ ./script.example.sh get file file2     

## API

    get_command
    get_config "default_command"
