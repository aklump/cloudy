<?php
$data = get_defined_vars();
unset($data['_GET']);
unset($data['_GET']);
unset($data['_POST']);
unset($data['_COOKIE']);
unset($data['_FILES']);
unset($data['argv']);
unset($data['_ENV']);
unset($data['_REQUEST']);
unset($data['_SERVER']);
unset($data['GLOBALS']);
echo json_encode($data);
