<?php
global $conn;
require_once 'connect_db.php';

define('data_dir', '/media/NAS/data');
define('fnDaily', 'HKU_daily.csv');
define('fnWeekly', 'HKU_weekly.csv');

$files = scandir(data_dir);

if (!$files) {
  echo "data path is empty\n";
  exit;
}

$count = 0;

// find daily file
$fullPath = data_dir . DIRECTORY_SEPARATOR . fnDaily;
if (file_exists($fullPath)) {
  // file modi time
  $ctime1 = filemtime($fullPath);
  $dStr = date("Ymd", $ctime1);
  $fn = "HKU_$dStr.csv";
  $exist = has_existing_row($conn, $fn, $dStr);
  if (!$exist) {write_to_db_daily($conn, $fn, $dStr); $count++;}
}

// find weekly file
$fullPath = data_dir . DIRECTORY_SEPARATOR . fnWeekly;
if (file_exists($fullPath)) {
  // file modi time
  $ctime1 = filemtime($fullPath);
  $st = date('Ymd', $ctime1);
  $ed = date('Ymd', strtotime($st . ' + 7 days')); 
  $fn = 'HKU_' . $st . '_' . $ed . '.csv'; 

//echo "ctime1=$ctime1, st=$st, fn=$fn\n";

  $exist = has_existing_row($conn, $fn, $st);
  if (!$exist) {write_to_db_weekly($conn, $fn, $st, $ed); $count++;}
}

$timestamp = date("Y-m-d H:i:s");
echo "[$timestamp] $count files saved to db\n";
$conn = null; // close connection


function has_existing_row($conn, $fn, $date) {
  $sql = "SELECT COUNT(*) FROM bwq WHERE filename='$fn'";
  
  try {
    $res = $conn->query($sql);
    $count = $res->fetchColumn();
    if ($count==0) return false; else return true;
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
}



// for daily rows
function write_to_db_daily($conn, $fn, $date) {
  $fullfilename = data_dir . DIRECTORY_SEPARATOR . fnDaily;
  $blob_data = file_get_contents($fullfilename);
  $sql = "INSERT INTO bwq(filename, date1, data, type) VALUES ('$fn', '$date', '$blob_data', 'daily')";
  try {
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $timestamp = date("Y-m-d H:i:s");
    echo "[$timestamp] New record created: $fn\n";
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
}

// for weekly rows
function write_to_db_weekly($conn, $fn, $date1, $date2) {
  $fullfilename = data_dir . DIRECTORY_SEPARATOR . fnWeekly;
  $blob_data = file_get_contents($fullfilename);
  $sql = "INSERT INTO bwq(filename, date1, date2, data, type) VALUES ('$fn', '$date1', '$date2', '$blob_data', 'weekly')";
  try {
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $timestamp = date("Y-m-d H:i:s");
    echo "[$timestamp] New record created: $fn\n";
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
}

function endsWith( $haystack, $needle) {
  $needle = strtolower($needle);
  $length = strlen( $needle );
  if( !$length ) {
    return true;
  }
  return substr( $haystack, -$length ) === $needle;
}

?>

