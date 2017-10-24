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
 * @property string     $description
 * @property boolean    $isNew
 * @property boolean    $isAction
 * @property array      $discount
 * @property boolean    $isAvailable
 * @property int        $availableAmount
 * @property string     $currency
 * @property boolean    $isDeleted
 * @property int        $dateCreated
 *
 * @method void save()
 */
class Product extends \MongoStar\Model {}