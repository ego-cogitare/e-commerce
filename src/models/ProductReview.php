<?php
namespace Models;

/**
 * Class ProductReview
 *
 * @collection ProductReview
 *
 * @primary id
 *
 * @property string     $id
 * @property string     $productId
 * @property int        $rate
 * @property string     $userName
 * @property string     $review
 * @property int        $dateCreated
 * @property boolean    $isApproved
 * @property boolean    $isDeleted
 *
 * @method void save()
 */
class ProductReview extends \MongoStar\Model {}