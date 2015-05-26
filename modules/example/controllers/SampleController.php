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

require_once BASE_PATH.'/modules/example/AppController.php';

/** example sample controller */
class Example_SampleController extends Example_AppController
{
    public $_models = array('User');
    public $_moduleModels = array('Wallet');

    /** init method */
    public function init()
    {
    }

    /**
     * view Action.
     */
    public function viewAction()
    {
        $this->view->header = 'Example Module Sample Controller View Action';
        $this->view->sampleList = array('sample 1', 'sample 2', 'sample 3');
        $this->view->json['json_sample'] = 'my_json_sample_value';
        // get userId 1 for now
        $userDao = $this->User->load(1); // use a core model
        $this->view->wallet = $this->Example_Wallet->createWallet($userDao, '10'); // use a model from this module
        $this->view->wallet->setCreditCardCount(3);
    }

    /**
     * delete Action.
     */
    public function deleteAction()
    {
        $this->view->header = 'Example Module Sample Controller Delete Action';
    }
}
