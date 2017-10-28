<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="./style.css">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
    <script src="index.js"></script>
    <title>JPA Dictionary</title>
  </head>
  <body id="index">
    <header>
      <h1><a href="./index.html" style="color: white;">JPA Dictionary</a></h1>
    </header>
    <div id="wrap">
      <div class="content">
        <section>
          <h2>JPAのメンバー・チームを調べる</h2>
          <form id="searchform" method="post">
            <div>
              <label for="search_term">プレイヤーかチームの名前、IDで検索（半角英数字のみ）</label><br>
              <input type="text" class="textbox" id="search_term">
              <button type="button" id="search_button">Seach</button>
            </div>
          </form>
        </section>
        <section>
          <table id="result_table">
            <thead>
              <th width="80px">Player ID</th>
              <th width="240px">Player Name</th>
              <th width="80px">Team ID</th>
              <th width="240px">Team Name</th>
            </thead>
            <tbody>
            </tbody>
          </table>
        </section>
      </div>
    </div>
  </body>
</html>
