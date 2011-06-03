<?php
/*=========================================================================
MIDAS Server
Copyright (c) Kitware SAS. 20 rue de la Villette. All rights reserved.
69328 Lyon, FRANCE.

See Copyright.txt for details.
This software is distributed WITHOUT ANY WARRANTY; without even
the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
PURPOSE.  See the above copyright notices for more information.
=========================================================================*/
/** notification manager*/
class Visualize_Notification extends MIDAS_Notification
  {
  public $_moduleComponents=array('Main');
  public $moduleName='visualize';
  /** init notification process*/
  public function init($type, $params)
    {
    switch ($type)
      {
      case MIDAS_NOTIFY_CAN_VISUALIZE:
        return $this->ModuleComponent->Main->canVisualizeWithParaview($params['item']) ||
             $this->ModuleComponent->Main->canVisualizeMedia($params['item']) ||
           $this->ModuleComponent->Main->canVisualizeTxt($params['item']) ||
          $this->ModuleComponent->Main->canVisualizeImage($params['item']) ||
          $this->ModuleComponent->Main->canVisualizePdf($params['item']);
        break;

      default:
        break;
      }
    }//end init  
  } //end class
?>