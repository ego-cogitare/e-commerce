<?php
namespace Models;

/**
 * Class Media
 *
 * @collection Media
 *
 * @primary id
 *
 * @property string     $id       
 * @property string     $name
 * @property string     $path
 * @property int        $size
 * @property string     $type
 * @property boolean    $isDeleted
 *
 * @method void save()
 */
class Media extends \MongoStar\Model {}