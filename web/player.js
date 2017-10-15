$(document).ready(function(){
  var search_val = [];
  var q = location.search.split("&");
  search_val.push(q[0].split("=")[1]);
  search_val.push(q[1].split("=")[1]);
  console.log(search_val);
  ajax_search(search_val);
  $(".btn").on("click",function(){
    window.location.href = './index.html';
  })
});

function ajax_search(search_val){
  $.ajax({
    type: "POST",
    url: "./player.php",
    data: {search_term : search_val},
    dataType: "json",
    success: function(data){
      $('.p_nm').text(data[0]['PlayerName']);
      $('.d_nm').append('<a href=./division.html?q='+search_val[1]+'>'+search_val[1]+'</a>');
      $('.t_nm').append('<a href=./team.html?q='+data[0]['TeamID']+'>'+data[0]['TeamName']+'</a>');
      $('.h_nm').append('<a href=./host.html?q='+data[0]['HostName']+'>'+data[0]['HostName']+'</a>');
      $('.sl').text(data[0]['SL']);
      $('.season').text(data[0]['Season']);
      $('.rnk').text(data[0]['Rank']);
      $('.mp').text(data[0]['MP']);
      $('.mw').text(data[0]['MW']);
      $('.points').text(data[0]['Points']);
      $('.win').text(data[0]['Win']*100+'%');
      $('.date').text(data[0]['YMD']);
      if (data[0]['SL'] > 5) {
        var css = {
          "color" : "#ffffff",
          "background" : "linear-gradient(135deg, #000000, #e7e7e7)",
        };
        $(".tier").text("Black Tier").css(css);
      } else if (data[0]['SL'] < 4) {
        var css = {
          "color" : "#ffffff",
          "background" : "linear-gradient(135deg, #04d500, #e7e7e7)",
        };
        $(".tier").text("Green Tier").css(css);
      } else {
        var css = {
          "color" : "#000000",
          "background" : "linear-gradient(135deg, #ffffff, #e7e7e7)",
        };
        $(".tier").text("White Tier").css(css);
      }
    }
  });
}
