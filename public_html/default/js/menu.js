$(function($) 
{ 
	$('.menu-option-selector-view').click(function () {
		$('.menu-option-selector-edit').toggle('blind');
	});
	$('.button-sticky').click(function () {
		$('.menu-option-selector-edit').toggle('blind');
	});
	$('.menu-item-link').hover(
	  function () {
		$(this).parent().find('.menu-item-info').show();
	  }, 
	  function () {
		$(this).parent().find('.menu-item-info').hide();
	  }
	);
	$('.filter_type_dish').click(function(){
		var arrayType = [];
		var checkBoxes = $("input[name=filter-type-dish]");
		$.each(checkBoxes, function() {
			if ($(this).prop('checked'))
				arrayType.push($(this).val());
		});
		if(arrayType.length > 0){
			$('.menu-item-dish-list').hide();
			$.each(arrayType, function( index, value ){
				$('.menu-item-type-'+value).show();
			});
		}else{
			$('.menu-item-dish-list').show();
		}		
	});
	$('.choose_zip_code').click(function(){
		$('.menu-option-selector-edit').toggle('blind');
	});
	$('.submit_zip_code').click(function(){
		$('.menu-option-delivery-dates-container').removeClass('menu-option-step-disabled');
	});
	$('.item-this-week a').on('click', function(){
		var value_date = $(this).data('date');
		$('.item-this-week').removeClass('selected');
		$('.item-next-week').removeClass('selected');
		$(this).parent().addClass('selected');
		$('#menu-option-delivery-date-submit').attr('rel', value_date)
	});
	$('.item-next-week a').on('click', function(){
		var value_date = $(this).data('date');
		$('.item-this-week').removeClass('selected');
		$('.item-next-week').removeClass('selected');
		$(this).parent().addClass('selected');
		$('#menu-option-delivery-date-submit').attr('rel', value_date)
	});
	$('#menu-option-delivery-date-submit').click(function(){
		var date = $(this).attr('rel');
		common.progress();
		$.ajax({
            'url': Settings.baseurl+'/product/setdeliverydate',
            'type': 'POST',
			'data': {'delivery-date': date},
            'success': function (result) 
			{
				common.finish();
                location.reload();
            }
        });
	});
	$('.order_now').click(function(){
		var id = $(this).attr('rel');
		common.progress();
		var quantity = $('.quantity', $(this).parent()).val();
		var deliveryDate = $('#menu-option-delivery-date-submit').attr('rel');
		var moreInfo = [];
        	moreInfo.push(deliveryDate);   		
		cart.addToCart(cart.typeDish, id, quantity, moreInfo);
		common.setFlashMessage('Món ăn bạn chọn đã được thêm vào giỏ hàng thành công');
		cart.incrementCountCart(quantity);
		common.finish();
	});
});