<?php
global $conn;
require_once 'connect_db.php';

$pwd = getcwd();
define('export_dir', $pwd . DIRECTORY_SEPARATOR . 'bwq_data');
// echo "export_dir: " . export_dir . "\n";

if (!file_exists(export_dir)) {
  mkdir(export_dir, 0755, true);
}

$fn1 = export_newest_daily($conn);
$fn2 = export_newest_weekly($conn);

$conn = null; // close connection

function export_newest_weekly($conn) {
  $sql = "SELECT * FROM bwq WHERE type='weekly' ORDER BY date1 DESC";
  
  try {
    $timestamp = date("Y-m-d H:i:s");     

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
      $fn = $row['filename'];
      $data = $row['data'];
      
      $fullfilename = export_dir . DIRECTORY_SEPARATOR . $fn;
      if (!$handle = fopen($fullfilename, 'w')) {
        echo "[$timestamp] Cannot open file $fullfilename\n";
        exit;
      }
      if (!fwrite($handle, $data)) {
        echo "[$timestamp] Cannot write to file $fullfilename\n";
        exit;
      }
    }
    
    $size = filesize($fullfilename);
    echo "[$timestamp] Saved file: $fullfilename  $size bytes\n";
    fclose($handle);
    return $fullfilename;
    
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
}

function export_newest_daily($conn) {
  $sql = "SELECT * FROM bwq WHERE type='daily' ORDER BY date1 DESC";
  
  try {
    $timestamp = date("Y-m-d H:i:s");     
  
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
      $fn = $row['filename'];
      $data = $row['data'];
      
      $fullfilename = export_dir . DIRECTORY_SEPARATOR . $fn;
      if (!$handle = fopen($fullfilename, 'w')) {
        echo "[$timestamp] Cannot open file $fullfilename\n";
        exit;
      }
      if (!fwrite($handle, $data)) {
        echo "[$timestamp] Cannot write to file $fullfilename\n";
        exit;
      }
    }
    
    $size = filesize($fullfilename);
    echo "[$timestamp] Saved file: $fullfilename  $size bytes\n";
    fclose($handle);
    return $fullfilename;
    
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
}

?>

