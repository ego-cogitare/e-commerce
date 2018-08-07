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
 * @property float      $price
 * @property string     $rawData
 * @property int        $dateCreated
 *
 * @method void save()
 */
class Payment extends \MongoStar\Model
{
}
