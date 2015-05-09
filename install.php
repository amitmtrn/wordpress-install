<?php

function downloadFile($url, $zipFile) {
  $zipResource = fopen($zipFile, "w");
  // Get The Zip File From Server
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_FAILONERROR, true);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_AUTOREFERER, true);
  curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 500);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_FILE, $zipResource);
  $page = curl_exec($ch);
  if(!$page) {
   echo "Error :- ".curl_error($ch);
  }
  curl_close($ch);
}

function extraxtZip($zipFile, $extractPath) {
  /* Open the Zip file */
  $zip = new ZipArchive;
  if($zip->open($zipFile) != "true"){
   echo "Error :- Unable to open the Zip File";
  }
  /* Extract Zip File */
  $zip->extractTo($extractPath);
  $zip->close();

}

function rmove($src, $dest) {

  // If source is not a directory stop processing
  if(!is_dir($src)) return false;

  // If the destination directory does not exist create it
  if(!is_dir($dest)) {
    if(!mkdir($dest)) {
    // If the destination directory could not be created stop processing
      return false;
    }
  }

  // Open the source directory to read in files
  $i = new DirectoryIterator($src);
  foreach($i as $f) {
    if($f->isFile()) {
      rename($f->getRealPath(), "$dest/" . $f->getFilename());
    } else if(!$f->isDot() && $f->isDir()) {
      rmove($f->getRealPath(), "$dest/$f");
      if(is_file($f->getRealPath()))
        unlink($f->getRealPath());
    }
  }
  if(is_file($src))
    unlink($src);
}

function destroy_dir($dir) {
  if ((!is_dir($dir) || is_link($dir))&&is_file()) return unlink($dir);
  foreach (scandir($dir) as $file) {
    if ($file == "." || $file == "..") continue;
    if (!destroy_dir($dir . DIRECTORY_SEPARATOR . $file)) {
      chmod($dir . DIRECTORY_SEPARATOR . $file, 0777);
    if (!destroy_dir($dir . DIRECTORY_SEPARATOR . $file)) return false;
    };
  }
  return rmdir($dir);
}
function init() {

  if (is_windows()) {
    echo "download file\n";
    downloadFile("https://wordpress.org/latest.zip", getcwd()."\wordpress.zip");
    echo "extract zip\n";
    extraxtZip(getcwd()."\wordpress.zip", getcwd());
    echo "move files from folder to current folder\n";
    rmove(getcwd()."\wordpress", getcwd());
    echo "remove folder\n";
    destroy_dir(getcwd()."\wordpress");
    echo "remove zip file\n";
    unlink(getcwd()."\wordpress.zip");
  } else {
    echo "download file\n";
    downloadFile("https://wordpress.org/latest.zip", getcwd()."/wordpress.zip");
    echo "extract zip\n";
    extraxtZip(getcwd()."/wordpress.zip", getcwd());
    echo "move files from folder to current folder\n";
    rmove(getcwd()."/wordpress", getcwd());
    echo "remove folder\n";
    destroy_dir(getcwd()."/wordpress");
    echo "remove zip file\n";
    unlink(getcwd()."/wordpress.zip");
  }
}

function is_windows() {
  return strpos(php_uname("a"),'Windows') !== false;
}

init();
?>
