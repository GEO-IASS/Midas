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

/** Notification manager for the communityagreement module */
class Communityagreement_Notification extends MIDAS_Notification
{
    public $_models = array('Community');

    /**
     * init notification process.
     */
    public function init()
    {
        $this->addCallBack('CALLBACK_CORE_GET_COMMUNITY_MANAGE_TABS', 'getCommunityManageTabs');
        $this->addCallBack('CALLBACK_CORE_GET_COMMUNITY_VIEW_JSS', 'getCommunityViewJSs');
        $this->addCallBack('CALLBACK_CORE_GET_COMMUNITY_VIEW_EXTRA_HTML', 'getCommunityViewExtraHtml');
    }

    /**
     * callback function to get 'community agreement' tab.
     *
     * @return array
     */
    public function getCommunityManageTabs($args)
    {
        $fc = Zend_Controller_Front::getInstance();
        $moduleWebroot = $fc->getBaseUrl().'/communityagreement';

        return array('Agreement' => $moduleWebroot.'/community/agreementtab');
    }

    /**
     * callback function to get java script.
     *
     * @return array
     */
    public function getCommunityViewJSs()
    {
        $fc = Zend_Controller_Front::getInstance();
        $moduleUriroot = $fc->getBaseUrl().'/modules/communityagreement';

        return array($moduleUriroot.'/public/js/community/community.agreementenforce.js');
    }

    /**
     * Callback function to get extra html on the community view page.
     * Adds an element for whether the community has an agreement set or not.
     */
    public function getCommunityViewExtraHtml($params)
    {
        /** @var Communityagreement_AgreementModel $agreementModel */
        $agreementModel = MidasLoader::loadModel('Agreement', 'communityagreement');
        $comm = $params['community'];
        $agreementDao = $agreementModel->getByCommunityId($comm->getKey());
        $val = '<span style="display: none;" id="hasAgreement">';
        $val .= $agreementDao != false ? 'true' : 'false';
        $val .= '</span>';

        return $val;
    }
}
