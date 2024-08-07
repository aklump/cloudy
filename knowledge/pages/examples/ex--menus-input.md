<!--
id: menus_input
tags: examples
-->

# User Input and Menus

## Any Keypress

```bash
wait_for_any_key
```

## Yes or No

```bash
prompt "Export the database first?" --caution
```

## Multiple Choice

* In this example `$search` can be user input, substring of a filename to look for.
* The options will be relative paths
* The `filepath=` assignment converts the selection to an absolute path.

```bash
choose__array=()
for i in *$search*.sql*; do
  [[ -f "$i" ]] && choose__array=("${choose__array[@]}" "$i")
done
for i in "${export_dir%/}"/*$search*.sql*; do
  [[ -f "$i" ]] && choose__array=("${choose__array[@]}" "$(path_make_relative "$i" "$PWD")")
done
! shortpath=$(choose "Choose a database export by number") && fail_because "Cancelled." && exit_with_failure
filepath=${PWD%/}/${shortpath#/}

echo "Your choice was: $filepath"
```

