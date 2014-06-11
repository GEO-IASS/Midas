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

require_once BASE_PATH.'/modules/oauth/models/base/ClientModelBase.php';

/** pdo model implementation */
class Oauth_ClientModel extends Oauth_ClientModelBase
  {
  /**
   * Return all client records owned by the given user
   */
  public function getByUser($userDao)
    {
    $sql = $this->database->select()->setIntegrityCheck(false)
                ->where('owner_id = ?', $userDao->getKey());
    $rows = $this->database->fetchAll($sql);
    $daos = array();
    foreach($rows as $row)
      {
      $daos[] = $this->initDao('Client', $row, $this->moduleName);
      }
    return $daos;
    }
  }
