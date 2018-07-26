<?php
    require_once 'Core.php';
    $core = new \Core('accessKeyId','secretkey','DescribeRegions');
    $response = $core->getData('http://ecs.aliyuncs.com/',[]);
    //$core = new \Core('accessKeyId','secretkey','DescribeInstanceStatus');
    //$response = $core->getData('http://ecs.aliyuncs.com/',['RegionId' => 'cn-beijing']);
    print_r($response);