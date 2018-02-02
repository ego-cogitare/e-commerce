<?php
namespace Models;

/**
 * Class PostComment
 *
 * @collection PostComment
 *
 * @primary id
 *
 * @property string     $id
 * @property string     $postId
 * @property string     $userName
 * @property string     $comment
 * @property int        $dateCreated
 * @property boolean    $isApproved
 * @property boolean    $isDeleted
 *
 * @method void save()
 */
class PostComment extends \MongoStar\Model {}