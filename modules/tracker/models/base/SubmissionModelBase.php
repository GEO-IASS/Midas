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

/**
 * Submission base model class for the tracker module.
 */
abstract class Tracker_SubmissionModelBase extends Tracker_AppModel
{
    /** Constructor. */
    public function __construct()
    {
        parent::__construct();

        $this->_name = 'tracker_submission';
        $this->_key = 'submission_id';
        $this->_mainData = array(
            'submission_id' => array('type' => MIDAS_DATA),
            'producer_id' => array('type' => MIDAS_DATA),
            'name' => array('type' => MIDAS_DATA),
            'uuid' => array('type' => MIDAS_DATA),
            'submit_time' => array('type' => MIDAS_DATA),
            'user_id' => array('type' => MIDAS_DATA),
            'official' => array('type' => MIDAS_DATA),
            'build_results_url' => array('type' => MIDAS_DATA),
            'extra_urls' => array('type' => MIDAS_DATA),
            'branch' => array('type' => MIDAS_DATA),
            'producer_revision' => array('type' => MIDAS_DATA),
            'reproduction_command' => array('type' => MIDAS_DATA),
            'producer' => array(
                'type' => MIDAS_MANY_TO_ONE,
                'model' => 'Producer',
                'module' => $this->moduleName,
                'parent_column' => 'producer_id',
                'child_column' => 'producer_id',
            ),
            'params' => array(
                'type' => MIDAS_ONE_TO_MANY,
                'model' => 'Param',
                'module' => $this->moduleName,
                'parent_column' => 'submission_id',
                'child_column' => 'submission_id',
            ),
            'user' => array(
                'type' => MIDAS_MANY_TO_ONE,
                'model' => 'User',
                'parent_column' => 'user_id',
                'child_column' => 'user_id',
            ),
        );

        $this->initialize();
    }

    /**
     * Associate the given submission and item.
     *
     * @param Tracker_SubmissionDao $submissionDao submission DAO
     * @param ItemDao $itemDao item DAO
     * @param string $label label
     * @param Tracker_TrendgroupDao $trendgroupDao trendgroup DAO
     */
    abstract public function associateItem($submissionDao, $itemDao, $label, $trendgroupDao);

    /**
     * Return the items associated with the given submission.
     *
     * @param Tracker_SubmissionDao $submissionDao submission DAO
     * @param Tracker_TrendgroupDao $trendgroupDao trendgroup DAO
     * @return array array of associative arrays with keys "item" and "label"
     */
    abstract public function getAssociatedItems($submissionDao, $trendgroupDao);

    /**
     * Create a submission.
     *
     * @param Tracker_ProducerDao $producerDao the producer to which the submission was submitted
     * @param string $uuid the uuid of the submission
     * @param string $name the name of the submission (defaults to '')
     * @param array $params the parameters used to generate the submission (defaults to null)
     * @return Tracker_SubmissionDao
     */
    abstract public function createSubmission($producerDao, $uuid, $name = '', $params = null);

    /**
     * Get a submission from its uuid.
     *
     * @param string $uuid the uuid of the submission
     * @return Tracker_SubmissionDao submission DAO
     */
    abstract public function getSubmission($uuid);

    /**
     * Return the submission with the given uuid (creating one if necessary).
     *
     * @param Tracker_ProducerDao $producerDao producer DAO
     * @param string $uuid the uuid of the submission
     * @return Tracker_SubmissionDao submission DAO
     */
    abstract public function getOrCreateSubmission($producerDao, $uuid);

    /**
     * Get submissions associated with a given producer.
     *
     * @param Tracker_ProducerDao $producerDao producer DAO
     * @return array submission DAOs
     */
    abstract public function getSubmissionsByProducer($producerDao);

    /**
     * Return the scalars for a given submission.
     *
     * @param Tracker_SubmissionDao $submissionDao submission DAO
     * @param bool $key whether to only retrieve scalars of key trends
     * @param bool|false|Tracker_TrendgroupDao $trendGroup dao of trend group to limit scalars
     * @return array scalar DAOs
     * @throws Zend_Exception
     */
    abstract public function getScalars($submissionDao, $key = false, $trendGroup = false);

    /**
     * Return the values (trend name, value, and unit in an array) from a given submission.
     *
     * @param Tracker_SubmissionDao $submissionDao submission DAO
     * @return array associative array with keys equal to the metric names
     */
    abstract public function getValuesFromSubmission($submissionDao);

    /**
     * Get the single latest submission associated with a given producer.
     *
     * @param Tracker_ProducerDao $producerDao producer DAO
     * @param false | string $date the end of the interval or false to use 23:59:59 of the current day
     * @param string $branch the branch of the submission for which to search
     * @param bool $onlyOneDay if true return submissions 24 hours back from $date (In the case of $date === false,
     * search only in the current day.) If false, search back as far as possible.
     * @return false | Tracker_SubmissionDao submission
     */
    abstract public function getLatestSubmissionByProducerDateAndBranch($producerDao,
                                                                        $date = false,
                                                                        $branch = 'master',
                                                                        $onlyOneDay = true);

    /**
     * Get trends associated with a submission.
     *
     * @param Tracker_SubmissionDao $submissionDao submission DAO
     * @param bool $key true if only key trends should be returned, false otherwise
     * @return array Tracker_TrendDaos
     */
    abstract public function getTrends($submissionDao, $key = true);

    /**
     * Return all distinct branch names of revisions producing submissions.
     *
     * @return array branch names
     */
    abstract public function getDistinctBranches();
}
