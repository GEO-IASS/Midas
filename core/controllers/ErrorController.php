<?php
class ErrorController extends AppController  
{  
    public $_models=array();
    public $_daos=array();
    public $_components=array('NotifyError','Utility');
    public $_forms=array();
    private $_error;  
    private $_environment;  
  
    public function init()  
      {  
      parent::init();  

      $error = $this->_getParam('error_handler');
      if(!isset($error)||empty($error))
        {
        return;
        }
      $mailer = new Zend_Mail();  
      $session = new Zend_Session_Namespace('Auth_User');  
      $db = Zend_Registry::get('dbAdapter');
      
      if(method_exists($db,"getProfiler"))
        {
        $profiler = $db->getProfiler();
        }
      else
        {
        $profiler = new Zend_Db_Profiler();  
        }  
      $environment = Zend_Registry::get('configGlobal')->environment;
      $this->_environment=$environment;
      $this->Component->NotifyError->initNotifier(
          $environment,  
          $error,  
          $mailer,  
          $session,  
          $profiler,  
          $_SERVER  
      );  

      $this->_error = $error;  

      $this->_environment = $environment;  
      $this->view->setScriptPath(BASE_PATH."/core/views");
     }  
  
    public function errorAction()  
      {  
      $error = $this->_getParam('error_handler');  
      if(!isset($error)||empty($error))
        {
        $this->view->message = 'Page not found'; 
        return;
        }
        
      $controller=$error->request->getParams();
      $controller=$controller['controller'];
      if($controller!='install'&&!file_exists(BASE_PATH."/core/configs/database.local.ini"))
        {
        $this->view->message="Midas is not installed. Please go the <a href='{$this->view->webroot}/install'> install page</a>.";
        return;
        }
      switch ($this->_error->type) {  
          case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:  
          case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:  
              $this->getResponse()->setHttpResponseCode(404);  
              $this->view->message = 'Page not found';  
              break;  

          default:  
              $this->getResponse()->setHttpResponseCode(500);  
              $this->_applicationError();  
              break;  
      } 
      $fullMessage = $this->Component->NotifyError->getFullErrorMessage();  
      if(isset($this->fullMessage))
        {
        $this->getLogger()->warn($this->fullMessage);
        }
      else
        {
        $this->getLogger()->warn('URL: '.$this->Component->NotifyError->curPageURL()."\n".$this->view->message);
        }
      
      }  
  
    private function _applicationError()  
      {  
      $fullMessage = $this->Component->NotifyError->getFullErrorMessage();  
      $shortMessage = $this->Component->NotifyError->getShortErrorMessage();  
      $this->fullMessage=$fullMessage;

      switch ($this->_environment) {  
          case 'production':  
              $this->view->message = $shortMessage;  
              break;  
          case 'testing':  
              $this->_helper->layout->setLayout('blank');  
              $this->_helper->viewRenderer->setNoRender();  

              $this->getResponse()->appendBody($shortMessage);  
              break;  
          default:  
              $this->view->message = nl2br($fullMessage);  
      }  

      $this->Component->NotifyError->notify();  
      }  
}  