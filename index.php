<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
</head>
<body>
<?php

set_time_limit(0);

require 'vendor/autoload.php';
require 'JobDetail.php';

use peter\JobDetail\JobDetail;

$detail = new JobDetail();

$jobs = $detail->printResult();
$len = count($jobs);
$baseUrl = 'https://www.104.com.tw';
echo '<table>';
echo '<tr><th>工作名稱</th><th>連結</th></tr>';

for($index=0;$index<$len;$index++) {
    echo '<tr>';
    echo '<td>'.$jobs[$index]['job_name'].'</td>';
    echo '<td><a href="'.$baseUrl.$jobs[$index]['job_link'].'" target="_blank">'.$jobs[$index]['job_name'].'</a></td>';
    echo '</tr>';
}
echo '</table>';
?>
</body>
</htm1>