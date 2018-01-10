<?php
namespace Models;

/**
 * Class ProductProperty
 *
 * @collection ProductProperty
 *
 * @primary id
 *
 * @property string     $id       
 * @property string     $key
 * @property boolean    $isDeleted
 *
 * @method void save()
 */
class ProductProperty extends \MongoStar\Model {}