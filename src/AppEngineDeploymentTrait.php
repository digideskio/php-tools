<?php
/*
 * Copyright 2016 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Google\Cloud\TestUtils;

use GuzzleHttp\Client;

/**
 * Trait AppEngineDeploymentTrait
 * @package Google\Cloud\TestUtils
 *
 * Use this trait to deploy the project to GCP for an end-to-end test.
 */
trait AppEngineDeploymentTrait
{
    /** @var  \Google\Cloud\TestUtils\GcloudWrapper */
    private static $gcloudWrapper;
    /** @var  \GuzzleHttp\Client */
    private $client;

    /**
     * Return the project id for the test.
     *
     * @return string
     */
    private static function getProjectId()
    {
        $projectId = getenv('GOOGLE_PROJECT_ID');
        if ($projectId === false) {
            self::fail('Please set GOOGLE_PROJECT_ID env var.');
        }
        return $projectId;
    }

    /**
     * Return the version id for the test.
     *
     * @return string
     */
    private static function getVersionId()
    {
        $versionId = getenv('GOOGLE_VERSION_ID');
        if ($versionId === false) {
            self::fail('Please set GOOGLE_VERSION_ID env var.');
        }
        return $versionId;
    }

    /**
     * Deploy the application.
     *
     * @beforeClass
     */
    public static function deployApp()
    {
        if (getenv('RUN_DEPLOYMENT_TESTS') !== 'true') {
            self::markTestSkipped(
                'To run this test, set RUN_DEPLOYMENT_TESTS env to true.'
            );
        }
        self::$gcloudWrapper = new GcloudWrapper(
            self::getProjectId(),
            self::getVersionId()
        );
        if (self::$gcloudWrapper->deploy() === false) {
            self::fail('Deployment failed.');
        }
    }

    /**
     * Delete the application.
     *
     * @afterClass
     */
    public static function deleteApp()
    {
        self::$gcloudWrapper->delete();
    }

    /**
     * Set up the client.
     *
     * @before
     */
    public function setUpClient()
    {
        $url = self::$gcloudWrapper->getBaseUrl();
        $this->client = new Client(['base_uri' => $url]);
    }
}