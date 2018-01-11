<?php
namespace Models;

/**
 * Class Menu
 *
 * @collection Menu
 *
 * @primary id
 *
 * @property string     $id
 * @property string     $parrentId
 * @property string     $title
 * @property string     $link
 * @property boolean    $isDeleted
 * @property boolean    $isHidden
 * @property int        $order
 * @property int        $dateCreated
 *
 * @method void save()
 */
class Menu extends \MongoStar\Model {
    
//    public static function getBootstrap()
//    {
//        $bootstrap = new self();
//        $bootstrap->parrentId = '';
//        $bootstrap->title = '';
//        $bootstrap->isDeleted = 0;
//        $bootstrap->isHidden = 0;
//        $bootstrap->order = 9999;
//        $bootstrap->dateCreated = time();
//
//        return $bootstrap;
//    }
}
