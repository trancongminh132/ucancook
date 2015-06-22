jQuery( function($) 
{ 
	$("#registerNewsletter").click(function()
	{
		var email = $('.newsletter-email').val();
		if(email == '')
			return;
		if (!common.validateEmail(email)) {
			$('.newsletter-form-message').show().html('Email không hợp lệ.');
			return;
		}
		common.progress();
		$.ajax({
            'url': Settings.baseurl+'/ajax/register-email-newsletter',
            'type': 'POST',
			'data': {'email': 'email'},
			'dataType':'json',
            'success': function (data) 
			{
				$('.newsletter-form-message').hide();
				common.finish();
				if(data.error == 1)
				{
					$('.newsletter-form-message').show().html('Địa chỉ email không hợp lệ.');
				}else if(data.error == 2)
				{
					$('.newsletter-form-message').show().html('Địa chỉ email đã được đăng ký.');
				}else{
					$('.newsletter-form-message').css('color','blue');
					$('.newsletter-form-message').show().html('Chúc mừng bạn đã đăng ký thành công.');
				}
            }
        });
	});
	$('.archive-dropdown').change(function()
	{
		window.location = Settings.baseurl+$(this).val();
	});
	
	$('.mobile-menu-button').click(function() {
		$('.mobile-menu-wrapper').toggle('blind');
	});
	$('.menu-search-button').click(function()
	{
		var display = $('.menu-search-form').is(':hidden');
		if(!display)
		{
			var keyword = $('.menu-search-field').val();
			window.location = Settings.baseurl+'/blog/tim-kiem?keyword='+keyword;
		}
		$('.menu-search-form').toggle('blind');
	});
	$('.top-slider ul').bxSlider({
		auto: true,
	});
	$(".menu-search-field").on('keypress', function (e) 
	{
		if(e.keyCode == 13)
		{
			var keyword = $('.menu-search-field').val();
			window.location = Settings.baseurl+'/blog/tim-kiem?keyword='+keyword;		
		}
	});
});