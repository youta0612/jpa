<?php

mkdir("./text/next_game", 0700);
echo 'created next_game'."\n";

foreach(glob('./pdf/next_game/*.pdf') as $path){
  if(is_file($path)){
    $pdf = basename( $path );
    echo $pdf;
    echo "\n";
    $results = shell_exec('pdftotext -layout ./pdf/next_game/'.$pdf.' ./text/next_game/'.$pdf.'.txt | sed \':loop; N; $!b loop; ;s/\n//g\'');
    echo $results;
  }
}
?>
