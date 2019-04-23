<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

/**
 * The "sources" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudsearchService = new Google_Service_CloudSearch(...);
 *   $sources = $cloudsearchService->sources;
 *  </code>
 */
class Google_Service_CloudSearch_Resource_QuerySources extends Google_Service_Resource
{
  /**
   * Returns list of sources that user can use for Search and Suggest APIs.
   * (sources.listQuerySources)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestOptions.searchApplicationId Id of the application
   * created using SearchApplicationsService.
   * @opt_param string requestOptions.timeZone Current user's time zone id, such
   * as "America/Los_Angeles" or "Australia/Sydney". These IDs are defined by
   * [Unicode Common Locale Data Repository (CLDR)](http://cldr.unicode.org/)
   * project, and currently available in the file
   * [timezone.xml](http://unicode.org/repos/cldr/trunk/common/bcp47/timezone.xml)
   * @opt_param string pageToken Number of sources to return in the response.
   * @opt_param bool requestOptions.debugOptions.enableDebugging If set, the
   * request will enable debugging features of Cloud Search. Only turn on this
   * field, if asked by Google to help with debugging.
   * @opt_param string requestOptions.languageCode The BCP-47 language code, such
   * as "en-US" or "sr-Latn". For more information, see
   * http://www.unicode.org/reports/tr35/#Unicode_locale_identifier. For
   * translations.
   * @return Google_Service_CloudSearch_ListQuerySourcesResponse
   */
  public function listQuerySources($optParams = array())
  {
    $params = array();
    $params = array_merge($params, $optParams);
    return $this->call('list', array($params), "Google_Service_CloudSearch_ListQuerySourcesResponse");
  }
}
