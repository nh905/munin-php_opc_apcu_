<?php
/**
 * @file
 * OpCache/APCU Info file.
 *
 * This file should be placed in your website's docroot so the opc_apcu_php_ munin
 * plugins can read the file to report APC usage statistics.
 */

$time = time();
if (function_exists("apcu_cache_info") && function_exists("apcu_sma_info")) {
  // Get APCU memory information.(
  $mem = apcu_sma_info();
  $mem_size = $mem['num_seg'] * $mem['seg_size'];
  $mem_avail= $mem['avail_mem'];
  $mem_used = $mem_size - $mem_avail;
  // Some code taken from the file apc.php by The PHP Group.
  $nseg = $freeseg = $fragsize = $freetotal = 0;
  for ($i=0; $i < $mem['num_seg']; $i++) {
    $ptr = 0;
    foreach ($mem['block_lists'][$i] as $block) {
      if ($block['offset'] != $ptr) {
        ++$nseg;
      }
      $ptr = $block['offset'] + $block['size'];
      // Only consider blocks < 5M for the fragmentation %.
      if ($block['size'] < (5 * 1024 * 1024)) $fragsize += $block['size'];
      $freetotal += $block['size'];
    }
    $freeseg += count($mem['block_lists'][$i]);
  }
  if ($freeseg < 2) {
    $fragsize = 0;
    $freeseg = 0;
  }
  /* User Cache ------------------------------------------------------------- */
  $cache = @apcu_cache_info(true);
  // Item hits, misses and inserts
  $user_hits = $cache['num_hits'];
  $user_misses = $cache['num_misses'];
  $user_inserts = $cache['num_inserts'];
  $user_req_rate = ($cache['num_hits'] + $cache['num_misses']) / ($time - $cache['start_time']);
  $user_hit_rate = ($cache['num_hits']) / ($time - $cache['start_time']); // Number of entries in cache $number_entries = $cache['num_entries'];
  $user_miss_rate = ($cache['num_misses']) / ($time - $cache['start_time']); // Total number of cache purges $purges = $cache['expunges'];
  $user_insert_rate = ($cache['num_inserts']) / ($time - $cache['start_time']);
  $user_number_entries = $cache['num_entries']; // Number of entries in cache
  $user_purges = $cache['expunges']; // Total number of cache purges
  $user_mem_size = $cache['mem_size'];
  // Build output array.
  $output = array(
//    'apcu_size: ' . $mem_size,
    'mode: apcu',
    'user_used: ' . $mem_used,
    'user_free: ' . ($mem_avail - $fragsize),
    'user_fragment_percentage: ' . sprintf("%.2f", ($fragsize/$mem_avail)*100),
    'user_fragmented: ' . sprintf("%.2f", $fragsize),
    'user_fragment_segments: ' . $freeseg,
    'user_size: ' . $user_mem_size,
    'user_hits: ' . sprintf("%.2f", ($user_hits + $user_misses) ? ($user_hits * 100 / ($user_hits + $user_misses)) : 0),
    'user_misses: ' . sprintf("%.2f", ($user_hits + $user_misses) ? ($user_misses * 100 / ($user_hits + $user_misses)) : 0),
    'user_request_rate: ' . sprintf("%.2f", $user_req_rate),
    'user_hit_rate: ' . sprintf("%.2f", $user_hit_rate),
    'user_miss_rate: ' . sprintf("%.2f", $user_miss_rate),
    'user_insert_rate: ' . sprintf("%.2f", $user_insert_rate),
    'user_entries: ' . $user_number_entries,
    'user_inserts: ' . $user_inserts,
    'user_purges: ' . $user_purges,
    'user_purge_rate: ' . sprintf("%.2f", $user_inserts ? (100 - ($user_number_entries / $user_inserts) * 100) : 0),
  );
} else {
  $output = array();
}

if (function_exists("opcache_get_status")) {
  $cache = opcache_get_status(false);     
  if (!empty($cache)) {
    $used = $cache['memory_usage']['used_memory'];
    $free = $cache['memory_usage']['free_memory'];
    $size = $used + $free;
    $fragmented = $cache['memory_usage']['wasted_memory'];
    $fragment_percentage = $cache['memory_usage']['current_wasted_percentage'];
    $hits = $cache['opcache_statistics']['hits'];
    $misses = $cache['opcache_statistics']['misses'];
//  $inserts = $cache['num_inserts'];
    $req_rate = ($hits + $misses) / ($time - $cache['opcache_statistics']['start_time']);
    $hit_rate = ($hits) / ($time - $cache['opcache_statistics']['start_time']); // Number of entries in cache $number_entries = $cache['num_entries'];
    $miss_rate = ($misses) / ($time - $cache['opcache_statistics']['start_time']); // Total number of cache purges $purges = $cache['expunges'];
//  $insert_rate = ($cache['num_inserts']) / ($time - $cache['start_time']);
    $number_entries = $cache['opcache_statistics']['num_cached_scripts']; // Number of entries in cache
//  $purges = $cache['expunges']; // Total number of cache purges
//  $optcode_mem_size = $cache['mem_size'];

    // Build output array.
    $outopc = array(                                   
      'mode: opcache',
      'size: ' . $size,
      'used: ' . $used,
      'free: ' . $free,
      'hits: ' . sprintf("%.2f", $hits * 100 / ($hits + $misses)),
      'misses: ' . sprintf("%.2f", $misses * 100 / ($hits + $misses)),
      'request_rate: ' . sprintf("%.2f", $req_rate),
      'hit_rate: ' . sprintf("%.2f", $hit_rate),
      'miss_rate: ' . sprintf("%.2f", $miss_rate),
      'entries: ' . $number_entries,
      'fragment_percentage: ' . sprintf("%.2f", $fragment_percentage),
      'fragmented: ' . $fragmented,
    );
  }
}
$output = array_merge($output, $outopc);
echo empty($output) ? 'Caches-not-installed' : implode(' ', $output);

