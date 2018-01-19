<?php
namespace Models;

/**
 * Class Tag
 *
 * @collection Tag
 *
 * @primary id
 *
 * @property string     $id
 * @property string     $title
 * @property boolean    $isDeleted
 * @property int        $dateCreated
 *
 * @method void save()
 */
class Tag extends \MongoStar\Model {}
