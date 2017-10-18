$(document).ready(function(){
  var q = location.search.split("=");
  var search_val = q[1];
  console.log(search_val);
  ajax_search(search_val);
  $(".btn").on("click",function(){
    window.location.href = './index.html';
  })
});

function ajax_search(search_val){
  $.ajax({
    type: "POST",
    url: "./team.php",
    data: {search_term : search_val},
    dataType: "json",
    success: function(data){
      console.log(data);
      var p = [];
      var n = [];
      if (data.length>0){
        var t_nm = data[0]['t_nm'];
        var h_nm = data[0]['h_nm'];
        var season = data[0]['season'];
        var d_nm = data[0]['d_nm'];
        $('.t_nm').text(t_nm+'('+h_nm+')');
        $('.season').text(season);
        $('.division').text(d_nm+' Division');
        // $('.t_nm').text()
        for(i=0; i<data.length; i++){
          console.log(data[i]);
          p.push(data[i]['points']);
          n.push(data[i]['m_nm']);
          var m_id = '<td><a href="./player.html?q0=' + data[i]['m_id'] + '&q1=' + data[i]['d_id']+ '">'+data[i]['m_id']+'</a></td>';
          var m_nm = '<td><a href="./player.html?q=' + data[i]['m_id'] + '&q1=' + data[i]['d_id']+ '">'+data[i]['m_nm']+'</a></td>';
          var sl = '<td>'+data[i]['sl']+'</td>';
          var mp = '<td>'+data[i]['mp']+'</td>';
          var mw = '<td>'+data[i]['mw']+'</td>';
          var points = '<td>'+data[i]['points']+'</td>';
          var win = '<td>'+data[i]['win']*100+'%</td>';
          $("#result_table tbody").append('<tr>' + m_id + m_nm + sl + mp + mw + points + win + '</tr>');
        }
      }
      chart(p, n);
      // if (data[0]['SL'] > 5) {
      //   var css = {
      //     "color" : "#ffffff",
      //     "background" : "linear-gradient(135deg, #000000, #e7e7e7)",
      //   };
      //   $(".tier").text("Black Tier").css(css);
      // } else if (data[0]['SL'] < 4) {
      //   var css = {
      //     "color" : "#ffffff",
      //     "background" : "linear-gradient(135deg, #04d500, #e7e7e7)",
      //   };
      //   $(".tier").text("Green Tier").css(css);
      // } else {
      //   var css = {
      //     "color" : "#000000",
      //     "background" : "linear-gradient(135deg, #ffffff, #e7e7e7)",
      //   };
      //   $(".tier").text("White Tier").css(css);
      // }
    }
  });
}

function chart(p, n){
  var w = $('.graph').width();
  var h = $('.graph').height();
  $('#myBarChart').attr('width', w);
  $('#myBarChart').attr('height', h);
var ctx = document.getElementById("myBarChart");
ctx.width = 300;
var myBarChart = new Chart(ctx, {
  //グラフの種類
  type: 'bar',
  //データの設定
  data: {
      //データ項目のラベル
      labels: n,
      //データセット
      datasets: [{
          //凡例
          label: "Points",
          //背景色
          backgroundColor: "rgb(50, 124, 148)",
          //グラフのデータ
          data: p
      }]
  },
});
}
