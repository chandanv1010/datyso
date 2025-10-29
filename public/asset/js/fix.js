(function($) {
	"use strict";
	var HT = {}; // Khai báo là 1 đối tượng
	var timer;
	var _token = $('meta[name="csrf-token"]').attr('content');

    HT.addCommas = (nStr) => {
        nStr = String(nStr);
        nStr = nStr.replace(/\./gi, "");
        let str ='';
        for (let i = nStr.length; i > 0; i -= 3){
            let a = ( (i-3) < 0 ) ? 0 : (i-3);
            str= nStr.slice(a,i) + '.' + str;
        }
        str= str.slice(0,str.length-1);
        return str;
    }

    HT.toggleDescription = () => {
        $('.text-des').each(function() {
            var maxLength = 180;
            var text = $(this).text();
            if (text.length > maxLength) {
                var truncatedText = text.substring(0, maxLength) + '...';
                $(this).text(truncatedText);
            }
        });
    }

    HT.slider = () => {
        if($('.dt-slider').length > 0){
            $('.dt-slider').slick({
                autoplay: true,
                autoplaySpeed: 2000,
                infinite: true,
                fade: true,
                cssEase: 'linear',
                arrows: false,
            });
        }
         
    }

    HT.scroll = () => {
        $(window).scroll(function() {
            if ($(document).scrollTop() > 50) {
                $('#isScroll').fadeIn(400).removeClass('visually-hidden');
            } else {
                $('#isScroll').fadeOut(400).addClass('visually-hidden');
            }
        });
    }

    HT.int = () => {
        $(document).on('change keyup blur', '.int', function(){
            let _this = $(this)
            let value = _this.val()
            if(value === ''){
                $(this).val('0')
            }
            value = value.replace(/\./gi, "")
            _this.val(HT.addCommas(value))
            if(isNaN(value)){
                _this.val('0')
            }
        })

        $(document).on('keydown', '.int', function(e){
            let _this = $(this)
            let data = _this.val()
            if(data == 0){
                let unicode = e.keyCode || e.which;
                if(unicode != 190){
                    _this.val('')
                }
            }
        })
    }

    HT.addToCart = () => {
        $(document).on('click', '.btn-addtocart', function(){
            const _this = $(this)
            const productId = _this.attr('data-id')
            $.ajax({
                url: '/ajax/cart/addToCart', 
                type: 'POST', 
                data: {
                    '_token' : _token,
                    'id' : productId,
                },
                dataType: 'json', 
                success: function(res) {
                    if(res){
                        toastr.success('Cập nhật trạng thái thành công !')
                    }
                },
            })
        })
    }

    HT.changeQuantity = () => {
        $(document).on('click', '.btn-qty', function(){
            let _this = $(this)
            let qtyElement = _this.siblings('.input-qty')
            let qty = qtyElement.val()
            let newQty = (_this.hasClass('minus')) ? parseInt(qty) - 1 : parseInt(qty) + 1
            newQty = (newQty < 1) ? 1 : newQty
            qtyElement.val(newQty)

            let option = {
                qty: newQty,
                rowId: _this.siblings('.rowId').val(),
                _token: _token
            }

            HT.handleUpdateCart(_this, option)
        })
   }

    HT.changeQuantityInput = () => {
        $(document).on('change', '.input-qty', function(){
            let _this = $(this)
            let option = {
                qty: (parseInt(_this.val()) == 0) ? 1 : parseInt(_this.val()),
                rowId: _this.siblings('.rowId').val(),
                _token: _token
            }

            if(isNaN(option.qty)){
                toastr.error('Số lượng nhập không chính xác', 'Thông báo từ hệ thống!')
                return false
            }

            HT.handleUpdateCart(_this, option)
        })
    }

    HT.handleUpdateCart = (_this, option) => {
        $.ajax({
            url: 'ajax/cart/update', 
            type: 'POST', 
            data: option, 
            dataType: 'json', 
            beforeSend: function() {
                
            },
            success: function(res) {
                toastr.clear()
                if(res.status){
                    HT.changeCartTotal(res)
                    toastr.success(res.message, 'Thông báo từ hệ thống!')
                }else{
                    toastr.error('Có vấn đề xảy ra! Hãy thử lại', 'Thông báo từ hệ thống!')
                }
            },
        });
    }

    HT.changeCartTotal = (res) => {
        $('.cart-total').html(HT.addCommas(res.response.cartTotal)+'đ')
    }

    HT.removeCartItem = () => {
        $(document).on('click', '.cart-item-remove', function(){
            let _this = $(this)
            let option = {
                rowId: _this.attr('data-row-id'),
                _token: _token
            }
            $.ajax({
                url: 'ajax/cart/delete', 
                type: 'POST', 
                data: option, 
                dataType: 'json', 
                beforeSend: function() {
                
                },
                success: function(res) {
                toastr.clear()
                if(res.status){
                    HT.changeCartTotal(res)
                    HT.removeCartItemRow(_this)
                    toastr.success(res.messages, 'Thông báo từ hệ thống!')
                }else{
                    toastr.error('Có vấn đề xảy ra! Hãy thử lại', 'Thông báo từ hệ thống!')
                }
                },
            });
        })
    }

    HT.removeCartItemRow = (_this) => {
        _this.parents('.cart-item').remove()
    }

    HT.productSwiperSlide = () => {
		document.querySelectorAll(".product-gallery").forEach(product => {
			var swiper = new Swiper(product.querySelector(".swiper-container"), {
				loop: true,
                autoHeight: true,
				// autoplay: {
				// 	delay: 2000,
				// 	disableOnInteraction: false,
				// },
				pagination: {
					el: '.swiper-pagination',
				},
				navigation: {
					nextEl: '.swiper-button-next',
					prevEl: '.swiper-button-prev',
				},
				thumbs: {
					swiper: {
						el: product.querySelector('.swiper-container-thumbs'),
						slidesPerView: 5,
						spaceBetween: 10,
						slideToClickedSlide: true,
					}
				}
			});
		});
	}

	$(document).ready(function(){
		HT.toggleDescription()
        HT.slider()
        HT.scroll()
        HT.int()
        HT.addToCart()
        HT.changeQuantity()
        HT.changeQuantityInput()
        HT.removeCartItem()
        HT.productSwiperSlide()
	});

})(jQuery);


