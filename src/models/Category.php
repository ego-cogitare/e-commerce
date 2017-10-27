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
 * @property string     $title
 * @property string     $description
 * @property boolean    $isDeleted
 * @property array      $settings
 *
 * @method void save()
 */
class Category extends \MongoStar\Model {}