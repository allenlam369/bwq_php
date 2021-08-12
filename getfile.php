<?php

if (!isset($argv[1])) {
  echo "Usage: php $argv[0] [filename]\n\n";
  exit;
}

global $conn;
require_once 'connect_db.php';

$fn = $argv[1];

$pwd = getcwd();
define('export_dir', $pwd . DIRECTORY_SEPARATOR . 'bwq_data');

// mkdir if path is not existing
if (!file_exists(export_dir)) {
  mkdir(export_dir, 0755, true);
}

$ffn = export_file($conn, $fn);

$conn = null; // close connection

function export_file($conn, $fn) {
  $sql = "SELECT * FROM bwq WHERE filename='$fn'";
  
  try {
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
      echo "$fn is not in database\n";
      return;
    }

    //------------------------
    $fn = $row['filename'];
    $data = $row['data'];
      
    $fullfilename = export_dir . DIRECTORY_SEPARATOR . $fn;
    
    // try open file to write
    if (!$handle = fopen($fullfilename, 'w')) {
      echo "Cannot open file $fullfilename\n";
      exit;
    }
    // try write file
    if (!fwrite($handle, $data)) {
      echo "Cannot write to file $fullfilename\n";
      exit;
    }
    //------------------------

    $size = filesize($fullfilename);
    echo "Saved file: $fullfilename  $size bytes\n";
    fclose($handle);
    return $fullfilename;
    
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
}

?>

