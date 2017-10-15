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
						mm.name				as PlayerName
					,	mm.skill_level		as SL
					,	tt.division_id		as DivisionID
					,	tt.name				as TeamName
					,	tt.id				as TeamID
					,	tt.host_name			as HostName
					,	rr.season			as Season
					,	rr.mp				as MP
					,	rr.mw				as MW
					,	rr.points			as Points
					,	rr.win_percentage	as Win
					,	rr.ymd				as YMD
					,	(
						select
							count(*)+1
						from
							jpa.m_member mm
						,	jpa.m_team tt
						,	jpa.t_member_result rr
						,	(
							select
								m.skill_level sl
							,	m.id
							,	r.points po
							from
								jpa.m_member m
							,	jpa.t_member_result r
							where
								m.id = :p_id
							and	m.id = r.member_id
							and	r.division_id = :d_id
							) a
						where
							mm.id = rr.member_id
						and	mm.team_id = tt.id
						and	tt.division_id = :d_id
						and	case
							when	a.sl <=3	then mm.skill_level <=3
							when	a.sl >=6	then mm.skill_level >=6
							else	mm.skill_level between 4 and 5
							end
						and	rr.points > a.po
						) as Rank
					from
						jpa.m_member mm
					,	jpa.m_team tt
					,	jpa.t_member_result rr
					where
						mm.id = :p_id
						and	mm.id = rr.member_id
						and	mm.team_id = tt.id
						and	tt.division_id = :d_id
					";
	$stmt = $dbh->prepare($sql);
  $stmt->bindParam(':d_id', $term[1]);
	$stmt->bindParam(':p_id', $term[0]);
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
