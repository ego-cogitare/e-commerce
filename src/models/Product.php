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
 * @property array      $categories
 * @property array      $pictures
 * @property string     $pictureId
 * @property string     $description
 * @property array      $relatedProducts
 * @property boolean    $isNovelty
 * @property boolean    $isAuction
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
        $bootstrap->isNovelty = false;
        $bootstrap->title = '';
        $bootstrap->description = '';
        $bootstrap->categories = [];
        $bootstrap->relatedProducts = [];
        $bootstrap->pictures = [];
        $bootstrap->pictureId = null;
        $bootstrap->discount = 0;
        $bootstrap->discountType = '';
        $bootstrap->dateCreated = time();
        
        return $bootstrap;
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