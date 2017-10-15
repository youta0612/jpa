<?php
try {
	// db access
	$dsn = 'mysql:dbname=jpa;host=localhost;charset=utf8';
	$user = 'root';
	$password = '';
	$dbh = new PDO($dsn, $user, $password);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$term = strip_tags(substr($_POST['search_term'],0, 100));
	$term = '%'.$term.'%';

	$sql = "select
						a.*
					from(
						select
							m.id 		as PlayerID
						,	m.name		as PlayerName
						,	t.id		as TeamID
						,	t.name		as TeamName
            , t.division_id as DivisionID
						from
							jpa.m_member m,
							jpa.m_team t
						where
							m.team_id = t.id
					) a
					where
						  a.PlayerID like :term
					or	a.PlayerName like :term
					or	a.TeamID like :term
					or	a.TeamName like :term";
	$stmt = $dbh->prepare($sql);
  $stmt->bindParam(':term', $term);
	$stmt->execute();

	$dbh = null;

  header('Content-type: application/json');
  echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch(Exception $e) {
// force exit
	print 'sorry, couldnt access database.';
	exit();
}
 ?>
