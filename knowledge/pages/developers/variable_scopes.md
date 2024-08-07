<!--
id: variable_scopes
tags: ''
-->

## Sourcing Scripts

_parent.sh_

```shell
FRUIT='apple'
echo "parent: $FRUIT"
# This is the same scope as parent.sh; think of it as COPY AND PASTE (or PHP Traits).
source child.sh
# We see that child.sh has affected our variable in this file.
echo "parent: $FRUIT"
```

_child.sh_

```shell
echo "child: $FRUIT"
# Child changes the variable, which the parent will experience
FRUIT='banana'
echo "child: $FRUIT"
```

_output is_

```text
$ ./parent.sh
parent: apple
child: apple
child: banana
parent: banana
$ echo $FRUIT
```

## Exporting Variables

You can see the dramatic difference in the subtle changes in how the script is called in this example. In order for `./parent.sh` (a subshell) to pass it's value up to the CLI (parent shell), you would have to use `export FRUIT`. In the second example, `export is unnecessary` because `. ./parent.sh` is sourcing the file and remains in the same shell.

```text
$ ./parent.sh
parent: apple
child: apple
child: banana
parent: banana
$ echo $FRUIT

$ . ./parent.sh
parent: apple
child: apple
child: banana
parent: banana
$ echo $FRUIT
banana
$
```

## Running Scripts

`. foo.sh`

* This is the source command, followed by a space, and then the script to be sourced. It executes the foo.sh script in the current shell itself. As a result:
* Any variables that are modified or created inside foo.sh will be available in the current shell after foo.sh completes.
* If the script foo.sh uses exit, it will close your current shell session.

`./foo.sh`

* This form runs foo.sh as a sub-process. Your shell will start a separate process to run the script. As a result:
* Any variables that are modified or created inside foo.sh will not be available in the parent shell after foo.sh completes.
* foo.sh needs to have execute (x) permissions for this to work.

Whether you should use ./foo.sh or . ./foo.sh (or its equivalent source ./foo.sh) depends on your specific needs.

./foo.sh: This runs the script in a new shell. It won't affect the environment of the current shell. Use this when you want to run a script that doesn't modify your current shell environment.

`. ./foo.sh` or `source ./foo.sh`: This sources the script in the current shell. Any environment variables or functions that the script defines or modifies will be available in your current shell after the script runs. Use this when running a script that defines or modifies environment variables or shell functions that you want to use after the script runs, such as a script that sets up environment variables for a project.

For shell scripts that are meant to initialize something in your environment, such as setting environment variables to configure a software, or defining functions to be used later, sourcing is the way to go. But for shell scripts that are meant to do a standalone job, running them in a new shell isolates them and prevents them from affecting the current shell environment, which is a safer option.
