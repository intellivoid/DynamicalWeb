<?php

    require('ppm');

    import('net.intellivoid.dynamical_web');


    $parser = new \DynamicalWeb\Classes\UserAgentParser\Parser();

    $user_agents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36',
        'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36',
        'Mozilla/5.0 (iPhone; CPU iPhone OS 13_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 [FBAN/FBIOS;FBDV/iPhone9,1;FBMD/iPhone;FBSN/iOS;FBSV/13.3.1;FBSS/2;FBID/phone;FBLC/en_US;FBOP/5;FBCR/]',
        'Mozilla/5.0 (iPhone; CPU iPhone OS 13_5_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 [FBAN/FBIOS;FBDV/iPhone8,1;FBMD/iPhone;FBSN/iOS;FBSV/13.5.1;FBSS/2;FBID/phone;FBLC/en_US;FBOP/5]',
        'Mozilla/5.0 (Linux; U; Android 8.1.0; zh-CN; EML-AL00 Build/HUAWEIEML-AL00) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 baidu.sogo.uc.UCBrowser/11.9.4.974 UWS/2.13.1.48 Mobile Safari/537.36 AliApp(DingTalk/4.5.11) com.alibaba.android.rimet/10487439 Channel/227200 language/zh-CN',
        'Mozilla/5.0 (Linux; Android 5.1.1; Navori QL Stix 3500 Build/LMY49F; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/67.0.3396.87 Safari/537.36',
        'Mozilla/5.0 (Linux; Android 6.0; vivo 1606 Build/MMB29M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.124 Mobile Safari/537.36',
        'Mozilla/5.0 (Linux; Android 7.1.1; Moto G Play Build/NPIS26.48-43-2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Mobile Safari/537.36',
        'Unknown (Generic HTTP 1.1 Client)'
    ];

    foreach($user_agents as $agent)
    {
        $parsed = $parser->parse($agent);
        print($agent . PHP_EOL);
        print('=== Device Information ===' . PHP_EOL);
        if($parsed->device->brand !== null)
            print('Brand: ' . $parsed->device->brand . PHP_EOL);
        if($parsed->device->family !== null)
            print('Family: ' . $parsed->device->family . PHP_EOL);
        if($parsed->device->model !== null)
            print('Model: ' . $parsed->device->model . PHP_EOL);
        print('=== Operating System ===' . PHP_EOL);
        if($parsed->os->family !== null)
            print('Family: ' . $parsed->os->family . PHP_EOL);
        if($parsed->os->major !== null)
            print('Version Major: ' . $parsed->os->major . PHP_EOL);
        if($parsed->os->minor !== null)
            print('Version Minor: ' . $parsed->os->minor . PHP_EOL);
        if($parsed->os->patch !== null)
            print('Version Patch: ' . $parsed->os->patch . PHP_EOL);
        if($parsed->os->patchMinor !== null)
            print('Version Patch Minor: ' . $parsed->os->patchMinor . PHP_EOL);
        print('=== User Agent ===' . PHP_EOL);
        if($parsed->ua->family !== null)
            print('Family: ' . $parsed->ua->family . PHP_EOL);
        if($parsed->ua->major !== null)
            print('Version Major: ' . $parsed->ua->major . PHP_EOL);
        if($parsed->ua->minor !== null)
            print('Version Minor: ' . $parsed->ua->minor . PHP_EOL);
        if($parsed->ua->patch !== null)
            print('Version Patch: ' . $parsed->ua->patch . PHP_EOL);

        print(PHP_EOL);
    }

