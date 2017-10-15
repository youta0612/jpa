<?php
/**
 * チーム情報のPDFをテキスト化したファイルからデータ抽出する
 * @author Youta Ogura
 */
 // 正規表現
 $reg = "/(  )+ ?/";
 $reg_season = "/(?:Spring|Summer|Fall|Winter) +20[0-9]+/";
 $reg_division = "/([0-9]{3} - [A-Za-z]+(?: I+))/";
 $reg_team = "/Team: (?:.*  )/";
 $reg_host = "/Host Location: (?:.*  )/";
 $reg_end = "Before Skill Level = Not Paid.";
 $reg_member = "/[0-9]{5} +[A-Za-z]+, [A-Za-z]+/";

 $team_array = array();
foreach(glob('./text/next_game/*.txt') as $path){
  if(is_file($path)){
    $txt = basename( $path );
    // ファイルを指定して読み込んで、1行ごとに配列に格納
    $file = './text/next_game/'.$txt;
    $data = file($file);
    // 格納した配列を要素ごとに分解して連想多次元配列に格納
    for($i = 0; $i < count($data); $i++){
      if(preg_match($reg_season, $data[$i])){
        preg_match($reg_season, $data[$i], $season);
      }
      // チームIDとチーム名取得
      if(preg_match($reg_team, $data[$i])){
        for($j=$i; $j<$i+11; $j++){
          if(preg_match($reg_team, $data[$i])){
            $pre_team = preg_split($reg, $data[$i]);
            // echo $j."\n";
            // var_dump($pre_team);
            // echo "v\n";
            // var_dump($v_team);
            $h_team = preg_split("/ /", $pre_team[0]);
            $v_team = preg_split("/ /", $pre_team[1]);
            $h_team_id = $h_team[1];
            $v_team_id = $v_team[1];
            for($k=2; $k<count($h_team); $k++){
              if($k==2){
                $h_team_name = $h_team[$k];
              } else {
                $h_team_name .= ' '.$h_team[$k];
              }
            }
            for($k=2; $k<count($v_team); $k++){
              if($k==2){
                $v_team_name = $v_team[$k];
              } else {
                $v_team_name .= ' '.$v_team[$k];
              }
            }//forloop to join hostname end
            $team_array[$h_team_id]['team_name'] = $h_team_name;
            $team_array[$v_team_id]['team_name'] = $v_team_name;
            // ホスト取得
            if(preg_match($reg_host, $data[$j])){
              $pre_host = preg_split($reg, $data[$j]);
              $spl_host = array();
              for($k=0; $k<count($pre_host); $k++){
                if($k==0){
                $host = preg_split("/ /", $pre_host[$k]);
                for($l=2; $l<count($host); $l++){
                  if($l==2){
                    $h_host_name = $host[$l];
                  } else {
                    $h_host_name .= ' '.$host[$l];
                  }
                }//forloop to join hostname end
                $team_array[$h_team_id]['host']=$h_host_name;
              } else if ($k==1){
                  $host = preg_split("/ /", $pre_host[$k]);
                    for($l=2; $l<count($host); $l++){
                      if($l==2){
                        $v_host_name = $host[$l];
                      } else {
                        $v_host_name .= ' '.$host[$l];
                      }
                    }//forloop to join hostname end
                    $team_array[$v_team_id]['host']=$v_host_name;
                }//if to sepalate host and visitor end
                  }//forloop to pick hostname end
              }//if to match host end
              if(preg_match($reg_member, $data[$j])){
                preg_match_all("/[0-9]{5}/", $data[$j], $pre_member, PREG_OFFSET_CAPTURE);
                foreach($pre_member as $key => $val){
                  foreach ($val as $k => $v) {
                    if($v[1] < 50){
                      $h_member = $v[0];
                      if(!in_array($h_member, $team_array[$h_team_id]['member'])) {
                        $team_array[$h_team_id]['member'][] = $h_member;
                      }
                    } else if ($v[1] > 50){
                      $v_member = $v[0];
                      if(!in_array($v_member, $team_array[$v_team_id]['member'])) {
                        $team_array[$v_team_id]['member'][] = $v_member;
                      }
                    }
                  }
                }
              }
            }//if to match team 2nd end
          }//forloop to sepalate teammember end
        } //if to match team 1st end
      }//file loop end
    }
  }
// var_dump($team_array);
// echo $season[0];
try {
	// db access
  echo "try db aceess\n";
	$dsn = 'mysql:dbname=jpa;host=localhost;charset=utf8';
	$user = 'root';
  $pw = '';
  $dbh = new PDO($dsn, $user, $pw);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  // // メンバーID、チームIDをm_memberにインサート
  echo "accessed\n";
  foreach($team_array as $key =>$val){
    foreach($val['member'] as $k => $v) {
      echo $key."\n";
      echo $v."\n";
      $sql = 'insert into m_member (id, team_id) values (?,?)
      ON DUPLICATE KEY UPDATE team_id=?;';
      echo $sql."\n";
      $stmt = $dbh->prepare($sql);
      echo "prepared statement.\n";
      $bind = array();
      $bind[] = $v;
      $bind[] = $key;
      $bind[] = $key;
      echo "bind parameter: \n";
      var_dump($bind);
      $stmt->execute($bind);
      echo "\n";
    }
  }
  // チームID、チーム名、ホスト、ディビジョンID、シーズンをm_teamにインサート
  foreach($team_array as $key => $val){
        echo $key."\n";
        echo $val['team_name']."\n";
        echo $val['host']."\n";
        echo substr($key, 0, 3)."\n";
        echo $season[0]."\n";
        $sql = 'INSERT INTO jpa.m_team(id, name, host_name, season, division_id) VALUES (?, ?, ?, ?, ?)
          ON DUPLICATE KEY UPDATE name=?, host_name=?, season=?, division_id=?';
        echo $sql."\n";
        $stmt = $dbh->prepare($sql);
        echo "prepared statement.\n";
        $bind = array();
        // for INSERT
        $bind[] = $key;
        $bind[] = $val['team_name'];
        $bind[] = $val['host'];
        $bind[] = $season[0];
        $bind[] = substr($key, 0, 3);
        // for UPDATE
        $bind[] = $val['team_name'];
        $bind[] = $val['host'];
        $bind[] = $season[0];
        $bind[] = substr($key, 0, 3);
        echo "bind parameter: \n";
        var_dump($bind);
        $stmt->execute($bind);
        echo "\n";
  }
  echo "insert successed!\n";

  $dbh = null;
} catch(Exception $e) {
// force exit
  echo "failed...\n";
	exit();
}
?>
