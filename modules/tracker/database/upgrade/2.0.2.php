<?php
/*=========================================================================
 Midas Server
 Copyright Kitware SAS, 26 rue Louis Guérin, 69100 Villeurbanne, France.
 All rights reserved.
 For more information visit http://www.kitware.com/.

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

/** Upgrade the tracker module to version 2.0.2 */
class Tracker_Upgrade_2_0_2 extends MIDASUpgrade
{
    /** Upgrade a MySQL database. */
    public function mysql()
    {
        $this->db->query(
            'CREATE TABLE IF NOT EXISTS `tracker_trend_threshold` ('.
            '    `trend_threshold_id` bigint(20) NOT NULL AUTO_INCREMENT,'.
            '    `producer_id` bigint(20) NOT NULL,'.
            '    `metric_name` varchar(255) NOT NULL,'.
            "    `abbreviation` varchar(255) NOT NULL DEFAULT '',".
            '    `warning` double,'.
            '    `fail` double,'.
            '    `max` double,'.
            '    PRIMARY KEY (`trend_threshold_id`),'.
            '    KEY (`producer_id`)'.
            ') DEFAULT CHARSET=utf8;'
        );

        $this->db->query(
            'ALTER TABLE `tracker_aggregate_metric_spec` '.
            "   ADD COLUMN `abbreviation` varchar(255) NOT NULL DEFAULT '', ".
            '   ADD COLUMN `warning` double, '.
            '   ADD COLUMN `fail` double, '.
            '   ADD COLUMN `max` double;'
        );
    }
}
