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

require_once BASE_PATH.'/modules/cleanup/constant/module.php';

/** Install the cleanup module. */
class Cleanup_InstallScript extends MIDASModuleInstallScript
{
    /** @var string */
    public $moduleName = 'cleanup';

    /** Post database install. */
    public function postInstall()
    {
        /** @var SettingModel $settingModel */
        $settingModel = MidasLoader::loadModel('Setting');
        $settingModel->setConfig(CLEANUP_DAYS_TO_KEEP_PARTIAL_FILES_KEY, CLEANUP_DAYS_TO_KEEP_PARTIAL_FILES_DEFAULT_VALUE, $this->moduleName);
    }
}
