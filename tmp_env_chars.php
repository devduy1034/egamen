<?php
require 'vendor/autoload.php';
$repo = Dotenv\Repository\RepositoryBuilder::createWithNoAdapters()->addAdapter(Dotenv\Repository\Adapter\PutenvAdapter::class)->immutable()->make();
Dotenv\Dotenv::create($repo, __DIR__)->load();
$tmn = getenv('VNPAY_TMN_CODE');
$sec = getenv('VNPAY_HASH_SECRET');
echo 'TMN=' . $tmn . ' len=' . strlen($tmn) . PHP_EOL;
echo 'SEC=' . $sec . ' len=' . strlen($sec) . PHP_EOL;
for($i=0;$i<strlen($sec);$i++){ $o=ord($sec[$i]); if($o<32||$o>126){ echo 'NONASCII at '.$i.'='.$o.PHP_EOL; } }
