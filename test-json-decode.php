<?php

# $data = '
$data = '
[
 {"name":"TF1","id":1,"service":
   [{"pvr_mode":"public","desc":"TF1 (TNT)","id":847}]
 }
]';

$data = file_get_contents('extra/json-data1.txt');

print_r(json_decode($data));

?>
