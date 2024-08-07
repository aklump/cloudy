<!--
id: menus
tags: usage
-->

# Menus

## Advanced Menus

This example shows how to generate a menu using PHP, JSON which has different labels and values.

_get_menu_json.php_
```php
<?php
$choices = [
  'foo' => 'The first option is "foo"',
  'bar' => 'The second option is "bar"',
];
echo json_encode([
  'values' => array_keys($choices),
  'labels' => array_values($choices),
  'count' => count($choices),
]);
```

```shell
json_set "$($CLOUDY_PHP "$ROOT/php/get_menu_json.php")"
choose__array=()
choose__labels=()
for (( i=0; i<$(json_get_value count); i++ )); do
  choose__array=("${choose__array[@]}" "$(json_get_value values.$i)")
  choose__labels=("${choose__labels[@]}" "$(json_get_value labels.$i)")
done
! filepath=$(choose "Pick") && exit_with_failure "Cancelled."
```


