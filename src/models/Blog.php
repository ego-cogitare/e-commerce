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
 * @property boolean    $showOnHome
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
        $bootstrap->showOnHome = false;
        $bootstrap->title = '';
        $bootstrap->briefly = '';
        $bootstrap->body = '';
        $bootstrap->tags = [];
        $bootstrap->pictures = [];
        $bootstrap->pictureId = null;
        $bootstrap->dateCreated = time();

        return $bootstrap;
    }
    
    /**
     * Post full api model
     */
    public function apiModel($skip = []) 
    {
        // Expand with pictures
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
        if (!in_array('pictures', $skip)) {
            $this->pictures = $pictures;
        }
        
        // If default picture not set use first available from pictures list
        if (is_null($defaultPicture) && count($pictures) > 0) {
            $defaultPicture = $pictures[0];
        }
        
        $tags = [];
        if (!in_array('tags', $skip) && count($this->tags) > 0) {
            foreach ($this->tags as $tagId) {
                $tags[] = \Models\Tag::fetchOne(['id' => $tagId])->toArray();
            }
        }
        $this->tags = $tags;
        
        $post = $this->toArray();
        
        // Remove text descriptions
        if (in_array('descriptions', $skip)) {
            $post['briefly'] = '';
            $post['body'] = '';
        }
        
        return array_merge(
            $post,
            ['picture' => $defaultPicture]
        );
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
        
        // Expand with tags
        $tags = [];
        if (count($this->tags) > 0) {
            foreach ($this->tags as $tagId) {
                $tags[] = \Models\Tag::fetchOne([
                    'id' => $tagId
                ])->toArray();
            }
        }
        $this->tags = $tags;

        return $this;
    }
}
