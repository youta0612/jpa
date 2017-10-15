$(document).ready(function(){
  // $("#search_results").slideUp();
    $("#search_button").click(function(e){
        e.preventDefault();
        ajax_search();
    });
    var stack = [];
    $("#search_term").keyup(function(e){
        e.preventDefault();
        var search_val=$("#search_term").val();
        stack.push(1);
        setTimeout(function(){
          stack.pop();
          if(stack.length==0){
            $("#result_table tbody").empty();
            ajax_search(search_val);
            stack=[];
          }
        }, 300);
    });

});

function ajax_search(search_val){
  if(search_val == ''){
    $("#result_table tbody").empty();
  } else {
    $.ajax({
      type: "POST",
      url: "./find.php",
      data: {search_term : search_val},
      dataType: "json",
      success: function(data){
        if (data.length>0){
          for(i=0; i<data.length; i++){
            console.log(data[i]);
            var p_id = '<td><a href="./player.html?q0=' + data[i]['PlayerID'] + '&q1=' + data[i]['DivisionID']+ '">'+data[i]['PlayerID']+'</a></td>';
            var p_nm = '<td><a href="./player.html?q0=' + data[i]['PlayerID'] + '&q1=' + data[i]['DivisionID']+ '">'+data[i]['PlayerName']+'</a></td>';
            var t_id = '<td><a href="./player.html?q0=' + data[i]['TeamID'] + '&q1=' + data[i]['DivisionID']+ '">'+data[i]['TeamID']+'</a></td>';
            var t_nm = '<td><a href="./player.html?q0=' + data[i]['TeamID'] + '&q1=' + data[i]['DivisionID']+ '">'+data[i]['TeamName']+'</a></td>';
            $("#result_table tbody").append('<tr>' + p_id + p_nm + t_id + t_nm + '</tr>');
          }
        }
      }
    });
  }
}
