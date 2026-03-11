<?php
require 'vendor/autoload.php';
$repo = Dotenv\Repository\RepositoryBuilder::createWithNoAdapters()->addAdapter(Dotenv\Repository\Adapter\PutenvAdapter::class)->immutable()->make();
Dotenv\Dotenv::create($repo, __DIR__)->load();
echo 'SITE_PATH=' . getenv('SITE_PATH') . PHP_EOL;
echo 'VNPAY_PUBLIC_URL=' . getenv('VNPAY_PUBLIC_URL') . PHP_EOL;
