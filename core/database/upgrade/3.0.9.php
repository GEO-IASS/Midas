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

/** Upgrade the core to version 3.0.9. */
class Upgrade_3_0_9 extends MIDASUpgrade
{
    /** Upgrade a MySQL database. */
    public function mysql()
    {
        $sql = "
      CREATE TABLE IF NOT EXISTS `uniqueidentifier` (
        `uniqueidentifier_id` varchar(255) NOT NULL,
        `resource_id` bigint(20),
        `resource_type` tinyint(4),
        PRIMARY KEY (`uniqueidentifier_id`)
      )   DEFAULT CHARSET=utf8;
      ";
        $this->db->query($sql);
    }

    /** Upgrade a PostgreSQL database. */
    public function pgsql()
    {
        $sql = "
      CREATE TABLE  uniqueidentifier (
        uniqueidentifier_id character varying(512)  PRIMARY KEY,
        resource_type  integer,
        resource_id bigint
      )
      ; ";
        $this->db->query($sql);
    }
}
