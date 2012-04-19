<?php

/**
 * Upgrade 3.2.6 moves all of our item thumbnails into the default assetstore
 * as bitstreams.
 */
class Upgrade_3_2_6 extends MIDASUpgrade
{
  var $assetstore;

  public function preUpgrade()
    {
    $modelLoader = new MIDAS_ModelLoader();
    $assetstoreModel = $modelLoader->loadModel('Assetstore');
    try
      {
      $this->assetstore = $assetstoreModel->getDefault();
      }
    catch(Exception $e)
      {
      }
    }

  public function mysql()
    {
    $this->db->query("ALTER TABLE `item` ADD COLUMN `thumbnail_id` bigint(20) NULL DEFAULT NULL");

    $this->_moveAllThumbnails();

    $this->db->query("ALTER TABLE `item` DROP `thumbnail`");
    }

  public function pgsql()
    {
    $this->db->query("ALTER TABLE item ADD COLUMN thumbnail_id bigint NULL DEFAULT NULL");

    $this->_moveAllThumbnails();

    $this->db->query("ALTER TABLE item DROP COLUMN thumbnail");
    }

  public function postUpgrade()
    {
    }

  private function _moveAllThumbnails()
    {
    // Iterate through all existing items that have thumbnails
    $sql = $this->db->select()
                ->from(array('item'))
                ->where('thumbnail != ?', '');
    $rowset = $this->db->fetchAll($sql);
    foreach($rowset as $row)
      {
      $itemId = $row['item_id'];
      $thumbnailBitstream = $this->_moveThumbnailToAssetstore($row['thumbnail']);
      if($thumbnailBitstream !== null)
        {
        $this->db->update('item',
                          array('thumbnail_id' => $thumbnailBitstream->getKey()),
                          array('item_id = ?' => $itemId));
        }
      }
    }

  private function _moveThumbnailToAssetstore($thumbnail)
    {
    $modelLoader = new MIDAS_ModelLoader();
    $bitstreamModel = $modelLoader->loadModel('Bitstream');

    $oldpath = BASE_PATH.'/'.$thumbnail;
    if(!file_exists($oldpath)) //thumbnail file no longer exists, so we remove its reference
      {
      return null;
      }

    $bitstreamDao = $bitstreamModel->createThumbnail($this->assetstore, $oldpath);
    return $bitstreamDao;
    }


}
?>
