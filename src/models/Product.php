<?php
namespace Models;

/**
 * Class Product
 *
 * @collection Product
 *
 * @primary id
 *
 * @property string     $id
 * @property string     $title
 * @property string     $categoryId
 * @property string     $brandId
 * @property array      $pictures
 * @property array      $properties
 * @property string     $pictureId
 * @property string     $briefly
 * @property string     $description
 * @property array      $relatedProducts
 * @property boolean    $isNovelty
 * @property boolean    $isAuction
 * @property boolean    $isBestseller
 * @property float      $price
 * @property string     $sku
 * @property string     $video
 * @property float      $discount
 * @property string     $discountType
 * @property int        $discountTimeout
 * @property boolean    $isAvailable
 * @property int        $availableAmount
 * @property boolean    $isDeleted
 * @property string     $type
 * @property int        $dateCreated
 *
 * @method void save()
 */
class Product extends \MongoStar\Model {

    public static function getBootstrap()
    {
        $bootstrap = new self();
        $bootstrap->type = 'bootstrap';
        $bootstrap->isDeleted = false;
        $bootstrap->isAvailable = true;
        $bootstrap->isAuction = false;
        $bootstrap->isBestseller = false;
        $bootstrap->isNovelty = false;
        $bootstrap->title = '';
        $bootstrap->briefly = '';
        $bootstrap->description = '';
        $bootstrap->brandId = '';
        $bootstrap->categoryId = '';
        $bootstrap->relatedProducts = [];
        $bootstrap->pictures = [];
        $bootstrap->properties = [];
        $bootstrap->pictureId = null;
        $bootstrap->price = 0;
        $bootstrap->sku = '';
        $bootstrap->video = '';
        $bootstrap->discount = 0;
        $bootstrap->discountType = '';
        $bootstrap->discountTimeout = '';
        $bootstrap->dateCreated = time();

        return $bootstrap;
    }
    
    /**
     * 
     * @param int $maxDepth
     * @param int $depth
     * @param array $skip List of products fields to ignore for expand
     * @return array
     */
    public function apiModel($maxDepth = 1, $depth = 0, $skip = [])
    {
        if ($depth > $maxDepth) {
            return null;
        }
        
        // Expand with related products
        $relatedProducts = [];
        if (!in_array('relatedProducts', $skip)) {
            if (count($this->relatedProducts) > 0) {
                foreach ($this->relatedProducts as $relatedProductId) {
                    $relatedProducts[] = self::fetchOne(['id' => $relatedProductId]);
                }
            }
            if (count($relatedProducts) > 0) {
                foreach ($relatedProducts as $key=>$product) {
                    $relatedProducts[$key] = $product->apiModel($maxDepth, $depth + 1, $expandFlags);
                }
            }
        }
        $this->relatedProducts = $relatedProducts;

        // Expand with properties
        $properties = [];
        if (!in_array('properties', $skip) && count($this->properties) > 0) {
            foreach ($this->properties as $propertyId) {
                $propValue = \Models\ProductProperty::fetchOne(['id' => $propertyId]);
                if ($propValue && $propLabel = \Models\ProductProperty::fetchOne(['id' => $propValue->parentId])) {
                    $properties[] = [
                        'label' => $propLabel->key,
                        'value' => $propValue->key,
                    ];
                }
            }
        }
        $this->properties = $properties;
        
        // Expand with pictures
        $defaultPicture = null;
        $pictures = [];
        if (count($this->pictures) > 0) {
            foreach ($this->pictures as $pictureId) {
                $picture = \Models\Media::fetchOne(['id' => $pictureId]);
                if ($picture) {
                    $pictures[] = $picture->toArray();
                    if ($pictureId === $this->pictureId) {
                        $defaultPicture = $picture->toArray();
                    }
                }
            }
        }
        if (!in_array('pictures', $skip)) {
            $this->pictures = $pictures;
        }
        
        // If default picture not set use first available from pictures list
        if (is_null($defaultPicture) && count($pictures) > 0) {
            $defaultPicture = $pictures[0];
        }
        
        // Expand with reviews
        $reviews = [];
        if (!in_array('reviews', $skip)) {
            $reviews = \Models\ProductReview::fetchAll([
                'productId' => $this->id,
                'isDeleted' => [
                    '$ne' => true
                ],
                'isApproved' => true
            ], ['dateCreated' => -1])->toArray();
        }
        
        // Product category
        $category = null;
        if (!in_array('category', $skip) && !empty($this->categoryId)) {
            $category = \Models\Category::fetchOne(['id' => $this->categoryId]);
            $category = $category ? $category->toArray() : null;
        }
        
        // Product brand
        $brand = null;
        if (!in_array('brand', $skip) && !empty($this->brandId)) {
            $brand = \Models\Brand::fetchOne(['id' => $this->brandId]);
            $brand = $brand ? $brand->toArray() : null;
        }
        
        $product = $this->toArray();
        
        // Remove text descriptions
        if (in_array('descriptions', $skip)) {
            $product['briefly'] = '';
            $product['description'] = '';
        }

        return array_merge(
            $product,
            ['picture' => $defaultPicture],
            ['category' => $category],
            ['brand' => $brand],
            ['reviews'=> $reviews]
        );
    }

    public function expand()
    {
        // Expand with related products
        $relatedProducts = [];
        if (count($this->relatedProducts) > 0) {
            foreach ($this->relatedProducts as $relatedProductId) {
                $relatedProducts[] = self::fetchOne([
                    'id' => $relatedProductId
                ])->toArray();
            }
        }
        if (count($relatedProducts) > 0) {
            foreach ($relatedProducts as $key=>$product) {
                $pictures = [];

                if (count($product['pictures']) > 0) {
                    foreach ($product['pictures'] as $pictureId) {
                        $pictures[] = \Models\Media::fetchOne([
                            'id' => $pictureId
                        ])->toArray();
                    }
                }
                $relatedProducts[$key]['pictures'] = $pictures;
            }
        }
        $this->relatedProducts = $relatedProducts;

        // Expand with pictures
        $pictures = [];
        if (count($this->pictures) > 0) {
            foreach ($this->pictures as $pictureId) {
                $pictures[] = \Models\Media::fetchOne([
                    'id' => $pictureId
                ])->toArray();
            }
        }
        $this->pictures = $pictures;
        
        if (empty($this->properties)) {
            $this->properties = [];
        }

        return $this;
    }
}
