<?php
namespace Models;

/**
 * Class Settings
 *
 * @collection Settings
 *
 * @primary id
 *
 * @property string     $id       
 * @property string     $key
 * @property string     $value
 *
 * @method void save()
 */
class Settings extends \MongoStar\Model {}