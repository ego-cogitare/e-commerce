<?php
    namespace Commands;

    use Models\Media;

    require_once __DIR__ . '/../../bootstrap.php';

    $images = Media::fetchAll([
        'isDeleted' => [
            '$ne' => true
        ]
    ]);

    foreach ($images as $image) {
        $destPath = __DIR__ . '/../../public/uploads/';
        $sourceImage = $destPath . $image->name;

        if (!file_exists($sourceImage)) {
            echo 'Image not found: ' . $sourceImage . PHP_EOL;
            continue;
        }

        $destName = $destPath . 'images/' . pathinfo($image->name, PATHINFO_FILENAME) . '.jpg';
        $imageSize = getimagesize($sourceImage);

        $img = new \Imagick($sourceImage);
        $img->setImageCompressionQuality(80);

        if ($imageSize[0] > 530 || $imageSize[1] > 455)
        {
            $img->scaleImage(530, 0);
            if($imageSize[1] > 455)
            {
                $img->scaleImage(0, 455);
            }
        }
        $img = $img->flattenImages();
        $img->writeImage($destName);
        $img->destroy();

        // Creating database record for the picture
        if (file_exists($destName))
        {
            $image->name = basename($destName);
            $image->path = '/uploads/images';
            $image->size = filesize($destName);
            $image->type = mime_content_type($destName);
            $image->save();
        }
    }

