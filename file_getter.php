<?php
  // コンテンツを取得
  $pdfs = [];
  $ch = curl_init();
  curl_setopt_array($ch, [
      CURLOPT_URL => 'http://www.poolplayers.jp/standings/',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_AUTOREFERER => true,
      CURLOPT_USERAGENT => 'Mozilla/5.0',
      CURLOPT_ENCODING => 'gzip',
  ]);
  $html = curl_exec($ch);
  // エラーを出さずにDOMDocumentに読み込む
  $dom = new DOMDocument;
  @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
  // DOMDocumentからXPath式を実行するためのDOMXPathを生成
  $xpath = new DOMXPath($dom);
  // var_dump($xpath);
  // class=standingsTableであるtableの配下のtd要素を全ノードから検索する
  foreach ($xpath->query('//table[contains(@class, "standingsTable")]/tr/td[a[not(contains(@href, "/team/") or contains(@href, "/9") or contains(@href, "/8") or contains(@href, "/R") or contains(@href, "/W") )]]') as $li) {
    // 取得したURLを代入する
    $pdf = $xpath->evaluate('string(.//a/@href)', $li);
    //ダウンロードして指定フォルダに保存
    echo "output URL\n";
    var_dump($pdf);
    echo "\n";
    $data = file_get_contents($pdf);
    preg_match("/(?:S|T|\/)[0-7][0-9]+.pdf$/", $pdf, $file_name);
    echo "output filename\n";
    var_dump($file_name[0]);
    echo "\n";
    if (preg_match("/^\/[0-7]/", $file_name[0])) {
      $spl_file_name = preg_split("/\//", $file_name[0]);
      file_put_contents('./pdf/schedule/'.$spl_file_name[1], $data);
      echo 'downloaded '.$spl_file_name[1].' into ./pdf/schedule';
      echo "\n";
    } else if (preg_match("/^S.*/", $file_name[0])) {
      file_put_contents('./pdf/next_game/'.$file_name[0], $data);
      echo 'downloaded '.$file_name[0].' into ./pdf/next_game';
      echo "\n";
    } else if (preg_match("/^T.*/", $file_name[0])) {
      file_put_contents('./pdf/mvp/'.$file_name[0], $data);
      echo 'downloaded '.$file_name[0].' into ./pdf/mvp';
      echo "\n";
    }

    // サーバ負荷を下げるためのWait
    usleep(500);
  }
?>
