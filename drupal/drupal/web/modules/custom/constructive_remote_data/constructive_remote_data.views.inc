<?php

/**
 * @file
 * Provides views data for the Constructive API.
 */

declare(strict_types=1);

/**
 * Implements hook_views_data().
 */
function constructive_remote_data_views_data(): array {
  $data = [];

  $data['constructive_remote_data']['table']['group'] = 'constructive_remote_data';
  $data['constructive_remote_data']['table']['entity type'] = 'node';
  $data['constructive_remote_data']['table']['entity revision'] = FALSE;
  $data['constructive_remote_data']['table']['base'] = [
    'title' => 'ConstructiveApi',
    'query_id' => 'views_remote_data_query',
  ];

  return $data;
}
