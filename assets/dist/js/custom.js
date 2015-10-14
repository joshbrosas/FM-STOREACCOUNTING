function toggle(id) {
$( "#txt_"+id ).keyup(function() {
  var txt = document.getElementsByName('txt_'+id)[0].value;
  if(txt == "")
    {
      document.getElementById("check_" + id).checked=false;
    }else{
      document.getElementById("check_" + id).checked=true;
    }
});}

$( "input" ).keypress(function(e) {
    var a = [];
    var k = e.which;

    for (i = 48; i < 58; i++)
    a.push(i);

    // allow a max of 1 decimal point to be entered
    if (this.value.indexOf(".") === -1) {
        a.push(46);
    }

    if (!(a.indexOf(k) >= 0)) e.preventDefault();
});
$("#formexport").submit(function () {

  var datepicker1 = $('#dpd1').val();
  var datepicker2 = $('#dpd2').val();

  if(datepicker1 == '' || datepicker2 == '')
  {
      alert('Please Fill all the required fields!');
      return false;
  }
  });

var nowTemp = new Date();
var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
 
var checkin = $('#dpd1').datepicker({
  onRender: function(date) {
    return date.valueOf() <= now.valueOf();
  }
}).on('changeDate', function(ev) {
  if (ev.date.valueOf() > checkout.date.valueOf()) {
    var newDate = new Date(ev.date)
    newDate.setDate(newDate.getDate());
    checkout.setValue(newDate);
  }
  checkin.hide();
  $('#dpd2')[0].focus();
}).data('datepicker');

var checkout = $('#dpd2').datepicker({
  onRender: function(date) {
    return date.valueOf() <= checkin.date.valueOf();
  }
}).on('changeDate', function(ev) {
  checkout.hide();
}).data('datepicker');

$(document).ready(function () {
 
window.setTimeout(function() {
    $("#alertclose").fadeTo(1500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
}, 4000);
 
});

$("#formexport").submit(function () {

  var datepicker1 = $('#dpd1').val();
  var datepicker2 = $('#dpd2').val();

  if(datepicker1 == '' || datepicker2 == '')
  {
      alert('Please Fill all the required fields!');
      return false;
  }
  });

$("#formitem").submit(function () {

  var datepicker1 = $('#dpd1').val();

  if(datepicker1 == '')
  {
      alert('Please Fill all the required fields!');
      return false;
  }
  });



$('#selectall').click(function() {
    var c = this.checked;
    $(':checkbox').prop('checked',c);
});