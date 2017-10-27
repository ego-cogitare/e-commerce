<?php
namespace Models;

/**
 * Class Category
 *
 * @collection Category
 *
 * @primary id
 *
 * @property string     $id
 * @property string     $parrentId
 * @property string     $title
 * @property string     $description
 * @property boolean    $isDeleted
 * @property boolean    $isHidden
 * @property float      $discount
 * @property string     $discountType
 *
 * @method void save()
 */
class Category extends \MongoStar\Model {}