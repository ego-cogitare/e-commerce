<?php
namespace Models;

/**
 * Class Brand
 *
 * @collection Brand
 *
 * @primary id
 *
 * @property string     $id       
 * @property string     $title
 * @property string     $slug
 * @property string     $pictureId
 * @property array      $pictures
 * @property string     $coverId
 * @property array      $covers
 * @property string     $type
 * @property string     $body
 * @property boolean    $isDeleted
 * @property int        $dateCreated
 *
 * @method void save()
 */
class Brand extends \MongoStar\Model 
{
    public static function getBootstrap()
    {
        $bootstrap = new self();
        $bootstrap->type = 'bootstrap';
        $bootstrap->isDeleted = false;
        $bootstrap->title = '';
        $bootstrap->slug = '';
        $bootstrap->body = '';
        $bootstrap->pictures = [];
        $bootstrap->pictureId = null;
        $bootstrap->covers = [];
        $bootstrap->coverId = null;
        $bootstrap->dateCreated = time();

        return $bootstrap;
    }
    
    public function apiModel($skip = []) 
    {
        $defaultPicture = null;
        $pictures = [];
        if (count($this->pictures) > 0) {
            foreach ($this->pictures as $pictureId) {
                $picture = \Models\Media::fetchOne(['id' => $pictureId]);
                if ($picture) {
                    $pictures[] = $picture->toArray();
                    if ($pictureId === $this->pictureId) {
                        $defaultPicture = $picture->toArray();
                    }
                }
            }
        }
        else {
            $this->pictures = [];
        }
        
        if (!in_array('pictures', $skip)) {
            $this->pictures = $pictures;
        }
        
        // If default picture not set use first available from pictures list
        if (is_null($defaultPicture) && count($pictures) > 0) {
            $defaultPicture = $pictures[0];
        }
        
        $defaultCover = null;
        $pictures = [];
        if (count($this->covers) > 0) {
            foreach ($this->covers as $pictureId) {
                $picture = \Models\Media::fetchOne(['id' => $pictureId]);
                if ($picture) {
                    $pictures[] = $picture->toArray();
                    if ($pictureId === $this->pictureId) {
                        $defaultCover = $picture->toArray();
                    }
                }
            }
        }
        else {
            $this->covers = [];
        }
        
        if (!in_array('covers', $skip)) {
            $this->covers = $pictures;
        }
        
        // If default picture not set use first available from pictures list
        if (is_null($defaultCover) && count($pictures) > 0) {
            $defaultCover = $pictures[0];
        }
        
        $brand = $this->toArray();
        
        return array_merge(
            $brand,
            ['picture' => $defaultPicture],
            ['cover' => $defaultCover]
        );
    }
}