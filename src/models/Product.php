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
 * @property string     $pictureId
 * @property string     $description
 * @property array      $relatedProducts
 * @property boolean    $isNovelty
 * @property boolean    $isAuction
 * @property boolean    $isBestseller
 * @property float      $price
 * @property float      $discount
 * @property string     $discountType
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
        $bootstrap->description = '';
        $bootstrap->brandId = '';
        $bootstrap->categoryId = '';
        $bootstrap->relatedProducts = [];
        $bootstrap->pictures = [];
        $bootstrap->pictureId = null;
        $bootstrap->price = 0;
        $bootstrap->discount = 0;
        $bootstrap->discountType = '';
        $bootstrap->dateCreated = time();

        return $bootstrap;
    }
    
    public function apiModel($maxDepth = 1, $depth = 0)
    {
        if ($depth > $maxDepth) {
            return null;
        }
        // Expand with related products
        $relatedProducts = [];
        if (count($this->relatedProducts) > 0) {
            foreach ($this->relatedProducts as $relatedProductId) {
                $relatedProducts[] = self::fetchOne(['id' => $relatedProductId]);
            }
        }
        if (count($relatedProducts) > 0) {
            foreach ($relatedProducts as $key=>$product) {
                $relatedProducts[$key] = $product->apiModel($maxDepth, $depth + 1);
            }
        }
        $this->relatedProducts = $relatedProducts;

        // Expand with pictures
        $defaultPicture = null;
        $pictures = [];
        if (count($this->pictures) > 0) {
            foreach ($this->pictures as $pictureId) {
                $picture = \Models\Media::fetchOne(['id' => $pictureId]);
                if ($picture) {
                    $pictures[] = $picture->toArray();
                    if ($pictureId === $this->pictureId) {
                        $picture = $picture->toArray();
                    }
                }
            }
        }
        $this->pictures = $pictures;
        
        // If default picture not set use first available from pictures list
        if (is_null($defaultPicture) && count($pictures) > 0) {
            $defaultPicture = $pictures[0];
        }
        
        // Product category
        $category = null;
        if (!empty($this->categoryId)) {
            $category = \Models\Category::fetchOne(['id' => $this->categoryId]);
            $category = $category ? $category->toArray() : null;
        }
        
        // Product brand
        $brand = null;
        if (!empty($this->brandId)) {
            $brand = \Models\Brand::fetchOne(['id' => $this->brandId]);
            $brand = $brand ? $brand->toArray() : null;
        }

        return array_merge(
            $this->toArray(),
            ['picture' => $defaultPicture],
            ['category' => $category],
            ['brand' => $brand]
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

        return $this;
    }
}
