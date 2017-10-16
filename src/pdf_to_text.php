<?php
//まず格納先を作る
mkdir("./text", 0700);
echo 'created text'."\n";
mkdir("./text/schedule", 0700);
echo 'created schedule'."\n";

foreach(glob('./pdf/schedule/*.pdf') as $path){
  if(is_file($path)){
    $pdf = basename( $path );
    echo $pdf;
    echo "\n";
    $results = shell_exec('pdftotext -layout ./pdf/schedule/'.$pdf.' ./text/schedule/'.$pdf.'.txt | sed \':loop; N; $!b loop; ;s/\n//g\'');
    echo $results;
  }
}
?>
