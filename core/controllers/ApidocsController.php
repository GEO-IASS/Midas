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
 * Apidocs Controller for the web API
 */
class ApidocsController extends AppController
{
    public $_components = array('Apidocs');

    /** init api actions */
    public function init()
    {
        $this->disableLayout();
        $this->disableView();
    }

    /** Index resource */
    public function indexAction()
    {
        $results = array();
        $results['apiVersion'] = '1.0';
        $results['swaggerVersion'] = '1.1';
        $baseUrl = $this->getRequest()->getScheme().'://'.$this->getRequest()->getHttpHost().$this->view->webroot;
        $results['basePath'] = $baseUrl.'/apidocs';
        $results['apis'] = array();

        $resources = $this->Component->Apidocs->getEnabledResources();
        foreach ($resources as $resourcePath) {
            if (strpos($resourcePath, '/') > 0) {
                $resourcePath = '/'.$resourcePath;
            }
            $curResource = array();
            $curResource['path'] = $resourcePath;
            $curResource['discription'] = 'Operations about '.$resourcePath;
            array_push($results['apis'], $curResource);
        }
        echo JsonComponent::encode($results);
    }

    /**
     * We override __call to intercept the path and transform
     * it into a resource and module name, and pass that into the ApidocsComponent.
     */
    public function __call($name, $args)
    {
        $pathParams = UtilityComponent::extractPathParams();
        $module = '';
        $resource = $this->getRequest()->getActionName();
        if (count($pathParams)) {
            if (in_array($this->getRequest()->getActionName(), Zend_Registry::get('modulesEnable'))) {
                $module = $this->getRequest()->getActionName();
                $resource = $pathParams[0];
            }
        }

        $results = $this->Component->Apidocs->getResourceApiDocs($resource, $module);
        echo JsonComponent::encode($results);
    }
}
