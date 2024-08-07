<!--
id: events
tags: usage
-->

# Events (Hooks)

* Event handlers are functions that must be defined before the Cloudy bootstrap.

        #!/usr/bin/env bash
        
        ...
        
        #
        # Define all event handlers here.
        #
        
        # Begin Cloudy Bootstrap
        s="${BASH_SOURCE[0]}";while ...

* To see the available events recursively search codebase for `event_dispatch "`.

## Cloudy Core Events

* pre_config
* compile_config
* boot
* clear_cache

## on_boot

If you define this function before the bootstrap it will be called once the minimum bootstrap has been called.  To see an example of this you can look to the `tests` path in _cloudy_installer.sh_.


## Example of a Custom Event

Imagine a custom event like showing info.  Let's call the event `show_info`.  You Cloudy script will fire or trigger the event with a line like this:

    event_dispatch "show_info" "do" "re" "mi"
    local trigger_result=$?
    
How might another script respond to this event?

    function on_show_info() {
        local do=$1
        local re=$2
        local mi=$3
        
        ...
    }

### Listening for events

You can register custom callbacks using `event_listen`.  See below...

### Using `additional_bootstrap` files

In some cases you will need to add your listeners in a custom bootstrap file which is registered in your configuration as `additional_bootstrap`.  They are sourced after all configuration has been loaded.  The contents of such file could look like this:

    #!/usr/bin/env bash
    
    function here_we_go() {
        debug "$FUNCNAME;\$FUNCNAME"
    }
    
    event_listen "boot" "here_we_go"


