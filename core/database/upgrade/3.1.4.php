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

class Upgrade_3_1_4 extends MIDASUpgrade
  {
  public function preUpgrade()
    {
    }

  public function mysql()
    {
    $sql = "CREATE TABLE IF NOT EXISTS `setting` (
            `setting_id` bigint(20) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `module`  varchar(255) NOT NULL,
            `value` text NULL DEFAULT NULL ,
            PRIMARY KEY (`setting_id`)
            )  DEFAULT CHARSET=utf8;";
    $this->db->query($sql);
    }

  public function pgsql()
    {
    $sql = "CREATE TABLE  setting (
              setting_id serial  PRIMARY KEY,
              name  character varying(256) NOT NULL,
              module  character varying(256) NOT NULL,
              value text NOT NULL
             )  ;";
    $this->db->query($sql);
    }

  public function postUpgrade()
    {
    }
  }
