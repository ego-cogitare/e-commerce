<?php
namespace Models;

/**
 * Class User
 *
 * @collection User
 *
 * @primary id
 *
 * @property string     $id       
 * @property string     $firstname
 * @property string     $lastname
 * @property string     $username
 * @property string     $password
 * @property array      $roleId
 * @property boolean    $isActive
 * @property boolean    $isDeleted
 * @property int        $createdAt
 *
 * @method void save()
 */
class User extends \MongoStar\Model {}