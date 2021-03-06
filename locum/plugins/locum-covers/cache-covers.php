#!/usr/bin/php5 -q
<?php
$break = 0;
$processes = 10;
$records_per_process = 1000;
$lowest_record_num = 1;

// Init scripts, library locations, and binaries
$script_dir = realpath(dirname(__FILE__));
$locum_lib_dir = substr($script_dir, 0, -21);

$locum_covers_config = parse_ini_file($locum_lib_dir . '/config/locum-covers.ini', true);

require_once($script_dir . '/locum-covers.php');

$locum = new locum_covers;

if ($argv[1] == "stats") {
  print "Cover Cache Statistics:\n";
  echo $locum->get_stats();
} else if (intval($argv[1]) >= $lowest_record_num && intval($argv[2]) > 0) {
  // Process a batch of covers if given start and end nums
  print "Batch Process:\n";
  $break = intval($argv[1]);
  $limit = intval($argv[2]);
  $type = ($argv[3] == 'RETRY' ? 'RETRY' : 'NEW');
  $locum->process_covers($break, $limit, $type);
} else {
  $batches = array();
  for ($n = 0; $n < $processes; $n++) {
    $batch = $locum->get_batch($break, $records_per_process);
    if ($batch['start'] && $batch['end']) {
      $batches[] = $batch;
      $break = $batch['end'];
      $type = $batch['type'];
    } else {
      print "Break 1";
      break;
    }
  }
  foreach ($batches as $limits) {
    $start = $limits['start'];
    $type = $limits['type'];
    exec("php " . $script_dir . "/cache-covers.php $start $records_per_process $type > " . $locum_covers_config['cover_cache']['log_path'] . "/covercache_$start-$type.log &");
  }
}

//exec("chown -R " . $locum_covers_config['cover_cache']['apache_user'] . '.' . $locum_covers_config['cover_cache']['apache_group'] . ' ' . $locum_covers_config['cover_cache']['image_path'] . '/');
?>
