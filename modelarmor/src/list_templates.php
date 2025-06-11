<?php
/*
 * Copyright 2025 Google LLC.
 *
 * Licensed under the Apache License, Version 2.0 (the 'License');
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an 'AS IS' BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
*/

declare(strict_types=1);

namespace Google\Cloud\Samples\ModelArmor;

// [START modelarmor_list_templates]
use Google\Cloud\ModelArmor\V1\Client\ModelArmorClient;
use Google\Cloud\ModelArmor\V1\ListTemplatesRequest;

/**
 * Lists all Model Armor templates in a location.
 *
 * @param string $projectId The ID of your Google Cloud Platform project (e.g. 'my-project').
 * @param string $locationId The ID of the location where the templates are stored (e.g. 'us-central1').
 */
function list_templates($projectId, $locationId): void
{
    // Specify regional endpoint.
    $options = ['apiEndpoint' => "modelarmor.$locationId.rep.googleapis.com"];

    // Instantiates a client.
    $client = new ModelArmorClient($options);

    // Build the resource name of the parent location.
    $parent = $client->locationName($projectId, $locationId);

    // Prepare the request.
    $listTemplatesrequest = new ListTemplatesRequest()->setParent($parent);

    // Send the request and collect all results.
    $templates = iterator_to_array($client->listTemplates($listTemplatesrequest)->iterateAllElements());

    foreach ($templates as $template) {
        printf('Template: %s' . PHP_EOL, $template->getName());
    }
}
// [END modelarmor_list_templates]

// The following 2 lines are only needed to execute the samples on the CLI
require_once __DIR__ . '/../../testing/sample_helpers.php';
\Google\Cloud\Samples\execute_sample(__FILE__, __NAMESPACE__, $argv);
