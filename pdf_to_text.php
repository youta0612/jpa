<?php
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
