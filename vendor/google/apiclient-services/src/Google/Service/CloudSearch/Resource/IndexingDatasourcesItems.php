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
 * The "items" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudsearchService = new Google_Service_CloudSearch(...);
 *   $items = $cloudsearchService->items;
 *  </code>
 */
class Google_Service_CloudSearch_Resource_IndexingDatasourcesItems extends Google_Service_Resource
{
  /**
   * Deletes Item resource for the specified resource name. (items.delete)
   *
   * @param string $name Required. Name of the item to delete. Format:
   * datasources/{source_id}/items/{item_id}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string connectorName Name of connector making this call. Format:
   * datasources/{source_id}/connectors/{ID}
   * @opt_param string version Required. The incremented version of the item to
   * delete from the index. The indexing system stores the version from the
   * datasource as a byte string and compares the Item version in the index to the
   * version of the queued Item using lexical ordering.
   *
   * Cloud Search Indexing won't delete any queued item with a version value that
   * is less than or equal to the version of the currently indexed item. The
   * maximum length for this field is 1024 bytes.
   * @opt_param bool debugOptions.enableDebugging If set, the request will enable
   * debugging features of Cloud Search. Only turn on this field, if asked by
   * Google to help with debugging.
   * @opt_param string mode Required. The RequestMode for this request.
   * @return Google_Service_CloudSearch_Operation
   */
  public function delete($name, $optParams = array())
  {
    $params = array('name' => $name);
    $params = array_merge($params, $optParams);
    return $this->call('delete', array($params), "Google_Service_CloudSearch_Operation");
  }
  /**
   * Deletes all items in a queue. This method is useful for deleting stale items.
   * (items.deleteQueueItems)
   *
   * @param string $name Name of the Data Source to delete items in a queue.
   * Format: datasources/{source_id}
   * @param Google_Service_CloudSearch_DeleteQueueItemsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Google_Service_CloudSearch_Operation
   */
  public function deleteQueueItems($name, Google_Service_CloudSearch_DeleteQueueItemsRequest $postBody, $optParams = array())
  {
    $params = array('name' => $name, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('deleteQueueItems', array($params), "Google_Service_CloudSearch_Operation");
  }
  /**
   * Gets Item resource by item name. (items.get)
   *
   * @param string $name Name of the item to get info. Format:
   * datasources/{source_id}/items/{item_id}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string connectorName Name of connector making this call. Format:
   * datasources/{source_id}/connectors/{ID}
   * @opt_param bool debugOptions.enableDebugging If set, the request will enable
   * debugging features of Cloud Search. Only turn on this field, if asked by
   * Google to help with debugging.
   * @return Google_Service_CloudSearch_Item
   */
  public function get($name, $optParams = array())
  {
    $params = array('name' => $name);
    $params = array_merge($params, $optParams);
    return $this->call('get', array($params), "Google_Service_CloudSearch_Item");
  }
  /**
   * Updates Item ACL, metadata, and content. It will insert the Item if it does
   * not exist. This method does not support partial updates.  Fields with no
   * provided values are cleared out in the Cloud Search index. (items.index)
   *
   * @param string $name Name of the Item. Format:
   * datasources/{source_id}/items/{item_id} This is a required field. The maximum
   * length is 1536 characters.
   * @param Google_Service_CloudSearch_IndexItemRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Google_Service_CloudSearch_Operation
   */
  public function index($name, Google_Service_CloudSearch_IndexItemRequest $postBody, $optParams = array())
  {
    $params = array('name' => $name, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('index', array($params), "Google_Service_CloudSearch_Operation");
  }
  /**
   * Lists all or a subset of Item resources. (items.listIndexingDatasourcesItems)
   *
   * @param string $name Name of the Data Source to list Items.  Format:
   * datasources/{source_id}
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool debugOptions.enableDebugging If set, the request will enable
   * debugging features of Cloud Search. Only turn on this field, if asked by
   * Google to help with debugging.
   * @opt_param string connectorName Name of connector making this call. Format:
   * datasources/{source_id}/connectors/{ID}
   * @opt_param bool brief When set to true, the indexing system only populates
   * the following fields: name, version, metadata.hash, structured_data.hash,
   * content.hash. If this value is false, then all the fields are populated in
   * Item.
   * @opt_param string pageToken The next_page_token value returned from a
   * previous List request, if any.
   * @opt_param int pageSize Maximum number of items to fetch in a request. The
   * max value is 1000 when brief is true.  The max value is 10 if brief is false.
   * The default value is 10
   * @return Google_Service_CloudSearch_ListItemsResponse
   */
  public function listIndexingDatasourcesItems($name, $optParams = array())
  {
    $params = array('name' => $name);
    $params = array_merge($params, $optParams);
    return $this->call('list', array($params), "Google_Service_CloudSearch_ListItemsResponse");
  }
  /**
   * Polls for unreserved items from the indexing queue and marks a set as
   * reserved, starting with items that have the oldest timestamp from the highest
   * priority ItemStatus. The priority order is as follows:  ERROR
   *
   * MODIFIED
   *
   * NEW_ITEM
   *
   * ACCEPTED
   *
   * Reserving items ensures that polling from other threads cannot create
   * overlapping sets.
   *
   * After handling the reserved items, the client should put items back into the
   * unreserved state, either by calling index, or by calling push with the type
   * REQUEUE.
   *
   * Items automatically become available (unreserved) after 4 hours even if no
   * update or push method is called. (items.poll)
   *
   * @param string $name Name of the Data Source to poll items. Format:
   * datasources/{source_id}
   * @param Google_Service_CloudSearch_PollItemsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Google_Service_CloudSearch_PollItemsResponse
   */
  public function poll($name, Google_Service_CloudSearch_PollItemsRequest $postBody, $optParams = array())
  {
    $params = array('name' => $name, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('poll', array($params), "Google_Service_CloudSearch_PollItemsResponse");
  }
  /**
   * Pushes an item onto a queue for later polling and updating. (items.push)
   *
   * @param string $name Name of the item to push into the indexing queue. Format:
   * datasources/{source_id}/items/{ID} This is a required field. The maximum
   * length is 1536 characters.
   * @param Google_Service_CloudSearch_PushItemRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Google_Service_CloudSearch_Item
   */
  public function push($name, Google_Service_CloudSearch_PushItemRequest $postBody, $optParams = array())
  {
    $params = array('name' => $name, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('push', array($params), "Google_Service_CloudSearch_Item");
  }
  /**
   * Unreserves all items from a queue, making them all eligible to be polled.
   * This method is useful for resetting the indexing queue after a connector has
   * been restarted. (items.unreserve)
   *
   * @param string $name Name of the Data Source to unreserve all items. Format:
   * datasources/{source_id}
   * @param Google_Service_CloudSearch_UnreserveItemsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Google_Service_CloudSearch_Operation
   */
  public function unreserve($name, Google_Service_CloudSearch_UnreserveItemsRequest $postBody, $optParams = array())
  {
    $params = array('name' => $name, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('unreserve', array($params), "Google_Service_CloudSearch_Operation");
  }
  /**
   * Creates an upload session for uploading item content. For items smaller than
   * 100 KiB, it's easier to embed the content inline within update.
   * (items.upload)
   *
   * @param string $name Name of the Data Source to start a resumable upload.
   * Format: datasources/{source_id}
   * @param Google_Service_CloudSearch_StartUploadItemRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Google_Service_CloudSearch_UploadItemRef
   */
  public function upload($name, Google_Service_CloudSearch_StartUploadItemRequest $postBody, $optParams = array())
  {
    $params = array('name' => $name, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('upload', array($params), "Google_Service_CloudSearch_UploadItemRef");
  }
}
