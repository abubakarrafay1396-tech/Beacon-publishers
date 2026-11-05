<?php
declare(strict_types=1);
header('Content-Type: text/plain; charset=UTF-8');
$host = preg_replace('/[^a-zA-Z0-9.:-]/', '', $_SERVER['HTTP_HOST'] ?? 'localhost');
echo "User-agent: *\nAllow: /\nSitemap: https://{$host}/sitemap.xml\n";
