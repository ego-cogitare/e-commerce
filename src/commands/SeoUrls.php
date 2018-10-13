<?php
    namespace Commands;

    use Models\{
        Product, Page, Brand, Category, Post
    };

    require_once __DIR__ . '/../../bootstrap.php';

    function rus2translit($string) {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '',    'ы' => 'y',   'ъ' => '',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
            'і' => 'i',   'є' => 'e',   'ї' => 'yi',

            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '',    'Ы' => 'Y',   'Ъ' => '',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
            'І' => 'I',   'Є' => 'E',   'Ї' => 'Yi'
        );

        return strtr($string, $converter);
    }

    function str2url($str) {
        $str = str_replace(['ö', 'Ö'], ['o','O'], $str);
        $str = rus2translit($str);
        $str = strtolower($str);
        $str = preg_replace('~\s+~u', '-', $str);
        $str = preg_replace('~[^-a-z0-9_]+~u', '', $str);
        $str = trim($str, "-");

        return $str;
    }

    foreach ([Product::class, Page::class, Brand::class, Category::class, Post::class] as $class) {
        $items = $class::fetchAll();

        if (empty($items)) {
            continue;
        }

        $slugs = [];

        foreach ($items as $item) {
            $item->slug = str2url($item->title);
            $item->save();
            $slugs[$item->slug][] = $item->id;
        }

        foreach ($slugs as $slug => $ids) {
            if (count($ids) < 2) {
                continue;
            }
            for ($i = 1; $i < count($ids); $i++) {
                if (($item = $class::fetchOne(['id' => $ids[$i]])) === null) {
                    continue;
                }
                $item->slug = $item->slug . '-' . $i;
                $item->save();
            }
        }
    }

