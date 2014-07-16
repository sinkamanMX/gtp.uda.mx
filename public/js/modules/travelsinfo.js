
$( document ).ready(function() {
  $(".chosen-select").chosen({disable_search_threshold: 10});
  
  var nowTemp = new Date();
  var dateInter  = parseInt(nowTemp.getMonth())+1;  
  var todayMonth = (dateInter<10) ? "0"+dateInter : dateInter;
  var todayDay   = (nowTemp.getDate()<10) ? "0"+nowTemp.getDate(): nowTemp.getDate();

  
  if($("#inputFechaIn").val()=="0000-00-00" || $("#inputFechaIn").val()==""){
    $("#inputFechaIn").val(nowTemp.getFullYear()+"-"+todayMonth+"-"+todayDay);
  }
  
    $('#inputFechaIn').datepicker({
    format: 'yyyy-mm-dd'
  });

  if($("#inputFechaFin").val()=="0000-00-00" || $("#inputFechaFin").val()==""){
    $("#inputFechaFin").val(nowTemp.getFullYear()+"-"+todayMonth+"-"+todayDay);
  }
  
    $('#inputFechaFin').datepicker({
    format: 'yyyy-mm-dd'
  }); 


    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

    var checkin = $('#inputFechaIn').datepicker({
  format: 'yyyy-mm-dd',
      onRender: function(date) {
        return date.valueOf() < now.valueOf() ? 'disabled' : '';
      }
    }).on('changeDate', function(ev) {
      if (ev.date.valueOf() > checkout.date.valueOf()) {
        var newDate = new Date(ev.date)
        newDate.setDate(newDate.getDate() + 1);
        checkout.setValue(newDate);
      }
      checkin.hide();
      $('#inputFechaFin')[0].focus();
    }).data('datepicker');

    var checkout = $('#inputFechaFin').datepicker({
      format: 'yyyy-mm-dd',
      onRender: function(date) {
        return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
      }
    }).on('changeDate', function(ev) {
      checkout.hide();
    }).data('datepicker');
});