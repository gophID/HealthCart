<script src="js/jquery-3.4.1.slim.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>


<script type="text/javascript">
	$(document).ready(function(){

		var keyTimer;
		$('#search-proizvod, #search-prodavac').bind('keyup paste', function(){
			$('.product-results .products-for-order .row').html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');
			var el = $(this);
			var prodavac = $('#search-prodavac').val();
			var proizvod = $('#search-proizvod').val();

			if(keyTimer){
		        clearTimeout(keyTimer);
		    }
		    keyTimer = setTimeout(function () {
		        $.ajax({
	                type: "GET",
	                url: 'ajax-search-products.php',
	                data: {
	                    prodavac: prodavac,
	                    proizvod: proizvod
	                },
	                success: function(response) {
                        if($('.product-results .products-for-order').length != 0)
                            $('.product-results .products-for-order .row').html(response);
                        else
                            $('.product-results.row').html(response);
	                },
	                error: function(response) {
	                    alert('Woops! Error!')
	                }
	            });
		    }, 1000);
		});


        $('.product-results').on('click', '.add_on_order', function(){
			var el = $(this).parent();
			var p_id = $(this).parent().find('input[name="product_id"]').val();
			var amount = $(this).parent().find('input.form-control').val();
            var product_name = $(this).closest('.product').find('.product-title p').text();
            var price = $(this).closest('.product').find('.product-price span').data('price');
            var prodavac_id = $(this).closest('.product').find('.product-prodavac span').data('prodavac-id');
            var prodavac_name = $(this).closest('.product').find('.product-prodavac span').data('prodavac');

            $(this).closest('.product').find('.product-order').hide();

			$.ajax({
                type: "POST",
                url: 'ajax-add-product.php',
                data: {
                    p_id: p_id,
                    name: product_name,
                    amount: amount,
                    price: price,
                    prodavac_id: prodavac_id,
                    prodavac_name: prodavac_name,
                },
                success: function(response) {
                	response = JSON.parse(response);
                    $(el).find('input.form-control').val('');

                    if($('.product-results .products-on-order .products-order-list h3').length == 0){
                        $('.product-results .products-on-order .products-order-list').append('<h3>Proizvodi za narudžbu</h3><hr>');
                    }
                    
                    var append_row_on_order = '<div class="row product-on-order" data-p='+response.p_id+'>';
                    append_row_on_order += '<div class="col-10"><label>Naziv:</label> '+product_name+'</div>'+
                                                '<div class="col-2 text-right">'+
                                                    '<i class="fas fa-times remove-product-from-session"></i>'+
                                            '</div>'+
                                            '<div class="col-12 product-order-price">'+
                                                '<label>Cijena:</label>'+
                                                '<span data-single-price="'+price+'" class="price-for-update"> '+(price*response.amount).toFixed(2)+'</span> HRK'+
                                            '</div>'+
                                            '<div class="col-12">'+
                                                '<div class="row">'+
                                                    '<label>Količina:</label>'+
                                                '</div>'+
                                                '<div class="row">'+
                                                    '<input class="form-control" type="number" min="1" name="" value="'+response.amount+'">'+
                                                    '<button class="btn btn-primary update_on_order">Ažuriraj</button>'+
                                                '</div>'+
                                            '</div>';
                    append_row_on_order += '</div>';

                    $('.product-results .products-on-order .products-order-list').append(append_row_on_order);

                    $('.product-results .products-on-order').addClass('active');
                    $('.product-results .products-for-order').addClass('active-order-preview');
                },
                error: function(response) {
                    alert('Woops! Error!')
                }
            });
		});


		$('.product-results').on('click', '.product-edit-delete .product-remove' ,function(){
			var p_id = $(this).attr('data-pid');
			var p_name = $(this).closest('.product').find('.product-title p').text();
			$('#product-delete-modal #delete-product-name').text(p_name);
			$('#product-delete-modal #product-remove-form input[name="pname"]').val(p_name);
			$('#product-delete-modal #product-remove-form input[name="pid"]').val(p_id);

			$('#product-delete-modal').modal({
				keyboard: false
			});
		});
        

        $('.products-on-order .products-order-list').on('click', '.remove-product-from-session', function(){
            var p_id = $(this).closest('.product-on-order').data('p');
            $.ajax({
                type: "POST",
                url: 'ajax-remove-product-session.php',
                data: {
                    p_id: p_id,
                },
                success: function(response) {
                    $('.products-for-order .product[data-p="'+p_id+'"]').find('.product-order').show();
                    $('.product-on-order[data-p="'+p_id+'"]').remove();

                    if($('.product-on-order').length == 0){
                        $('.product-results .products-on-order .products-order-list h3').remove();
                        $('.product-results .products-on-order').removeClass('active');
                        $('.product-results .products-for-order').removeClass('active-order-preview');
                    }
                },
                error: function(response) {
                    alert('Woops! Error!');
                }
            });
        });


        $('.products-on-order .products-order-list').on('click', '.update_on_order', function(){
            var p_id = $(this).closest('.product-on-order').data('p');
            var amount = $(this).closest('.product-on-order').find('input').val();

            $.ajax({
                type: "POST",
                url: 'ajax-update-product-session.php',
                data: {
                    p_id: p_id,
                    amount: amount
                },
                success: function(response) {
                    var single_price = $('.product-on-order[data-p="'+p_id+'"]').find('.product-order-price span').data('single-price');
                    $('.product-on-order[data-p="'+p_id+'"]').find('.product-order-price span').text(' '+(single_price*amount).toFixed(2));
                    $('.product-on-order[data-p="'+p_id+'"]').addClass('updated');

                    setTimeout(function(){
                        $('.product-on-order[data-p="'+p_id+'"]').removeClass('updated');
                    },3000);
                },
                error: function(response) {
                    alert('Woops! Error!')
                }
            });
        });


		function readURL(input, modal) {
            if (input.files && input.files[0]) {
                $('.img-row .row p').text(input.files[0].name);
                var reader = new FileReader();
                
                reader.onload = function (e) {
                    if($('.new-img-preview img').length == 1){
                        $('.new-img-preview img').attr('src', e.target.result);
                    }else{
                        $('.new-img-preview').append('<img src="'+e.target.result+'">');
               			$('.img-row .row.new-img-name').append('<i class="fas fa-times"></i>');

                        //Remove preview image
						$('.new-img-name').on('click', '.fa-times' ,function(){
							$(this).remove();
							$('.new-img-preview img').remove();
							$('.img-row .row p').text('');
						});
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }


        $('#product-edit-form #img, #product-insert-form #img').change(function(){
            readURL(this);
        });


        $('#product-insert-form .product-insert').click(function(e){
        	e.preventDefault();
        	var submit = true;
        	$('#product-insert-form input').removeClass('custom-error-input');
        	$.each($('#product-insert-form input'), function(key, val){
        		if($.trim($(val).val()) == ''){
        			$(val).addClass('custom-error-input');
        			submit = false;
        		}
        	});
        	if($('#product-insert-form #img').val() == ''){
        		$('#product-insert-form .img-row .btn').removeClass('btn-primary');
        		$('#product-insert-form .img-row .btn').addClass('btn-danger');
        		submit = false;
        	}else{
        		$('#product-insert-form .img-row .btn').removeClass('btn-danger');
        		$('#product-insert-form .img-row .btn').addClass('btn-primary');
        	}

        	if(submit){
        		$('#product-insert-form').submit();
        	}
        });


        function calculateOrderPreviewTotal(){
            var sum = 0;
            $.each($('form tbody tr.product-tr'), function(key, val){
                var amount = $(val).find('input[type="number"]').val();
                var single_price = $(val).find('.product-single-price input').val();
                $(val).find('.product-total-price span').text((amount*single_price).toFixed(2)+' HRK');

                sum+=parseFloat(amount)*parseFloat(single_price);
            });

            $('form tbody tr .sum-number strong').text(sum.toFixed(2)+' HRK');
        }


        $('#order-preview-form').on('click', '.remove-product-from-session', function(){
            var el_form = $('#order-preview-form');
            var el = $(this);
            var p_id = $(this).parent().data('pid');
            $.ajax({
                type: "POST",
                url: 'ajax-remove-product-session.php',
                data: {
                    p_id: p_id,
                },
                success: function(response) {
                    $(el).closest('tr').remove();

                    if($('#order-preview-form tbody tr.product-tr').length == 0){
                        $(el_form).parent().append('<p class="text-center" style="margin-bottom: 0;">Ne postoje proizvodi za narudžbu. Povratak na <a href="dashboard.php">proizvode.</a></p>');
                        $(el_form).remove();
                    }

                    calculateOrderPreviewTotal();
                },
                error: function(response) {
                    alert('Woops! Error!');
                }
            });
        });


        $('#order-preview-form').on('click', '.update_on_order', function(e){
            e.preventDefault();

            var amount = $(this).parent().find('input').val();
            var p_id = $(this).closest('tr').find('.product-name input').val();

            $.ajax({
                type: "POST",
                url: 'ajax-update-product-session.php',
                data: {
                    p_id: p_id,
                    amount: amount
                },
                success: function(response) {
                    calculateOrderPreviewTotal();
                },
                error: function(response) {
                    alert('Woops! Error!')
                }
            });
        });

        $('.order-results.order-list').on('click', '.remove-order' ,function(){
            var o_id = $(this).data('oid');
            var o_prefix = $(this).data('o_prefix');
            $('#order-delete-modal #delete-order-id').text(o_prefix+o_id);
            $('#order-delete-modal #order-remove-form input[name="oid"]').val(o_id);

            $('#order-delete-modal').modal({
                keyboard: false
            });
        });

        $('#order-update-form').on('click', '.remove-product-from-order' ,function(){
            var p_id = $(this).parent().data('pid');
            var p_name = $(this).closest('.product-tr').find('.product-name span').text();

            $('#order-product-delete-modal #delete-order-product-name').text(p_name);
            $('#order-product-delete-modal #order-product-remove-form input[name="pid"]').val(p_id);

            $('#order-product-delete-modal').modal({
                keyboard: false
            });
        });

        $('#order-update-form').on('click', '.remove-order' ,function(){
            $('#order-delete-modal').modal({
                keyboard: false
            });
        });


        $('#order-update-form').on('click', '.update_on_order', function(e){
            e.preventDefault();

            var amount = $(this).parent().find('input').val();
            var p_id = $(this).closest('tr').find('.product-name input').val();

            calculateOrderPreviewTotal();
        });


        $('#order-show .product-action').on('click', 'a', function(e){
            e.preventDefault();

            var td = $(this).closest('td');
            var pid = $(this).data('pid');
            var oid = $(this).data('oid');
            var textarea_el = $(this).closest('td').find('textarea'); 
            var comment = textarea_el.val()
            var status = 0;
            if($(this).hasClass('btn-success'))
                status = 1;

            if($.trim(comment) != ''){
                console.log(pid);
                console.log(oid);
                console.log(status);
                console.log(comment);
                $.ajax({
                    type: "POST",
                    url: 'ajax-update-product-status.php',
                    data: {
                        pid: pid,
                        oid: oid,
                        status: status,
                        comment: comment
                    },
                    success: function(response) {
                        td.find('.row:first-child').html('<p>'+comment+'</p>');
                        if(status == 0)
                            td.find('.row:last-child').html('<p><i class="fas fa-times-circle failure-order"></i></p>');
                        else
                            td.find('.row:last-child').html('<p><i class="fas fa-check-circle success-order"></i></p>');
                    },
                    error: function(response) {
                        alert('Woops! Error!')
                    }
                });
            }else{
                textarea_el.addClass('error');
                setTimeout(function(){
                    textarea_el.removeClass('error');
                },3000);
            }
        });



	});
</script>