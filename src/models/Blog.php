<?php
namespace Models;

/**
 * Class Blog
 *
 * @collection Blog
 *
 * @primary id
 *
 * @property string     $id
 * @property string     $title
 * @property string     $briefly
 * @property string     $body
 * @property array      $tags
 * @property array      $pictures
 * @property string     $pictureId
 * @property boolean    $isVisible
 * @property int        $dateCreated
 * @property string     $type
 * @property boolean    $isDeleted
 *
 * @method void save()
 */
class Post extends \MongoStar\Model 
{
    public static function getBootstrap()
    {
        $bootstrap = new self();
        $bootstrap->type = 'bootstrap';
        $bootstrap->isDeleted = false;
        $bootstrap->isVisible = true;
        $bootstrap->title = '';
        $bootstrap->briefly = '';
        $bootstrap->body = '';
        $bootstrap->tags = [];
        $bootstrap->pictures = [];
        $bootstrap->pictureId = null;
        $bootstrap->dateCreated = time();

        return $bootstrap;
    }

    public function expand()
    {
        // Expand with pictures
        $pictures = [];
        if (count($this->pictures) > 0) {
            foreach ($this->pictures as $pictureId) {
                $pictures[] = \Models\Media::fetchOne([
                    'id' => $pictureId
                ])->toArray();
            }
        }
        $this->pictures = $pictures;

        return $this;
    }
}
