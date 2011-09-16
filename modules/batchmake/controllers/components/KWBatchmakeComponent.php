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
?>
<?php
include_once BASE_PATH . '/library/KWUtils.php';
/**
 *  Batchmake_KWBatchmakeComponent
 *  provides utility methods needed to interact with Batchmake via Midas3.
 */
class Batchmake_KWBatchmakeComponent extends AppComponent
{

  protected static $configPropertiesRequirements = array(MIDAS_BATCHMAKE_TMP_DIR_PROPERTY => MIDAS_BATCHMAKE_CHECK_IF_CHMODABLE_RW,
  MIDAS_BATCHMAKE_BIN_DIR_PROPERTY => MIDAS_BATCHMAKE_CHECK_IF_READABLE,
  MIDAS_BATCHMAKE_SCRIPT_DIR_PROPERTY => MIDAS_BATCHMAKE_CHECK_IF_READABLE,
  MIDAS_BATCHMAKE_APP_DIR_PROPERTY => MIDAS_BATCHMAKE_CHECK_IF_READABLE,
  MIDAS_BATCHMAKE_DATA_DIR_PROPERTY => MIDAS_BATCHMAKE_CHECK_IF_CHMODABLE_RW,
  MIDAS_BATCHMAKE_CONDOR_BIN_DIR_PROPERTY => MIDAS_BATCHMAKE_CHECK_IF_READABLE);

  /**
   * accessor functin to return the names of the config propeties, and
   * their filesystem requirements;
   */
  public function getConfigPropertiesRequirements()
    {
    return self::$configPropertiesRequirements;
    }


  protected static $applicationsPaths = array(MIDAS_BATCHMAKE_CONDOR_STATUS => MIDAS_BATCHMAKE_CONDOR_BIN_DIR_PROPERTY,
  MIDAS_BATCHMAKE_CONDOR_QUEUE => MIDAS_BATCHMAKE_CONDOR_BIN_DIR_PROPERTY,
  MIDAS_BATCHMAKE_CONDOR_SUBMIT => MIDAS_BATCHMAKE_CONDOR_BIN_DIR_PROPERTY,
  MIDAS_BATCHMAKE_CONDOR_SUBMIT_DAG => MIDAS_BATCHMAKE_CONDOR_BIN_DIR_PROPERTY,
  MIDAS_BATCHMAKE_EXE => MIDAS_BATCHMAKE_BIN_DIR_PROPERTY);



  // component configuration settings
  protected $componentConfig;
  // individual config properties, for convenience
  protected $configScriptDir;
  protected $configAppDir;
  protected $configTmpDir;
  protected $configBinDir;
  protected $configDataDir;
  protected $configCondorBinDir;

  /**
   * Constructor, loads ini from standard config location, unless a
   * supplied alternateConfig.
   * @param string $alternateConfig path to alternative config ini file
   */
  public function __construct($alternateConfig = null)
    {
    $this->loadConfigProperties($alternateConfig);
    } // end __construct($alternateConfig)


  /**
   * helper function to load the correct config file
   * @param string $alternateConfig path to alternative config ini file
   * @return config array with config properties
   */
  protected function loadConfig($alternateConfig = null)
    {
    if($alternateConfig)
      {
      $config = parse_ini_file($alternateConfig, false);
      }
    elseif(file_exists(MIDAS_BATCHMAKE_MODULE_LOCAL_CONFIG))
      {
      $config = parse_ini_file(MIDAS_BATCHMAKE_MODULE_LOCAL_CONFIG, false);
      }
    else
      {
      $config = parse_ini_file(MIDAS_BATCHMAKE_MODULE_CONFIG);
      }
    return $config;
    }

  /**
   * @method loadConfigProperties
   * will load the configuration property values for this module, and filter
   * out only those properties that are in the 'batchmake.' config namespace,
   * removing the 'batchmake.' from the key name.
   * @param string $alternateConfig a path to an alternate config ini file
   * @return array of batchmake module specific config properties
   */
  public function loadConfigProperties($alternateConfig = null)
    {
    $configPropertiesParamVals = array();
    $rawConfig = $this->loadConfig($alternateConfig);

    $modulePropertyNamespace = MIDAS_BATCHMAKE_MODULE . '.';
    foreach($rawConfig as $configProperty => $configPropertyVal)
      {
      $ind = strpos($configProperty, $modulePropertyNamespace);
      if($ind !== false && $ind  == 0)
        {
        $reducedKey = substr($configProperty, strpos($configProperty, '.') + 1);
        $configPropertiesParamVals[$reducedKey] = $configPropertyVal;
        }
      }

    $this->componentConfig = $configPropertiesParamVals;
    $this->configScriptDir = $this->componentConfig[MIDAS_BATCHMAKE_SCRIPT_DIR_PROPERTY];
    $this->configAppDir = $this->componentConfig[MIDAS_BATCHMAKE_APP_DIR_PROPERTY];
    $this->configTmpDir = $this->componentConfig[MIDAS_BATCHMAKE_TMP_DIR_PROPERTY];
    $this->configBinDir = $this->componentConfig[MIDAS_BATCHMAKE_BIN_DIR_PROPERTY];
    $this->configDataDir = $this->componentConfig[MIDAS_BATCHMAKE_DATA_DIR_PROPERTY];
    $this->configCondorBinDir = $this->componentConfig[MIDAS_BATCHMAKE_CONDOR_BIN_DIR_PROPERTY];
    return $this->componentConfig;
    }

  // above here is config setup
  // below here is config testing

  /**
   * @method checkFileFlag()
   * @TODO from KWUtils, may need to be moved, but first tested
   * checks whether the file at the passed in path has the passed in options.
   */
  protected function checkFileFlag($file, $options = 0x0)
    {
    $exist    = file_exists($file);
    Zend_Loader::loadClass("InternationalizationComponent", BASE_PATH.'/core/controllers/components');
    $status =  ($exist ? InternationalizationComponent::translate(MIDAS_BATCHMAKE_EXIST_STRING) : InternationalizationComponent::translate(MIDAS_BATCHMAKE_NOT_FOUND_ON_CURRENT_SYSTEM_STRING));
    $ret = $exist;

    if($exist && ($options & MIDAS_BATCHMAKE_CHECK_IF_READABLE))
      {
      $readable = is_readable($file);
      $status .= $readable ? " / Readable" : " / NotReadable";
      $ret = $ret && $readable;
      }

    if($exist && ($options & MIDAS_BATCHMAKE_CHECK_IF_WRITABLE))
      {
      $writable = is_writable($file);
      $status .= $writable ? " / Writable" : " / NotWritable";
      $ret = $ret && $writable;
      }
    if($exist && ($options & MIDAS_BATCHMAKE_CHECK_IF_EXECUTABLE))
      {
      $executable = is_executable($file);
      $status .= $executable ? " / Executable" : " / NotExecutable";
      $ret = $ret && $executable;
      }
    if(!KWUtils::isWindows() && $exist && ($options & MIDAS_BATCHMAKE_CHECK_IF_CHMODABLE))
      {
      $chmodable = $this->IsChmodable($file);
      $status .= $chmodable ? " / Chmodable" : " / NotChmodable";
      $ret = $ret && $chmodable;
      }
    return array($ret, $status);
    }

  /**
   * @method isChmodable
   * Check if current PHP process has permission to change the mode
   * of $fileOrDirectory.
   * @TODO from KWUtils, may need to be moved, but first tested
   * Note: If return true, the mode of the file will be MIDAS_BATCHMAKE_DEFAULT_MKDIR_MODE
   *       On windows, return always True
   */
  protected function isChmodable($fileOrDirectory)
    {
    if(KWUtils::isWindows())
      {
      return true;
      }

    if(!file_exists($fileOrDirectory))
      {
      Zend_Loader::loadClass("InternationalizationComponent", BASE_PATH.'/core/controllers/components');
      self::Error(InternationalizationComponent::translate(MIDAS_BATCHMAKE_FILE_OR_DIRECTORY_DOESNT_EXIST_STRING).' ['.$fileOrDirectory.']');
      return false;
      }

    // Get permissions of the file
    // TODO On CIFS filesytem, even if the function GetFilePermissions call clearstatcache(), the value returned can be wrong
    $current_perms = KWUtils::DEFAULT_MKDIR_MODE;
    if($current_perms === false)
      {
      return false;
      }

    if(is_writable($fileOrDirectory))
      {
      // Try to re-apply them
      $return = chmod($fileOrDirectory, $current_perms);
      }
    else
      {
      $return = false;
      }
    return $return;
    }

  /**
   * @method testconfig()
   * @param array $alternateConfigValues an alternative set of values to test,
   * usually testing a possible configuration set to be saved.
   * performs validation on current config setup.
   */
  public function testconfig($alternateConfigValues = null)
    {
    //default to correct config
    $total_config_correct = 1;
    $configStatus = array();

    if($alternateConfigValues)
      {
      $configToTest = $alternateConfigValues;
      }
    else
      {
      $configToTest = $this->componentConfig;
      }

    foreach(self::$configPropertiesRequirements as $configProperty => $configPropertyRequirement)
      {
      $configPropertyVal = $configToTest[$configProperty];
      if($configPropertyVal)
        {
        // if the property exists, check its configuration
        list($result, $status) = $this->checkFileFlag($configPropertyVal, $configPropertyRequirement);
        $configStatus[] = array(MIDAS_BATCHMAKE_PROPERTY_KEY => $configProperty, MIDAS_BATCHMAKE_STATUS_KEY => $status, MIDAS_BATCHMAKE_TYPE_KEY => $result ? MIDAS_BATCHMAKE_STATUS_TYPE_INFO : MIDAS_BATCHMAKE_STATUS_TYPE_ERROR);
        // the property is in error, therefore so is the global config
        if(!$result)
          {
          $total_config_correct = 0;
          }
        }
      else
        {
        // property doesn't exist, both the property and global config are in error
        $configStatus[] = array(MIDAS_BATCHMAKE_PROPERTY_KEY => $configProperty, MIDAS_BATCHMAKE_STATUS_KEY => MIDAS_BATCHMAKE_CONFIG_VALUE_MISSING, MIDAS_BATCHMAKE_TYPE_KEY => MIDAS_BATCHMAKE_STATUS_TYPE_ERROR);
        $total_config_correct = 0;
        }
      }

    // for now assuming will run via condor, so require all of the condor setup

    foreach(self::$applicationsPaths as $app => $pathProperty)
      {
      $appPath = $configToTest[$pathProperty] ."/" . KWUtils::formatAppName($app);
      list($result, $status) = $this->checkFileFlag($appPath, MIDAS_BATCHMAKE_CHECK_IF_EXECUTABLE);
      Zend_Loader::loadClass("InternationalizationComponent", BASE_PATH.'/core/controllers/components');

      $applicationString = InternationalizationComponent::translate(MIDAS_BATCHMAKE_APPLICATION_STRING);
      $configStatus[] = array(MIDAS_BATCHMAKE_PROPERTY_KEY => $applicationString . ' ' .$appPath, MIDAS_BATCHMAKE_STATUS_KEY => $status, MIDAS_BATCHMAKE_TYPE_KEY => $result ? MIDAS_BATCHMAKE_STATUS_TYPE_INFO : MIDAS_BATCHMAKE_STATUS_TYPE_ERROR);
      // the property is in error, therefore so is the global config
      if(!$result)
        {
        $total_config_correct = 0;
        }
      }

    // Process web server user information

    // TODO what should be done if there are warnings??
    $processUser  = posix_getpwuid(posix_geteuid());
    $processGroup = posix_getgrgid(posix_geteuid());

    $phpProcessString = InternationalizationComponent::translate(MIDAS_BATCHMAKE_PHP_PROCESS_STRING);
    $phpProcessUserString = $phpProcessString . ' ' . InternationalizationComponent::translate(MIDAS_BATCHMAKE_PHP_PROCESS_USER_STRING);
    $phpProcessNameString = InternationalizationComponent::translate(MIDAS_BATCHMAKE_PHP_PROCESS_NAME_STRING);
    $phpProcessGroupString = InternationalizationComponent::translate(MIDAS_BATCHMAKE_PHP_PROCESS_GROUP_STRING);
    $phpProcessHomeString = InternationalizationComponent::translate(MIDAS_BATCHMAKE_PHP_PROCESS_HOME_STRING);
    $phpProcessShellString = InternationalizationComponent::translate(MIDAS_BATCHMAKE_PHP_PROCESS_SHELL_STRING);
    $unknownString = InternationalizationComponent::translate(MIDAS_BATCHMAKE_UNKNOWN_STRING);

    $phpProcessUserNameString = $phpProcessUserString . '[' . $phpProcessNameString . ']';
    $phpProcessUserGroupString = $phpProcessUserString . '[' . $phpProcessGroupString . ']';
    $phpProcessUserHomeString = $phpProcessUserString . '[' . $phpProcessHomeString . ']';
    $phpProcessUserShellString = $phpProcessUserString . '[' . $phpProcessShellString . ']';

    $processProperties = array($phpProcessUserNameString => !empty($processUser[MIDAS_BATCHMAKE_PHP_PROCESS_NAME_STRING]) ? $processUser[MIDAS_BATCHMAKE_PHP_PROCESS_NAME_STRING] : "",
    $phpProcessUserGroupString => !empty($processGroup[MIDAS_BATCHMAKE_PHP_PROCESS_NAME_STRING]) ? $processGroup[MIDAS_BATCHMAKE_PHP_PROCESS_NAME_STRING] : "",
    $phpProcessUserHomeString => !empty($processUser[MIDAS_BATCHMAKE_DIR_KEY]) ? $processUser[MIDAS_BATCHMAKE_DIR_KEY] : "",
    $phpProcessUserShellString => !empty($processUser[MIDAS_BATCHMAKE_PHP_PROCESS_SHELL_STRING]) ? $processUser[MIDAS_BATCHMAKE_PHP_PROCESS_SHELL_STRING] : "");

    foreach($processProperties as $property => $value)
      {
      $status   = !empty($value);
      $configStatus[]   = array(MIDAS_BATCHMAKE_PROPERTY_KEY => $property,
      MIDAS_BATCHMAKE_STATUS_KEY => $status ? $value : $unknownString,
      MIDAS_BATCHMAKE_TYPE_KEY => $status ? MIDAS_BATCHMAKE_STATUS_TYPE_INFO : MIDAS_BATCHMAKE_STATUS_TYPE_WARNING);
      }

    return array($total_config_correct, $configStatus);

    }

  /**
    * @method isConfigCorrect
    * helper method to return true if the config is correct, false otherwise
    * @param array $alternateConfigValues an alternative set of values to test,
    * usually testing a possible configuration set to be saved.
    * @return true if config correct, false otherwise
    */
  public function isConfigCorrect($alternateConfigValues = null)
    {
    $applicationConfig = $this->testconfig($alternateConfigValues);
    return $applicationConfig[0] == 1;
    }

  // above here is config testing
  // below here is execution functionality


  /**
   * @method getBatchmakeScripts
   * will create a list of Batchmake scripts that exist in the MIDAS_BATCHMAKE_SCRIPT_DIR_PROPERTY
   * with a .bms extension.
   * @return array of batchmake scripts
   */
  public function getBatchmakeScripts()
    {
    $globPattern = $this->configScriptDir . '/*' . MIDAS_BATCHMAKE_BATCHMAKE_EXTENSION;
    $scripts = glob($globPattern);
    $scriptNames = array();
    foreach($scripts as $scriptPath)
      {
      $parts = explode('/', $scriptPath);
      $scriptNames[] = $parts[count($parts) - 1];
      }
    return $scriptNames;
    }



  /**
   * will createa  new batchmake task, along with a work directory
   * @param type $userDao
   * @return string the path to the workDir for this batchmake task
   */
  public function createTask($userDao)
    {
    $modelLoad = new MIDAS_ModelLoader();
    $batchmakeTaskModel = $modelLoad->loadModel('Task', 'batchmake');
    $taskDao = $batchmakeTaskModel->createTask($userDao);
    $userId = $taskDao->getUserId();
    $taskId = $taskDao->getKey();
    $subdirs = array(MIDAS_BATCHMAKE_SSP_DIR, $userId, $taskId);
    // create a workDir based on the task and user
    $workDir = KWUtils::createSubDirectories($this->configTmpDir . "/", $subdirs);
    return $workDir;
    }

  /**
   * @method preparePipelineScripts
   * will look in the scriptDir for a batchmake script and symlink it to the
   * workDir, will then find any batchmake scripts that need to be included
   * other than a config script, and symlink them in from the scriptDir,
   * and for each of these additional scripts, will perform the same
   * operation (symlinking included batchmake scripts),
   * will throw a Zend_Exception if any symlink fails or if a target file
   * doesn't exist.
   * @param $workDir the temporary work dir
   * @param $scriptName the original batchmake script
   * @param $processed a list of those scripts already processed
   * @return the array of scripts processed
   */
  public function preparePipelineScripts($workDir, $scriptName, $processed = array(), &$currentPath = array())
    {
    // check for cycles
    if(array_search($scriptName, $currentPath) !== false)
      {
      throw new Zend_Exception("Cycle found in the include graph of batchmake scripts.");
      }
    // push this script onto the currentPath
    $currentPath[] = $scriptName;
    // don't process any already processed
    if(!array_key_exists($scriptName, $processed))
      {
      // symlink the top level scrip
      $scriptLink = $workDir . '/' . $scriptName;
      $scriptTarget = $this->configScriptDir . '/' . $scriptName;
      if(!file_exists($scriptTarget) || !symlink($scriptTarget, $scriptLink))
        {
        throw new Zend_Exception($scriptTarget . ' could not be sym-linked to ' . $scriptLink);
        }
      // now consider this script to be processed
      $processed[$scriptName] = $scriptName;
      }

    // read through the script looking for includes
    $contents = file_get_contents($this->configScriptDir . '/' . $scriptName);
    // looking for lines like
    // Include(PixelCounter.config.bms)
    // /i means case insensitive search
    $pattern = '/include\s*\(\s*(\S*)\s*\)/i';
    preg_match_all($pattern, $contents, $matches);
    // ensure that there actually are matches
    if($matches && count($matches) > 1)
      {
      // we just want the subpattern match, not the full match
      // the subpattern match is the name of the included file
      $subpatternMatches = $matches[1];
      // now that we have the matches, we only want the ones that are not .config.bms
      foreach($subpatternMatches as $ind => $includeName)
        {
        // only want the includes that are not .config.bms scripts
        if(strpos($includeName, '.config.bms') === false)
          {
          // recursively process this script, updating the $processed list upon success
          // essentially performing depth first search in a graph
          // there could be a problem with a cycle in the include graph,
          // so pass along the currentPath
          $processed = $this->preparePipelineScripts($workDir, $includeName, $processed, $currentPath);
          }
        }
      }
    // pop this script off of the current path
    array_pop($currentPath);
    // return the processed list
    return $processed;
    }


  /**
   * @method preparePipelineBmms
   * will look in the $workDir for all batchmake scripts that are passed
   * in the array $bmScripts, for each of these, it will find all of the apps
   * included in them using the SetApp Batchmake command, and sym link the
   * corresponding bmm file to the tmpDir, these bmm files are expected to be
   * in the $binDir, will throw a Zend_Exception if any symlink fails or if a
   * bmm file doesn't exist, or if one of the batchmake scripts doesn't exist.
   * @param $workDir the temporary work dir
   * @param $bmScripts the array of Batchmake scripts in the $tmpDir to process
   * @return an array of [ bmmfile => bmScript where bmmfile first found ]
   */
  public function preparePipelineBmms($workDir, $bmScripts)
    {
    // initialize the list of bmms that have been processed
    $processed = array();
    foreach($bmScripts as $bmScript)
      {
      $scriptPath = $workDir . '/' . $bmScript;
      if(!file_exists($scriptPath))
        {
        throw new Zend_Exception($scriptPath . ' could not be found');
        }
      $contents = file_get_contents($scriptPath);
      // /i means case insensitive search
      // read through the script looking for lines like
      // SetApp(pixelCounter @PixelCounter)
      $pattern = '/setapp\s*\(\s*\S*\s*@(\S*)\s*\)/i';
      preg_match_all($pattern, $contents, $matches);
      // ensure that there actually are matches
      if($matches && count($matches) > 1)
        {
        // we just want the subpattern match, not the full match
        // the subpattern match is the name of the included file
        $subpatternMatches = $matches[1];
        // now that we have the matches, get the app names to use for the bmm
        foreach($subpatternMatches as $ind => $appName)
          {
          if(!array_key_exists($appName, $processed))
            {
            $bmmTarget = $this->configBinDir . '/' . $appName . '.bmm';
            $bmmLink = $workDir . '/' . $appName . '.bmm';
            if(!file_exists($bmmTarget) || !symlink($bmmTarget, $bmmLink))
              {
              throw new Zend_Exception($bmmTarget . ' could not be sym-linked to ' . $bmmLink);
              }
            // track which bmScript we first saw this app in
            $processed[$appName] = $bmScript;
            }
          }
        }
      }
    return $processed;
    }

  /**
   * @method compileBatchMakeScript will check that the passed in $batchmakescript
   * in the passed in $workDir will compile without errors.
   * @param string $workDir directory where the work for SSP should be done
   * @param string $bmScript name of the script, should be in $tmpDir
   * @return type
   */
  public function compileBatchMakeScript($workDir, $bmScript)
    {
    // Prepare command
    $params = array(
      '-ap', $this->configAppDir,
      '-p', $workDir,
      '-c', $workDir.$bmScript,
      );
    $cmd = KWUtils::prepareExecCommand($this->configBinDir . '/'. MIDAS_BATCHMAKE_EXE, $params);
    if($cmd === false)
      {
      return false;
      }

    // Run command
    KWUtils::exec($cmd, $output, $workDir, $returnVal);

    if($returnVal !== 0)
      {
      throw new Zend_Exception("Failed to run: [".$cmd."], output: [".implode(",", $output )."]");
      }

    // if BatchMake reports errors, throw an exception
    foreach($output as $ind => $val)
      {
      if(preg_match("/(\d+) error/", $val, $matches))
        {
        // number of errors is index 1, this is based on BatchMake's output
        // it will output the number of errors even if 0
        if($matches[1] == "0")
          {
          return true;
          }
        else
          {
          throw new Zend_Exception("Compiling script [".$bmScript."] yielded output: [".implode(",", $output )."]");
          }
        }
      }

    throw new Zend_Exception("Error in BatchMake script, the compile step didn't report errors, output: [".implode(",", $cmd_output )."]");
    }



  /**
   * @method generateCondorDag will create condor scripts and a condor dag
   * from the batchmake script $bmScript, in the directory $workDir.
   * @param type $workDir
   * @param type $bmScript
   */
  public function generateCondorDag($workDir, $bmScript)
    {
    $dagName      = $bmScript.'.dagjob';

    // Prepare command
    $params = array(
      '-ap', $this->configAppDir,
      '-p', $workDir,
      '--condor', $workDir.$bmScript, $workDir.$dagName,
      );

    $cmd = KWUtils::prepareExecCommand($this->configBinDir . '/'. MIDAS_BATCHMAKE_EXE, $params);

    // Run command
    KWUtils::exec($cmd, $output, $workDir, $returnVal);

    if($returnVal !== 0)
      {
      throw new Zend_Exception("Failed to run: [".$cmd."], output: [".implode(",", $cmd_output )."]");
      }
    return $dagName;
    }

  /**
   * @method condorSubmitDag will submit the passed in $dagScript to condor,
   * executing in the passed in $workDir
   * @param type $workDir
   * @param type $dagScript
   */
  public function condorSubmitDag($workDir, $dagScript)
    {
    // Prepare command
    $params = array($dagScript);

    $cmd = KWUtils::prepareExecCommand($this->configCondorBinDir . '/'. MIDAS_BATCHMAKE_CONDOR_SUBMIT_DAG, $params);

    // Run command
    KWUtils::exec($cmd, $output, $workDir, $returnVal);

    if($returnVal !== 0)
      {
      throw new Zend_Exception("Failed to run: [".$cmd."], output: [".implode(",", $cmd_output )."]");
      }
    }





} // end class
?>
