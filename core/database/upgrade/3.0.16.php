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

/** Upgrade the core to version 3.0.16. */
class Upgrade_3_0_16 extends MIDASUpgrade
{
    /** Post database upgrade. */
    public function postUpgrade()
    {
        $this->renameTableField('item', 'date', 'date_update', 'timestamp', 'timestamp without time zone', false);
        $this->addTableField('item', 'date_creation', 'timestamp', 'timestamp without time zone', false);
        $this->renameTableField('folder', 'date', 'date_update', 'timestamp', 'timestamp without time zone', false);
        $this->addTableField('folder', 'date_creation', 'timestamp', 'timestamp without time zone', false);
    }
}
