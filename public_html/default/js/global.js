jQuery(function($) 
{ 
	// check login
	if(Account.UID == 0)
	{
		loginaccount.get_login();
	}
	$('#flash-block-close').on('click', function(){
		$('.flash-block').hide();
	});
	
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
});

/******************************
Class loginaccount
******************************/
var loginaccount = {
	boxy: {},
	token: '',
	timeout: 0,
	options: {
		redirect_url: '',
		handler: null
	},
	build_login_template : function(returnUrl) 
	{
		if(typeof returnUrl == "undefined" || returnUrl == "")
			window.location = Settings.baseurl;
		else
			window.location = returnUrl
	},
	close_popup: function(a){
		null != a ? a = Boxy.get(a.target) : (a = $(".boxy-content"), a = Boxy.get(a));
        a.hide();
        a.unload()
	},
	popup_login : function(a){
		Boxy.load(Settings.baseurl + "/user/popup-login", {
            modal: !0
        });
        this.callback = a
	},
	popup_register: function(){
        Boxy.load(Settings.baseurl + "/user/popup-register", {
            modal: !0
        });
    },
	get_login : function(returnUrl){
		$.get(Settings.baseurl + "/user/getlogininfo?v=" + (new Date).getTime(), {}, function(a) {
                Account.UID = a.uid; 
				if(Account.UID != 0) loginaccount.build_login_template(returnUrl);
        }, "json");
        return !1;
	}
};

PopupManager = {
	popup_window: null,
	interval_time: 80,
	interval: null,
	open: function(a, b, c, e) {
		this.popup_window = window.open(a, "", this.getWindowParams(b, c));
		this.interval = window.setInterval(this.waitForPopupClose, this.interval_time, e);
		return this.popup_window
	},
	waitForPopupClose: function(a) {
		PopupManager.isPopupClosed() && (PopupManager.destroyPopup(), a ? "function" == typeof a && a() : window.location.reload())
	},
	getWindowParams: function(a, b) {
		var c = this.getCenterCoords(a,
			b);
		return "width=" + a + ",height=" + b + ",status=1,location=1,resizable=yes,left=" + c[0] + ",top=" + c[1]
	},
	getCenterCoords: function(a, b) {
		var c = this.getWindowInnerSize(),
			e = this.getParentCoords(),
			f = e[0] + Math.max(0, Math.floor((c[0] - a) / 2)),
			c = e[1] + Math.max(0, Math.floor((c[1] - b) / 2));
		return [f, c]
	},
	destroyPopup: function() {
		this.popup_window = null;
		window.clearInterval(this.interval);
		this.interval = null
	},
	getParentCoords: function() {
		var a = 0,
			b = 0;
		"screenLeft" in window ? (a = window.screenLeft, b = window.screenTop) : "screenX" in window &&
			(a = window.screenX, b = window.screenY);
		return [a, b]
	},
	getWindowInnerSize: function() {
		var a = 0,
			b = 0,
			c = null;
		"innerWidth" in window ? (a = window.innerWidth, b = window.innerHeight) : ("BackCompat" === window.document.compatMode && "body" in window.document ? c = window.document.body : "documentElement" in window.document && (c = window.document.documentElement), null !== c && (a = c.offsetWidth, b = c.offsetHeight));
		return [a, b]
	},
	isPopupClosed: function() {
		return !this.popup_window || this.popup_window.closed
	},
	getWindowInnerSize: function() {
		var a = 0,
			b = 0,
			c = null;
		"innerWidth" in window ? (a = window.innerWidth, b = window.innerHeight) : ("BackCompat" === window.document.compatMode && "body" in window.document ? c = window.document.body : "documentElement" in window.document && (c = window.document.documentElement), null !== c && (a = c.offsetWidth, b = c.offsetHeight));
		return [a, b]
	}
};
/******************************
End Class Login
******************************/

/******************************
Class common
******************************/
var common = {
	progress:function(){
		$('#loading').show();
	},
	finish:function(){
		$('#loading').hide();
	},
	addDot : function (str) 
	{
		var amount = new String(str);
		amount = amount.split("").reverse();

		var output = "";
		for ( var i = 0; i <= amount.length-1; i++ )
		{
			output = amount[i] + output;
			if ((i+1) % 3 == 0 && (amount.length-1) !== i)output = '.' + output;
		}
		return output;
	},
	removeDot:function(number)
	{
		number = parseInt(number.replace(/[.]/g, ""));
		return number;
	},
	validateEmail:function(email){
		var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
		if(filter.test(email))
	    	return true;
		return false;
	},
	setFlashMessage:function(message){
		$('.flash-block').show();
		$('.flash_message').html(message);
		$("html, body").delay(2000).animate({
			scrollTop: $('.flash-block').offset().top - 80
		}, 2000);
	}
}
/******************************
End Class common
******************************/

/******************************
Class cart
******************************/
var cart = {
	cookie_list_product : 'ucancook_cart',
	shippingCost : 0,
	cityId:0,
	typeGift:1,
	typeDish:2,
	typeIngredient:3,
	isLoadedCart:0,
	sameAddress:true,
	addToCart : function(type, id, quantity, moreInfo)
	{
		this.isLoadedCart = 0;
		var isCheck = 0;
		var strCart = $.cookie(cart.cookie_list_product);
		if(typeof(strCart)!="null" && strCart != null)
		{	    	
			var arrayItem = strCart.split(';');	
			var quantityCurrent = 0;
			for(i = 0;i < arrayItem.length; i++)
			{
				var data = arrayItem[i];
				if(data != "")
				{
					data = data.split(',');
					if(data[1] > 0)
					{
						if(type == this.typeGift || type == this.typeDish)
						{
							isCheck = 0;
						}else{
							if(data[1] == id && data[0] == type){
								isCheck = 1;	
								quantityCurrent = parseInt(data[1]);			
							}
						}
					}
				}
			}			
			
			if(isCheck == 1)
			{
				var strReplace = id + ',' + type + ',' + quantityCurrent + ';';
				strCart = strCart.replace(strReplace, ""); 
				quantity = parseInt(quantity) + parseInt(quantityCurrent) + ';';
			}
			
			if(type == this.typeGift || type == this.typeDish){
				var infoMoreJson = moreInfo.join('-');
				id = id + ',' + type + ',' + quantity + ',' + infoMoreJson + ';' + strCart;				
			}else{
				id = id + ',' + type + ',' + quantity + ';' + strCart;
			}
			
		}else
		{
			if(type == this.typeGift || type == this.typeDish)
			{
				var infoMoreJson = moreInfo.join('-');
				id = id + ',' + type + ',' + quantity + ',' + infoMoreJson + ';';			
			}else{
				id = id + ',' + type + ',' + quantity + ';';
			}
		}
		
		var date = new Date();
		date.setTime(date.getTime() + (24 * 3600 * 1000));
		$.cookie(cart.cookie_list_product, id, {
			expires: date, 
			path: '/', 
			domain: Settings.domain
		});	
	},
	changeCity : function(cityId)
	{
		$.get(Settings.baseurl + '/order/getdistrict/city_id/'+ cityId, function(data){
			var district = $('#district');
			if(district.prop) {
				var options = district.prop('options');
			}
			else {
				var options = district.attr('options');
			}
			
			$('option', district).remove();
			$.each(data, function(val, text) {
				options[options.length] = new Option(text.district_name, val);
			});			
		}, 'json');
		this.cityId = cityId;
		this.calculateShipping(cityId);
	},
	updateQuantityItem:function(objSelect)
	{
		common.progress(); 
		var obj = $('.basket-order-item-remove-link', $(objSelect).parent()); 
		var id = $(obj).data('item-id');
		var type = $(obj).data('item-type');
		var quantity = $(obj).data('item-quantity');
		var quantityNew = $(objSelect).val();
		var datamore = $(obj).data('item-more-info');
		var price =  $(obj).data('item-price');
		var strQuantity = $(obj).data('item-quantity-array');
			strQuantity = strQuantity.toString();			
		var strCart = $.cookie(this.cookie_list_product);
		this.doUpdateQuantityItem(obj, strCart, id, type, quantity, quantityNew, datamore, strQuantity, price);
	},
	doUpdateQuantityItem : function(obj, strCart, id, type, quantity, quantityNew, datamore, strQuantity, price)
	{
		if(typeof(strCart)!="null" && strCart != null)
		{	
			switch(type)
			{
				case cart.typeDish:
					var strReplace = id + ',' + type + ',' + quantity + ',' + datamore+';';
					var strReplaceNew = id + ',' + type + ',' + quantityNew + ',' + datamore+';';
					strCart = strCart.replace(strReplace, strReplaceNew); 
					break;
				case cart.typeIngredient:
					var strReplace = id + ',' + type + ',' + quantity +';';
					var strReplaceNew = id + ',' + type + ',' + quantityNew +';';
					strCart = strCart.replace(strReplace, strReplaceNew); 
					break;
			}
			
			id = strCart;
			var date = new Date();
			date.setTime(date.getTime() + (7 * 24 * 3600 * 1000));
			$.cookie(this.cookie_list_product, id, {
				expires: date, 
				path: '/', 
				domain: Settings.domain
			});			
			
			var priceSub = $('.item_price_cart', $(obj).parent().parent().parent()).html();
				priceSub = common.removeDot(priceSub);
			
			var priceRow = quantityNew*price;
			var priceDiff = priceRow - priceSub;
			
			$('.item_price_cart', $(obj).parent().parent().parent()).html(common.addDot(priceRow));
			var currentTotal = $('.total_price_order').html();
				currentTotal = common.removeDot(currentTotal);			
				currentTotal = currentTotal + (priceDiff);
			$('.total_price_order').html(common.addDot(currentTotal)+' VNĐ');
			
			if(currentTotal >= 500000)
			{
				this.shippingCost = 0;
				$('.shipOrderPrice').html('Miễn phí');
			}else{
				this.shippingCost = 20000;
				$('.shipOrderPrice').html('20.000&nbsp;VNĐ');
			}
			
			$('.total_price_order_final').html(common.addDot(currentTotal+this.shippingCost));		
		}	
		common.finish();
	},
	deleteItemCart : function(obj)
	{
		common.progress(); 
		var id = $(obj).data('item-id');
		var type = $(obj).data('item-type');
		var quantity = $(obj).data('item-quantity');
		var datamore = $(obj).data('item-more-info');
		var strQuantity = $(obj).data('item-quantity-array');
			strQuantity = strQuantity.toString();
		var strCart = $.cookie(this.cookie_list_product);
		if(typeof(strCart)!="null" && strCart != null)
		{	
			switch(type)
			{
				case cart.typeDish:
					var strReplace = id + ',' + type + ',' + quantity + ',' + datamore+';';
					strCart = strCart.replace(strReplace,""); 
					break;
				case cart.typeIngredient:
					var strReplace = id + ',' + type + ',' + quantity+';';
					strCart = strCart.replace(strReplace,""); 
					break;
				default:
					var arrayDataMore = datamore.split(';');
					var arrayQuantity = strQuantity.split(';');
					$.each( arrayDataMore, function( key, value ) {
						var strReplace = id + ',' + type + ',' + arrayQuantity[key] + ',' + value+';';
						strCart = strCart.replace(strReplace,"");
					});
					break;
			}
			id = strCart;
			var date = new Date();
			date.setTime(date.getTime() + (7 * 24 * 3600 * 1000));
			$.cookie(this.cookie_list_product, id, {
				expires: date, 
				path: '/', 
				domain: Settings.domain
			});	
			var priceSub = $('.item_price_cart', $(obj).parent().parent().parent()).html();
				priceSub = common.removeDot(priceSub);
			$(obj).parent().parent().parent().remove();
			this.calculateTotalPrice(priceSub);
		}	
		common.finish();
	},
	deleteItemCartPopup : function(obj)
	{
		common.progress(); 
		var id = $(obj).data('item-id');
		var type = $(obj).data('item-type');
		var quantity = $(obj).data('item-quantity');
		var datamore = $(obj).data('item-more-info');
		var strQuantity = $(obj).data('item-quantity-array');
			strQuantity = strQuantity.toString();
		var strCart = $.cookie(this.cookie_list_product);
		if(typeof(strCart)!="null" && strCart != null)
		{	
			switch(type)
			{
				case cart.typeDish:
					var strReplace = id + ',' + type + ',' + quantity + ',' + datamore+';';
					strCart = strCart.replace(strReplace,""); 
					break;
				case cart.typeIngredient:
					var strReplace = id + ',' + type + ',' + quantity+';';
					strCart = strCart.replace(strReplace,""); 
					break;
				default:
					var arrayDataMore = datamore.split(';');
					var arrayQuantity = strQuantity.split(';');
					$.each( arrayDataMore, function( key, value ) {
						var strReplace = id + ',' + type + ',' + arrayQuantity[key] + ',' + value+';';
						strCart = strCart.replace(strReplace,"");
					});
					break;
			}
			id = strCart;
			var date = new Date();
			date.setTime(date.getTime() + (7 * 24 * 3600 * 1000));
			$.cookie(this.cookie_list_product, id, {
				expires: date, 
				path: '/', 
				domain: Settings.domain
			});	
			var priceSub = $('.cart-item-price', $(obj).parent().parent()).html();
				priceSub = common.removeDot(priceSub);
			$(obj).parent().parent().parent().remove();
			this.calculateTotalPricePopup(priceSub);
		}	
		common.finish();
	},
	bindQuickCart : function()
	{
		if(this.isLoadedCart)
			return;		
		$.post(Settings.baseurl +'/ajax/get-data-cart',{},
		function(data){
			cart.isLoadedCart = 1;
			var totalItem = data.length;
			var html = '';
			var totalPrice = 0;
			if(totalItem > 0){
				$('.cart-checkout-button').show();
				for(i=0; i< totalItem; i++)
				{	
					totalPrice += data[i].price*data[i].quantity;
					if(data[i].type == cart.typeGift)					
						html+='<li class="cart-item" data-item-id="'+data[i].id+'"><div class="cart-item-details"><span class="cart-item-price">'+common.addDot(data[i].price*data[i].quantity)+'</span><i class="cart-item-img icon-giftcard"></i><span class="cart-item-title">'+data[i].name+'</span><span class="cart-item-quantity">SL: '+data[i].quantity+'</span><span class="cart-item-remove"><a href="javascript:void(0)" class="cart-item-remove-link" data-item-type="'+data[i].type+'" data-item-id="'+data[i].id+'" data-item-name="'+data[i].name+'" data-item-quantity="'+data[i].quantity+'" onclick="cart.deleteItemCartPopup(this)" data-item-more-info="'+data[i].more_info+'" data-item-quantity-array="'+data[i].str_quantity+'">Loại bỏ</a></span></div></li>';						
					else			
						html+='<li class="cart-item" data-item-id="'+data[i].id+'"><div class="cart-item-details"><span class="cart-item-price">'+common.addDot(data[i].price*data[i].quantity)+'</span><div class="cart-item-img"><img style="width:40px;height:40px" src="'+data[i].image+'" alt="'+data[i].name+'"></div><span class="cart-item-title">'+data[i].name+'</span><span class="cart-item-quantity">SL: '+data[i].quantity+'</span><span class="cart-item-remove"><a href="javascript:void(0)" class="cart-item-remove-link" data-item-type="'+data[i].type+'" data-item-id="'+data[i].id+'" data-item-name="'+data[i].name+'" data-item-quantity="'+data[i].quantity+'" data-item-more-info="'+data[i].more_info+'" data-item-quantity-array="" onclick="cart.deleteItemCartPopup(this)">Loại bỏ</a></span></div></li>';	
				}
			}else{
				html+= '<li class="cart-item cart-item-warning"><div class="cart-item-details"><i class="dropdown-basket-warning-icon icon-warning"></i><p class="dropdown-basket-warning">Hiện không có sản phẩm nào.</p></div></li>';	
				$('.cart-checkout-button').hide();
			}
			$('.total_price_cart_popup').html(common.addDot(totalPrice));
			$('#dropdown-cart .cart-items').html(html);
		},'json');
	},
	incrementCountCart:function(quantity)
	{
		var count = $('.label-cart-count').html();
		if(typeof count != 'undefined')
		{
			count = parseInt(count);
			quantity = parseInt(quantity);
			count = count + quantity;
			$('.label-cart-count').html(count);
		}else{
			count = parseInt(quantity);
			$('.nav-cart-basket-link').html('<i class="cart-icon"></i>Giao hàng<span class="label-cart-count">'+count+'</span>');
		}
	},
	calculateTotalPricePopup:function(priceSub)
	{
		var currentTotal = $('.total_price_cart_popup').html();
			currentTotal = common.removeDot(currentTotal);			
			currentTotal = currentTotal - priceSub;
		$('.total_price_cart_popup').html(common.addDot(currentTotal));		
	},
	calculateTotalPrice:function(priceSub)
	{
		var currentTotal = $('.total_price_order').html();
			currentTotal = common.removeDot(currentTotal);			
			currentTotal = currentTotal - priceSub;
		$('.total_price_order').html(common.addDot(currentTotal)+' VNĐ');
		$('.total_price_order_final').html(common.addDot(currentTotal+this.shippingCost));		
	}
};
/******************************
End Class cart
******************************/