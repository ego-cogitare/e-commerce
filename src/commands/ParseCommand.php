<?php
    namespace Commands;

    use Components\ExcelParser;
    use Components\ParserContainer;
    use Models\Product;
    use Models\Brand;
    use Models\Category;
    use Models\ProductProperty;
    use Models\Media;
    
    require_once '../../bootstrap.php';
    
    $parser_config = [
        'Dr George.xlsx' => [
            'dataRows' => [0, 11],
            'sku' => 0,
            'propsColumns' => [1, 2, 7],
            'title' => 3,
            'briefly' => 4,
            'description' => 4,
            'pricePdv' => 6,
            'priceNds' => 6,
            'brand' => '/Dr\.Goerg/',
            'picture' => [
                'column' => 'F',
                'offset' => 1
            ],
            'awards' => [
                'column' => 'I',
                'offset' => 1
            ],
        ],
        'Прайс Baby Bjorn.xlsx' => [
            'dataRows' => [0, 60],
            'sku' => 1,
            'propsColumns' => [0, 2],
            'title' => 3,
            'briefly' => 4,
            'description' => 4,
            'pricePdv' => 6,
            'priceNds' => 6,
            'brand' => '/Baby Bjorn/',
            'picture' => [
                'column' => 'F',
                'offset' => 1
            ],
        ],
        'Прайс соки EOS.xlsx' => [
            'dataRows' => [0, 18],
            'sku' => 0,
            'propsColumns' => [1, 2, 7, 8],
            'title' => 3,
            'briefly' => 4,
            'description' => 4,
            'pricePdv' => 6,
            'priceNds' => 6,
            'brand' => '/EOS BIO/',
            'picture' => [
                'column' => 'F',
                'offset' => 1
            ],
            'awards' => [
                'column' => 'J',
                'offset' => 1
            ],
        ],
        'Прайс снеки.xlsx' => [
            'dataRows' => [0, 16],
            'sku' => 0,
            'propsColumns' => [1, 2, 7, 8],
            'title' => 3,
            'briefly' => 4,
            'description' => 4,
            'pricePdv' => 6,
            'priceNds' => 6,
            'brand' => '/VitaSnack/',
            'picture' => [
                'column' => 'F',
                'offset' => 1
            ],
            'awards' => [
                'column' => 'J',
                'offset' => 1
            ],
        ],
        'Прайс продукты питания Primeal, Ma vie sans gluten, bisson.xlsx' => [ //-
            'dataRows' => [0, 37],
            'sku' => 0,
            'propsColumns' => [1, 2, 7],
            'title' => 3,
            'briefly' => 4,
            'description' => 4,
            'pricePdv' => 6,
            'priceNds' => 6,
            'brand' => '/Le Pain des fleurs/',
            'picture' => [
                'column' => 'F',
                'offset' => 1
            ],
            'awards' => [
                'column' => 'I',
                'offset' => 1
            ],
        ],
        'Прайс органические специи Lebensbaum.xlsx' => [
            'dataRows' => [0, 25],
            'sku' => 0,
            'propsColumns' => [1, 2, 7, 8],
            'title' => 3,
            'briefly' => 4,
            'description' => 4,
            'pricePdv' => 6,
            'priceNds' => 6,
            'brand' => '/Lebensbaum/',
            'picture' => [
                'column' => 'F',
                'offset' => 1
            ],
            'awards' => [
                'column' => 'J',
                'offset' => 1
            ],
        ],
        'Прайс молоко Ecomil (розширена інформація).xlsx' => [
            'dataRows' => [0, 40],
            'sku' => 0,
            'propsColumns' => [1, 2, 7, 8],
            'title' => 3,
            'briefly' => 4,
            'description' => 4,
            'pricePdv' => 6,
            'priceNds' => 6,
            'brand' => '/Ecomil та NaturGreen/',
            'picture' => [
                'column' => 'F',
                'offset' => 1
            ],
            'awards' => [
                'column' => 'J',
                'offset' => 1
            ],
        ],
        'Прайс Джордан 2018.xlsx' => [
            'dataRows' => [0, 31],
            'sku' => 1,
            'propsColumns' => [0],
            'title' => 2,
            'briefly' => 3,
            'description' => 3,
            'pricePdv' => 4,
            'priceNds' => 4,
            'brand' => '/TM Jordan/',
            'picture' => [
                'column' => 'F',
                'offset' => 1
            ],
        ],
        'Прайс гималайская соль 2018.xlsx' => [
            'dataRows' => [0, 6],
            'sku' => 0,
            'propsColumns' => [1, 2, 7, 8],
            'title' => 3,
            'briefly' => 4,
            'description' => 4,
            'pricePdv' => 6,
            'priceNds' => 6,
            'brand' => '/Гималайская соль/',
            'picture' => [
                'column' => 'F',
                'offset' => 1
            ],
            'awards' => [
                'column' => 'J',
                'offset' => 1
            ],
        ],
        'Прайс  бытовая химия Sonett 2018.xlsx' => [
            'dataRows' => [0, 38],
            'sku' => 0,
            'propsColumns' => [1, 2],
            'title' => 3,
            'briefly' => 4,
            'description' => 4,
            'pricePdv' => 6,
            'priceNds' => 6,
            'brand' => '/Sonett/',
            'picture' => [
                'column' => 'F',
                'offset' => 1
            ],
            'awards' => [
                'column' => 'H',
                'offset' => 1
            ],
        ],
        'Прайс UrtekramФОТО2018.xlsx' => [
            'dataRows' => [0, 56],
            'sku' => 0,
            'propsColumns' => [1, 2],
            'title' => 3,
            'briefly' => 4,
            'description' => 4,
            'pricePdv' => 6,
            'priceNds' => 6,
            'brand' => '/Urtekram/',
            'picture' => [
                'column' => 'F',
                'offset' => 1
            ],
            'awards' => [
                'column' => 'H',
                'offset' => 1
            ],
        ],
        'Прайс  Medela  2018.xlsx' => [
            'dataRows' => [0, 33],
            'sku' => 1,
            'propsColumns' => [0, 2],
            'title' => 3,
            'briefly' => 4,
            'description' => 4,
            'pricePdv' => 6,
            'priceNds' => 6,
            'brand' => '/Medela/',
            'picture' => [
                'column' => 'F',
                'offset' => 1
            ],
        ],
    ];
    
    $parser = new ParserContainer();
    
    foreach ($parser_config as $file_name => $parser_config)
    {
        $excel_parser = new ExcelParser(__DIR__ . '/../data/' . $file_name, $parser_config);
        // Create parser instanse
        $parser->setParser($excel_parser);

        // Parse results
        $parser->parse();

        $brand = null;
        $category = null;
        $data = $parser->getData();
        
        foreach ($data as $key => $row)
        {
            // Obtain table header
            if ($key === 0)
            {
                $header_row = $row;
                //print_r($header_row);exit;
                continue;
            }
            
            // Check if row with category/brand titles
            if (!empty($row[0]) && empty($row[1]) && empty($row[2]))
            {
                if (!preg_match($parser_config['brand'], $row[0], $matches)) 
                {
                    throw new \Exception('Brand not found.');
                }
                // Try to find brand with one of the matched names
                $brand = Brand::fetchOne([
                    'title' => $matches[0],
                    'isDeleted' => [
                        '$ne' => true
                    ]
                ]);
                // If brand not found - add new brand record
                if (empty($brand)) 
                {
                    $brand = Brand::getBootstrap();
                    $brand->type = 'final';
                    $brand->title = $matches[0];
                    $brand->save();
                }
                
                // Try to find category
                $category = Category::fetchOne([
                    'title' => $row[0],
                    'isDeleted' => [
                        '$ne' => true
                    ]
                ]);
                if (empty($category))
                {
                    $category = Category::getBootstrap();
                    $category->title = $row[0];
                    $category->type = 'final';
                    $category->save();
                }
                continue;
            }
            
            $pictureIds = [
                'picture' => [],
                'awards' => [],
            ];
            
            foreach (array_keys($pictureIds) as $pictureType)
            {
                if (isset($parser_config[$pictureType])) 
                {
                    // Get product awards
                    $picNamePattern = __DIR__ . '/../data/' . $file_name . '_' 
                        . $parser_config[$pictureType]['column'] 
                        . ($parser_config[$pictureType]['offset'] + $key) . '_*.*';

                    $pictures = glob($picNamePattern);

                    if (sizeof($pictures) < 1)
                    {
                        echo $row[$parser_config['sku']] . ' - product picture not found!' . PHP_EOL;
                    }
                    else
                    {
                        // Move pictures to project media content folder
                        foreach ($pictures as $picturePath)
                        {
                            $destPath = __DIR__ . '/../../public/uploads/';
                            $destName = $destPath . md5($picturePath) . '.png';// . pathinfo($picturePath, PATHINFO_EXTENSION);
                            $imageSize = getimagesize($picturePath);

                            if ($imageSize[0] > 530 || $imageSize[1] > 455) 
                            {
                                $img = new \Imagick($picturePath); 
                                $img->scaleImage(530, 0); 
                                if($imageSize[1] > 455) 
                                { 
                                    $img->scaleImage(0, 455); 
                                    $img->writeImage($destName); 
                                } 
                                else 
                                { 
                                    $img->writeImage($destName); 
                                } 
                                $img->destroy();
                            }
                            else 
                            {
                                copy($picturePath, $destName);
                            }

                            // Creating database record for the picture
                            if (file_exists($destName))
                            {
                                $media = new Media();
                                $media->name = basename($destName);
                                $media->path = '/uploads';
                                $media->size = filesize($destName);
                                $media->type = mime_content_type($destName);
                                $media->isDeleted = false;
                                $media->save();
                                $pictureIds[$pictureType][] = $media->id;
                            }
                            else
                            {
                                echo 'File not copied (' . $destName . ')' . PHP_EOL;
                            }
                        }
                    }
                }
            }
            
            $product = new Product;
            
            /** 
             * Autogenerated fields
             */
            $product->isDeleted = false;
            $product->dateCreated = time();
            $product->type = 'final';
            $product->isAvailable = true;
            $product->isAuction = false;
            $product->isBestseller = false;
            $product->isNovelty = false;
            $product->relatedProducts = [];
            $product->video = '';
            $product->discountTimeout = 0;
            
            /** 
             * Fields from excel
             */
            $product->sku = $row[$parser_config['sku']];
            $product->title = preg_replace('/\s+/', ' ', $row[$parser_config['title']]);
            $product->briefly = preg_replace('/\s+/', ' ', $row[$parser_config['briefly']]);
            $product->description = preg_replace('/\s+/', ' ', $row[$parser_config['description']]);
            if (empty($parser_config['priceNds'])) 
            {
                $product->price = 0.0;
                $product->discount = 0.0;
                $product->discountType = '';
            }
            else 
            {
                // Clear prices
                $price_nds = (float)preg_replace('/[^\d\.]*/', '', $row[$parser_config['priceNds']]);
                $price_pdv = (float)preg_replace('/[^\d\.]*/', '', $row[$parser_config['pricePdv']]);
                
                $product->price = $price_nds;
                $product->discount = $price_nds - $price_pdv;
                $product->discountType = 'const';
            }
            $product->awards = $pictureIds['awards'];
            $product->pictures = $pictureIds['picture'];
            $product->pictureId = sizeof($pictureIds['picture']) > 0 ? $pictureIds['picture'][0] : '';
            $product->brandId = $brand->id;
            $product->categoryId = $category->id;
            
            // Save product properties
            $properties = [];
            foreach ($header_row as $i => $prop_title)
            {
                if (in_array($i, $parser_config['propsColumns']))
                {
                    // Save product properties titles
                    $property_title = ProductProperty::fetchOne([
                        'key' => $prop_title,
                        'isDeleted' => [
                            '$ne' => true
                        ]
                    ]);
                    if (empty($property_title))
                    {
                        $property_title = new ProductProperty;
                        $property_title->key = $prop_title;
                        $property_title->isDeleted = false;
                        $property_title->parentId = '';
                        $property_title->save();
                    }
                    
                    // Save product properties
                    $key = (string)trim($row[$i]);
                    
                    if (!empty($key))
                    {
                        $property_product = ProductProperty::fetchOne([
                            'key' => $key,
                            'isDeleted' => [
                                '$ne' => true
                            ],
                            'parentId' => $property_title->id
                        ]);
                        
                        if (empty($property_product))
                        {
                            $property_product = new ProductProperty;
                            $property_product->key = $row[$i];
                            $property_product->isDeleted = false;
                            $property_product->parentId = $property_title->id;
                            $property_product->save();
                        }
                        
                        $properties[] = $property_product->id;
                    }
                }
            }
            $product->properties = $properties;
            
            $product->save();
        }
    }

