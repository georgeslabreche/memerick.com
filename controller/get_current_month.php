<?php
$current_date = getdate();
print json_encode($current_date["mon"]);
?>