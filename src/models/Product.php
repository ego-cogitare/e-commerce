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
 * @property boolean    $isNew
 * @property boolean    $isAction
 * @property float      $discount
 * @property string     $discountType
 * @property boolean    $isAvailable
 * @property int        $availableAmount
 * @property boolean    $isDeleted
 * @property int        $dateCreated
 *
 * @method void save()
 */
class Product extends \MongoStar\Model {}