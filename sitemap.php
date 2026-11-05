<?php
declare(strict_types=1);
require_once __DIR__ . '/content.php';
header('Content-Type: application/xml; charset=UTF-8');
$base = rtrim(((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http') . '://' . preg_replace('/[^a-zA-Z0-9.:-]/', '', $_SERVER['HTTP_HOST'] ?? 'localhost'), '/');
$routes = ['/', '/services', '/genres', '/blog', '/calculator', '/faq', '/contact', '/privacy', '/terms'];
foreach (articles() as $article) $routes[] = '/blog/' . $article[0];
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
foreach ($routes as $route) echo '<url><loc>' . htmlspecialchars($base . $route, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</loc></url>';
echo '</urlset>';
