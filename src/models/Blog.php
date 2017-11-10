<?php
namespace Models;

/**
 * Class Page
 *
 * @collection Blog
 *
 * @primary id
 *
 * @property string     $id
 * @property string     $title
 * @property string     $body
 * @property boolean    $isVisible
 * @property int        $dateCreated
 * @property boolean    $isDeleted
 *
 * @method void save()
 */
class Post extends \MongoStar\Model {}
