<?php
    namespace Controllers\Store;

    class SeoController
    {
        public function metaTags($request, $response, $args)
        {
            global $app;

            $settings = $app->getContainer()->settings;

            $metaTags = <<<META_TAGS
                <meta name="keywords" itemprop="keywords" content="organic food">
                <meta name="description" itemprop="description" content="Обычные моющие средства насыщают жильё отравляющими веществами. Мы убираем в доме не только для красоты, но и для здоровья. Чтобы не дышать пылью и плесенью">
                <meta property="og:image" content="http://shop.junimed.ua/images/header-logo.png">
                <meta property="og:description" content="Обычные моющие средства насыщают жильё отравляющими веществами. Мы убираем в доме не только для красоты, но и для здоровья. Чтобы не дышать пылью и плесенью">
                <meta property="og:title" content="{$settings['appName']}">
                <meta property="og:locale" content="ru_RU">
                <meta property="og:type" content="article">
                <meta property="og:url" content="{$settings['siteUrl']}{$request->getParam('path')}">
                <meta property="og:site_name" content="{$settings['appName']}">
                <link rel="canonical" href="http://{$settings['siteUrl']}/" />
META_TAGS;

            return $response->write($metaTags);
        }
    }
