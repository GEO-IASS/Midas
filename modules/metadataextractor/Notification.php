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

/** Notification manager for the metadataextractor module */
class Metadataextractor_Notification extends MIDAS_Notification
{
    /** @var array */
    public $_moduleComponents = array('Extractor');

    /** @var string */
    public $moduleName = 'metadataextractor';

    /** Initialize the notification process. */
    public function init()
    {
        $this->addTask(
            'TASK_METADATAEXTRACTOR_EXTRACT',
            'extractMetadata',
            'Extract Metadata. Parameters: Item, Revision'
        );
        $this->addEvent('EVENT_CORE_UPLOAD_FILE', 'TASK_METADATAEXTRACTOR_EXTRACT');
    }

    /**
     * Handle TASK_METADATAEXTRACTOR_EXTRACT.
     *
     * @param array $params parameters
     */
    public function extractMetadata($params)
    {
        $this->ModuleComponent->Extractor->extract($params[1]);
    }
}
