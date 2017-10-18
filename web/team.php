<?php
try {
	// db access
	$dsn = 'mysql:dbname=jpa;host=localhost;charset=utf8';
	$user = 'root';
	$password = '';
	$dbh = new PDO($dsn, $user, $password);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$term = $_POST['search_term'];

	$sql = "select
          	t.name				as t_nm
          ,	t.host_name			as h_nm
          ,	t.season			as season
          ,	d.division_name		as d_nm
          , d.division_id     as d_id
          ,	m.id				as m_id
          ,	m.name				as m_nm
          ,	m.skill_level		as sl
          ,	r.mp				as mp
          ,	r.mw				as mw
          ,	r.points			as points
          ,	r.win_percentage	as win

          from
          	jpa.m_team t
          ,	jpa.division_m d
          ,	jpa.m_member m
          ,	jpa.t_member_result r

          where
          	t.id = :t_id
          and	t.division_id = d.division_id
          and	t.id = m.team_id
          and m.id = r.member_id

          order by
          	m.skill_level desc
          	";
	$stmt = $dbh->prepare($sql);
  $stmt->bindParam(':t_id', $term);
	$stmt->execute();

	$dbh = null;

	//pick up data from db and indicate them
	header('Content-type: application/json');
  echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));


} catch(Exception $e) {
// force exit
	print 'sorry, couldnt access database.';
	exit();
}
 ?>
