<?php
    namespace Commands;

    use Models\Media;
    use Models\Product;

    require_once __DIR__ . '/../../bootstrap.php';

    $products = Product::fetchAll([
        'isDeleted' => [
            '$ne' => true
        ]
    ]);

    $picturesPath = __DIR__ . '/../../public/uploads/images';

    foreach ($products as $product) {
        if (empty($product->pictures)) {
            continue;
        }

        foreach ($product->pictures as $pictureId) {
            /** Get picture file data */
            $picture = Media::fetchOne(['id' => $pictureId]);

            if ($picture === null) {
                continue;
            }

            $imageNum = 0;

            do {
                $imageNum++;
                $newName = $product->slug . '-' . $imageNum . '.' . end(explode('.', $picture->name));
            }
            while (file_exists($picturesPath . '/' . $newName));

            if (rename($picturesPath . '/' . $picture->name, $picturesPath . '/' . $newName)) {
                $picture->name = $newName;
                $picture->save();
            }
        }
    }

