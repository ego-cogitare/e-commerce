<?php
    namespace Commands;

    use Models\{
        Product, Page, Brand, Category, Post
    };

    require_once __DIR__ . '/../../bootstrap.php';

    $urlPaths = [
        Product::class => 'product',
        Page::class => 'post',
        Brand::class => 'brand',
        Category::class => 'category',
        Post::class => 'post',
    ];

    $headerMenu = [
        'about-us',
        'delivery',
        'contacts',
        'blog',
    ];

    $sitemap = <<<SITEMAP
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    %URLS%
</urlset> 
SITEMAP;

    $urls = '';

    foreach ($headerMenu as $path) {
        $date = date('Y-m-d');
        $urls .= <<<URL
<url>
        <loc>{$config['settings']['siteUrl']}/{$path}</loc>
        <lastmod>{$date}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
URL;
    }

    foreach ($urlPaths as $class => $path) {
        $items = $class::fetchAll(['isDeleted' => ['$ne' => true]]);

        if (empty($items)) {
            continue;
        }

        foreach ($items as $item) {
            $date = date('Y-m-d', $item->dateCreated);
            $urls .= <<<URL
<url>
        <loc>{$config['settings']['siteUrl']}/{$path}/{$item->slug}</loc>
        <lastmod>{$date}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
URL;
        }
    }

    $sitemap = str_replace('%URLS%', $urls, $sitemap);

    file_put_contents(__DIR__ . '/../../public/sitemap.xml', $sitemap);