// Empty JS for your own code to be here
$(document).ready(function() 
    { 
 // call the tablesorter plugin
  $("#klevels").tablesorter({
    theme: 'default',

    // use save sort widget
    widgets: ["saveSort", "zebra"]

  });

  $('.clrs').click(function(){
    $('#klevels')
      .trigger('saveSortReset') // clear saved sort
      .trigger("sortReset");    // reset current table sort
    return false;
  });

		$(".pagination").rPage();
    } 
);

$('#kpForm').submit(function() {
	var status = confirm("Все данные написаны правильно? Тогда нажмите ОК");
	if(status == false){
	return false;
}
	else{
	return true; 
	}
});