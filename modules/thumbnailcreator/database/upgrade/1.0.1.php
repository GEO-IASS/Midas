<?php
/*=========================================================================
 MIDAS Server
 Copyright (c) Kitware SAS. 26 rue Louis Guérin. 69100 Villeurbanne, FRANCE
 All rights reserved.
 More information http://www.kitware.com

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

         http://www.apache.org/licenses/LICENSE-2.0.txt

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
=========================================================================*/

/**
 * Add a table that will store a separate thumbnail for an item
 */
class Thumbnailcreator_Upgrade_1_0_1 extends MIDASUpgrade
{
    /** Mysql upgrade */
    public function mysql()
    {
        $this->db->query(
            "CREATE TABLE IF NOT EXISTS thumbnailcreator_itemthumbnail (
      `itemthumbnail_id` bigint(20) NOT NULL AUTO_INCREMENT,
      `item_id` bigint(20),
      `thumbnail` varchar(255),
      INDEX(`item_id`),
      PRIMARY KEY (`itemthumbnail_id`)
      )"
        );
    }

    /** Pgsql upgrade */
    public function pgsql()
    {
        $this->db->query(
            "CREATE TABLE thumbnailcreator_itemthumbnail (
      itemthumbnail_id serial PRIMARY KEY,
      item_id integer,
      thumbnail character varying(255)
      )"
        );
        $this->db->query(
            "CREATE INDEX thumbnailcreator_itemthumbnail_item_id ON thumbnailcreator_itemthumbnail (item_id)"
        );
    }
}
