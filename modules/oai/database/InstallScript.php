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

require_once BASE_PATH.'/modules/oai/constant/module.php';

/** Install the oai module. */
class Oai_InstallScript extends MIDASModuleInstallScript
{
    /** @var string */
    public $moduleName = 'oai';

    /** Post database install. */
    public function postInstall()
    {
        /** @var SettingModel $settingModel */
        $settingModel = MidasLoader::loadModel('Setting');
        $settingModel->setConfig(OAI_REPOSITORY_IDENTIFIER_KEY, OAI_REPOSITORY_IDENTIFIER_DEFAULT_VALUE, $this->moduleName);
        $settingModel->setConfig(OAI_REPOSITORY_NAME_KEY, OAI_REPOSITORY_NAME_DEFAULT_VALUE, $this->moduleName);
        $settingModel->setConfig(OAI_ADMIN_EMAIL_KEY, OAI_ADMIN_EMAIL_DEFAULT_VALUE, $this->moduleName);
    }
}
