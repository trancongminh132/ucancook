var Utility = {};
Utility.Boxfunction = function()
{
	$('a.func').toggle(function () {
		  $(this).next().show("fast");
	  },
	  function() {
		  $(this).next().hide("fast");
	  }).blur(function(){
		$(this).next().hide("fast");			
	});
};

Utility.Checkall = function()
{
	$('#checkAll').click(function () {
		$('.table_member').find(':checkbox').attr('checked', this.checked);
	});	
};