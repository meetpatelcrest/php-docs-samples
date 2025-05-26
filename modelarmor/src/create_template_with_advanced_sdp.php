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

require_once __DIR__ . '/../vendor/autoload.php';

if (count($argv) < 4 || count($argv) > 6) {
    return printf("Usage: php %s PROJECT_ID LOCATION_ID TEMPLATE_ID [INSPECT_TEMPLATE] [DEIDENTIFY_TEMPLATE]\n", basename(__FILE__));
}
list($_, $projectId, $locationId, $templateId) = $argv;
$inspectTemplate = $argv[4] ?? '';
$deidentifyTemplate = $argv[5] ?? '';

// [START modelarmor_create_template_with_advanced_sdp]
// Import the ModelArmor client library.
use Google\Cloud\ModelArmor\V1\Client\ModelArmorClient;
use Google\Cloud\ModelArmor\V1\SdpAdvancedConfig;
use Google\Cloud\ModelArmor\V1\Template;
use Google\Cloud\ModelArmor\V1\FilterConfig;
use Google\Cloud\ModelArmor\V1\RaiFilterSettings;
use Google\Cloud\ModelArmor\V1\RaiFilterSettings\RaiFilter;
use Google\Cloud\ModelArmor\V1\CreateTemplateRequest;
use Google\Cloud\ModelArmor\V1\RaiFilterType;
use Google\Cloud\ModelArmor\V1\DetectionConfidenceLevel;
use Google\Cloud\ModelArmor\V1\SdpFilterSettings;

/** Uncomment and populate these variables in your code */
// $projectId = "YOUR_GOOGLE_CLOUD_PROJECT"; (e.g. 'my-project');
// $locationId = 'YOUR_LOCATION_ID'; (e.g. 'my-location');
// $templateId = 'YOUR_TEMPLATE_ID'; (e.g. 'my-template');
// $inspectTemplate = 'YOUR_INSPECT_TEMPLATE'; (e.g. 'organizations/{organization}/inspectTemplates/{inspect_template}')
// $deidentifyTemplate = 'YOUR_DEIDENTIFY_TEMPLATE'; (e.g. 'organizations/{organization}/deidentifyTemplates/{deidentify_template}')

// Specify regional endpoint.
$options = ['apiEndpoint' => "modelarmor.$locationId.rep.googleapis.com"];

$client = new ModelArmorClient($options);

// Build the resource name of the parent location.
$parent = $client->locationName($projectId, $locationId);

$filters = [
    (new RaiFilter())
        ->setFilterType(RaiFilterType::DANGEROUS)
        ->setConfidenceLevel(DetectionConfidenceLevel::HIGH),
    (new RaiFilter())
        ->setFilterType(RaiFilterType::HARASSMENT)
        ->setConfidenceLevel(DetectionConfidenceLevel::MEDIUM_AND_ABOVE),
    (new RaiFilter())
        ->setFilterType(RaiFilterType::HATE_SPEECH)
        ->setConfidenceLevel(DetectionConfidenceLevel::HIGH),
    (new RaiFilter())
        ->setFilterType(RaiFilterType::SEXUALLY_EXPLICIT)
        ->setConfidenceLevel(DetectionConfidenceLevel::HIGH)
];

$raiFilterSetting = (new RaiFilterSettings())->setRaiFilters($filters);

$sdpAdvancedConfig = (new SdpAdvancedConfig())
    ->setInspectTemplate($inspectTemplate)
    ->setDeidentifyTemplate($deidentifyTemplate);

$sdpSettings = (new SdpFilterSettings())->setAdvancedConfig($sdpAdvancedConfig);

// Define the template configuration with advanced SDP settings.
$templateFilterConfig = (new FilterConfig())
    ->setRaiSettings($raiFilterSetting)
    ->setSdpSettings($sdpSettings);

$template = (new Template())->setFilterConfig($templateFilterConfig);

$request = (new CreateTemplateRequest())
    ->setParent($parent)
    ->setTemplateId($templateId)
    ->setTemplate($template);

$response = $client->createTemplate($request);

printf('Template created: %s' . PHP_EOL, $response->getName());
// [END modelarmor_create_template_with_advanced_sdp]
