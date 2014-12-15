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
 * Notification manager for the @MN@ module.
 *
 * @package Modules\@MN_CAP@\Notification
 */
class @MN_CAP@_Notification extends MIDAS_Notification
{
    /** @var string */
    public $moduleName = '@MN@';

    /** @TODO Initialize the notification process. */
    public function init()
    {
        $fc = Zend_Controller_Front::getInstance();
        $this->moduleWebroot = $fc->getBaseUrl().'/modules/'.$this->moduleName;
        $this->coreWebroot = $fc->getBaseUrl().'/core';

        $this->addCallBack('CALLBACK_CORE_ITEM_DELETED', 'handleItemDeleted');
    }

    /**
     * @TODO Handle the callback when an item is deleted.
     *
     * @param array $params parameters
     */
    public function handleItemDeleted($params)
    {
        $itemDao = $params['item'];

        // TODO: Do something with this item DAO
    }
}
