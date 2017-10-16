<?php

mkdir("./text/mvp", 0700);
echo 'created mvp'."\n";

foreach(glob('./pdf/mvp/*.pdf') as $path){
  if(is_file($path)){
    $pdf = basename( $path );
    echo $pdf;
    echo "\n";
    $results = shell_exec('pdftotext -layout ./pdf/mvp/'.$pdf.' ./text/mvp/'.$pdf.'.txt | sed \':loop; N; $!b loop; ;s/\n//g\'');
    echo $results;
  }
}
?>
