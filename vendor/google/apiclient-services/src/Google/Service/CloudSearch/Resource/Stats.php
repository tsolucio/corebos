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
 * The "stats" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudsearchService = new Google_Service_CloudSearch(...);
 *   $stats = $cloudsearchService->stats;
 *  </code>
 */
class Google_Service_CloudSearch_Resource_Stats extends Google_Service_Resource
{
  /**
   * Gets indexed item statistics aggreggated across all data sources.
   * (stats.getIndex)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param int toDate.day Day of month. Must be from 1 to 31 and valid for
   * the year and month.
   * @opt_param int toDate.month Month of date. Must be from 1 to 12.
   * @opt_param int toDate.year Year of date. Must be from 1 to 9999.
   * @opt_param int fromDate.month Month of date. Must be from 1 to 12.
   * @opt_param int fromDate.day Day of month. Must be from 1 to 31 and valid for
   * the year and month.
   * @opt_param int fromDate.year Year of date. Must be from 1 to 9999.
   * @return Google_Service_CloudSearch_GetCustomerIndexStatsResponse
   */
  public function getIndex($optParams = array())
  {
    $params = array();
    $params = array_merge($params, $optParams);
    return $this->call('getIndex', array($params), "Google_Service_CloudSearch_GetCustomerIndexStatsResponse");
  }
}
