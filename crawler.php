<?php

set_time_limit(0);

require 'vendor/autoload.php';
require 'JobDetail.php';

use peter\JobDetail\JobDetail;

$detail = new JobDetail();

$url = 'https://www.104.com.tw/jobbank/joblist/joblist.cfm?jobsource=n104bank1&area=6001001000,6001004000,6001006001&cat=2007001006,2007002004,2007002001,2007002003,2007002005';
$getTotalPage = $detail->getPage($url);

for($index=1;$index<$getTotalPage;$index++) {
    $result = $detail->generateJson($url.'&page='.$index);
    if ($result === false) {
        'Exception happened and ignore this request...'.PHP_EOL;
    }
    sleep(10);
}

$detail->parseJson();
