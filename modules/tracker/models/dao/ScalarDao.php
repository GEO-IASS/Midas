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
 * Scalar DAO for the tracker module.
 *
 * @method int getScalarId()
 * @method void setScalarId(int $scalarId)
 * @method int getSubmissionId()
 * @method void setSubmissionId(int $submissionId)
 * @method Tracker_SubmissionDao getSubmission()
 * @method void setSubmission(Tracker_SubmissionDao $submissionDao)
 * @method int getTrendId()
 * @method void setTrendId(int $trendId)
 * @method float getValue()
 * @method void setValue(float $value)
 * @method string getSubmitTime() // read-only from submission join
 * @method int getOfficial() // read-only from submission join
 * @method Tracker_TrendDao getTrend()
 * @method void setTrend(Tracker_TrendDao $trendDao)
 */
class Tracker_ScalarDao extends Tracker_AppDao
{
    /** @var string */
    public $_model = 'Scalar';

    /** @var string */
    public $_module = 'tracker';
}
