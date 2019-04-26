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
 * The "clusters" collection of methods.
 * Typical usage is:
 *  <code>
 *   $bigtableadminService = new Google_Service_BigtableAdmin(...);
 *   $clusters = $bigtableadminService->clusters;
 *  </code>
 */
class Google_Service_BigtableAdmin_Resource_ProjectsInstancesClusters extends Google_Service_Resource
{
  /**
   * Creates a cluster within an instance. (clusters.create)
   *
   * @param string $parent The unique name of the instance in which to create the
   * new cluster. Values are of the form `projects//instances/`.
   * @param Google_Service_BigtableAdmin_Cluster $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string clusterId The ID to be used when referring to the new
   * cluster within its instance, e.g., just `mycluster` rather than
   * `projects/myproject/instances/myinstance/clusters/mycluster`.
   * @return Google_Service_BigtableAdmin_Operation
   */
  public function create($parent, Google_Service_BigtableAdmin_Cluster $postBody, $optParams = array())
  {
    $params = array('parent' => $parent, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('create', array($params), "Google_Service_BigtableAdmin_Operation");
  }
  /**
   * Deletes a cluster from an instance. (clusters.delete)
   *
   * @param string $name The unique name of the cluster to be deleted. Values are
   * of the form `projects//instances//clusters/`.
   * @param array $optParams Optional parameters.
   * @return Google_Service_BigtableAdmin_BigtableadminEmpty
   */
  public function delete($name, $optParams = array())
  {
    $params = array('name' => $name);
    $params = array_merge($params, $optParams);
    return $this->call('delete', array($params), "Google_Service_BigtableAdmin_BigtableadminEmpty");
  }
  /**
   * Gets information about a cluster. (clusters.get)
   *
   * @param string $name The unique name of the requested cluster. Values are of
   * the form `projects//instances//clusters/`.
   * @param array $optParams Optional parameters.
   * @return Google_Service_BigtableAdmin_Cluster
   */
  public function get($name, $optParams = array())
  {
    $params = array('name' => $name);
    $params = array_merge($params, $optParams);
    return $this->call('get', array($params), "Google_Service_BigtableAdmin_Cluster");
  }
  /**
   * Lists information about clusters in an instance.
   * (clusters.listProjectsInstancesClusters)
   *
   * @param string $parent The unique name of the instance for which a list of
   * clusters is requested. Values are of the form `projects//instances/`. Use ` =
   * '-'` to list Clusters for all Instances in a project, e.g.,
   * `projects/myproject/instances/-`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string pageToken DEPRECATED: This field is unused and ignored.
   * @return Google_Service_BigtableAdmin_ListClustersResponse
   */
  public function listProjectsInstancesClusters($parent, $optParams = array())
  {
    $params = array('parent' => $parent);
    $params = array_merge($params, $optParams);
    return $this->call('list', array($params), "Google_Service_BigtableAdmin_ListClustersResponse");
  }
  /**
   * Updates a cluster within an instance. (clusters.update)
   *
   * @param string $name (`OutputOnly`) The unique name of the cluster. Values are
   * of the form `projects//instances//clusters/a-z*`.
   * @param Google_Service_BigtableAdmin_Cluster $postBody
   * @param array $optParams Optional parameters.
   * @return Google_Service_BigtableAdmin_Operation
   */
  public function update($name, Google_Service_BigtableAdmin_Cluster $postBody, $optParams = array())
  {
    $params = array('name' => $name, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('update', array($params), "Google_Service_BigtableAdmin_Operation");
  }
}
