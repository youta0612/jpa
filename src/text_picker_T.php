<?php
/**
 * 個人成績のPDFをテキスト化したファイルからデータ抽出する
 * @author Youta Ogura
 */
 // 正規表現
 $reg = "/(  )+ ?/";
 $reg_name = "/[A-Za-z]+ (?:[A-Za-z]* )?[A-Za-z]+/";
 $reg_id = "/[0-9]{5}/";
 $reg_numbers = "/[1-9]{1} +[0-9]{1,2} +[0-9]{1,2} +[0-9]{1,3} +[0-9]{1,2}/";
 $reg_division = "/Division\(s\) +[0-9]{3}/";
 $reg_season = "/(?:Spring|Summer|Fall|Winter) +20[0-9]+/";
 $reg_ymd = "/[0-9]{1,2}\/[0-9]{1,2}\/20[0-9]+/";
// var_dump(glob('./text/mvp/*.txt'));
// $array = array(
//   array(
//     array()
//   )
// );
$incliments = -1;
foreach(glob('./text/mvp/*.txt') as $path){
  if(is_file($path)){
    $txt = basename( $path );
    // ファイルを指定して読み込んで、1行ごとに配列に格納
    $file = './text/mvp/'.$txt;
    $data = file($file);
    // 格納した配列を要素ごとに分解してメンバーIDをキーにした連想多次元配列に格納
    for($i = 0; $i < count($data); $i++){
      if(preg_match($reg_id, $data[$i])) {
        $split=preg_split($reg, $data[$i]);
        $win_per = round ( (float)$split[6] / (float)$split[5], $precision=2 );
        // プレイヤー情報を連想多次元配列に格納
        $array[$incliments][$split[2]] = array(
          'name'=>$split[1],
          'team'=>$split[3],
          'sl'=>(int)$split[4],
          'mp'=>(int)$split[5],
          'mw'=>(int)$split[6],
          'points'=>(int)$split[7],
          'win percentage'=>$win_per
        );
      } else if ( preg_match($reg_season, $data[$i]) ) {
        // シーズンが存在する行だけ取り出す
        preg_match($reg_season, $data[$i], $season);
        $array[$incliments]['common_inf']['season'] = $season[0];

      } else if ( preg_match($reg_ymd, $data[$i]) ) {
        // 発表日付が存在する行だけ取り出す
        preg_match($reg_ymd, $data[$i], $pre_ymd);
        $spl_ymd = preg_split("/\//", $pre_ymd[0]);
        $ymd = $spl_ymd[2].'/'.$spl_ymd[1].'/'.$spl_ymd[0];
        $array[$incliments]['common_inf']['ymd'] = $ymd;

      }
    }
    $incliments ++;
  } //end of if
} //end of foreach

try {
	// db access
  echo "try db aceess\n";
	$dsn = 'mysql:dbname=jpa;host=localhost;charset=utf8';
	$user = 'root';
  $pw = '';
  $dbh = new PDO($dsn, $user, $pw);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  // メンバーID、名前、SL、チーム名をmember_Mにインサート
  echo "accessed\n";
  foreach($array as $div){
    foreach($div as $inf => $val) {
      if($inf!='common_inf'){
        echo $inf."\n";
        echo $val['name']."\n";
        echo $val['sl']."\n";
        $sql = 'UPDATE jpa.m_member SET name = ?, skill_level = ?
          WHERE id = ?;';
        echo $sql."\n";
        $stmt = $dbh->prepare($sql);
        echo "prepared statement.\n";
        $bind = array();
        $bind[] = $val['name'];
        $bind[] = $val['sl'];
        $bind[] = $inf;
        echo "bind parameter: \n";
        var_dump($bind);
        $stmt->execute($bind);
        echo "\n";
      }
    }
  }
  // メンバーID、MP、MW、勝率、ポイント合計、シーズン、日付をmember_result_Tにインサート
  foreach($array as $div){
    foreach($div as $inf => $val) {
      if($inf!='common_inf'){
        echo $inf."\n";
        echo $div['common_inf']['season']."\n";
        echo $val['mp']."\n";
        echo $val['mw']."\n";
        echo $val['points']."\n";
        echo $val['win percentage']."\n";
        echo $div['common_inf']['ymd']."\n";
        $sql = 'insert into jpa.t_member_result (member_id, season, mp, mw, points, win_percentage, ymd)
        	values (?, ?, ?, ?, ?, ?, ?)
          ON DUPLICATE KEY UPDATE
          season=?, mp=?, mw=?, points=?, win_percentage=?, ymd=?';
        echo $sql."\n";
        $stmt = $dbh->prepare($sql);
        echo "prepared statement.\n";
        $bind = array();
        // for INSERT
        $bind[] = $inf;
        $bind[] = $div['common_inf']['season'];
        $bind[] = $val['mp'];
        $bind[] = $val['mw'];
        $bind[] = $val['points'];
        $bind[] = $val['win percentage'];
        $bind[] = $div['common_inf']['ymd'];
        // for UPDATE
        $bind[] = $div['common_inf']['season'];
        $bind[] = $val['mp'];
        $bind[] = $val['mw'];
        $bind[] = $val['points'];
        $bind[] = $val['win percentage'];
        $bind[] = $div['common_inf']['ymd'];
        echo "bind parameter: \n";
        var_dump($bind);
        $stmt->execute($bind);
        echo "\n";
      }
    }
  }
  echo "insert successed!\n";

  $dbh = null;
} catch(Exception $e) {
// force exit
  echo "failed...\n";
	exit();
}
?>
