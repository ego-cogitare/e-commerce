<?php
namespace Models;

/**
 * Class Order
 *
 * @collection Order
 *
 * @primary id
 *
 * @property string     $id
 * @property array      $products
 * @property string     $firstName
 * @property string     $lastName
 * @property string     $email
 * @property string     $phone
 * @property int        $dateCreated
 * @property boolean    $isDeleted
 *
 * @method void save()
 */
class Order extends \MongoStar\Model {}
