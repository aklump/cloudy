<?php
$result = json_decode($argv[1], JSON_PRETTY_PRINT);
if (FALSE === $result) {
  fail_because("Could not parse the provided JSON");
  fail_because($argv[1]);
}
else {
  echo "The provided color is: " . $result['color'];
}
