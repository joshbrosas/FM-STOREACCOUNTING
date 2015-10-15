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



$("#formexport").submit(function () {

  var datepicker1 = $('#dpd1').val();
  var datepicker2 = $('#dpd2').val();

  if(datepicker1 == '' || datepicker2 == '')
  {
      alert('Please Fill all the required fields!');
      return false;
  }
});

$('#dpd1').datepicker();

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
  }});

$('#selectall').click(function() {
    var c = this.checked;
    $(':checkbox').prop('checked',c);
});

$('#selectsales').click(function() {
    var c = this.checked;
    $(':checkbox').prop('checked',c);
});