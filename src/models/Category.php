<?php
namespace Models;

/**
 * Class Category
 *
 * @collection Category
 *
 * @primary id
 *
 * @property string     $id
 * @property string     $parrentId
 * @property string     $title
 * @property string     $description
 * @property array      $pictures
 * @property string     $pictureId
 * @property float      $discount
 * @property string     $discountType
 * @property boolean    $isDeleted
 * @property boolean    $isHidden
 * @property int        $order
 *
 * @method void save()
 */
class Category extends \MongoStar\Model {

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
