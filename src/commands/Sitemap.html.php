<?php
    namespace Commands;

    use Models\{
        Product, Page, Brand, Category, Post
    };

    require_once __DIR__ . '/../../bootstrap.php';

    $urlPaths = [
        //Product::class => 'product',
        Page::class => 'post',
        Brand::class => 'brand',
        Category::class => 'category',
        Post::class => 'post',
    ];

    $headerMenu = [
        '/' => 'Главная',
        'about-us' => 'О нас',
        'delivery' => 'Доставка',
        'contacts' => 'Контакты',
        'blog' => 'Блок',
    ];

    $sitemap = <<<HTML
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Карта сайта</title>
        <meta charset="utf-8"/>
        <style>
            body {
                font-family: 'Arial';
            }
            body ul li ul {
                margin-bottom: 10px;
            }
            body a {
                color: #333;
                font-size: 0.95em;
                text-decoration: none;
            }
            body ul li ul li a {
                font-size: 0.85em;
            }
            body a:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <ul>%URLS%</ul>
    </body>
</html>
HTML;

    $urls = '';

    foreach ($headerMenu as $path => $title) {
        $urls .= sprintf('<li><a href="%s">%s</a></li>', $config['settings']['siteUrl'] . '/' . $path, $title);
    }

    foreach ($urlPaths as $class => $path) {
        $items = $class::fetchAll(['isDeleted' => ['$ne' => true]]);

        if (empty($items)) {
            continue;
        }

        foreach ($items as $item) {
            if (empty($item->title)) {
                continue;
            }
            $category = '';
            if ($class === Category::class) {
                $products = Product::fetchAll(['isDeleted' => ['$ne' => true], 'categoryId' => $item->id]);
                if (!empty($products)) {
                    $category = '<ul>%products%</ul>';
                    $productList = '';
                    foreach ($products as $product) {
                        if (empty($product->title)) {
                            continue;
                        }
                        $productList .= sprintf('<li><a href="%s">%s</a></li>', $config['settings']['siteUrl'] . '/product/' . $product->slug, $product->title);
                    }
                    $category = str_replace('%products%', $productList, $category);
                }
            }
            $urls .= sprintf('<li><a href="%s">%s</a>%s</li>', $config['settings']['siteUrl'] . '/'. $path . '/' . $item->slug, $item->title, $category);
        }
    }

    $sitemap = str_replace('%URLS%', $urls, $sitemap);

    file_put_contents(__DIR__ . '/../../public/sitemap.html', $sitemap);