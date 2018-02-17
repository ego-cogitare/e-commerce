<?php
namespace Models;

/**
 * Class Payment
 *
 * @collection Payment
 *
 * @primary id
 *
 * @property string     $id
 * @property string     $orderId
 * @property string     $email
 * @property float      $price
 * @property int        $dateCreated
 * @property boolean    $isDeleted
 *
 * @method void save()
 */
class Payment extends \MongoStar\Model
{
 
}
