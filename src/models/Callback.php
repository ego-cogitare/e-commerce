<?php
namespace Models;

/**
 * Class Callback
 *
 * @collection Callback
 *
 * @primary id
 *
 * @property string     $id
 * @property string     $name
 * @property string     $phone
 * @property boolean    $isProcessed
 * @property string     $comment
 * @property int        $dateCreated
 * @property boolean    $isDeleted
 *
 * @method void save()
 */
class Callback extends \MongoStar\Model
{
}
