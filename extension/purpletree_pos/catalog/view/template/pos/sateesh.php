
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <title>Admin | POS - Customer Order System</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	
	<!-- ================== BEGIN core-css ================== -->
	<link href="{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/plugins/fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet" />
	<link href="{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/plugins/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
	<link href="{{ baseurl }}extension/purpletree_pos/catalog/view/stylesheet/purpletree/jquery-ui.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
	<link href="{{ baseurl }}extension/purpletree_pos/catalog/view/stylesheet/purpletree/perfect-scrollbar.css" rel="stylesheet" />
	<!-- ================== END core-css ================== -->
	
	<!-- ================== BEGIN core-js ================== -->
	<script src="https://code.iconify.design/iconify-icon/3.0.0/iconify-icon.min.js" type="176830d651252eee6a38ea9a-text/javascript"></script>
	{#<script type="text/javascript" src="{{ baseurl }}extension/purpletree_pos/admin/view/javascript/ptsbarcode/jquery.min.js"></script>#}
	<script src="https://seantheme.com/cyber/assets/plugins/jquery/dist/jquery.min.js"></script>
	<script src="{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/jquery/jquery-ui.min.js" type="176830d651252eee6a38ea9a-text/javascript"></script>
	<script src="{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/plugins/bootstrap/dist/js/bootstrap.bundle.min.js" type="176830d651252eee6a38ea9a-text/javascript"></script>
	<script src="{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/perfect-scrollbar.min.js" type="176830d651252eee6a38ea9a-text/javascript"></script>
	<script src="{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/js.cookie.min.js" type="176830d651252eee6a38ea9a-text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pako/2.1.0/pako.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/js-base64@3.7.5/base64.min.js"></script>

	<!-- ================== END core-js ================== -->
	
  <script type="176830d651252eee6a38ea9a-module" crossorigin src="{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/app.js"></script>
  <link rel="stylesheet" crossorigin href="{{ baseurl }}extension/purpletree_pos/catalog/view/stylesheet/purpletree/app.min.css">


    <script type="text/javascript" src="{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/js/jquery.scannerdetection.js"></script>
    <script type="text/javascript"> 
     var pos_search = $('#pos_search').val();
     
    	$(document).scannerDetection({
    	timeBeforeScanTest: 200, // wait for the next character for upto 200ms
    	avgTimeByChar: 20, // it's not a barcode if a character takes longer than 100ms
    	preventDefault: false,
    	endChar: [13],  // be sure the scan is complete if key 13 (enter) is detected
    	onComplete: function(barcode, qty){
        validScan = true;
    	barcodeScanner(barcode);
        } // main callback function	,
    	,
    	onError: function(string, qty) {
    	}	
    });
    </script>
    
    <style>
/* Container for all products */
#posproduct {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); /* 3–4 items per row */
  gap: 20px; /* space between cards */
  padding: 20px;
}

/* Product box */
.product-container {
  background: #111;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.4);
  transition: transform 0.2s ease;
  position: relative;
}

.product-container:hover {
  transform: scale(1.03);
}

/* Image section */
.product-container .img {
  width: 100%;
  height: 180px;
  background-size: cover;
  background-position: center;
  position: relative;
}

.product-container .img img {
  width: 57%;
  height: 100%;
  object-fit: cover;
}

/* Text section */
.product-container .text {
  padding: 12px;
  color: #fff;
  background: #1c1c1c;
}

.product-container .title {
  font-weight: bold;
  font-size: 16px;
  margin-bottom: 4px;
}

.product-container .desc {
  font-size: 13px;
  color: #ccc;
  margin-bottom: 6px;
}

.product-container .price {
  font-size: 15px;
  color: #fff;
  font-weight: bold;
}

.product-container .price-new {
  color: #00e676;
  font-weight: bold;
}

.product-container .pts-price-old {
  text-decoration: line-through;
  color: #aaa;
  margin-left: 5px;
  font-size: 13px;
}

/* Info icon */
.prod-info {
  position: absolute;
  top: 10px;
  right: 10px;
  z-index: 99;
  font-size: 20px;
  color: #00bcd4;
  cursor: pointer;
}
</style>



</head>
<body>
	<!-- BEGIN #appLoader -->
	<div id="appLoader" class="app-loader">
		<div class="d-flex align-items-center gap-3">
			<div class="app-loader-circle"></div>
			<div class="app-loader-text">LOADING...</div>
		</div>
	</div>
	<!-- END #appLoader -->
	
	<!-- BEGIN #appCover -->
	<div id="appCover" class="app-cover"></div>
	<!-- END #appCover -->
	
	<!-- BEGIN #app -->
	<div id="app" class="app app-content-full-height app-without-sidebar app-without-header">
		<!-- BEGIN #appContent -->
		<div id="appContent" class="app-content p-0">
			<!-- BEGIN pos -->
			<div class="pos pos-with-menu pos-with-sidebar" id="pos">
				<div class="pos-container">
					<!-- BEGIN pos-menu -->
					<div class="pos-menu">
						<!-- BEGIN logo -->
						<div class="logo">
							<a href="index.html">
								<div class="logo-img"><iconify-icon icon="solar:widget-line-duotone"></iconify-icon></div>
								<div class="logo-text">POS SYSTEM</div>
							</a>
						</div>
						<!-- END logo -->
						<!-- BEGIN nav-container -->
						<div class="nav-container">
							<div class="h-100" data-scrollbar="true" data-skip-mobile="true">
								<ul class="nav nav-tabs">
						
						<li class="nav-item">
							<a class="nav-link" href="#" data-toggle="modal" data-target="#posbarcode" data-filter="barcode">
                                <iconify-icon icon="mdi:barcode-scan" class="nav-icon"></iconify-icon>
								{{ text_barcode }}
							</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" href="#" id="allcategory" data-filter="categories">
								<iconify-icon icon="solar:folder-line-duotone" class="nav-icon"></iconify-icon>
								{{ text_categories }}
							</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" href="#" id="holdorders" data-filter="holdorders">
								<iconify-icon icon="solar:archive-line-duotone" class="nav-icon"></iconify-icon>
								{{ text_hold_order }}
							</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" href="#" id="pos_orders" data-filter="pos_orders">
								<iconify-icon icon="solar:box-minimalistic-line-duotone" class="nav-icon"></iconify-icon>
								{{ text_order }}
							</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" href="#" id="returnorders" data-filter="returnorders">
								<iconify-icon icon="solar:undo-left-line-duotone" class="nav-icon"></iconify-icon>
								{{ text_return }}
							</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" href="#" id="salereports" data-filter="salereports">
								<iconify-icon icon="solar:chart-line-duotone" class="nav-icon"></iconify-icon>
								{{ text_report }}
							</a>
						</li>

					</ul>
							</div>
						</div>
						<!-- END nav-container -->
					</div>
					<!-- END pos-menu -->
				
					<!-- BEGIN pos-content -->
					<div class="pos-content">
						<div class="pos-content-container p-lg-3 p-2 h-100" data-scrollbar="true">
						    
				    		<div class="product-row pos-search-div mb-3">
                    			<input id="pos_search" class="pos-search form-control" type="text" placeholder="{{ text_search }}">
                    		</div>

						    
							<div class="row g-2 g-lg-3">
								<!-- Dynamic Product Area -->
                        		<div class="row g-2 g-lg-3 text-center" id="posproduct">
                        			<!-- Dynamic products will be injected here via JS -->
                        		</div>
                        
                        		<!-- Category Products -->
                        		<div class="row g-2 g-lg-3 text-center mt-3" id="posCateProduct">
                        			<!-- Dynamic category-based products -->
                        		</div>
                        
                        		<!-- Popular Products -->
                        		<div class="row g-2 g-lg-3 text-center mt-3" id="pos-popular">
                        			<!-- Popular items will appear here -->
                        		</div>

<!-- Bottom Fixed Action Menu -->
		<div class="mt-4 pos-fixed-bottom pos-sidebar-nav position-fixed rounded-top shadow-sm bottom-0 w-auto mb-2 ">
			<ul class="nav nav-tabs nav-fill nav-wizards-2">
				<li class="nav-item">
					<a class="nav-link" href="#" data-toggle="modal" id="customerpopup" data-target="#">
						<iconify-icon icon="solar:user-rounded-line-duotone" class="nav-icon"></iconify-icon>
						{{ text_customer }}
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="#" data-toggle="modal" id="discountpopup" data-target="#">
						<iconify-icon icon="solar:tag-line-duotone" class="nav-icon"></iconify-icon>
						{{ text_discount }}
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="#" data-toggle="modal" id="discountcouponpopup" data-target="#">
						<iconify-icon icon="solar:scissors-line-duotone" class="nav-icon"></iconify-icon>
						{{ text_coupon }}
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="#" data-toggle="modal" id="taxpopup" data-target="#">
						<iconify-icon icon="solar:cart-line-duotone" class="nav-icon"></iconify-icon>
						{{ text_tax }}
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="#" data-toggle="modal" id="additionalcharge" data-target="#">
						<iconify-icon icon="solar:bill-list-line-duotone" class="nav-icon"></iconify-icon>
						{{ text_charge }}
					</a>
				</li>
			</ul>
		</div>


							</div>
						</div>
					</div>
					<!-- END pos-content -->
				
					<!-- BEGIN pos-sidebar -->
					<div class="pos-sidebar" id="pos-sidebar">
						<div class="h-100 d-flex flex-column p-0">
							<!-- BEGIN pos-sidebar-header -->
							<div class="pos-sidebar-header">
								<div class="back-btn">
									<button type="button" data-toggle-class="pos-mobile-sidebar-toggled" data-toggle-target="#pos" class="btn">
										<i class="fa fa-chevron-left"></i>
									</button>
								</div>
								<div class="title text-uppercase">Table 01</div>
								<div class="order text-uppercase">{{ text_order }}: <span class="fw-semibold text-white">#0056</span></div>
							</div>
							<!-- END pos-sidebar-header -->
							
							
							<div class="pos-sidebar-header" style="background-color:#ff9500">
                            <div class="back-btn">
                            <button type="button" data-dismiss-class="pos-mobile-sidebar-toggled" data-target="#pos-customer" class="btn">
                            <svg viewbox="0 0 16 16" class="bi bi-chevron-left" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"></path>
                            </svg>
                            </button>
                            </div>
                            <!--<div class="icon"><img src="{{ image_path }}pos-icon-table.svg"></div>-->
                            <div class="title">{{ text_order }}</div>
                            <!--<div class="order">Order: <b>#0056</b></div>-->
                            <div class="cart-header-item"><span data-toggle="modal" id="pos_custom_product" data-target="#pos_custom_prod" onClick="addCustomProduct();"><i  class="fas fa-plus"></i></span></div>
                            <a class="nav-link" href="#" data-toggle="modal" id="customerpopup" data-target="#">
                                    <i class="fa fa-user fa-fw fa-lg"style="color: white;"></i>
                                  </a>
                            <div class="pos-delet-margin-left" style="font-size: 18px;">
                            	<a data-toggle="modal" id="pos_cart_hold"><i class="fas fa-archive" title="Hold Cart"></i></a>
                            </div>
                            <div class="pos-delet-margin-left"><a id="pos-delete-cart"title="Clear cart"><i class="fas fa-trash"     style="font-size: 16px";></i></a></div>
                            
                            <div class="pos-delet-margin-left"><a href="{{ logout }}" id="pos-logout" title="Logout"><i class="fa fa-sign-out-alt"  style="font-size: 18px; text-decoration:none; color:white;"></i></a></div>
                            
                            </div>

							
							
						
							<!-- BEGIN pos-sidebar-nav -->
							<div class="pos-sidebar-nav">
								<ul class="nav nav-tabs nav-fill">
									<li class="nav-item">
										<a class="nav-link fw-semibold active" id="pos-order-count" href="#" data-toggle="tab" data-target="#newOrderTab"> {{ text_new_order }}</a>
									</li>
									<li class="nav-item">
										<a class="nav-link fw-semibold"  href="#" data-toggle="tab" data-target="#orderHistoryTab">{{ text_order_history }}</a>
									</li>
								</ul>
							</div>
							<!-- END pos-sidebar-nav -->
						
							<!-- BEGIN pos-sidebar-body -->
							<div class="pos-sidebar-body tab-content mb-2px" data-scrollbar="true" data-height="100%">
								<!-- BEGIN #newOrderTab -->
								<div class="tab-pane fade h-100 show active" id="newOrderTab">
									<!-- BEGIN pos-order -->
									<div class="pos-table" id="pos-to-cart" style="margin-left: 37px; margin-top: 18px;">
                                        </div>

									<div hidden class="pos-order">
										<div class="pos-order-product">
											<div class="img" style="background-image: url(assets/img/pos/product-2.jpg)"></div>
											<div class="flex-1">
												<div class="fw-semibold text-body text-uppercase">Grill Pork Chop</div>
												<div class="text-body text-opacity-75">$12.99</div>
												<div class="text-body text-opacity-75 mb-3">- size: large</div>
												<div class="d-flex gap-2">
													<a href="#" class="btn btn-secondary btn-sm px-0"><iconify-icon icon="material-symbols-light:check-indeterminate-small" class="d-block fs-20px my-n1"></iconify-icon></a>
													<input type="text" class="form-control form-control-sm w-30px px-0 text-center" value="1" />
													<a href="#" class="btn btn-secondary btn-sm px-0"><iconify-icon icon="material-symbols-light:add" class="d-block fs-20px my-n1"></iconify-icon></a>
												</div>
											</div>
										</div>
										<div class="pos-order-price d-flex flex-column">
											<div class="flex-1 fw-semibold text-white">$12.99</div>
											<div class="text-end">
												<a href="#" class="btn btn-secondary btn-sm px-0"><iconify-icon icon="material-symbols-light:delete-outline-sharp" class="d-block fs-20px my-n1"></iconify-icon></a>
											</div>
										</div>
									</div>
								</div>
								<!-- END #orderHistoryTab -->
							
								<!-- BEGIN #orderHistoryTab -->
								<div class="tab-pane fade h-100" id="orderHistoryTab">
									<div class="h-100 d-flex align-items-center justify-content-center text-center p-20">
										<div>
											<div class="mb-3 mt-n5">
												<iconify-icon icon="solar:bag-smile-line-duotone" class="display-2 text-body text-opacity-50"></iconify-icon>
											</div>
											<div class="text-uppercase fw-semibold text-body text-opacity-50">No order history found</div>
										</div>
									</div>
								</div>
								<!-- END #orderHistoryTab -->
							</div>
							<!-- END pos-sidebar-body -->
						
							<!-- BEGIN pos-sidebar-footer -->
							<div class="pos-sidebar-footer">
								<div class="d-flex align-items-center mb-2">
									<div class="text-uppercase">Subtotal</div>
									<div class="flex-1 text-end fs-6 fw-semibold" id="yousubtotal">$30.98</div>
								</div>
								<div class="d-flex align-items-center">
									<div class="text-uppercase">Discount</div>
									<div class="flex-1 text-end fs-6 fw-semibold" id="yousaveddiscount">$2.12</div>
								</div>
								<hr class="my-3" />
								<div class="d-flex align-items-center mb-2">
									<div class="text-uppercase">Total</div>
									<div class="flex-1 text-end fs-6 fw-semibold" id="youreward">$33.10</div>
								</div>
								<div class="mt-3">
									<div class="d-flex">
										<a href="#" class="btn btn-outline-secondary btn-sm w-70px me-2 d-flex flex-column align-items-center justify-content-center">
											<iconify-icon icon="solar:soundwave-line-duotone" class="fs-24px d-flex justify-content-center text-body my-1"></iconify-icon>
											<span>SERVICE</span>
										</a>
										<a data-toggle="modal" data-target="#pts_cart_detail" id="pts-cart-detail" onclick="cartDetails();" href="#" class="btn btn-outline-secondary btn-sm w-70px me-2 d-flex flex-column align-items-center justify-content-center">
											<iconify-icon icon="solar:bill-list-line-duotone" class="fs-24px d-flex justify-content-center text-body my-1"></iconify-icon>
											<span class="fw-semibold">BILL</span>
										</a>
										<a data-toggle="modal" onClick="orderSummery();" id="submit_order" href="#" class="btn btn-success btn-sm flex-fill d-flex flex-column align-items-center justify-content-center">
											<iconify-icon icon="solar:map-arrow-right-line-duotone" class="fs-24px d-flex justify-content-center my-1"></iconify-icon>
											<span class="fw-semibold">{{ text_submit_order }}</span>
										</a>
									</div>
								</div>
							</div>
							<!-- END pos-sidebar-footer -->
						</div>
					</div>
					<!-- END pos-sidebar -->
				</div>
			</div>
			<!-- END pos -->
			
			<!-- BEGIN pos-mobile-sidebar-toggler -->
			<a href="#" class="pos-mobile-sidebar-toggler" data-toggle-class="pos-mobile-sidebar-toggled" data-toggle-target="#pos-customer">
				<iconify-icon icon="solar:bag-smile-line-duotone"></iconify-icon>
				<span class="badge">5</span>
			</a>
			<!-- END pos-mobile-sidebar-toggler -->
			<a href="javascript:;.html" class="btn btn-icon btn-circle btn-primary btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>

		</div>
		<!-- END #appContent -->
		
	</div>
	<!-- END #app -->
	





	{#Modals#}
	<!-- Discount popup code -->
<div class="modal" id="discountproduct">
     <div class="modal-dialog">
		 <div class="modal-content">
			 <div class="modal-header">
			 <h2> {{ text_Apply_custom_discount }} </h2>
			 <button class="close" type="button" data-dismiss="modal" aria-label="Close">&times;</button>
			 </div>
			 <div class="modal-body">				 
				<div class="form-group">
					<label>{{ text_discount_type }}</label>				  
					<select id="pos_discount_type" name="pos_discount_type" class="form-control" required>
					<option value="0">{{ text_discount_option_percentage }}</option>
					<option value="1">{{ text_discount_option_fixed }}</option>
					</select>					  
				</div>
				<div class="input-group mb-3">
					<div class="input-group-prepend">
					    <span class="input-group-text">{{ text_discount_value }}</span>
					</div>
				    <input type="number" id="pos_discount_value" name="pos_discount_value" class="form-control">                    					
				</div> 
				<div id="discount_error" class="error_all"></div>
                <div class="text-right">
				     <button type="submit" class="btn btn-success" onclick="pos_discount();">{{ text_discount_submit }}</button>
				</div>				
			 </div>
		 </div>
	 </div>
 </div>
 <!-- Discount popup code -->
 <!-- Coupon popup code -->
 <div class="modal" id="discountbycoupon">
     <div class="modal-dialog">
		 <div class="modal-content" style="min-height: 250px;">
			 <div class="modal-header">
			 <h2>{{ text_coupons }}</h2>
			 <button class="close" type="button" data-dismiss="modal" aria-label="Close">&times;</button>
			 </div>
			 <div class="modal-body" style="margin-left: 25px;">
			 <form id="coupon_value">
			 <div id="coupon_list" class="form-group required">	
			 </div> 
			 </form>
			 <div id="coupan_error" class="error_all"></div>
                <div class="text-right">
					<button type="submit" class="btn btn-success" onclick="pos_coupon();" style="margin-right: 25px;">{{ text_apply_coupon}}</button>
				</div>				
			 </div>
		 </div>
	 </div>
 </div>
 <!-- Coupon popup code -->
 <!-- Custom Tax code -->
 <div class="modal" id="customtaxapply">
     <div class="modal-dialog">
		 <div class="modal-content" style="height:400px;">
			 <div class="modal-header">
			 <h2>{{ text_apply_custom_tax }}</h2>
			 <button class="close" type="button" data-dismiss="modal" aria-label="Close">&times;</button>
			 </div>
			 <div class="modal-body" style="margin-left: 25px;">
				<div class="form-group required">					
					<label>{{ text_title }}</label>					
				    <input type="text" id="pos_tax_title" name="pos_tax_title" class="form-control" style="width: 90%;">                    					
				</div>
                <div class="form-group">
					<label>{{ text_type }}</label>				  
					<select id="pos_tax_type" name="pos_tax_type" class="form-control" required style="width: 90%;">
					<option value="0">{{ text_percentage }}</option>
					<option value="1">{{ text_fixed }}</option>
					</select>					  
				</div>
                <div class="form-group required">					
					<label>{{ text_value }}</label>					
				    <input type="number" id="pos_tax_value" name="pos_tax_value" class="form-control" style="width: 90%;">                    					
				</div>				
				<div id="tax_error" class="error_all"></div>
                <div class="text-right">
				     <button type="submit" class="btn btn-success" onclick="pos_customtax();" style="margin-right: 25px;">{{ text_submit }}</button>
				</div>				
			 </div>
		 </div>
	 </div>
 </div>
 <!-- Custom Tax code -->
 <!-- Additional charge code -->
 <div class="modal" id="additionalchargeapply">
     <div class="modal-dialog">
		 <div class="modal-content" style="height:400px;">
			 <div class="modal-header">
			 <h2>{{ text_apply_custom_charge }}</h2>
			 <button class="close" type="button" data-dismiss="modal" aria-label="Close">&times;</button>
			 </div>
			 <div class="modal-body" style="margin-left: 25px;">
				<div class="form-group required">					
					<label>{{ text_title }}</label>					
				    <input type="text" id="pos_charge_title" name="pos_charge_title" class="form-control" style="width: 90%;">                    					
				</div>
                <div class="form-group">
					<label>{{ text_type }}</label>				  
					<select id="pos_charge_type" name="pos_charge_type" class="form-control" required style="width: 90%;">
					<option value="0">{{ text_percentage }}</option>
					<option value="1">{{ text_fixed }}</option>
					</select>					  
				</div>
                <div class="form-group required">					
					<label>{{ text_value }}</label>					
				    <input type="number" id="pos_charge_value" name="pos_charge_value" class="form-control" style="width: 90%;">                    					
				</div>				
				<div id="charge_error" class="error_all"></div>
                <div class="text-right">
				     <button type="submit" class="btn btn-success" onclick="pos_additionalcharge();" style="margin-right: 25px;">{{ text_submit }}</button>
				</div>				
			 </div>
		 </div>
	 </div>
 </div>
 <!-- Additional charge code -->
 <!-- Customer popup code -->
 <div class="modal" id="customersearch">
     <div class="modal-dialog">
		 <div class="modal-content">
			 <div class="modal-header">
			 <h2> {{ text_customer }} </h2>
			 <button class="close" style="font-size:35px;" type="button" data-dismiss="modal" aria-label="Close">&times;</button>
			 </div>
			 <div class="modal-body" style="margin-left: 25px;">
			   
				<div class="form-group required">
				    <input type="text" id="customer_search" class="form-control pos_text_insert" style="width: 90%;" placeholder="{{ text_customer_search }}">
				</div>
				<div id="customer_lists" style="font-size:13px;"></div>
                <div class="col-sm-12 text-center">
				  <button class="btn btn-primary pos_text_insert" data-toggle="modal" data-target="#addnewcustomer" id="addnewcustomers" style="margin:10px">{{ text_Add_new_customer }}</button>
				</div>				
			 </div>
		 </div>
	 </div>
 </div>
 <!-- Customer popup code -->
 <!-- Hold Order -->
  <div class="modal" id="hold-order">
     <div class="modal-dialog">
		 <div class="modal-content">
			 <div class="modal-header">
			 <h2> {{ text_hold_cart_note }}</h2>
			 <button class="close" style="font-size:35px;" type="button" data-dismiss="modal" aria-label="Close">&times;</button>
			 </div>
			 <div class="modal-body" style="margin-left: 25px;">
			   
				<div class="form-group required">
				<textarea id="hold_order_msg" name="hold_order_msg" rows="4" cols="62"></textarea>
				</div>
				<div id="customer_lists" style="font-size:13px;"></div>
                <div class="col-sm-12 text-center">
				  <button class="btn btn-primary pos_text_insert" id="add_hold_order" style="margin:10px">{{ text_submit }}</button>
				</div>				
			 </div>
		 </div>
	 </div>
 </div>
 <!-- Hold Order -->
 
  <!-- Add new Customer code -->
 <div class="modal" id="addnewcustomer">
     <div class="modal-dialog">
		 <div class="modal-content" style="width:75%;">
			 <div class="modal-header">
			 <h2> {{ text_Add_new_customer }} </h2>
			 <button class="close" type="button" data-dismiss="modal" aria-label="Close" style="font-size:33px;">&times;</button>
			 </div>
			 <div class="modal-body" style="margin-left: 25px;">
			    <div id="error_customer_msg" class="error_all"></div>
                <div class="form-group required">
					<label>{{ text_Add_new_customer_group }} </label>				  
					<select id="customer_group" name="customer_group" class="form-control" required style="width: 90%;">
					<!-- <option value="1">Default</option> -->
					<!-- <div id="customer_option"></div> -->
					</select>					  
				</div>
				<div class="form-group required">					
					<label>{{ text_Add_new_customer_first_name }}</label>					
				    <input type="text" id="f_name" name="f_name" class="form-control" placeholder="{{ text_Add_new_customer_first_name_placeholder }}" style="width: 90%;">                    					
				</div>
                <div class="form-group required">					
					<label>{{ text_Add_new_customer_last_name }}</label>					
				    <input type="text" id="l_name" name="l_name" class="form-control" placeholder="{{ text_Add_new_customer_last_name_placeholder }}" style="width: 90%;">                    					
				</div>
                <div class="form-group required">					
					<label>{{ text_email }}</label>					
				    <input type="email" id="email" name="email" class="form-control" style="width: 90%;">                    					
				</div>
                <div class="form-group required">					
					<label>{{ text_Add_new_customer_telephone}}</label>					
				    <input type="text" id="telephone" name="telephone" class="form-control" placeholder="{{ text_Add_new_customer_telephone_placeholder}}" style="width: 90%;">                    					
				</div>
               <div class="text-right">
			        <!--<button class="btn btn-primary" id="pts_selectbutton">{{ text_Add_new_customer_select}}</button>-->
			        <button type="submit" class="btn btn-primary" onclick="pts_addnewcustomerbutton();">{{ text_Add_new_customer_Add_select}}</button>
			   </div>			   
			 </div>
		 </div>
	 </div>
 </div>
 <!-- Add new Customer code -->
 <!-- Hold cart popup code -->
 <div class="modal" id="pos_carthold_popup">
     <div class="modal-dialog">
		 <div class="modal-content" style="width:75%;">
			 <div class="modal-header">
			 <h2> Hold cart Data </h2>
			 <button class="close" type="button" data-dismiss="modal" aria-label="Close">&times;</button>
			 </div>
			 <div class="modal-body" style="margin-left: 25px; margin-right: 25px">
				<label>Hold cart Data</label>
                <textarea name="pos_hold_data" id="pos_hold_data" onkeyup="stoppedTyping()" cols="30" rows="10" class="form-control">
                </textarea>
                <div class="text-right" style="margin-top: 10px;">
                 <button type="submit" id="hold_carts" class="btn btn-success" disabled="">Submit</button>
                </div>				
			 </div>
		 </div>
	 </div>
 </div>
 <!-- Hold Cart popup code -->
  <!-- cart detail -->
<div class="modal fade fade-down" id="pts_cart_detail">
     <div class="modal-dialog">
		 <div class="modal-content" style="width:100%; height:100%;">
			 <div class="modal-header">
			 <h2> {{ text_cart_details }}</h2>
			 <button class="close" type="button" data-dismiss="modal" aria-label="Close">&times;</button>
			 </div>
			 <div class="modal-body" style="margin-left: 25px;">
				<div class="form-group required">					
					          <div  id="pts-cart-product-totals">

							  </div> 
							  <div id="pts-cart-product-detail">
							  </div>
				</div> 
					
			 </div>
		 </div>
	 </div>
 </div>
 <!-- cart detail -->
 <!-- custom product -->
 <div class="modal fade fade-down" id="pos_custom_prod">
     <div class="modal-dialog">
		 <div class="modal-content" style="width:100%; height:100%;">
			 <div class="modal-header">
			 <h2>{{ text_add_custom_prod }}</h2>
			 <button class="close" type="button" data-dismiss="modal" aria-label="Close">&times;</button>
			 </div>
			 <div class="modal-body" style="margin-left: 25px;">
				<div class="form-group required">					
					          <div  id="pts-custom-product">

							  </div> 
				</div> 
					
			 </div>
		 </div>
	 </div>
 </div>
 <!-- custom product -->
 <!-- custom checkout order summary -->
 <div class="modal" id="pos_checkout">
  <div class="container-fluid" style="background-color:#f8f9fa;height:100%;">
  <div class="col-sm-12  row">
    <div class="col-sm-6 pts_checkout">
      <div  id="pts_summ_text">
	  <div class="text-left" id="back-button" >
	  <a href="" data-dismiss="modal" onclick="refreshPage();"><i class="fas fa-arrow-left"></i></a>
		  <div class="text-center" id="pts_summ_text">{{ text_summary }}</div>
	  </div>
	  </div>
	  <div  id="pts-customer-block" class="d-flex flex-row align-items-center">
		  <div  id="icon" class="text-left"><i class="far fa-user"></i>
		  </div>
		  <div  id="customer_name" class="flex-grow-1"></div>
	  </div>
	  
	   <div id="pts_order_detail" >
	   </div>
	   <div id="pts-cart-total-block" style="background-color:#f8f9fa;">
		   
		</div>
		<div id="pts_total_count">
		</div>
	   
    </div>
	
     <div class="col-sm-6 "style="background-color:#f8f9fa";>
		   <div>
			  <div class="text-left">
				  <div class="text-center mt-5" id="pts_order_total_amnt">{{ text_total_amount }}</div>
			  </div>
		  </div>
		  <div  id="pts_order_total_amount" class="d-flex flex-row align-items-center">
			  
		  </div>
	  <!--pts order accordian -->
	<div class="accordion mt-3 mb-3" id="ptsaccordion">
	  <div class="card">
		<div class="card-header" id="headingOne">
		  <h2 class="mb-0">
			<button class="btn btn-link  btn-lg btn-block clearfix accordion-heading" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne" style="color:#000000;text-decoration:none;box-shadow: none;">
			  <div >{{ text_shipping_payment_address }}</div>
			</button>
		  </h2>
		</div>

		<div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#ptsaccordion">

		</div>
	  </div>
	 
	</div>
<!--add adress in order sumry -->
	<div  style="margin-top: 10px;">
		<div  class="d-flex flex-wrap flex-row align-items-center buttons">
			<button data-toggle="modal" data-target="#addaddress" onclick="customer_address();";  class="flex-fill btn btn-primary btn-lg" style="margin-top: 0px; margin-right: 0px;">{{ text_add_address }}
			</button>
		</div>
	</div>
	
	<div  style="margin-top:10px;">
		<div  class="block-header text-center"><b>{{ text_payment_place_order }}</b></div>
	</div>
	
	 <div class="d-flex flex-wrap flex-row align-items-center buttons" >
		 <button  class="flex-fill btn btn-primary btn-lg" style="margin-right:20px;
    margin-bottom:10px;" data-toggle="modal" data-target="#pos_cash_payment">{{ text_cash }}</button>
		 <button  class="flex-fill btn btn-primary btn-lg" style="margin-right:20px;
    margin-bottom:10px;"  data-toggle="modal" data-target="#pos_credit">{{ text_credit_debit_card }}</button>
	 </div>

    </div>
  </div>
</div>
 </div>
 <!-- custom checkout order summary -->
 
  <!-- Add Address -->
 <div class="modal" id="addaddress" style="background:#000000b0;">
     <div class="modal-dialog">
		 <div class="modal-content">
			 <div class="modal-header">
			 <h2>{{ text_address }}</h2>
			 <button class="close" style="font-size:35px;" type="button" data-dismiss="modal" aria-label="Close">&times;</button>
			 </div>
			 <div class="modal-body" style="margin-left: 25px;">
			 <div id="customer_addresses">
				</div>

                <div class="col-sm-12 text-center">
				  <button class="btn btn-primary pos_text_insert" data-toggle="modal" data-target="#addnewaddress" id="addnewaddressorder" style="margin:10px" data-dismiss="modal"onClick="validaddnewaddress();">{{ text_add_new_address }}</button>
				</div>				
			 </div>
		 </div>
	 </div>
 </div>
  <!-- Add Address -->
    <!-- Add New Address -->
 <div class="modal" id="addnewaddress" style="background:#000000b0;">
     <div class="modal-dialog">
		 <div class="modal-content" style="width:75%;">
			 <div class="modal-header">
			 <h2>{{ text_add_new_address }}</h2>
			 <button class="close" type="button" data-dismiss="modal" aria-label="Close" style="font-size:33px;">&times;</button>
			 </div>
			 <div class="modal-body" style="margin-left: 25px;">
                <form method="post"  id="pts_submit_add" class="form-horizontal">
				  <div class="form-group required">					
					<label class="control-label">{{ text_add_new_address_first_name }}</label>					
				    <input type="text" id="firstname" name="firstname" class="form-control" placeholder="" style="width: 90%;">                    					
				</div>
                <div class="form-group required">					
					<label class="control-label">{{ text_add_new_address_last_name }}</label>					
				    <input type="text" id="lastname" name="lastname" class="form-control" placeholder="" style="width: 90%;">                    					
				</div>
                <div class="form-group required">					
					<label class="control-label">{{ text_address1 }}</label>					
				    <input type="address" id="address1" name="address_1" class="form-control" style="width: 90%;">                    					
				</div>
				<div class="form-group required">					
					<label>{{ text_address2 }}</label>					
				    <input type="address" id="address2" name="address_2" class="form-control" style="width: 90%;">                    					
				</div>
				<div class="form-group required">					
					<label>{{ text_company }}</label>					
				    <input type="address" id="company" name="company" class="form-control" style="width: 90%;">                    					
				</div>
				<div class="form-group required">					
					<label class="control-label">{{ text_city }}</label>					
				    <input type="address" id="city" name="city" class="form-control" style="width: 90%;">                    					
				</div>
				<div class="form-group required">					
					<label>{{ text_postcode }}</label>					
				    <input type="number" id="postcode" name="postcode" class="form-control" style="width: 90%;">                    					
				</div>
				<div class="form-group required">
					<label class="control-label">{{ text_country }}</label>				  
					<select id="country_id" name="country_id" class="form-control" style="width: 90%;">
					<!--<option value="">--select country--</option>
					 <option value="India">India</option>
					 <option value="China">China</option>-->
					</select>					  
				</div>
				<div class="form-group required">
					<label class="control-label">{{ text_region_state }}</label>				  
					<select id="zone_id" name="zone_id" class="form-control" style="width: 90%;">
					<!--<option value="">--select state--</option>
					 <option value="UP">UP</option>
					 <option value="MP">MP</option>-->
					</select>					  
				</div>
				<div class="form-group">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" id="setPaymentaddress1" name="setPaymentaddress1" class="custom-control-input" value="1">
						<label for="setPaymentaddress1" class="custom-control-label">{{ text_add_new_address_set_payment_address }}</label>
					</div>
				</div>
				
				<div  class="form-group">
					<div class="custom-control custom-checkbox">
						<input  type="checkbox" id="setShippingaddress1" name="setShippingaddress1" class="custom-control-input" value="1">
						<label for="setShippingaddress1" class="custom-control-label">{{ text_add_new_address_set_shipping_address }}</label>
					</div>
				</div>
               </form>
               <div class="text-right">
			   
			       <button class="btn btn-primary" onClick="submit_address();" id="submit">{{ text_submit }}</button>  
			     
			   </div>
			   <div id="addnewaddresserror"></div>
			 </div>
		 </div>
	 </div>
 </div>
<!-- Add New Address -->
<!--pos credit/debit-->
<div class="modal fade" id="pos_credit" style="background:#000000b0;">
     <div class="modal-dialog">
		 <div class="modal-content">
			 <div class="modal-header">
			 <h2>{{ text_please_confirm }}</h2>
			 <button class="close" style="font-size:35px;" type="button" data-dismiss="modal" aria-label="Close">&times;</button>
			 </div>
			 <div class="modal-body" style="margin-left: 25px;">
			      <h6>{{ text_accept_payment }}</h6>
				<div id="customer_lists" style="font-size:13px;"></div>
                <div class="col-sm-12 text-center">
				  <button class="btn btn-danger pos_text_insert"  style="margin:10px" data-dismiss="modal">{{ text_confirm_remove_no }}</button><button class="btn btn-success pos_text_insert" data-toggle="modal"  id="totalamount" data-dismiss="modal" style="margin:10px" Onclick="cardAmnt();">{{ text_confirm_remove_yes }}</button>
				</div>				
			 </div>
		 </div>
	 </div>
 </div>
 
 
 <div class="modal" id="totalamnt" style="background:#000000b0;">
     <div class="modal-dialog">
		 <div class="modal-content">
			 <div class="modal-header">
			 <h2>{{ text_total_amount }}<span id="creditamnt"></span></h2>
			 <button class="close" style="font-size:35px;" type="button" data-dismiss="modal" aria-label="Close">&times;</button>
			 </div>
			 <div class="modal-body" style="margin-left:25px; margin-right:25px;">
			        <!--<h6>Order comment(optional)</h6>-->
				   <!--textarea class="form-control" style="height:200px"> </textarea-->
                <div class="col-sm-12 text-center"> 
				  <button class="btn btn-success pos_text_insert credit_place_order" id="credit_place_order" style="margin:10px">{{ text_place_order }}</button>
				</div>				
			 </div>
		 </div>
	 </div>
 </div>
 <!--pos credit/debit-->
 <!--pos cash payment-->
<div class="modal fade fade-down" id="pos_cash_payment" style="background:#000000b0;">
     <div class="modal-dialog">
		 <div class="modal-content pts-cash-payment">
			 <div class="modal-header">
			 <h2>{{ text_please_confirm }}</h2>
			 <button class="close" style="font-size:35px;" type="button" data-dismiss="modal" aria-label="Close">&times;</button>
			 </div>
			 <div class="modal-body" style="margin-left: 25px;">
			      <h6>{{ text_accept_payment_cash }}</h6>
				<div id="customer_lists" style="font-size:13px;"></div>
                <div class="col-sm-12 text-center">
				  <button class="btn btn-danger pos_text_insert"  style="margin:10px" data-dismiss="modal">{{ text_confirm_remove_no }}</button><button class="btn btn-success pos_text_insert" data-toggle="modal"  id="totalamount" data-dismiss="modal" style="margin:10px" Onclick="cashAmnt();">{{ text_confirm_remove_yes }}</button>
				</div>				
			 </div>
		 </div>
	 </div>
 </div>
 
 
 <div class="modal fade fade-down" id="cashtotalamnt" style="background:#000000b0;">
     <div class="modal-dialog">
		 <div class="modal-content">
			 <div class="modal-header">
			 <h2>{{ text_total_amount }}<span id="cashtotal"></span> </h2>
			 <button class="close" style="font-size:35px;" type="button" data-dismiss="modal" aria-label="Close">&times;</button>
			 </div>
			 <div class="modal-body" style="margin-left:25px; margin-right:25px;">
					<div class="cash-tender"></div>
					
                <div class="col-sm-12 text-center"> 
				  <button class="btn btn-success pos_text_insert cash_place_order" style="margin:10px" id="cash_place_order">{{ text_place_order }}</button>
				</div>				
			 </div>
		 </div>
	 </div>
 </div>
 <!-- Table showing Total, Discount, and Reward -->



{#New Modals#}
<div class="modal modal-pos-item fade" id="modalPosItem">
<div class="modal-dialog modal-lg pts-product-option-model">
<div class="modal-content">
<div class="modal-body p-0">
<a href="#" data-dismiss="modal" id = "modalPosItem-close" class="close"><i class="fa fa-times" style="font-size:22px;"></i></a>
<div class="pos-product">
<div class="pos-product-info pts-product-option">
<form id="optionData"> 
<div id="pos-product-options" class="option-row">
</div>
</form>
<div class="btn-row">
<a href="#" class="btn btn-default" data-dismiss="modal">{{ text_prod_option_cancel }}</a>
<a class="btn btn-success option-product-id" option-product-id="" onclick="add_to_cart(this);">{{ text_prod_option_addtocart }}<i class="fa fa-plus fa-fw ml-2"></i></a>
</div>
</div>
</div>
</div>
</div>
</div>
</div>


<!-- product information -->

<div class="modal modal-pos-item fade" id="productInfo">
<div class="modal-dialog modal-lg pts-product-option-model">
<div class="modal-content">
<div class="modal-body p-0">
<a href="#" data-dismiss="modal" id = "productInfo-close" class="close"><i class="fa fa-times"style="font-size:22px;"></i></a>
<div class="pos-product">
<div class="pos-product-info pts-product-option">
<div id="pos-productinfo" class="row">

</div>
</div>
</div>
</div>
</div>
</div>
</div>
<!-- Out of stock -->
<div class="modal modal-pos-item fade" id="productOutofstock">
<div class="modal-dialog modal-lg pts-product-outofstock-model">
<div class="modal-content">
<div class="modal-body p-0">
<a href="#" data-dismiss="modal" id = "productOutofstock-close" class="close"><i class="fa fa-times"style="font-size:22px;"></i></a>
<div class="pos-product">
<div class="pos-productOutofstock pts-product-outofstock">
<div id="pos-productOutofstock" class="row">
	<div class="col-sm-12"><h2>Alert</h2></div>
   <div class="col-sm-12" style="font-size:14px;">
   <p>Product is out of stock</p>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/js/js-app.min.js"></script>
<script src="{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/js/theme-apple.min.js"></script>






<!-- Google Analytics Code -->
<script src="{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/rocket-loader.min.js" data-cf-settings="176830d651252eee6a38ea9a-|49" defer></script>
<script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon='{"version":"2024.11.0","token":"4db8c6ef997743fda032d4f73cfeff63","r":1,"server_timing":{"name":{"cfCacheStatus":true,"cfEdge":true,"cfExtPri":true,"cfL4":true,"cfOrigin":true,"cfSpeedBrain":true},"location_startswith":null}}' crossorigin="anonymous"></script>

<script src="{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/js/demo-pos-customer-order.demo.js"></script>

<script defer src="{{ baseurl }}extension/purpletree_pos/catalog/view/javascript/purpletree/js/beacon.min.js" data-cf-beacon='{"rayId":"65cbb86a6d0c61f5","version":"2021.5.2","r":1,"token":"4db8c6ef997743fda032d4f73cfeff63","si":10}'></script>
<script type="text/javascript">


//Global variable
var cartProductData = [];
var pos_cart = [];
var pos_cart1 = [];
	{% if agent_load_data %}
		var pointOfSaleData =  {{ POS }};
		//localStorage.setItem("posData", JSON.stringify(pointOfSaleData));
		var jsonString = JSON.stringify(pointOfSaleData);

		// Step 2: Compress using gzip (pako)
		var compressed = pako.gzip(jsonString);

		// Step 3: Encode to Base64 so we can store as string
		var base64Compressed = Base64.fromUint8Array(compressed);

		// Step 4: Save to localStorage
		localStorage.setItem("posData", base64Compressed);
		sessionStorage.removeItem('shipping_address');
		sessionStorage.removeItem('payment_address');
		sessionStorage.removeItem('currentCustomerData');
		localStorage.removeItem('filter_report')
		sessionStorage.removeItem('totals');
		sessionStorage.removeItem('sub_total');
		sessionStorage.removeItem('shipping_address_enable');
		sessionStorage.removeItem('payment_address_enable');
		sessionStorage.removeItem('grand_total');
		sessionStorage.removeItem('admin_tax');
		localStorage.removeItem('return_product');
		localStorage.removeItem('pos_orders_data');
		localStorage.removeItem('pos_cart');
		localStorage.removeItem('cartData');
	{% endif %}
	var base64Compressed = localStorage.getItem("posData");
	var compressed = Base64.toUint8Array(base64Compressed);
	var jsonString = pako.ungzip(compressed, { to: 'string' });
	var posData = JSON.parse(jsonString);
	const globalPOS = posData;
		//var posData = JSON.parse(localStorage.getItem("posData"));

// customer shipping address and Payment address

// Shipping Address

	//Tax 
	
sessionStorage.setItem("payment_address_enable",false);	
sessionStorage.setItem("shipping_address_enable",false);	
	$.each(posData.countries,function(key,val){
		$.each(posData.zone,function(key1,val1){
			
		});
	});
		
	var store_receipt_information = {
	   'store_name' : posData.store_name,
	   'address' : posData.config_address,
	   'zone_code' : posData.zone_data.name,
	   'country' : posData.country_data.name,
	};	
	
	
	
	var default_store_address = {
	   'address_id' : '',
	   'firstname' : posData.store_name,
	   'lastname' : '',
	   'company' : '',
	   'address_1' : posData.config_address,
	   'address_2' : '',
	   'postcode' : '',
	   'city' : '',
	   'zone_id' : posData.config_zone_id,
	   'zone' : posData.zone_data.code,
	   'zone_code' : posData.zone_data.name,
	   'country_id' : posData.config_country_id,
	   'country' : posData.country_data.name,
	   'iso_code_2' : posData.country_data.iso_code_2,
	   'iso_code_3' : posData.country_data.iso_code_3,
	   'address_format' : '',
	   'custom_field' : []
	
	};	
	
var default_shipping_address = {
	    'address_id' : '',
	   'firstname' : posData.store_name,
	   'lastname' : '',
	   'company' : '',
	   'address_1' : posData.config_address,
	   'address_2' : '',
	   'postcode' : '',
	   'city' : '',
	   'zone_id' : posData.config_zone_id,
	   'zone' : posData.zone_data.code,
	   'zone_code' : posData.zone_data.name,
	   'country_id' : posData.config_country_id,
	   'country' : posData.country_data.name,
	   'iso_code_2' : posData.country_data.iso_code_2,
	   'iso_code_3' : posData.country_data.iso_code_3,
	   'address_format' : '',
	   'custom_field' : []
	};	
		
class Tax {
	tax_rates= [];
	shippingAddressData;
	tax_rate_obj;
		constructor(){ 
			this.shippingAddressData =  posData.shippingAddressData;
			this.paymentAddressData =  posData.paymentAddressData;
			this.storeAddressData =  posData.storeAddressData;
			this.rateNameData =  posData.rateNameData;
			this.tax_rates = [];
			this.tax_rate_obj = {};
		}
	unsetRates() {
		this.tax_rates = [];
	}
	
	setShippingAddress(country_id, zone_id) {
	var tax_rates_array={};
	var tax_rate_obj={};
		$.each(this.shippingAddressData,function(key,val){
			if(val.country_id == country_id && ((val.zone_id == zone_id) || (val.zone_id == '0'))){
			 tax_rate_obj[val.tax_rate_id] = {
				'country_id' :country_id,
				'zone_id' :zone_id,
				'tax_class_id' :val.tax_class_id,
				'tax_rate_id' :val.tax_rate_id,
				'name' :val.name,
				'rate' :val.rate,
				'type' :val.type,
				'priority' :val.priority,
			 };
			tax_rates_array[val.tax_class_id]=tax_rate_obj;
			}
		});
		if(Object.keys(tax_rates_array).length){
			this.tax_rates.push(tax_rates_array);
			return this.tax_rates;
		}
	}
	
	setPaymentAddress(country_id, zone_id) {
		var tax_rates_array={};
		var tax_rate_obj={};
		$.each(this.paymentAddressData,function(key,val){
			if(val.country_id == country_id && ((val.zone_id == zone_id) || (val.zone_id == '0'))){
			 tax_rate_obj[val.tax_rate_id] = {
			 'country_id' :country_id,
				'zone_id' :zone_id,
				'tax_class_id' :val.tax_class_id,
				'tax_rate_id' :val.tax_rate_id,
				'name' :val.name,
				'rate' :val.rate,
				'type' :val.type,
				'priority' :val.priority,
			 };
			tax_rates_array[val.tax_class_id]=tax_rate_obj;
			}
		});
		if(Object.keys(tax_rates_array).length){
			this.tax_rates.push(tax_rates_array);
			return this.tax_rates;
		}
	}
	
	setStoreAddress(country_id, zone_id) {
		var tax_rates_array={};
		var tax_rate_obj={};
		$.each(this.storeAddressData,function(key,val){
			if(val.country_id == country_id && ((val.zone_id == zone_id) || (val.zone_id == '0'))){
			 tax_rate_obj[val.tax_rate_id] = {
			 'country_id' :country_id,
				'zone_id' :zone_id,
				'tax_class_id' :val.tax_class_id,
				'tax_rate_id' :val.tax_rate_id,
				'name' :val.name,
				'rate' :val.rate,
				'type' :val.type,
				'priority' :val.priority,
			 };
			tax_rates_array[val.tax_class_id]=tax_rate_obj;
			}
		});
		if(Object.keys(tax_rates_array).length){
			this.tax_rates.push(tax_rates_array);
		return this.tax_rates;
		}
	}
	
	calculate(value, tax_class_id, calculate = true) {
		if (tax_class_id && calculate) {
			var amount = 0;
			var tax_rates = {};
			tax_rates = this.getRates(value, tax_class_id);
			$.each(tax_rates,function(key,tax_rate){
				if (calculate != 'P' && calculate != 'F') {
					amount += parseFloat(tax_rate.amount);
				} else if (tax_rate.type == calculate) {
					amount += parseFloat(tax_rate.amount);
				}
			});
			return value + amount;
		} else {
			return value;
		}
	}
	
	
	getTax(value, tax_class_id) {
		var amount = 0;
		let tax_rates = this.getRates(parseFloat(value), parseFloat(tax_class_id));
		$.each(tax_rates,function(key,tax_rate){
			amount = parseFloat(amount) + parseFloat(tax_rate.amount);
		});
		return amount;
	}
	
	getRateName(tax_rate_id) {
	var tax_name = '';
	if(this.rateNameData != null){
		$.each(this.rateNameData,function(key,val){
			if(val.tax_rate_id == tax_rate_id){
				tax_name = val.name;
			}
		});
	}
	return tax_name;
	}
	
	getRates(value, tax_class_id) {

		var tax_rate_data = {};
		var amount = 0;
		
// set data		
		var temp={};
		var temp1={};
		$.each(this.tax_rates,function(key_11,val11){
			$.each(val11,function(key_22,val22){
				$.each(val22,function(key_33,val33){
					temp[key_33]=val33;
				});
			temp1[key_22]=temp;
			});
		});
// set data				
	//	$.each(this.tax_rates,function(key_0,val){
	
	if(temp1[tax_class_id]){
		$.each(temp1[tax_class_id],function(key_1,tax_rate){
		//	if(tax_rate.tax_class_id == tax_class_id){
				if(tax_rate_data[tax_rate.tax_rate_id]){
					amount = parseFloat(tax_rate_data[tax_rate.tax_rate_id]['amount']);
				} else {
					amount = 0;
				}
				
				if (tax_rate.type == 'F') {
					amount += tax_rate.rate;
				} else if (tax_rate.type == 'P') {
					amount += (value / 100 * tax_rate.rate);
				}
				
				tax_rate_data[tax_rate.tax_rate_id] = {
							'tax_rate_id' : parseInt(tax_rate.tax_rate_id),
							'name'        : tax_rate.name,
							'rate'        : parseFloat(tax_rate.rate),
							'type'        : tax_rate.type,
							'amount'      : parseFloat(amount)
						};
			//}
		});
		}
	//	});
		return tax_rate_data;
	}
}
var taxObj = new Tax();

setPosTax(taxObj);
function setPosTax(taxObj) {
	taxObj.tax_rates = [];
	shipping_address = null;
	payment_address = null;
	//sessionStorage.removeItem('shipping_address');
//sessionStorage.removeItem('payment_address');

	if(sessionStorage.getItem('shipping_address') != null){
		shipping_address = JSON.parse(sessionStorage.getItem('shipping_address'));
	}
	
	if(sessionStorage.getItem('payment_address') != null){
		payment_address = JSON.parse(sessionStorage.getItem('payment_address'));
	}
	if(shipping_address != null){
		pts_country_id = parseInt(shipping_address.country_id);
		pts_zone_id = parseInt(shipping_address.zone_id);
		taxObj.setShippingAddress(pts_country_id,pts_zone_id);
	} else if(posData.config_tax_default=='shipping'){
		taxObj.setShippingAddress(posData.config_country_id,posData.config_zone_id);
	}
	
	if(payment_address != null){
		pts_country_id = parseInt(payment_address.country_id);
		pts_zone_id = parseInt(payment_address.zone_id);
		taxObj.setPaymentAddress(pts_country_id,pts_zone_id);
	} else if(posData.config_tax_default=='payment'){
		taxObj.setPaymentAddress(posData.config_country_id,posData.config_zone_id);
	}
	
	taxObj.setStoreAddress(posData.config_country_id,posData.config_zone_id);
		//Tax 
}	


// Coupon Calculation
class Coupon {
	constructor(){ 
		this.coupon =  posData.coupon;
		this.coupon_category =  posData.coupon_category;
		this.coupon_product =  posData.coupon_product;
		this.coupon_history =  posData.coupon_history;
		this.category_path =  posData.category_path;
		this.product_to_category =  posData.getProducttoCategory;
		this.date =  posData.currentDate;
		this.coupon_query = [];
	}
	
	getCoupon(code) {
		status = true;
		//sessionStorage.removeItem('currentCustomerData');
		let customerData = JSON.parse(sessionStorage.getItem('currentCustomerData'));
		var customer_id = 0;
		if(customerData != null){
			customer_id = customerData.customer_id;
		}
		var coupon_qry =[];
		var coupon_query=[];
		if(this.coupon != null){
			$.each(this.coupon,function(key,val){
				if(val.code == code && ((val.date_start = '0000-00-00' || val.date_start < this.date) && (val.date_end = '0000-00-00' || val.date_end > this.date))){
				coupon_qry.push(val);
				}
			});
		}
		if (coupon_qry.length) {
		coupon_query = coupon_qry[0];
			if (parseFloat(coupon_query.total) > getSubTotal()) {
				status = false;
			}

			var coupon_total = parseInt(this.getTotalCouponHistoriesByCoupon(code));

			if (parseInt(coupon_query.uses_total) > 0 && (coupon_total >= parseInt(coupon_query.uses_total))) {
				status = false;
			}
			
			if (parseInt(coupon_query.logged) && !parseInt(customer_id)) {
				status = false;
			}
			
			if (customer_id) {
				var customer_total = this.getTotalCouponHistoriesByCustomerId(code,parseInt(customer_id));
				
			if (coupon_query.uses_customer > 0 && (customer_total >= coupon_query.uses_customer)) {
					status = false;
				}
			}

		// Products
			var coupon_product_data = [];
			if(this.coupon_product != null){
				$.each(this.coupon_product,function(cp_key,product){
					if(product.coupon_id == coupon_query.coupon_id){
						coupon_product_data.push(product.product_id);
					}
				});
			}
			
		// Categories
			var coupon_category_data = [];
			var coupon_category = this.coupon_category;
			var category_path = this.category_path;
			var product_to_category = this.product_to_category;
			if(coupon_category != null){
				$.each(coupon_category,function(cc_key,cc_val){
					if(category_path != null){
						$.each(category_path,function(cp_key,cp_val){
							if((parseInt(cc_val.category_id) == parseInt(cp_val.path_id)) && (parseInt(cc_val.coupon_id) == parseInt(coupon_query.coupon_id))){
								coupon_category_data.push(cc_val.category_id);
							}
						});
					}
				});
			}
			
		var product_data=[];	
		var product_data_temp=[];	

		if(coupon_product_data.length || coupon_category_data.length){
			$.each(CartObj.getProducts(),function(gpKey,product){

				$.each(coupon_product_data,function(key,value){
					if(parseInt(value) == parseInt(product.product_id)){
						product_data.push(product.product_id);
					}
				});

				$.each(coupon_category_data, function(ccd_key,category_id){
					if(product_to_category != null){
						$.each(product_to_category, function(ptc_key,ptc_val){
							if((ptc_val.product_id == product.product_id) && (ptc_val.category_id ==  category_id)){
								product_data.push(product.product_id);
							}
						});
					}
				});
				
				
			});
			
			if (!product_data.length) {
				 status = false;
			}
		}
		} else {
		  status =false;
		}
 		
		if (status == 'true') {
			return {
				'coupon_id'     : coupon_query.coupon_id,
				'code'          : coupon_query.code,
				'name'          : coupon_query.name,
				'type'          : coupon_query.type,
				'discount'      : coupon_query.discount,
				'shipping'      : coupon_query.shipping,
				'total'         : coupon_query.total,
				'product'       : product_data,
				'date_start'    : coupon_query.date_start,
				'date_end'      : coupon_query.date_end,
				'uses_total'    : coupon_query.uses_total,
				'uses_customer' : coupon_query.uses_customer,
				'status'        : coupon_query.status,
				'date_added'    : coupon_query.date_added
			};
		} else {
		  return null;
		}
	}
	getTotal(coupon_code) {
		var total={};
		var totalAmt=[];
		//if (sessionStorage.getItem("coupon") != null) {
			//var coupon = JSON.parse(sessionStorage.getItem("coupon"));
			var coupon_info = this.getCoupon(coupon_code);
			if (coupon_info != null) {
				var discount_total = 0;
				var sub_total=0;
				if (!coupon_info.product.length) {
					sub_total = getSubTotal();
				} else {
					sub_total = 0;
				$.each(CartObj.getProducts(),function(pro_key,product){
				 if(coupon_info.product.indexOf(product.product_id) != -1 ){
				 sub_total += product.total;
				 }
				
				
					//$.each(coupon_info.product,function(key,value){
				//	if(parseInt(product.product_id) == value){
					//	sub_total += product.total;
					//}
				//});	
				
				
				
				
				});

				}
				
				if (coupon_info.type == 'F') {
					coupon_info.discount = Math.min(coupon_info.discount, sub_total);
				}
				
				$.each(CartObj.getProducts(),function(pro_key,product){
					var discount = 0;

					if (!coupon_info.product.length) {
						var status = true;
					} else {
						var pro_status = false;
						$.each(coupon_info.product,function(key,value){
							if(parseInt(product.product_id) == value){
								pro_status = true;
							}
						});	
				
						var status = pro_status;
					}

					
					if (status) {
					var discount=0;
						if (coupon_info.type == 'F') {
							discount = parseFloat(coupon_info.discount) * (product.total / sub_total);
						} else if (coupon_info.type == 'P') {
							discount = product.total / 100 * parseFloat(coupon_info.discount);
						}

						if (parseInt(product.tax_class_id)) {
							var tax_rates = taxObj.getRates(product.total - (product.total - discount), parseInt(product.tax_class_id));

					$.each(tax_rates, function (taxKey,tax_rate){
						if (tax_rate.type == 'P') {
									totalAmt.push(tax_rate.amount);
									//total['taxes'][tax_rate.tax_rate_id] -= tax_rate.amount;
								}
		
						});
						}
					}

					discount_total += discount;
			});

				if (discount_total > total.total) {
					discount_total = total.total;
				}
				if (discount_total > 0) {
				return discount_total;
				//	total['totals'] = {
				//		'code'       : 'coupon',
				//		'title'      : 'Coupon(2222)',
				//		'value'      : -discount_total,
				//		'sort_order' : 1
				//	};

				//	total['total'] -= discount_total;
				} else {
					return 0;
				}
			}
		//}
	}
	
	getTotalCouponHistoriesByCoupon(coupon) {
		var coupon_count=[];
		if(this.coupon != null){
			$.each(this.coupon,function(key,val){
				if(this.coupon_history!=null){
					$.each(this.coupon_history,function(key1,val1){
						if(val.coupon_id == val1.coupon_id && val.code == coupon){
							coupon_count.push(val1);
						}
					});
				}
			});
		}
		return coupon_count.length;
	}
	
	getTotalCouponHistoriesByCustomerId(coupon, customer_id) {
		var coupon_count=[];
		if(this.coupon != null){
			$.each(this.coupon,function(key,val){
				if(this.coupon_history!=null){
					$.each(this.coupon_history,function(key1,val1){
						if(parseInt(val.coupon_id) == parseInt(val1.coupon_id) && val.code == coupon && parseInt(val1.customer_id) == customer_id){
							coupon_count.push(val1);
						}
					});
				}
			});
		}
		return coupon_count.length;
	}
	
	
}	

var couponObj = new Coupon;
// End Coupon Calculation
// Cart
class Cart {
	getProducts() {
			var product_data = [];
			var product_query = [];
			var product_join = [];
			var price=0;
			var option_query=[];
			var option_query_temp=[];
			var option_value_query=[];
			var option_value_query_temp=[];
			if(typeof localStorage.getItem("pos_cart") !='undefined'){
			var cart_query = JSON.parse(localStorage.getItem("pos_cart"));
		var products = posData.productForCart;
		var productDescription = posData.posProductDescription;
		var productDiscount = posData.posProductDiscount;
		var productSpecial = posData.posProductSpecial;
		var customerData = JSON.parse(sessionStorage.getItem("currentCustomerData"));
		var customer_group_id = 1;
		if(customerData != null){
			customer_group_id = parseInt(customerData.customer_group_id);
		} 
		if(cart_query != null){
		if(cart_query.length){
		$.each(cart_query, function(index,cart){
		

		var stock = true;
		// join Product and product description table
		if(typeof products!='undefined'){
			$.each(products, function(index,product){
				if(product.product_id==cart.product_id){
					product_query=Object.assign(product, productDescription[product.product_id]);	
				}
			});
		}
		// End join Product and product description table
		
		if(localStorage.getItem("custom_product") != null){
			 var customProducts = JSON.parse(localStorage.getItem("custom_product"));
			 $.each(customProducts, function(customKey,customProduct){
				 if(parseInt(customProduct.product_id) ==  parseInt(cart.product_id)){
					product_query = customProduct;
				 }
			 });
		}

		if(product_query && cart.quantity > 0){
			var option_price = 0;
			var option_points = 0;
			var option_weight = 0;
			var option_data = [];
			// product option code here
			$.each(cart.option,function(product_option_id,value){
			option_query_temp=[];
			$.each(posData.posProductOption,function(key,po){
			if(po.product_option_id == product_option_id && po.product_id == cart.product_id){
				$.each(posData.posOption,function(key1,o){
					if(parseInt(po.option_id) == parseInt(o.option_id)){
						$.each(posData.posOptionDescription,function(key2,od){
							if(parseInt(o.option_id) == parseInt(od.option_id) && parseInt(od.language_id) == parseInt(posData.language_id)){
								option_query_temp.push({
								'product_option_id':po.product_option_id,
								'option_id':po.option_id,
								'name':od.name,
								'type':o.type
								});	
							}
						});
					}
				});
			}
			});
			option_value_query_temp=[];
		if(option_query_temp.length){
		$.each(option_query_temp,function(keys,option_query){
			if (option_query.type == 'select' || option_query.type == 'radio') {
			$.each(posData.posProductOptionValue,function(key3,pov){
				if(parseInt(pov.product_option_value_id) == parseInt(value) && parseInt(pov.product_option_id) == parseInt(product_option_id)){
					$.each(posData.posOptionValue,function(key4,ov){
						if(parseInt(pov.option_value_id) == parseInt(ov.option_value_id)){
							$.each(posData.posOptionValueDescription,function(key4,ovd){
								if(parseInt(ov.option_value_id) == parseInt(ovd.option_value_id) && parseInt(ovd.language_id) == parseInt(posData.language_id)){
									option_value_query_temp.push({
									'name': ovd.name, 
									'option_value_id': parseInt(pov.option_value_id),
									'quantity': parseInt(pov.quantity),
									'subtract': parseInt(pov.subtract),
									'price': parseFloat(pov.price),
									'price_prefix': pov.price_prefix,
									'points': parseInt(pov.points),
									'points_prefix': pov.points_prefix,
									'weight': parseInt(pov.weight),
									'weight_prefix': pov.weight_prefix
									});
								}
							});
						}
					});
				}
			});	
			if(option_value_query_temp.length){
			$.each(option_value_query_temp,function(key6,option_value_query){
			if (option_value_query.price_prefix == '+') {
				option_price += option_value_query.price;
			} else if (option_value_query.price_prefix == '-') {
				option_price -= option_value_query.price;
			}

			if (option_value_query.points_prefix == '+') {
				option_points += option_value_query.points;
			} else if (option_value_query.points_prefix == '-') {
				option_points -= option_value_query.points;
			}

			if (option_value_query.weight_prefix == '+') {
				option_weight += option_value_query.weight;
			} else if (option_value_query.weight_prefix == '-') {
				option_weight -= option_value_query.weight;
			}

			if (option_value_query.subtract && (!option_value_query.quantity || (option_value_query.quantity < cart.quantity))) {
				stock = false;
			}
			
			option_data.push({
				'product_option_id'       : product_option_id,
				'product_option_value_id' : value,
				'option_id'               : option_query.option_id,
				'option_value_id'         : option_value_query.option_value_id,
				'name'                    : option_query.name,
				'value'                   : option_value_query.name,
				'type'                    : option_query.type,
				'quantity'                : option_value_query.quantity,
				'subtract'                : option_value_query.subtract,
				'price'                   : option_value_query.price,
				'price_prefix'            : option_value_query.price_prefix,
				'points'                  : option_value_query.points,
				'points_prefix'           : option_value_query.points_prefix,
				'weight'                  : option_value_query.weight,
				'weight_prefix'           : option_value_query.weight_prefix
				});
				});
			}
			} else if(option_query.type == 'checkbox' && value.length){
			
			$.each(value,function(key11,product_option_value_id){
			$.each(posData.posProductOptionValue,function(key22,pov){
				if(parseInt(pov.product_option_value_id) == parseInt(product_option_value_id) && parseInt(pov.product_option_id) == parseInt(product_option_id)){
					$.each(posData.posOptionValueDescription,function(key23,ovd){
						if((parseInt(posData.language_id) == parseInt(ovd.language_id)) &&  (parseInt(pov.option_value_id) == parseInt(ovd.option_value_id))){
							option_value_query = {
								'name': ovd.name, 
								'option_value_id': parseInt(pov.option_value_id),
								'quantity': parseInt(pov.quantity),
								'subtract': parseInt(pov.subtract),
								'price': parseFloat(pov.price),
								'price_prefix': pov.price_prefix,
								'points': parseInt(pov.points),
								'points_prefix': pov.points_prefix,
								'weight': parseInt(pov.weight),
								'weight_prefix': pov.weight_prefix
							};

			if (option_value_query.price_prefix == '+') {
				option_price += option_value_query.price;
			} else if (option_value_query.price_prefix == '-') {
				option_price -= option_value_query.price;
			}

			if (option_value_query.points_prefix == '+') {
				option_points += option_value_query.points;
			} else if (option_value_query.points_prefix == '-') {
				option_points -= option_value_query.points;
			}

			if (option_value_query.weight_prefix == '+') {
				option_weight += option_value_query.weight;
			} else if (option_value_query.weight_prefix == '-') {
				option_weight -= option_value_query.weight;
			}

			if (option_value_query.subtract && (!option_value_query.quantity || (option_value_query.quantity < cart.quantity))) {
				stock = false;
			}
			
			option_data.push({
			'product_option_id'       : product_option_id,
			'product_option_value_id' : product_option_value_id,
			'option_id'               : option_query.option_id,
			'option_value_id'         : option_value_query.option_value_id,
			'name'                    : option_query.name,
			'value'                   : option_value_query.name,
			'type'                    : option_query.type,
			'quantity'                : option_value_query.quantity,
			'subtract'                : option_value_query.subtract,
			'price'                   : option_value_query.price,
			'price_prefix'            : option_value_query.price_prefix,
			'points'                  : option_value_query.points,
			'points_prefix'           : option_value_query.points_prefix,
			'weight'                  : option_value_query.weight,
			'weight_prefix'           : option_value_query.weight_prefix
			});	
							}
						});
					}
				});
			});			
			} else if(option_query.type == 'text' || option_query.type == 'textarea' || option_query.type == 'file' || option_query.type == 'date' || option_query.type == 'datetime' || option_query.type == 'time'){
				option_data.push({
				'product_option_id'       : product_option_id,
				'product_option_value_id' : '',
				'option_id'               : option_query.option_id,
				'option_value_id'         : '',
				'name'                    : option_query.name,
				'value'                   : value,
				'type'                    : option_query.type,
				'quantity'                : '',
				'subtract'                : '',
				'price'                   : '',
				'price_prefix'            : '',
				'points'                  : '',
				'points_prefix'           : '',
				'weight'                  : '',
				'weight_prefix'           : ''
				});
			}			
		});
		}	
	});
		
			// product option code here
			//alert(JSON.stringify(product_query));
			price = parseFloat(product_query.price);
			// Product Discounts
				var discount_quantity = 0;
				$.each(cart_query, function(index,cart_2){
					if(cart_2.product_id==cart.product_id){
						discount_quantity += parseInt(cart_2.quantity);
					}
				});

				if(productDiscount != null){
					if(productDiscount.length){
						$.each(productDiscount,function(index,discountValue){
							if((discountValue.product_id==cart.product_id) && (discountValue.customer_group_id == customer_group_id) && (discountValue.quantity <= discount_quantity) && ((discountValue.date_start = '0000-00-00' || discountValue.date_start < posData.currentDate) && (discountValue.date_end = '0000-00-00' || discountValue.date_end > posData.currentDate)) ){
								price = parseFloat(discountValue.price);
							}
						});
					}
				}

			// Product special price
			if(productSpecial != null){
				if(productSpecial.length){
					$.each(productSpecial,function(index,specialValue){
						if((specialValue.product_id==cart.product_id) && (specialValue.customer_group_id == customer_group_id) && ((specialValue.date_start = '0000-00-00' || specialValue.date_start < posData.currentDate) && (specialValue.date_end = '0000-00-00' || specialValue.date_end > posData.currentDate)) ){					price = parseFloat(specialValue.price);
						}
					});
				}
			}
		// Stock
				if (!product_query.pos_quantity || (product_query.pos_quantity < cart.quantity)) {
					stock = false;
				}
				
				var real_price = price;				
			if(sessionStorage.getItem("updateCartProduct") != null){
				var changePrice = JSON.parse(sessionStorage.getItem("updateCartProduct"));
				if(typeof changePrice[cart.cart_id]!='undefined'){
					if(changePrice[cart.cart_id].price){
							price =parseFloat(changePrice[cart.cart_id].price);
						    var change_price = price;
					}
				}
			// discount price cart product and ordersummary
			
				var changePrice = JSON.parse(sessionStorage.getItem("updateCartProduct"));
				if(typeof changePrice[cart.cart_id]!='undefined'){
					if(typeof changePrice[cart.cart_id].discount!='undefined'){
						if(changePrice[cart.cart_id].discount.value){
						var pos_discount = parseFloat(changePrice[cart.cart_id].discount.value);
						if(changePrice[cart.cart_id].discount.type == 'percentage'){
						pos_discount = (price*pos_discount)/100;
						}
							price = price-pos_discount;
                            var disc_price = price;
						}
					}
				}
			    
				var perproddisctypeval = JSON.parse(sessionStorage.getItem("updateCartProduct"));
				if(typeof perproddisctypeval[cart.cart_id]!='undefined'){
					if(typeof perproddisctypeval[cart.cart_id].discount!='undefined'){
						if(perproddisctypeval[cart.cart_id].discount.type){
						
						 var disctype = perproddisctypeval[cart.cart_id].discount.type;
						 var discval = perproddisctypeval[cart.cart_id].discount.value;
						}
					}
				}

				var changeWeight = JSON.parse(sessionStorage.getItem("updateCartProduct"));
				if(typeof changeWeight[cart.cart_id]!='undefined'){
					if(typeof changeWeight[cart.cart_id].quantity_type!='undefined'){
					if(changeWeight[cart.cart_id].quantity_type== 'weight'){
					if(parseFloat(changeWeight[cart.cart_id].quantity_value)){
							price = (price /parseFloat(product_query.weight))*(changeWeight[cart.cart_id].quantity_value);
						}
						}
					}
				}
			}
			
	// Reward Points
		var reward = 0
	// Downloads
		var download_data = [];	
	//recurring
		var recurring = false
	var productData = posData.products[product_query.product_id];
	product_data.push({
						'cart_id'         : cart.cart_id,
						'product_id'      : product_query.product_id,
						'name'            : product_query.name,
						'model'           : product_query.model,
						'shipping'        : product_query.shipping,
						'image'           : product_query.image,
						'option'          : option_data,
						'download'        : download_data,
						'quantity'        : cart.quantity,
						'minimum'         : product_query.minimum,
						'subtract'        : product_query.subtract,
						'stock'           : stock,
						'disc_type'       : disctype,
						'disc_value'      : discval,
						'change_price'    : (change_price + option_price),
						'disc_price'      : (productData.provided_discount),
						'real_price'      : (productData.mrp + option_price),
						'price'           : (productData.price + option_price),
						'total'           : (price + option_price) * cart.quantity,
						'reward'          : (productData.reward * cart.quantity),
						'points'          : (product_query.points ? (product_query.points + option_points) * cart.quantity : 0),
						'tax_class_id'    : product_query.tax_class_id,
						'weight'          : (parseFloat(product_query.weight) + parseFloat(option_weight)) * parseInt(cart.quantity),
						'weight_class_id' : product_query.weight_class_id,
						'length'          : product_query.length,
						'width'           : product_query.width,
						'height'          : product_query.height,
						'length_class_id' : product_query.length_class_id,
						'recurring'       : recurring
						});
					}
				});
			}
		}
	}
		return product_data;
	}
	hasShipping() {
	const shippingObj = new Promise((resolve, reject) => {
			$.each(this.getProducts(),function(key,product){
				if(parseInt(product.shipping)){
				resolve('true');
				}
			});
			resolve('false');
		});
		return shippingObj;
	}
}

var CartObj = new Cart();
// Cart

		$( document ).ready(function() {	
			posFuntions.posProduct(posData.products,'all');
		});
		
		$(document ).delegate( "#posallpro", "click", function() {
			posFuntions.posProduct(posData.products,'all'); 
		});
		$(document ).delegate( "#pospopular", "click", function() {	
		 posFuntions.posProduct(posData.popular_products,'popular'); 
		});	

		posData.order_total=0;
		getCartProductHTML();

	$('#pos_search').keyup(function () {	
			var modalPosItem='';
			$('.product-area').remove();
			if($(".nav-link").hasClass("active")){
			   var data_type = $(".active").parent().closest('li').attr("id");
			   
			       if(data_type == "pospopular"){
				    var pro_data = posData.popular_products;
				   }else{
				    var pro_data = posData.products;
				   }
				}
			$.each(pro_data, function( index, popular ) {

			modalPosItem='';
		if(popular.options.length){
			modalPosItem='modalPosItem';
		}
 			
			var search_strr = $('#pos_search').val();
			var search_str = new RegExp(search_strr, "i");
			var match = false;
			var pro_name = popular.heading_title;
			var pro_model = popular.model;
			var pro_mpn = popular.mpn;
			var pro_isbn = popular.isbn;
			var pro_jan = popular.jan;
			var pro_sku = popular.sku;
			var pro_ean = popular.ean;
			var pro_upc = popular.upc;
			var pro_quantity = popular.pos_quantity;
				  if(match == false){
				  match = search_str.test(pro_name);
				  }
				  if(match == false){
				   match = search_str.test(pro_model);
				  }
				  if(match == false){
				   match = search_str.test(pro_mpn);
				  }
				  if(match == false){
				   match = search_str.test(pro_isbn);
				  }
				   if(match == false){
				   match = search_str.test(pro_jan);
				   }
				   if(match == false){
				   match = search_str.test(pro_sku);
				   }
				   if(match == false){
				   match = search_str.test(pro_ean);
				   }
				   if(match == false){
				   match = search_str.test(pro_upc);
				   }				
			if(match == true){
			infoIconColor='red';
			if(parseInt(pro_quantity)){
			infoIconColor='#44b3de';
			}
			
			html  = '<div  class="product-container product-area" style="position:relative;"  data-type="'+ data_type +'">';
			
			html += '<i class="fas fa-info-circle prod-info" productinfo="'+ popular.product_id +'" onclick="productInfo(event);" style="right: 5px; top: 5px; color: '+infoIconColor+'; font-size:20px; position: absolute;z-index: 99;top: 10px;right: 10px;cursor: pointer;"></i>';
			html  += '<a dataproduct="'+ popular.product_id +'" href="'+ popular.href +'" class="product" data-toggle="modal" onclick="productdata(this)" >';
			html += '<div class="img" style="background-image: url()"><img src="https://seantheme.com/cyber/assets/img/pos/product-17.jpg"></div>';
			html += '<div class="text"><div class="title">'+ popular.heading_title +'</div><div class="desc">'+ popular.model +'</div>';
			if (popular.price) {
            html += '<p class="price">'; 
			if(!popular.special){
                 html += format(taxObj.calculate(popular.price,popular.tax_class_id,posData.config_tax),posData.currency_code);
             } else {
				 html += '<span class="price-new">'+  format(taxObj.calculate(popular.special,popular.tax_class_id,posData.config_tax),posData.currency_code) +'</span> <span class="pts-price-old">'+ format(taxObj.calculate(popular.price,popular.tax_class_id,posData.config_tax),posData.currency_code) +'</span>'; 
			 }
			
				  html +='</p>';
        }

		html +='</div></a></div>';
			$('#posproduct').append(html);	
		  }
		});	
	});		
var posFuntions={};
posFuntions.posProduct= function(products,dataType){
		$('.product-category-area').remove();
		$('.product-area').remove();
		var modalPosItem='';
		if(typeof products!='undefined'){
		$.each(products, function( index, product ) { 
			 modalPosItem='';
		 if(product.options.length){
			 modalPosItem='modalPosItem';
		 }
		var pro_quantity = product.pos_quantity;
			infoIconColor='red';
			if(parseInt(pro_quantity)){
			infoIconColor='#44b3de';
			}
			
		html  = '<div  class="product-container product-area" style="position:relative;" data-type="'+dataType+'">';
		
		html += '<i class="fas fa-info-circle prod-info" productinfo="'+ product.product_id +'" onclick="productInfo(event);" style="right: 5px; top: 5px; color: '+infoIconColor+'; font-size:20px; position: absolute;z-index: 99;top: 10px;right: 10px;cursor: pointer;"></i>';
		
		html  += '<a dataproduct="'+ product.product_id +'" href="'+ product.href +'" class="product" data-toggle="modal" onclick="productdata(this)" >';
		
		html += '<div class="img" style="padding: 5px;"><img src="https://seantheme.com/cyber/assets/img/pos/product-17.jpg"></div>';
		html += '<div class="text"><div class="title">'+ product.heading_title +'</div><div class="desc">'+ product.model +'</div>';

		if (product.price) {
            html += '<p class="price">'; 
			if(!product.special){ 
                 html += format(taxObj.calculate(product.mrp,product.tax_class_id,posData.config_tax),posData.currency_code);
             } else {
				 html += '<span class="price-new">'+ format(taxObj.calculate(product.special,product.tax_class_id,posData.config_tax),posData.currency_code) +'</span> <span class="pts-price-old">'+ format(taxObj.calculate(product.price,product.tax_class_id,posData.config_tax),posData.currency_code) +'</span>'; 
			 }
			
				  html +='</p>';
        }
		html +='</div></a></div>';
		$('#posproduct').append(html);			
		
		});
	}
}

function barcodeScanner(barcode){
			var modalPosItem='';
			$('.product-area').remove();
			   var data_type = $(".active").parent().closest('li').attr("id");
				    var pro_data = posData.products;
			$.each(pro_data, function( index, popular ) {

			modalPosItem='';
		if(popular.options.length){
			modalPosItem='modalPosItem';
		}
 			
			var search_strr = barcode;
			var search_str = new RegExp(search_strr, "i");
			var match = false;
			var pro_mpn = popular.mpn;
			var pro_isbn = popular.isbn;
			var pro_jan = popular.jan;
			var pro_sku = popular.sku;
			var pro_ean = popular.ean;
			var pro_upc = popular.upc;
			var pro_quantity = popular.pos_quantity;
				  if(match == false){
				   match = search_str.test(pro_mpn);
				  }
				  if(match == false){
				   match = search_str.test(pro_isbn);
				  }
				   if(match == false){
				   match = search_str.test(pro_jan);
				   }
				   if(match == false){
				   match = search_str.test(pro_sku);
				   }
				   if(match == false){
				   match = search_str.test(pro_ean);
				   }
				   if(match == false){
				   match = search_str.test(pro_upc);
				   }				
			if(match == true){
			infoIconColor='red';
			if(parseInt(pro_quantity)){
			infoIconColor='#44b3de';
			}
			
			html  = '<div class="product-container product-area" style="position:relative;"  data-type="'+ data_type +'">';
			
			html += '<i class="fas fa-info-circle prod-info" productinfo="'+ popular.product_id +'" onclick="productInfo(event);" style="right: 5px; top: 5px; color: '+infoIconColor+'; font-size:20px; position: absolute;z-index: 99;top: 10px;right: 10px;cursor: pointer;"></i>';
			html  += '<a id="scaned_product" dataproduct="'+ popular.product_id +'" href="'+ popular.href +'" class="product" data-toggle="modal" onclick="productdata(this)" >';
			html += '<div class="img" style="background-image: url()"><img src="'+ popular.thumb +'"></div>';
			html += '<div class="text"><div class="title">'+ popular.heading_title +'</div><div class="desc">'+ popular.model +'</div>';
			if (popular.price) {
            html += '<p class="price">'; 
			if(!popular.special){
                 html += format(taxObj.calculate(popular.mrp,popular.tax_class_id,posData.config_tax),posData.currency_code);
             } else {
				 html += '<span class="price-new">'+  format(taxObj.calculate(popular.special,popular.tax_class_id,posData.config_tax),posData.currency_code) +'</span> <span class="pts-price-old">'+ format(taxObj.calculate(popular.price,popular.tax_class_id,posData.config_tax),posData.currency_code) +'</span>'; 
			 }
			
				  html +='</p>';
        }

		html +='</div></a></div>';
			$('#posproduct').append(html);
			setTimeout(function(){			
				$('#scaned_product').trigger('click');	
			},100);
		  }
		});	
}


function productInfoHTML(data){
	price=data.price;
if(data.special){
	price=data.special;
}
var defaultLanguage='1';
var weightUnit='';
if(typeof posData.weightUnit != 'undefined'){
	$.each(posData.weightUnit,function(key,val){
		if(val.weight_class_id==data.weight_class_id && val.language_id==defaultLanguage){
			weightUnit = val.unit;
		}
		});
}
var defaultLanguage='1';
var lengthUnit='';
if(typeof posData.lengthUnit != 'undefined'){
	$.each(posData.lengthUnit,function(key,val){
		if(val.length_class_id==data.length_class_id && val.language_id==defaultLanguage){
			lengthUnit = val.unit;
		}
		});
}
$('.product-information').remove();
var html='';
html+='<div class="product-information row">';
html+='<div class="col-sm-12"><header><h2>{{ text_prod_information }}</h2></header></div>';
html+='<div class="col-sm-4"><div class="text-center"><img  class="img-fluid" src="'+data.thumb+'" alt="'+data.heading_title+'"></div></div>';
html+='<div class="col-sm-8">';
html+='<div class="text-left"><span> {{ text_prod_information_prod_name }} '+data.heading_title+'</span></div>';
html+='<div class="text-left"><span> {{ text_prod_information_price }} '+price+'</span></div>';
html+='<div class="text-left"><span> {{ text_prod_information_quantity }} '+data.pos_quantity+'</span></div>';
html+='<div class="text-left"><span> {{ text_prod_information_weight }} '+parseFloat(data.weight).toFixed(2)+' '+weightUnit+'</span></div>';
html+='<div class="text-left"><span> {{ text_prod_information_length }} '+parseFloat(data.length).toFixed(2)+' '+lengthUnit+'</span></div>';
html+='<div class="text-left"><span> {{ text_prod_information_width }}  '+parseFloat(data.width).toFixed(2)+' '+lengthUnit+'</span></div>';
html+='<div class="text-left"><span> {{ text_prod_information_height }} '+parseFloat(data.height).toFixed(2)+' '+lengthUnit+'</span></div>';
html+='</div>';
html+='</div>';
$('#pos-productinfo').append(html);
}

function productInfo(event){
var product_id=event.target.attributes['productinfo'].value;
var productData = posData.products[product_id];
productInfoHTML(productData);
$('#productInfo').modal('show');
}

posFuntions.posCategoryProduct= function(products,dataType){
		$('.product-category-area').remove();
		var modalPosItem='';
		if(typeof products!='undefined'){
		$.each(products, function( index, popular ) { 
			modalPosItem='';
		if(popular.options.length){
			modalPosItem='modalPosItem';
		}
		
		var pro_quantity = popular.pos_quantity;
			infoIconColor='red';
			if(parseInt(pro_quantity)){
			infoIconColor='#44b3de';
			}
		
		html  = '<div  class="product-container product-category-area" style="position:relative" data-type="'+dataType+'">';
		
		html += '<i class="fas fa-info-circle prod-info" productinfo="'+ popular.product_id +'" onclick="productInfo(event);" style="right: 5px; top: 5px; color: '+infoIconColor+'; font-size:20px; position: absolute;z-index: 99;top: 10px;right: 10px;cursor: pointer;"></i>';
		
		html  += '<a dataproduct="'+ popular.product_id +'" href="'+ popular.href +'" class="product" data-toggle="modal" onclick="productdata(this)" >';
		html += '<div class="img" style="padding: 5px;"><img src="'+ popular.thumb +'"></div>';
		html += '<div class="text"><div class="title">'+ popular.heading_title +'</div><div class="desc">'+ popular.model +'</div>';
		if (popular.price) {
            html += '<p class="price">'; 
			if(!popular.special){
                 html += format(taxObj.calculate(popular.mrp,popular.tax_class_id,posData.config_tax),posData.currency_code);
             } else {
				 html += '<span class="price-new">'+ format(taxObj.calculate(popular.special,popular.tax_class_id,posData.config_tax),posData.currency_code) +'</span> <span class="pts-price-old">'+ format(taxObj.calculate(popular.price,popular.tax_class_id,posData.config_tax),posData.currency_code) +'</span>'; 
			 }
			 
				  html +='</p>';
        }

		html +='</div></a></div>';
		$('#posCateProduct').append(html);	
		});
		}
}

		function emptyCart(){
			localStorage.removeItem('pos_cart');
			sessionStorage.removeItem('custom_tax');
			sessionStorage.removeItem('admin_tax');
			sessionStorage.removeItem('discount');
			sessionStorage.removeItem('custom_charge');
			sessionStorage.removeItem('coupon');
			sessionStorage.removeItem('coupon_code');
			sessionStorage.removeItem('updateCartProduct');
			sessionStorage.removeItem('totals');
			localStorage.removeItem('cartData');
			sessionStorage.removeItem('grand_total');
			sessionStorage.removeItem('split_payment');
			sessionStorage.removeItem('sub_total');
			localStorage.removeItem('custom_product');
		}
		function removeProductFromCart(cart_id){
		
		 let updateCartProduct = JSON.parse(sessionStorage.getItem("updateCartProduct"));
			cartUpdateProductData={};
			$.each(updateCartProduct, function( index, cartData1 ) {
				if(parseInt(cartData1.cart_id)!=parseInt(cart_id)){
					cartUpdateProductData[index]= cartData1;  
				}
			});
			sessionStorage.setItem("updateCartProduct",JSON.stringify(cartUpdateProductData));
			cartUpdateProduct = JSON.parse(sessionStorage.getItem("updateCartProduct"));
			if(Object.keys(cartUpdateProduct).length == 0){
				sessionStorage.removeItem('updateCartProduct');
			}
			pos_cart=[];
			let cartProducts = JSON.parse(localStorage.getItem("pos_cart"));
			cartProductData=[];
			$.each(cartProducts, function( index, cartData ) {
				if(parseInt(cartData.cart_id)!=parseInt(cart_id)){
					cartProductData.push(cartData);  
					pos_cart.push(cartData);
				}
			});
			localStorage.setItem("pos_cart",JSON.stringify(cartProductData));
			cartProduct = JSON.parse(localStorage.getItem("pos_cart"));
			if(cartProduct.length ==0 ){
				sessionStorage.removeItem('custom_tax');
				sessionStorage.removeItem('discount');
				sessionStorage.removeItem('custom_charge');
				sessionStorage.removeItem('admin_tax');
				sessionStorage.removeItem('coupon');
				sessionStorage.removeItem('coupon_code');
				sessionStorage.removeItem('totals');
				alert(getSubTotal());
				$("#total").text(format('0',posData.currency_code));
				$("#yousaveddiscount").text(format('0',posData.currency_code));
				$("#yousubtotal").text(format('0',posData.currency_code));
				$("#youreward").text('0');
				
			}
			
			if(sessionStorage.getItem("coupon") != null){
				var coupon = JSON.parse(sessionStorage.getItem("coupon"));
				var coupon_sts = couponObj.getCoupon(coupon.coupon_code);
				if(coupon_sts == null){
					sessionStorage.removeItem('coupon');
					sessionStorage.removeItem('coupon_code');
					
				}
			}
			
			getCartProductHTML();
			couponApply(0);
		}

		function getSubTotal() {
			var total = 0;
			if(typeof CartObj.getProducts() != 'undefined'){
				$.each(CartObj.getProducts(),function(key,val){
				total+=val.total;
				});
			}
			return total;
		}
	
		function getTotal() {
			var total = 0;
			$.each(CartObj.getProducts(),function(key,val){
				total += (taxObj.calculate(val.price,val.tax_class_id,posData.config_tax))* val.quantity;
			});
			posData.order_total=total;
			getTotalTax();
			return total;
			
		}
		
		function getDiscount() {
			var total = 0;
			$.each(CartObj.getProducts(),function(key,val){
				total += val.disc_price* val.quantity;
			});
			return total;
			
		}
		
		function getReward() {
			var total = 0;
			$.each(CartObj.getProducts(),function(key,val){
				total += val.reward;
			});
			return total;
			
		}
		
		function getTotalProductQty() {
			var total = 0;
			if(typeof CartObj.getProducts() != 'undefined'){
				$.each(CartObj.getProducts(),function(key,val){
				total+=parseInt(val.quantity);
				});
			}
			return total;
		}
		
		function getTotalProducts() {
			var total = CartObj.getProducts().length;
			return total;
		}
		
		function getTotalOrderQty(data) {
			var total = 0;
			var pos_orders_data = [];
			if(localStorage.getItem("pos_orders_data") != null){
			   var pos_orders_data = JSON.parse(localStorage.getItem("pos_orders_data"));
			}
		   $.each(pos_orders_data,function(key,val){
		  $.each(val.products,function(productkey,productval){
				  if( parseInt(productval.order_id) == parseInt(data)){
				      total+=parseInt(productval.quantity);
				   }
					});
					});
				return total;
		}
		
		
		function getTotalOrderItem(data) {
		var pos_orders_data = [];
			if(localStorage.getItem("pos_orders_data") != null){
			   var pos_orders_data = JSON.parse(localStorage.getItem("pos_orders_data"));
			}
					$.each(pos_orders_data,function(key,val){
						if(parseInt(val.order_id) === parseInt(data)){
									var total = pos_orders_data.length;
									
						}
					});
                  return total;
		}
		
	function getTotalTax() {
			var total_tax=[];
			$.each(getTaxes(),function(key,value){
				if(value > 0){
					total_tax.push({
						'code'       : 'tax',
						'title'      : taxObj.getRateName(key),
						'value'      : value,
						'sort_order' : 1
					});
				}
			});
			sessionStorage.setItem("admin_tax", JSON.stringify(total_tax));
		}

	function getRateName (id){
		if(posData.rateNameData != null){
			$.each(posData.rateNameData, function(key,val){
			if(parseInt(val.tax_rate_id)==parseInt(id)){
				return val.name;
			}
			
			});
		}
		return null;
	}	
		
	
		function getCartTotalProduct() {
			return CartObj.getProducts().length
		}
		
		
	
//Add to Cart	
function addToCartProducts(productData){
var options = [];
if(productData.filter_options){
	options = productData.filter_options;
}
let customer_id=0;
if(sessionStorage.getItem('currentCustomerData') != null){
	currentCustomerData = JSON.parse(sessionStorage.getItem('currentCustomerData'));
	customer_id=currentCustomerData.customer_id;
	}
	
	
	var posCartData = 
		{
			'cart_id':parseInt((Math.floor(Math.random() * 10000000000) + 10000000000).toString().substring(1)),
			'customer_id':customer_id,
			'product_id':productData.product_id,
			'recurring_id':'0',
			'option':options,
			'quantity':productData.minimum
		};
var duplicate_check=false;
	pos_cart =[];
	if(localStorage.getItem("pos_cart") != null){
		pos_cart = JSON.parse(localStorage.getItem("pos_cart"))
	}
	if(pos_cart.length){
		$.each(pos_cart,function(key,val){
			if((parseInt(val.product_id)==parseInt(productData.product_id)) && (JSON.stringify(val.option) == JSON.stringify(options))){
				pos_cart[key]['quantity']=parseInt(val.quantity)+parseInt(posCartData.quantity);
				duplicate_check=true;
			}
		});
		if(!duplicate_check){
			pos_cart.push(posCartData);	
		}
	} else {
		pos_cart.push(posCartData);	
	}

localStorage.setItem("pos_cart", JSON.stringify(pos_cart));
return JSON.parse(localStorage.getItem("pos_cart"));
}

//Add to Cart	
// get Cart Product HTML

function getOrderPrepareData(){
return new Promise((resolve, reject) => {
Promise.all([CartObj.hasShipping()]).then(function(values){
var OrerData = {};
		cartProductForOrder();	
	
	// var payment_method = {
		// 'title' : 'Cash On Delivery',
	    // 'code' : 'cod'
	
	// };
		payment_title_string='';
		if(sessionStorage.getItem("split_payment") != null){
		   var setpayments = JSON.parse(sessionStorage.getItem("split_payment"));
		   cash=parseFloat(setpayments.cash);
		   card=parseFloat(setpayments.card);
		   custom=parseFloat(setpayments.custom);
		   title =[];
		   //amount =[];
		   if(cash != 0){
			title.push('Cash');
			//amount.push({'title':'Cash','value':cash});
		   }
		    if(card != 0){
			title.push('Debit/Credit Card');
			//amount.push({'title':'Debit/Credit Card','value':card});
		   }
		    if(custom != 0){
			
			title.push($('#cus_pay_title').val());
			//amount.push({'title':$('#cus_pay_title').val(),'value':custom});
		   }
		   if(title.length > 1){
		   payment_title= title.join(" + ");
		   payment_title_string = 'Split Payment('+ payment_title +')';
		   } else {
		   payment_title_string = title[0]+" Payment";
		   }
		   }
{% if version_compare %}
	var payment_method = {
		'name' : payment_title_string,
	    'code' : 'cod.cod'
	
	};
	{% else %}
	var payment_method = {
		'title' : payment_title_string,
	    'code' : 'cod'
	
	};
 {% endif %}
 {% if version_compare %}
	var shipping_method = {
		'name' : 'Free Shipping',
	    'code' : 'free.free'
	};
{% else %}
	var shipping_method = {
		'title' : 'Free Shipping',
	    'code' : 'free.free'
	};	
{% endif %}
	sessionStorage.setItem('payment_method', JSON.stringify(payment_method));
	sessionStorage.setItem('shipping_method', JSON.stringify(shipping_method));
	
	if(sessionStorage.getItem('payment_address') == null){
		var default_store_address = {
	   'address_id' : '',
	   'firstname' : posData.store_name,
	   'lastname' : '',
	   'company' : '',
	   'address_1' : posData.config_address,
	   'address_2' : '',
	   'postcode' : '',
	   'city' : '',
	   'zone_id' : posData.config_zone_id,
	   'zone' : posData.zone_data.code,
	   'zone_code' : posData.zone_data.name,
	   'country_id' : posData.config_country_id,
	   'country' : posData.country_data.name,
	   'iso_code_2' : posData.country_data.iso_code_2,
	   'iso_code_3' : posData.country_data.iso_code_3,
	   'address_format' : '',
	   'custom_field' : []
	
	};
	//console.log('default_store_address');
	//console.log(default_store_address);
	sessionStorage.setItem("payment_address",JSON.stringify(default_store_address));
	}
	
	//console.log(posData.store_name);
	
	payment_address = JSON.parse(sessionStorage.getItem('payment_address'));
	
	payment_method = JSON.parse(sessionStorage.getItem('payment_method'));
	shipping_address={};
	if(sessionStorage.getItem('shipping_address') != null){
	shipping_address = JSON.parse(sessionStorage.getItem('shipping_address'));
	}
	shipping_method = JSON.parse(sessionStorage.getItem('shipping_method'));
	totals = JSON.parse(sessionStorage.getItem('totals'));
	currentCustomerData = JSON.parse(sessionStorage.getItem('currentCustomerData'));
	cartProductsData = JSON.parse(sessionStorage.getItem('cartProductsData'));
	
	if(typeof currentCustomerData=='undefined' || currentCustomerData=='' || currentCustomerData==null){
		  OrerData.message="Please add/select customer.";
		  OrerData.status="error";
	}
	let cartProducts = JSON.parse(localStorage.getItem("pos_cart"));
	
	if(typeof cartProducts=='undefined' || cartProducts == null || cartProducts == '' ){
		 OrerData.message="Please add product to cart.";
		 OrerData.status="error";
	}	

var hasShipping = values[0];
if(OrerData.status !='error'){
pos_shiping = {};
if(hasShipping == "true"){
if(Object.keys(shipping_address).length){
	pos_shiping = {
			'firstname' : shipping_address.firstname,
			'lastname' : shipping_address.lastname,
			'company' : shipping_address.company,
			'address_1' : shipping_address.address_1,
			'address_2' : shipping_address.address_2,
			'city' : shipping_address.city,
			'postcode' : shipping_address.postcode,
			'zone' : shipping_address.zone,
			'zone_id' : shipping_address.zone_id,
			'country' : shipping_address.country,
			'country_id' : shipping_address.country_id,
			'address_format' : shipping_address.address_format,
			'custom_field' : [],
			'method' : shipping_method.title,
			'code' : shipping_method.code,
		}
		}
} else {
	pos_shiping = {
		'firstname' : '',
		'lastname' : '',
		'company' : '',
		'address_1' : '',
		'address_2' : '',
		'city' : '',
		'postcode' : '',
		'zone' : '',
		'zone_id' : '',
		'country' : '',
		'country_id' : '',
		'address_format' : '',
		'custom_field' : [],
		'method' : '',
		'code' : '',
		}
}
var ppts_total= getTotal();
if(sessionStorage.getItem("grand_total") != null){
let g_total = JSON.parse(sessionStorage.getItem("grand_total"));
ppts_total = g_total.value;
}

OrerData = {
	'cart' : cartProducts,
	'totals' : totals,
	'agent_id':posData.agent_id,
	'products' : cartProductsData,
	'customer_id' : (currentCustomerData.customer_id),
	'payment_address' : payment_address,
	'payment_method' : payment_method,
	'shipping_address' : pos_shiping,
	'shipping_method' : shipping_method,
    'total' : ppts_total
};
}
resolve(OrerData);
});
});
}

//taxObj.calculate(product.price,popular.tax_class_id,posData.config_tax)
//$this->tax->getTax($product['price'], $product['tax_class_id'])
function cartProductForOrder(){
	var order_data = [];
	if(typeof CartObj.getProducts() != "undefined"){
		$.each(CartObj.getProducts(),function(key,product){
				data = {
					'product_id' : product.product_id,
					'name'       : product.name,
					'model'      : product.model,
					'option'     : product.option,
					'download'   : product.download,
					'quantity'   : product.quantity,
					'subtract'   : product.subtract,
					'price'      : product.price,
					'total'      : product.total,
					'tax'        : taxObj.getTax(parseFloat(product.price),parseFloat(product.tax_class_id)),
					'reward'     : product.reward
				};
				
				order_data.push(data);

		});
	}
	sessionStorage.setItem('cartProductsData', JSON.stringify(order_data));
}


function format(number, currency, value = '', format = true) {
var currencyData = globalPOS;
//var currencyData= JSON.parse(localStorage.getItem("posData"));
var currencyVal='';
	if(typeof currencyData.currency != 'undefined'){
		$.each(currencyData.currency,function(key,val){
			if(currency == val.code){
				currencyVal= val;
			}
		});
	}
	
	if(currencyVal){
		symbol_left = currencyVal.symbol_left;
		symbol_right = currencyVal.symbol_right;
		decimal_place = currencyVal.decimal_place;

		if (!value) {
			value = currencyVal.value;
		}
		
		var amount = value ? parseFloat(number) * value : parseFloat(number);
		
		amount = amount.toFixed(decimal_place);
		
		if (!format) {
			return amount;
		}

		var string = '';

		if (symbol_left) {
			string += symbol_left;
		}

		string += number_format(amount, parseInt(decimal_place), currencyData.lang.decimal_point, currencyData.lang.thousand_point);

		if (symbol_right) {
			string += symbol_right;
		}
		let x = document.cookie
		return string;
	}	
}

function number_format (number, decimals, dec_point, thousands_sep) {
    // Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}
function refreshPage() {
setPosTax(taxObj);
getCartProductHTML();
posFuntions.posProduct(posData.products,'all');
}

function getCartProductHTML(){
setPosTax(taxObj);
$('.subtotal').remove();
$('.pos-table-row').remove();
	 let html='';	
	 if(CartObj.getProducts().length) {
	 $('.empty_cart').remove();
		$.each(CartObj.getProducts(),function(key,val){
		    qtytypeweightUnit = '';
		$.each(posData.weightUnit,function(key1,val1){
			if(val.weight_class_id==val1.weight_class_id){
				qtytypeweightUnit = val1.unit;
			}
		});
		
		     
			 html = '<div class="row pos-table-row" id="remove_row'+val.cart_id+'">';
			 html += '<div class="col-8">';
			 html += '<div class="pos-product-thumb">';
			// html += '<div class="img" style="background-image: url('')"></div>';
			 html += '<div class="info">';
			 html += '<div class="title">'+val.name+'</div>';
			 html += '<div class="title">'+val.disc_price*val.quantity+'</div>';
			 html += '<div class="title">'+val.reward+'</div>';
			if(val.option){
				$.each(val.option,function(key21,option){
					html += '<small style="display:block">'+ option.name +': '+ option.value +'</small>';
				});
			}
			 html += '<div class="single-price">'+format(taxObj.calculate(val.price,val.tax_class_id,posData.config_tax),posData.currency_code)+'</div>';
			 html += '<div class="input-group qty">';
			 html += '<div class="input-group-append">';
			 
			 html += '<a onclick="sub_qty('+val.cart_id+');" class="btn btn-default"><i class="fa fa-minus"></i></a>';
			 
			 html += '</div>';
			 html += '<input id = "pos-pro-qty'+val.cart_id+'" type="text" class="form-control" value="'+val.quantity+'" readonly>';
			 html += '<input id = "add-pos-pro-qty" type="hidden" value="'+val.product_id+'">';
			 html += '<div class="input-group-prepend">';
			 html += '<a  onclick="add_qty('+val.cart_id+');" class="btn btn-default"><i class="fa fa-plus"></i></a>';
			 html += '</div>';
			 html += '</div>';
			 html += '</div>';
			 html += '</div>';
			 html += '</div>';
			 html += '<div class="col-2 total-price1" id="pro-total-price1'+val.cart_id+'">'+format((taxObj.calculate(val.price,val.tax_class_id,posData.config_tax)*val.quantity),posData.currency_code)+'</div>';
			 //html += '<div class="col-1 total-price"><a  onclick="showDetail('+val.cart_id+');" ><i class="fas fa-pen-square prod-info" style="color: #44b3de;"></i></a></div>';
			 html += '<div class="col-1 total-price"> <a  onclick="conform_remove('+val.cart_id+');"><i class="fa fa-trash" style="color: red;"></i></a></div>';	 
			 html += '<div class="pos-remove-confirmation pos-remove-height" id="cofrow'+val.cart_id+'" style = "display:none;">Remove this item?<a  onclick="conform_remove_no('+val.cart_id+');" class="btn btn-white ml-auto mr-2">{{ text_confirm_remove_no }}</a><a onclick="conform_remove_yes('+val.cart_id+');" class="btn btn-danger">{{ text_confirm_remove_yes }}</a></div>';
			 
			 html +='<div id = "pos-pro-detail'+val.cart_id+'" style="display:none;">';
			  del= '';
			  del_close= '';
			 if(val.disc_value){
			   del= '<del>';
			   del_close= '</del>';
			 }
			 if(val.change_price){
			 html +='<div class="row" style="margin-top: 10px;"><div class="col-4"><span>{{ text_prod_information_price }}</span><span>'+del+' '+format(taxObj.calculate(val.change_price,val.tax_class_id,posData.config_tax),posData.currency_code)+' '+del_close+'</span></div>';
			 }else{
			 html +='<div class="row" style="margin-top: 10px;"><div class="col-4"><span>{{ text_prod_information_price }}</span><span>'+del+' '+format(taxObj.calculate(val.real_price,val.tax_class_id,posData.config_tax),posData.currency_code)+' '+del_close+'</span></div>';
			 }
			 if(val.disc_type == 'percentage' && val.disc_value){
			 html +='<div class="col-4"><span style="margin-left:-21px";>{{ text_dis }}(<span >%</span> ):<span>'+val.disc_value+'</span> </span></div>';
			 }else if(val.disc_type == 'Fixed' && val.disc_value){
			  html +='<div class="col-4"><span style="margin-left:-21px";>{{ text_dis }}(<span >Fixed</span> ):<span>'+val.disc_value+'</span> </span></div>';
			 }else{ 
			 html +='<div class="col-4"><span style="margin-left:-21px";>{{ text_dis }}(<span >%</span> ):<span >N/A</span> </span></div>';
			 }
			 if(val.disc_price){
			 html +='<div class="col-4" ><span style="margin-left:-18px";>Disc. Price: </span>'+format(taxObj.calculate(val.disc_price,val.tax_class_id,posData.config_tax),posData.currency_code)+'</div></div>';
			 }else{
			 html +='<div class="col-4" ><span style="margin-left:-18px";>{{ text_disc_price }}: </span>N/A</div></div>';
			 }
			 html +='<div  class=" row input-group"><div  class="input-group-prepend"><span  class="input-group-text">{{ text_chng_price }}</span><div class="input-group-text"><div  class="input-group-prepend"><input id="changeprice_checkbox'+val.cart_id+'" type="checkbox" value="checkbox"></div></div><span  class="input-group-text">'+getCurrencySymbol(posData.currency_code)+'</span></div><input id="changeprice'+val.cart_id+'" type="number" formcontrolname="changeprice" class="form-control"><input  id="target-price" type="hidden"></div>';

			 html +='<div  class="form-group m-t-10"><label  for="quantity">{{ text_qty_type }}</label><br><div  class="custom-control custom-radio custom-control-inline"><input id="quantity_unit'+val.cart_id+'" name="quantity_type" type="radio" value="unit" formcontrolname="quantityType" class="custom-control-input"><label  for="quantity_unit'+val.cart_id+'" class="custom-control-label"><span>{{ text_unit }}</span></label></div>'
			  if(val.weight > 0){
				html +='<div class="custom-control custom-radio custom-control-inline m-b-10"><input id="quantity_weight'+val.cart_id+'" name="quantity_type"  type="radio"  value="weight" formcontrolname="quantityType" class="custom-control-input"><label  for="quantity_weight'+val.cart_id+'" class="custom-control-label"><span>{{ text_weight }}</span> ('+qtytypeweightUnit+')</label></div>';
				}
			 html +='<div  class=" row input-group row" style="margin-top: 10px;"><div  class="input-group-prepend"><span  class="input-group-text">{{ text_quantity }}</span><div class="input-group-prepend"><span class="input-group-text qty-decrease" onclick="sub_qty('+val.cart_id+')";>-</span></div></div><input  id="changequantity'+val.cart_id+'" type="number" formcontrolname="changequantity" class="form-control"><input  id="target-price" type="hidden"><div class="input-group-append"><span  class="input-group-text qty-increase" onclick="add_qty('+val.cart_id+');">+</span></div></div>';
			 
			 
			 html +='<div class="input-group row" style="margin-top: 10px;"><div  class="input-group-prepend"><span  class="input-group-text">{{ text_discount }}</span></div><select id="discountType'+val.cart_id+'" formcontrolname="discountType" class="form-control "><option  value="0">{{ text_percentage }}</option><option  value="1">{{ text_fixed }}</option></select><input  type="number" id="discountValue'+val.cart_id+'" formcontrolname="discountValue" class="form-control "><input  id="target-discountValue'+val.cart_id+'" type="hidden"><div  class="input-group-append"></div></div>';
			 html +='<div class="row" style="margin-top: 10px;"><div class="btn-group"><button type="button" ptproddata="'+val.cart_id+'" class="btn btn-success updateptscart" >{{ text_update }}</button></div></div>';
			 html +='</div>';
			 html += '</div>';	
	
			 $('#pos-to-cart').append(html);
		});		
				
	
		
		setOrderItems();
	} else {
			let message='{{ text_cart_empty }}';
				html = '<div class="h-100 d-flex align-items-center justify-content-center text-center p-20 empty_cart"><div><h4>'+message+'</h4></div></div>';	
			 $('#newOrderTab').append(html);
			 $("#total").text(format('0',posData.currency_code));
			 $("#yousaveddiscount").text(format('0',posData.currency_code));
			 $("#yousubtotal").text(format('0',posData.currency_code));
			 $("#youreward").text('0');
			}
	}
// get Cart Product HTML

function setOrderItems(){
		$('.subtotal').remove();
		var orderItemArr=[];
		getTotal();
		var sub_total={
					'code' : 'sub_total',
                    'title' : '{{ text_sub_total }}',
                    'value' : getSubTotal(),
                    'sort_order' :1,
					'removable' : false,
					'id' : '',
					'custom_title' : ''
		
		};
sessionStorage.setItem("sub_total", JSON.stringify(sub_total));
var sub_total = JSON.parse(sessionStorage.getItem("sub_total"));
var custom_tax = JSON.parse(sessionStorage.getItem("custom_tax"));
var admin_tax = JSON.parse(sessionStorage.getItem("admin_tax"));
var custom_charge = JSON.parse(sessionStorage.getItem("custom_charge"));
var discount = JSON.parse(sessionStorage.getItem("discount"));
orderItemArr.push(sub_total);
if(sessionStorage.getItem("coupon") != null){
	var coupon = JSON.parse(sessionStorage.getItem("coupon"));
	orderItemArr.push(coupon);
	posData.order_total = parseFloat(posData.order_total)+parseFloat(coupon.value);
}

// Admin Tax
	if(typeof admin_tax != "undefined"){
		$.each(admin_tax,function(tax_key,tax_val){
		orderItemArr.push(tax_val);
		});
	}
// Admin Tax
	var total_tax=0;
	if(custom_tax != null){
	$.each(custom_tax,function(key,TaxValue){
	if(TaxValue.type == 'percentage'){
	tax_value = (parseFloat(getSubTotal())*parseFloat(TaxValue.value))/100;
	} else {
	tax_value = parseFloat(TaxValue.value);
	}
	total_tax+=tax_value;
		let tax = {
					'code' : 'tax',
                    'title' : TaxValue.title,
                    'value' : tax_value,
                    'sort_order' :1,
					'removable' : true,
					'id' : key,
					'custom_title' : 'custom_tax'
				};
		orderItemArr.push(tax);
		});
	}
	posData.order_total = parseFloat(posData.order_total)+parseFloat(total_tax);
	
	// Additional charges
		var total_charge=0;
	if(custom_charge != null){
	$.each(custom_charge,function(key,ChargeValue){
	if(ChargeValue.type == 'percentage'){
	charge_value = (parseFloat(getSubTotal())*parseFloat(ChargeValue.value))/100;
	} else {
	charge_value = parseFloat(ChargeValue.value);
	}
	total_charge+=charge_value;
		let charge = {
					'code' : 'tax',
                    'title' : ChargeValue.title,
                    'value' : charge_value,
                    'sort_order' :1,
					'removable' : true,
					'id' : key,
					'custom_title' : 'custom_charge'
				};
		orderItemArr.push(charge);
		});
	}
	posData.order_total = parseFloat(posData.order_total)+parseFloat(total_charge);
	// Additional charges
	// discount code
		var total_discount=0;
	if(discount != null){
	$.each(discount,function(key,discountValue){
	if(discountValue.type == 'percentage'){
	discount_value = (parseFloat(posData.order_total)*parseFloat(discountValue.value))/100;
	} else {
	discount_value = parseFloat(discountValue.value);
	}
	total_discount+=discount_value;
		let discount = {
					'code' : 'tax',
                    'title' : discountValue.title,
                    'value' : -discount_value,
                    'sort_order' :1,
					'removable' : true,
					'id' : key,
					'custom_title' : 'discount'
					
				};
		orderItemArr.push(discount);
		});
	}
	posData.order_total = parseFloat(posData.order_total)-parseFloat(total_discount);
	// discount code

		var grand_total={
					'code' : 'total',
                    'title': '{{ text_total }}',
                    'value': posData.order_total,
                    'sort_order': 9,
					'removable' : false,
					'id' : '',
					'custom_title' : ''
		
		};
sessionStorage.setItem("grand_total", JSON.stringify(grand_total));
var grand_total = JSON.parse(sessionStorage.getItem("grand_total"));
$("#total").text(format(grand_total.value,posData.currency_code));
$("#yousaveddiscount").text(format(getDiscount(),posData.currency_code));
$("#yousubtotal").text(format(getSubTotal(),posData.currency_code));
$("#youreward").text(getReward());
orderItemArr.push(grand_total);
sessionStorage.setItem("totals", JSON.stringify(orderItemArr));
		if(typeof orderItemArr!= 'undefined'){
			$.each(orderItemArr,function(key,val){
				html='';
				html+='<div class="subtotal '+val.title+'">';
				html+='<div class="text">'+val.title+'</div>';
				html+='<div class="price" >'+format(val.value,posData.currency_code)+'</div>';
				html+='</div>';	
				//$('#pos-total').append(html);
			});
		}
}

//Options
function productdata(data){
var product_id = data.attributes.dataproduct.value;
var productData = posData.products[product_id];


var pro_name=productData.name; 
var pro_image=productData.thumb; 
var pro_price=productData.price; 
var pro_minimum=productData.minimum; 
var pos_pro_option_status = 0;
var productOptions=productData.options;

if (productData.pos_quantity < 1){
	$('#modalPosItem').modal('hide');
	$('#productOutofstock').modal('show');
}

//add to cart
	if(!productOptions.length && (productData.pos_quantity > 0)){
		var cartToCartData = addToCartProducts(productData);
		var cartProducts = CartObj.getProducts();
		var cartProductHTML = getCartProductHTML();

		cartProductData.push(productData);
		localStorage.setItem("cartData", JSON.stringify(cartProductData));
		var cartData = JSON.parse(localStorage.getItem("cartData"));
		couponApply(0);
		PlaySound();
	}
//End Add to cart

	$('.pos-product-option').remove();
	$('#pos-product-options').empty();
	if(productOptions.length && (parseInt(productData.pos_quantity) > 0)){
	var html='';
	var required;
	var text_select='Select';
	var button_upload='button_upload'; 
	var text_loading='text_loading';
	
		$.each(productOptions, function( key,option ) { 
		 required='';
//select
		if(option.type=='select'){
		if(parseInt(option.required)){
			required = "required";
		}
			html += '<div class="form-group '+required+' pos-product-option">';
			html += '<label class="control-label" for="input-option'+option.product_option_id+'">'+option.name+'</label>';

			html += '<select name="select-'+option.product_option_id+'" id="input-option'+option.product_option_id+'" class="form-control">';
			html += '<option value="">'+text_select+'</option>';
			$.each(option.product_option_value, function( key1,option_value ) { 
			html += '<option value="'+option_value.product_option_value_id+'">'+option_value.name+'';
			if(option_value.price){
			html +='('+ option_value.price_prefix +' '+ format(taxObj.calculate(option_value.price,productData.tax_class_id,posData.config_tax),posData.currency_code)+' )';
			}
			html += '</option>';
			});
			html += '</select>';
			html += '</div>';
		}
	// End select
	// radio
		if(option.type=='radio'){
			if(parseInt(option.required)){
				required = "required";
			}
			html += '<div class="form-group '+required+' pos-product-option">';
			html += '<label class="control-label">'+ option.name +'</label>';
			html += '<div id="input-option'+ option.product_option_id +'">';
			$.each(option.product_option_value, function( key1,option_value ) { 
			html += '<div class="radio">';
			html += '<label>';
			html += '<input type="radio" name="radio-'+option.product_option_id +'" value="'+ option_value.product_option_value_id +'" />';
			if(option_value.image){
			if(option_value.price){
			opt_price= option_value.price_prefix +' '+ format(taxObj.calculate(option_value.price,productData.tax_class_id,posData.config_tax),posData.currency_code);
			}
			html += ' <img src="' +option_value.image +'" alt="'+ option_value.name +' '+opt_price+'" class="img-thumbnail" /> ';
			}
			html += ' '+option_value.name ;
			if(option_value.price){
			html += '('+ option_value.price_prefix +' '+ format(taxObj.calculate(option_value.price,productData.tax_class_id,posData.config_tax),posData.currency_code) +')';
			}
			html += '</label>';
			html += '</div>';
			});
			html += '</div></div>';
	}
		
	// End radio
	// Checkbox
		if(option.type=='checkbox'){
			if(parseInt(option.required)){
				required = "required";
			}
			html += '<div class="form-group '+required+' pos-product-option">';
			html += '<label class="control-label">'+option.name+'</label>';
			html += '<div id="input-option'+ option.product_option_id +'">';
			$.each(option.product_option_value, function( key1,option_value ) { 
		 
			html += '<div class="checkbox">';
			html += '<label>';
			html += '<input type="checkbox" name="checkbox-'+option.product_option_id +'" value="'+ option_value.product_option_value_id +'" />';
			if(option_value.image){
			if(option_value.price){
			opt_price= option_value.price_prefix +' '+ format(taxObj.calculate(option_value.price,productData.tax_class_id,posData.config_tax),posData.currency_code);
			}
			html += ' <img src="' +option_value.image +'" alt="'+ option_value.name +' '+opt_price+'" class="img-thumbnail" /> ';
			}
			html += ' '+option_value.name ;
			if(option_value.price){
		  
			html += '('+ option_value.price_prefix +' '+ format(taxObj.calculate(option_value.price,productData.tax_class_id,posData.config_tax),posData.currency_code) +')';
			}
			html += '</label>';
			html += '</div>';
			});
			html += '</div></div>';
	}
// End Checkbox
// Text
	if(option.type=='text'){
		if(parseInt(option.required)){
					required = "required";
				}
			html += '<div class="form-group '+required+' pos-product-option">';
			html += '<label class="control-label" for="input-option'+option.product_option_id+'">'+option.name+'</label>';
			html += '<input type="text" name="text-'+ option.product_option_id +'" value="'+option.value +'" placeholder="'+ option.name +'" id="input-option'+ option.product_option_id +'" class="form-control" />';
			html += '</div>';
	}    
// End Text
// textarea
	if(option.type=='textarea'){
		if(parseInt(option.required)){
					required = "required";
				}
			html += '<div class="form-group '+required+' pos-product-option">';
			html += '<label class="control-label" for="input-option'+option.product_option_id+'">'+option.name+'</label>';
			html += '<textarea name="textarea-'+ option.product_option_id +'" rows="5" placeholder="'+ option.name +'" id="input-option'+ option.product_option_id +'" class="form-control" />'+option.value+'</textarea>';
			html += '</div>';
	}    
// End textarea

// file
	if(option.type=='file'){
		if(parseInt(option.required)){
					required = "required";
				}
			html += '<div class="form-group '+required+' pos-product-option">';
			html += '<label class="control-label" >'+option.name+'</label>';
			html += '<button type="button" id="button-upload'+ option.product_option_id +'" data-loading-text="'+ text_loading +'" class="btn btn-default btn-block" onclick="uploadImage(this)"><i class="fa fa-upload"></i> '+ button_upload +'</button>';
			html += '<input type="hidden" name="file-'+ option.product_option_id +'" value="" placeholder="'+ option.name +'" id="input-option'+ option.product_option_id +'"/>';
			html += '</div>';
	}    
// End file

// date
	if(option.type=='date'){
		if(parseInt(option.required)){
					required = "required";
				}
			html += '<div class="form-group '+required+' pos-product-option">';
			html += '<label class="control-label" for="input-option'+option.product_option_id+'">'+option.name+'</label>';
			html += '<div class="input-group date">';
			
			html += '<input type="date" name="date-'+ option.product_option_id +'" value="'+ option.value +'" data-date-format="YYYY-MM-DD" id="input-option'+ option.product_option_id +'" class="form-control" />';
			html += ' <span class="input-group-btn">';
			html += ' <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>';
			html += ' </span>';
			html += '</div></div>';
	}    
// End date
// datetime
	if(option.type=='datetime'){
		if(parseInt(option.required)){
					required = "required";
				}
			html += '<div class="form-group '+required+' pos-product-option">';
			html += '<label class="control-label" for="input-option'+option.product_option_id+'">'+option.name+'</label>';
			html += '<div class="input-group datetime">';
			html += '<input type="datetime-local" name="datetime-'+ option.product_option_id +'" value="'+ option.value +'" data-date-format="YYYY-MM-DD HH:mm" id="input-option'+ option.product_option_id +'" class="form-control" />';
			html += ' <span class="input-group-btn">';
			html += ' <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>';
			html += ' </span>';
			html += '</div></div>';
	}    
// End datetime

// time
	if(option.type=='time'){
		if(parseInt(option.required)){
					required = "required";
				}
			html += '<div class="form-group '+required+' pos-product-option">';
			html += '<label class="control-label" for="input-option'+option.product_option_id+'">'+option.name+'</label>';
			html += '<div class="input-group time">';
			html += '<input type="time" name="time-'+ option.product_option_id +'" value="'+ option.value +'" data-date-format="HH:mm" id="input-option'+ option.product_option_id +'" class="form-control" />';
			html += ' <span class="input-group-btn">';
			html += ' <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>';
			html += ' </span>';
			
			html += '</div></div>';
	}    
// End time
		});
		$('.option-product-id').attr('option-product-id',product_id);
		$('#pos-product-options').append(html);
		$('#modalPosItem').modal('show');
	}
}

//Options
// barcode data 
   
function myBarcode(){
 var barcode = document.getElementById("barcode").value;
 if(barcode == ''){
   $('#posbarcode').modal('hide');
   alert('Please Enter Barcode');  
 }else{ 
 $('.product-area').remove();
 $('.product-category-area').remove();
            var result = '';
			var modalPosItem='';
			if(typeof posData.products!='undefined'){
			$.each(posData.products, function( index, popular ) {					
			if(barcode == popular.upc || barcode == popular.ean || barcode == popular.mpn || barcode == popular.jan || barcode == popular.isbn || barcode == popular.sku){
			result = popular;
		  }		  
		});	
		}
		if(result){	
			modalPosItem='';
		if(result.options.length){
			modalPosItem='modalPosItem';
		}	
		var pro_quantity = result.pos_quantity;
			infoIconColor='red';
			if(parseInt(pro_quantity)){
			infoIconColor='#44b3de';
			}
			
		    html  = '<div  class="product-container product-area" style="position:relative;"  data-type="all">';
			
			html += '<i class="fas fa-info-circle prod-info" productinfo="'+ result.product_id +'" onclick="productInfo(event);" style="right: 5px; top: 5px; color: '+infoIconColor+'; font-size:20px; position: absolute;z-index: 99;top: 10px;right: 10px;cursor: pointer;"></i>';
			
			html  += '<a dataproduct="'+ result.product_id +'" href="'+ result.href +'" class="product" data-toggle="modal" onclick="productdata(this)" >';
			html += '<div class="img" style="background-image: url()"><img src="'+ result.thumb +'"></div>';
			html += '<div class="text"><div class="title">'+ result.heading_title +'</div><div class="desc">'+ result.model +'</div>';
			if (result.price) {
            html += '<p class="price">'; 
			if(!result.special){
                 html += format(taxObj.calculate(result.price,result.tax_class_id,posData.config_tax),posData.currency_code);
             } else {
				 html += '<span class="price-new">'+ format(taxObj.calculate(result.special,result.tax_class_id,posData.config_tax),posData.currency_code) +'</span> <span class="pts-price-old">'+ format(taxObj.calculate(result.price,result.tax_class_id,posData.config_tax),posData.currency_code) +'</span>'; 
				 
			 }
			 
				  html +='</p>';
        }

		html +='</div></a></div>';
			$('#posproduct').append(html);
			}else{
			   $('#posbarcode').modal('hide');
			   html  = '<h2 class="product-area">{{ text_popup_msg }}</h2>';
			   $('#posproduct').append(html);
			}
		$('#posbarcode').modal('hide');
          document.getElementById('barcode').value='';		
 } 
} 
//add to cart 
var cart_pro_total = 0;
var cart_pro_sub_total = 0;
var por_order_count = 0;
var por_add_check = [];
var pos_cart_item = [];

function validateProductOptions(product_id){
	var productData = posData.products[product_id];
	var productOptions=productData.options;
	var optionsArr=[];
	var optionObj={};
	var optionObject={};
	var optArr=[];
	var optObj={}
	var returnstatus=true;
	var options = $('#optionData').serializeArray();
	$.each(options,function(key,val){
			optionObj={}
			tempId=	val['name'].split("-");
			if(tempId[0] == 'checkbox'){
				optionObject[tempId[1]]= [];
			}
			optionObj.product_option_id=tempId[1];
			optionObj.value=val.value;
			optionsArr.push(optionObj);	
	});
	
	$.each(options,function(key,val1){
			tempId1 = val1['name'].split("-");
		if(tempId1[0] == 'checkbox'){
			optionObject[tempId1[1]].push(val1.value);
		} else {
			optionObject[tempId1[1]] = val1.value;
		}
	});
	
	if(typeof productData.options != 'undefined'){
		$.each(productData.options,function(key,optData){
			optionsts=true;
			optObj={};
			if(typeof optionsArr !='undefined'){
				$.each(optionsArr,function(k1,v1){
					if(optData.product_option_id==v1.product_option_id){
					optObj.product_option_id=v1.product_option_id;
					optObj.value=v1.value;
					optionsts=false;
					} 
				});	
			}
			if(optionsts){
				optObj.product_option_id=optData.product_option_id;
				optObj.value='';
			}
			optArr.push(optObj);
		});
	}
	var errorData={};
	var errorData={};
	var errorDataObj={};
	var json={};
	var tempObj={};
	var option_id;
	if(typeof productData.options !='undefined'){
		$.each(productData.options,function(kk1,vv1){
			if(typeof optArr!='undefined' ){
				$.each(optArr,function(kk2,vv2){
					if(vv1.product_option_id == vv2.product_option_id){
						if(parseInt(vv1.required) && vv2.value==''){
						errorDataObj[vv1.product_option_id]= vv1.name+' required!';
						returnstatus=false;
						} 
					}
				});
			}
		});
	}
	errorData.option=errorDataObj;
	json.error=errorData;	
$('.alert-dismissible, .text-danger').remove();
$('.form-group').removeClass('has-error');
	if (json['error']) {
		if (json['error']['option']) {
			for (i in json['error']['option']) {
				var element = $('#input-option' + i.replace('_', '-'));

				if (element.parent().hasClass('input-group')) {
					element.parent().after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
				} else {
					element.after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
				}
			}
		}

		if (json['error']['recurring']) {
			$('select[name=\'recurring_id\']').after('<div class="text-danger">' + json['error']['recurring'] + '</div>');
		}

		// Highlight any found errors
		$('.text-danger').parent().addClass('has-error');
	}
var optionFilterData={'options':optionObject,'option_status':returnstatus};
	return optionFilterData;
}


function add_to_cart(event=''){
	let product_id=event.attributes['option-product-id'].value;
	var returnstatus= validateProductOptions(product_id);
		if(returnstatus.option_status){
			var productData = posData.products[product_id];
			productData.filter_options=returnstatus.options;
			var cartToCartData = addToCartProducts(productData);
			var cartProducts = CartObj.getProducts();
			var cartProductHTML = getCartProductHTML();

			cartProductData.push(productData);
			localStorage.setItem("cartData", JSON.stringify(cartProductData));
			var cartData = JSON.parse(localStorage.getItem("cartData"));
			couponApply(0);
			PlaySound();
			$("#modalPosItem-close").click();
		}
}
function showDetail(cart_id){

	  if( $('.pos-pro-detail').hasClass("active-pro-dit")){
		  $('.pos-pro-detail').hide();
		  $('.pos-pro-detail'+cart_id).removeClass("active-pro-dit");
	  }
	if( $('#pos-pro-detail'+cart_id).hasClass("active-pro-dit")){
	  $('#pos-pro-detail'+cart_id).hide();
	  $('#pos-pro-detail'+cart_id).removeClass("active-pro-dit");
	 }else{
	 $('#pos-pro-detail'+cart_id).show();
	 $('#pos-pro-detail'+cart_id).addClass("active-pro-dit");
	 $('#pos-pro-detail'+cart_id).addClass("pos-pro-detail");
	 }
}

	
function sub_qty(cart_id){
			let cartProducts = JSON.parse(localStorage.getItem("pos_cart"));
			let minimum=1;
			if(cartProducts.length){
			var localvar=[];
			pos_cart=[];
			$.each(cartProducts,function(key,val){
				if(val.cart_id==cart_id){
				if(typeof posData.products[val.product_id] != 'undefined'){
					minimum = posData.products[val.product_id].minimum;
				}
				if(val.quantity > minimum ){
				val.quantity= parseInt(val.quantity)-1;
				} 
					localvar.push(val);
					pos_cart.push(val);
				} else {
					pos_cart.push(val);
					localvar.push(val);
				}
			});

			localStorage.setItem("pos_cart",JSON.stringify(localvar));
			getCartProductHTML();
			PlaySound();
			}
couponApply(0);
	// let sub_pro_qty = $('#pos-pro-qty'+add_pro_id).val();
	// if(sub_pro_qty > 1){
	//	sub_pro_qty-=1;
		
	// let total_pro_price = parseFloat(pos_pos_price) * sub_pro_qty;
	// $('#pro-total-price'+add_pro_id).empty();
	// $('#pro-total-price'+add_pro_id).append('$'+total_pro_price);
   //  $('#pos-pro-qty'+add_pro_id).val(sub_pro_qty);
	// pos_cart_item[add_pro_id].qty = sub_pro_qty;
	// cart_pro_total -= parseFloat(pos_pos_price);
	//  cart_pro_sub_total -= parseFloat(pos_pos_price); 
	// $('#pos-subtotal').empty();
	// $('#pos-subtotal').append('$'+cart_pro_sub_total);
	// $('#pos-total').empty();
	// $('#pos-total').append('$'+cart_pro_total);
	 //}
	}
function add_qty(cart_id){
let cartProducts = JSON.parse(localStorage.getItem("pos_cart"));
if(cartProducts.length){
var localvar=[];
pos_cart=[];
$.each(cartProducts,function(key,val){
	if(val.cart_id==cart_id){
	val.quantity= parseInt(val.quantity)+1;
		localvar.push(val);
		pos_cart.push(val);
	} else {
		pos_cart.push(val);
		localvar.push(val);
	}
});


localStorage.setItem("pos_cart",JSON.stringify(localvar));
getCartProductHTML();
PlaySound();
}
couponApply(0);
	}
function conform_remove(cart_id){
	 $('#cofrow'+cart_id).removeAttr("style");
	 if($(".pos-remove-confirmation").hasClass("pos-remove-conf")){
	 $('#cofrow'+cart_id).removeClass("pos-remove-conf");
	 }
	}
function conform_remove_yes(cart_id){
	removeProductFromCart(cart_id);
	let pos_qnt = $('#pos-pro-qty'+cart_id).val();
	// cart_pro_total -= parseFloat(pos_pos_price * pos_qnt);
	//  cart_pro_sub_total -= parseFloat(pos_pos_price * pos_qnt);
	 $('#remove_row'+cart_id).remove();
	 if(por_order_count > 0){
	 por_order_count--;	  
	// $('#pos-subtotal').empty();
	// $('#pos-subtotal').append('$'+cart_pro_sub_total);
	 $('#pos-total').empty();
	// $('#pos-total').append('$'+cart_pro_total);
	$('#pos-order-count').empty();
	$('#pos-order-count').append('New Order ('+por_order_count+')');
	 pos_cart_item.pop(cart_id);
	por_add_check.pop(cart_id);	 
	}
	}
function conform_remove_no(cart_id){
	$('#cofrow'+cart_id).addClass("pos-remove-conf");
	}	
$(document ).delegate( "#pos-delete-cart", "click", function() {
if(localStorage.getItem("pos_cart") == null){
errorMessage('Warning: Cart is empty!',2000);
} else {
	var cnfresult = confirm("{{ text_delete_cart_confirm_msg }}");
	if(cnfresult == true){
		pos_cart=[];
		emptyCart();
		getCartProductHTML();
		$("#total").text(format('0',posData.currency_code));
		$("#yousaveddiscount").text(format('0',posData.currency_code));
		$("#yousubtotal").text(format('0',posData.currency_code));
		$("#youreward").text('0');
	}
}
});	
function PlaySound() {
		  var sound = ('https://myteknoland.com/MTL/extension/purpletree_pos/image/catalog/pos/beep-07a.wav');
		  var audio = new Audio(sound);
            audio.play();
      }
//End add to cart

// Category
  var categorybutton=['0'];
 $(document).on("click","#allcategory",function(){
 $('.product-category-area').remove();
		categorybutton=['0'];
		categoryHTML (posData.allTopCategory);
    });
	
	 $(document).on("click","#backCategory",function(e){
	 var cnt=1;
if(typeof backcategory!='undefined'){
	backcategory=false;
	cnt=2;
}	 
$('.product-category-area').remove();
let cate_id=0;
let len =categorybutton.length;
if(typeof categorybutton[len-cnt]!='undefined'){
		cate_id=categorybutton[len-cnt];
	}
	categorybutton.pop();
	backChildCategory (cate_id);
    });
	
function categoryHTML (data){
 $('.product-area').remove();
 //$('#pos_serch_div').remove();
  html='<div id="backCategory" class="product-area" style="width: 100%;height: 34px;"><span class="btn btn-success" style="float: right;">{{ text_categories_back_btn }}</span></div>';
  $('#posproduct').append(html);
		var childcats = [];
		$.each(data, function(index, category){ 		
		  html  = '<div class="allcategorys product-area"><div top_category="'+category.top_category_id+'" id="'+ index +'" onClick="childCategory(this.attributes.top_category.value);">'+ category.name +'</div>';
		  
		   if(category.children.length > 0){ 
				html += '<div dataid="'+ index +'" onClick="subcategory(this);"><i class="fa fa-file pull-right"></i></div>';
		   } 
		  html += '</div>';
		  $('#posproduct').append(html);
		});
}	

function backChildCategory (data){
$('.product-category-area').remove();
products = categoryToProduct (data);
posFuntions.posCategoryProduct(products,'all');

var parent_cate_id = data;
var categoryData=[];
	 $.each(posData.allCategories, function(index, data){
		 if( data.parent_id== parent_cate_id ){
				 $.each(posData.allCategoriesDescription, function(index, cateDesc){
					 if(cateDesc.category_id == data.category_id){
						data.top_category_id = data.category_id;
						data.name = cateDesc.name;
					 }
				 });
		 data.children = ptsSubCategory (data.category_id);
		 categoryData.push(data);
		 }
	 });
	 categoryHTML (categoryData);
} 
	
	
	function categoryToProduct (category_id){
		products=[];
		if(typeof posData.getProducttoCategory!='undefined'){
			$.each(posData.getProducttoCategory, function(index, categoryToProduct){
			if(categoryToProduct.category_id==category_id){
					$.each(posData.products, function(index, product){
						if(categoryToProduct.product_id==index){
						products.push(product);
						}
					});
				}
			});
		}
		return products;
	}
	
	
function childCategory (data){
backcategory=true;
$('.product-category-area').remove();
products = categoryToProduct (data);
posFuntions.posCategoryProduct(products,'all');

if(categorybutton.length==0){
	categorybutton.push('0');
}
categorybutton.push(data);
var parent_cate_id = data;
var categoryData=[];
	 $.each(posData.allCategories, function(index, data){
		 if( data.parent_id== parent_cate_id ){
				 $.each(posData.allCategoriesDescription, function(index, cateDesc){
					 if(cateDesc.category_id == data.category_id){
						data.top_category_id = data.category_id;
						data.name = cateDesc.name;
					 }
				 });
		 data.children = ptsSubCategory (data.category_id);
		 categoryData.push(data);
		 }
	 });
	 categoryHTML (categoryData);
} 


function ptsSubCategory (data){
var parent_cate_id = data;
var categoryData=[];
	 $.each(posData.allCategories, function(index, data){
		 if( data.parent_id== parent_cate_id ){
				 $.each(posData.allCategoriesDescription, function(index, cateDesc){
					 if(cateDesc.category_id == data.category_id){
						data.top_category_id = data.category_id;
						data.name = cateDesc.name;
					 }
				 });
		 categoryData.push(data);
		 }
	 });
return categoryData;
}

// Category

// Discount code
var discountpopup_cnt = 0;
$(document).delegate( "#discountpopup", "click", function() {
	   if(getCartTotalProduct() != 0){
			$('#discountpopup').attr('data-target','#discountproduct');
			$('#pos_discount_value').val('');
			$('#discount-error').remove();
			
		} else {
		$('#discountpopup').attr('data-target','');
		$('#discount-error').remove();
			errorMessage('{{ text_error_msg }}',1000);
		}	
		discountpopup_cnt++;
		if(discountpopup_cnt == 1){
		$('#discountpopup').trigger('click');
		}
  });
  
function pos_discount(){
        var discount_type = $('#pos_discount_type').val();
        var discount_value = $('#pos_discount_value').val();
	    if(discount_value != '' && discount_value != 0){
			 if(discount_type == 1){
				tax_type = 'Fixed';
				tax_title = 'Discount (Fixed)'
			} else {
				tax_type = 'percentage';
				tax_title = 'Discount ('+discount_value+'%)';
			}
			
	var CustomDicountData = JSON.parse(sessionStorage.getItem("discount"));
	if(CustomDicountData == null){
	CustomDicountData = [];
	}
	let custom_discount = {
					'type' : tax_type,
					'code' : 'tax',
                    'title' : tax_title,
                    'value' : discount_value,
                    'sort_order' :1
				};
	CustomDicountData.push(custom_discount);
	sessionStorage.setItem("discount", JSON.stringify(CustomDicountData));
	setOrderItems();
	$('#discountproduct').modal('hide');
	successMessage('{{ text_discount_success_msg }}',4000);
	   } else{
	       $('#discount-error').remove();
	       $('#discount_error').append('<span id="discount-error">{{ text_discount_error_msg }}</span>');
	       error.style.color = "red"; 
	   }
    }
  // End Discount code
  
  // Coupon code
  var discountcouponpopup_cnt = 0;
$(document).delegate( "#discountcouponpopup", "click", function() {
  if(getCartTotalProduct() != 0){
	$('#discountcouponpopup').attr('data-target','#discountbycoupon');
	discountcouponpopup_cnt++;
	
	 $('#coupan-error').remove();
	 if(posData.coupon.length){
		$('#coupon_list').empty();	
			html = '';
		$.each(posData.coupon,function(couponKey,couponVal){
			html += '<div class="row">';
			html += '<div class="text-left pts-coupon" >';
			html += '<input type="radio" name="pos_coupon" value="'+couponVal.code+'" id="pos_coupon_'+couponVal.code+'" /></div>';		 
			html += '<div class="text-left pts-coupon" > <label for="pos_coupon_'+couponVal.code+'" >'+couponVal.code+'</label></div>	';	 
			html += '<div class="text-left pts-coupon" ><label for="pos_coupon_'+couponVal.code+'" >'+couponVal.name+'</label></div>	';
			html += '</div>';
		});
		$('#coupon_list').append(html);	
		if(discountcouponpopup_cnt == 1){
		$('#discountcouponpopup').trigger('click');
		}
	 }
	} else {
	$('#discountcouponpopup').attr('data-target','');
	$('#coupan-error').remove();
	errorMessage('{{ text_error_msg }}',1000);
	}
  });
  function pos_coupon(){
     var pos_coupon = $('#coupon_value').serializeArray();
	 if(pos_coupon.length){
		$.each(pos_coupon,function(ckey,cval){
			if(cval.name = 'pos_coupon'){
			 let pos_coupon = cval.value;
			if(pos_coupon){
			sessionStorage.setItem("coupon_code", JSON.stringify(pos_coupon));
			couponApply();
			}
			}
		});
	 }
  }
  
  function couponApply(message_show=1){
			var pos_coupon = JSON.parse(sessionStorage.getItem('coupon_code'));
  			var couponData = couponObj.getCoupon(pos_coupon);
			if(couponData != null){
				var coupon_discount = couponObj.getTotal(pos_coupon);
				$('#discountbycoupon').modal('hide');
		setTimeout(function(){
		if(message_show){
				successMessage('{{ text_coupon_success_msg }}',2000);
			}
			let coupon = {
					'coupon_code' : couponData.code,
					'code' : 'coupon',
                    'title' : 'Coupon  ('+couponData.code+')',
                    'value' : -parseFloat(coupon_discount),
                    'sort_order' :1
				};
			sessionStorage.setItem("coupon", JSON.stringify(coupon));
			setOrderItems();
			},1000);
	
			} else {
			if(message_show){
				errorMessage('{{ text_coupon_warning_msg }}',2000);
			}
			}
  }
  // End Coupon code
  // Custom tax code
  var taxpopup_cnt = 0;
  $(document ).delegate( "#taxpopup", "click", function() {
  if(getCartTotalProduct() != 0){
	 $('#taxpopup').attr('data-target','#customtaxapply');
		$('#pos_tax_title').val('');
		$('#pos_tax_value').val('');
	 	 $('#tax-error').remove();
		 taxpopup_cnt++;
		 if(taxpopup_cnt == 1){
		$('#taxpopup').trigger('click');
		}
		 } else {
		  $('#taxpopup').attr('data-target','');
	 	 $('#tax-error').remove();
	errorMessage('{{ text_error_msg }}',1000);
	}
  });
  function pos_customtax(){
    var pos_tax_title = $('#pos_tax_title').val();
	 var pos_tax_type = $('#pos_tax_type').val();
	 var pos_tax_value = $('#pos_tax_value').val();
	 if(pos_tax_title != '' && pos_tax_value != ''){
	 if(pos_tax_type == 1){
		tax_type = 'Fixed';
		tax_title = pos_tax_title+" (Fixed)";
	} else {
		tax_type = 'percentage';
		tax_title = pos_tax_title+" ("+pos_tax_value+"%)";
	}
	//sessionStorage.removeItem("custom_tax");
	//sessionStorage.setItem("custom_tax", JSON.stringify(custom_tax));
	var CustomTaxData = JSON.parse(sessionStorage.getItem("custom_tax"));
	if(CustomTaxData == null){
	CustomTaxData = [];
	}
	let custom_tax = {
					'type' : tax_type,
					'code' : 'tax',
                    'title' : tax_title,
                    'value' : pos_tax_value,
                    'sort_order' :1
				};
	CustomTaxData.push(custom_tax);
	sessionStorage.setItem("custom_tax", JSON.stringify(CustomTaxData));
	setOrderItems();
	$('#customtaxapply').modal('hide');
	successMessage('{{ text_customtax_success_msg }}',4000);
	//var TaxData = JSON.parse(sessionStorage.getItem("custom_tax"));
	
	} else {
	    $('#tax-error').remove();
	    $('#tax_error').append('<span id="tax-error">{{ text_customttax_error_msg }}</span>');
	}
  }
  
   // Custom tax code
   // Custom additional charge code
   var  additionalcharge_cnt =0;
     $(document ).delegate( "#additionalcharge", "click", function() {
	
  if(getCartTotalProduct() != 0){
		$('#additionalcharge').attr('data-target','#additionalchargeapply');
		$('#pos_charge_title').val('');
		$('#pos_charge_value').val('');
		$('#charge-error').remove();
		additionalcharge_cnt++;
		if(additionalcharge_cnt == 1){
		$('#additionalcharge').trigger('click');
		}
		 } else {
		 $('#additionalcharge').attr('data-target','');
		$('#charge-error').remove();
		errorMessage('{{ text_error_msg }}',1000);
	}
	 

  });
  
 function pos_additionalcharge(){
 
     var pos_charge_title = $('#pos_charge_title').val();
	 var pos_charge_type = $('#pos_charge_type').val();
	 var pos_charge_value = $('#pos_charge_value').val();
	 if(pos_charge_title != '' && pos_charge_value != ''){
		 if(pos_charge_type == 1){
			charge_type = 'Fixed';
			charge_title = pos_charge_title+" (Fixed)";
		}else{
			charge_type = 'percentage';
			charge_title = pos_charge_title+" ("+pos_charge_value+"%)";
		}
		
	var CustomChargeData = JSON.parse(sessionStorage.getItem("custom_charge"));
	if(CustomChargeData == null){
	CustomChargeData = [];
	}
	let custom_charge = {
					'type' : charge_type,
					'code' : 'tax',
                    'title' : charge_title,
                    'value' : pos_charge_value,
                    'sort_order' :1
				};
	CustomChargeData.push(custom_charge);
	sessionStorage.setItem("custom_charge", JSON.stringify(CustomChargeData));
	setOrderItems();
	$('#additionalchargeapply').modal('hide');
	successMessage('{{ text_customtcharge_success_msg }}',4000);
	
	}else{
	    $('#charge-error').remove();
	    $('#charge_error').append('<span id="charge-error">{{ text_customtcharge_error_msg }}</span>');
	}
  }
  
   // Custom additional charge code
    // Add new customer code 
	var customerpopup_cnt = 0;
	$(document ).delegate( "#customerpopup", "click", function() {
      $('#customerpopup').attr('data-target','#customersearch');
	  $('#customer_search').val('');
	   $('.pts_customer_row').remove();
	   customerpopup_cnt++;
	   if(customerpopup_cnt == 1){
		$('#customerpopup').trigger('click');
		}
   }); 
   
    $('#customer_search').keyup(function () {
    $.ajax({
       url: 'index.php?route=extension/purpletree_pos/pos/home|autocomplete&filter=' + this.value,
	   type: 'get',
      dataType: 'json',
      success: function(json) {
	  $('.pts_customer_row').remove();
	 if(typeof json != 'undefined') {
		sessionStorage.setItem("customerSData", JSON.stringify(json));
		$.each(json,function(key,val){
		if(val.firstname != ''){
			html = '<div class="col-sm-12 row overflow-auto pts_customer_row" onclick="ptscustomerdata('+val.customer_id+')"><div class="col-sm-4">'+ val.firstname+' '+ val.lastname  +' ('+ val.telephone +')</div><div class="col-sm-8">'+ val.email +'</div></div>';
			$('#customer_lists').append(html);
			}
		});
	}
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
  });
  
  
  // $('#custom_place_order').click(function () {
   // var paymentMethod = JSON.parse(sessionStorage.getItem("split_payment"));
   // if(paymentMethod.splitpay === false){
  // var grand_total = JSON.parse(sessionStorage.getItem("grand_total"));
     // sessionStorage.removeItem('split_payment');
		// split_payment ={
					// 'cash':0,
					// 'card':0,
					// 'custom':grand_total.value,
					// 'splitpay':false
					
				// };
		// sessionStorage.setItem("split_payment",JSON.stringify(split_payment));
		// placeOrder();
	// }
	
	   // if(paymentMethod.splitpay === true){
			// placeOrder();
		// }
		// });
  
   $('#credit_place_order').click(function () {
    var paymentMethod = JSON.parse(sessionStorage.getItem("split_payment"));
   //if(paymentMethod.splitpay === false){
   var grand_total = JSON.parse(sessionStorage.getItem("grand_total"));
   sessionStorage.removeItem('split_payment');
	split_payment ={
					'cash':0,
					'card':grand_total.value,
					'custom':0,
					'splitpay':false
				};
	sessionStorage.setItem("split_payment",JSON.stringify(split_payment));
	placeOrder();
	//}
	// if(paymentMethod.splitpay === true){
			// placeOrder();
		// }
  });
  $('#cash_place_order').click(function () {
   var paymentMethod = JSON.parse(sessionStorage.getItem("split_payment"));
   //if(paymentMethod.splitpay === false){
  var grand_total = JSON.parse(sessionStorage.getItem("grand_total"));
	sessionStorage.removeItem('split_payment');
	split_payment ={
					'cash':grand_total.value,
					'card':0,
					'custom':0,
					'splitpay':false
				};
	sessionStorage.setItem("split_payment",JSON.stringify(split_payment));
	placeOrder();
	//}
	// if(paymentMethod.splitpay === true){
			// placeOrder();
		// }
  });
  
  function placeOrder() { 
  Promise.all([getOrderPrepareData()]).then(function(values){
   var orderData = values[0];
  if(orderData.status !='error'){
    $.ajax({
      url: 'index.php?route=extension/purpletree_pos/pos/home|addorder',
	  type: 'post',
      dataType: 'json',
	  data: orderData, 
	  beforeSend: function() {
		$('#pts-spinner').css('display','block');
		$('#pts-check').css('display','none');
		$('#submit_order').addClass('inactivesubmit');
			},
	  complete: function() {
		$('#pts-spinner').css('display','none');
		$('#pts-check').css('display','block');
		$('#submit_order').removeClass('inactivesubmit');
			},
      success: function(json) {
	  if(json.status=='success'){
	  	sessionStorage.setItem('order_id', json.order_id);
		sessionStorage.removeItem('payment_address');
		sessionStorage.removeItem('payment_method');
		sessionStorage.removeItem('shipping_address');
		sessionStorage.removeItem('shipping_method');
		sessionStorage.removeItem('totals');
		sessionStorage.removeItem('currentCustomerData');
		sessionStorage.removeItem('cartProductsData');
		
		localStorage.removeItem('pos_cart');
		sessionStorage.removeItem('custom_tax');
		sessionStorage.removeItem('admin_tax');
		sessionStorage.removeItem('discount');
		sessionStorage.removeItem('custom_charge');
		sessionStorage.removeItem('coupon');
		sessionStorage.removeItem('coupon_code');
		sessionStorage.removeItem('updateCartProduct');
		localStorage.removeItem('cartData');
		sessionStorage.removeItem('grand_total');
		sessionStorage.removeItem('split_payment');
		sessionStorage.removeItem('sub_total');
		localStorage.removeItem('custom_product');
		refreshPage();
		successMessage(json.message,4000);
		pos_cart=[];
		localStorage.removeItem('pos_cart');
		getCartProductHTML();
		$('#cashtotalamnt').modal('hide');
		$('#totalamnt').modal('hide');
		$('#totalamntcus').modal('hide');
		$('#pos_checkout').modal('hide');
		split_payment ={
			'cash':0,
			'card':0,
			'custom':0,
			'splitpay':false		
		};
		sessionStorage.setItem("split_payment",JSON.stringify(split_payment));
	  }
	   if(json.status=='error'){
		alert(json.message);
	   }
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
	} else {
	  errorMessage(orderData.message,2000);
	return false;
	}
	});
  };
  
  function successMessage(message,timeout){
	  toastr.options.preventDuplicates = true;
	  toastr.options.closeButton = true;
	  toastr.options.timeOut = timeout;
	  toastr.success(message,'',{"iconClass": 'pts-success-color'});
  }
  
    function errorMessage(message,timeout){
	  toastr.options.preventDuplicates = true;
	  toastr.options.closeButton = true;
	  toastr.options.timeOut = timeout;
	  toastr.success(message,'',{"iconClass": 'pts-error-color'});
  }

function ptscustomerdata(customer_id){
  var customerSearchData = JSON.parse(sessionStorage.getItem("customerSData"));
  if (typeof customerSearchData != 'undefined'){
	$.each(customerSearchData,function(key,val){
		if(val.customer_id==customer_id){
		sessionStorage.removeItem('shipping_address');
		sessionStorage.removeItem('payment_address');
		sessionStorage.setItem("currentCustomerData",JSON.stringify(val));
		if(posData.config_tax_customer=='shipping'){
		if(Object.keys(val.default_address).length){
			//sessionStorage.setItem("shipping_address",JSON.stringify(val.default_address));
		}
		}
		if(posData.config_tax_customer=='payment'){
		//sessionStorage.setItem("payment_address",JSON.stringify(val.default_address));
		}
		setPosTax(taxObj);
		posFuntions.posProduct(posData.products,'all'); 
		getCartProductHTML();
		 $('.close').trigger('click');
		 successMessage('{{ text_customer_search_success_msg }}',2000);
		}
	});
  }
}
   
   // Back to Customer search form
   $('#pts_selectbutton').on('click', function(){
     $('.close').trigger('click');
     $('#customerpopup').trigger('click');
   });
   
   // Add new customer form open
   $('#addnewcustomers').on('click', function(){
       $('.close').trigger('click');
	   var chars = 'abcdefghijklmnopqrstuvwxyz1234567890';
       var string = '';
	   var strings = '';
       for(var ii=0; ii<16; ii++){
       string += chars[Math.floor(Math.random() * chars.length)];
		}
		for(var ii=0; ii<16; ii++){
       strings += chars[Math.floor(Math.random() * chars.length)];
		}
		//alert(string + '@gmail.com');
	   $('#email').val(string + '@'+ strings +'.com');
	   var customer_group = posData.ptscustomergroup_name;
	   $.each(customer_group, function( index, customer_groups ) {
	   html = '<option value="'+ customer_groups.customer_group_id +'">'+ customer_groups.name +'</option>';
	   $('#customer_group').append(html);
	   });
   });
   function pts_addnewcustomerbutton(event){ 
      $('#telephone-error').remove();  
      var customer_groups = $('#customer_group').val();
      var f_name          = $('#f_name').val();
      var l_name          = $('#l_name').val();
      var email           = $('#email').val();
      var telephone       = $('#telephone').val();
	  var password        = "admin123";
	  
	  if(f_name.length == ''){
	  $('#error_customer_msg').append('<span id="telephone-error">Please enter firstname.</span> ');
	  return false;
	  }if(f_name.length < 3){
	  $('#error_customer_msg').append('<span id="telephone-error">Firstname must be minimum 3 character .</span> ');
	  return false;
	  }else if(l_name == ''){	  
	  $('#error_customer_msg').append('<span id="telephone-error">Please enter lastname.</span>');
	  return false;
	  }else if(email == ''){
	  $('#error_customer_msg').append('<span id="telephone-error">Please enter email.</span>');
	  return false;
	  }else if(email.indexOf('@')<= 0){
	  $('#error_customer_msg').append('<span id="telephone-error">Invalid @ position in email.</span>');
	  return false;
	  }else if((email.charAt(email.length-4)!='.') && (email.charAt(email.length-3)!='.')){
	  $('#error_customer_msg').append('<span id="telephone-error">Invalid . position at 4,3 in email.</span>');
	  return false;
	  }else if(telephone == ''){
	   $('#telephone-error').remove();
	   $('#error_customer_msg').append('<span id="telephone-error">Please enter the Telephone number.</span>');
	  return false;
	  }else if(isNaN(telephone)){
	   $('#telephone-error').remove();
	   $('#error_customer_msg').append('<span id="telephone-error">Please enter only Numeric value.</span>');
	  return false;
	  }else{
		    var addcustomer = {
			   customer_group_id : customer_groups,
			   firstname         : f_name,
			   lastname          : l_name,
			   email             : email,
			   telephone         : telephone,
			   password          : password
		    };
    $.ajax({
       url: 'index.php?route=extension/purpletree_pos/pos/home|allCustomerData',
	   type: 'post',
      dataType: 'json',
	  data: addcustomer,
      success: function(json) {
	  if(json.status=='success'){
	  sessionStorage.removeItem('shipping_address');
	  sessionStorage.setItem('payment_address', JSON.stringify(default_store_address));
	  sessionStorage.setItem('currentCustomerData', JSON.stringify(json.customer_fulldata));
	  $('.close').trigger('click');
	  
	  }
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
	}  
}
    // Add new customer code 
	
	// Hold cart button code
$('#add_hold_order').on('click', function(){
	if(localStorage.getItem("pos_cart") != null){
	$('#pos_cart_hold').attr('data-target','#hold-order');
	let cartProducts = JSON.parse(localStorage.getItem("pos_cart"));
	let message = $('#hold_order_msg').val();
	if(sessionStorage.getItem("currentCustomerData") != null){
		let customerData = JSON.parse(sessionStorage.getItem("currentCustomerData"));
		customerData={'id':customerData.customer_id,'name':customerData.name,'message':message,'customerData':customerData};
		let hold_cart=[];
			let hold_cart_data={};
			hold_cart_data.data=customerData;
			hold_cart_data.cart_data=cartProducts;
			
			if(localStorage.getItem("hold_cart") != null){
			let hold_cart = JSON.parse(localStorage.getItem("hold_cart"));
			
					hold_cart.push(hold_cart_data);
			localStorage.setItem("hold_cart", JSON.stringify(hold_cart));
			localStorage.removeItem('pos_cart');
			if($('#holdorders').hasClass('active')){
				holdCartArea();
			}
			getCartProductHTML();
			} else {
			hold_cart.push(hold_cart_data);
			localStorage.setItem("hold_cart", JSON.stringify(hold_cart));
			localStorage.removeItem('pos_cart');
			if($('#holdorders').hasClass('active')){
				holdCartArea();
			}
			getCartProductHTML();
			}
		} else {
		let customerData={'id':0,'name':'Guest','message':message,'customerData':''};
		let hold_cart=[];
			let hold_cart_data={};
			hold_cart_data.data=customerData;
			hold_cart_data.cart_data=cartProducts;
			
			if(localStorage.getItem("hold_cart") != null){
			let hold_cart = JSON.parse(localStorage.getItem("hold_cart"));
			
					hold_cart.push(hold_cart_data);
			localStorage.setItem("hold_cart", JSON.stringify(hold_cart));
			localStorage.removeItem('pos_cart');
			if($('#holdorders').hasClass('active')){
				holdCartArea();
			}
			getCartProductHTML();
			} else {
			hold_cart.push(hold_cart_data);
			localStorage.setItem("hold_cart", JSON.stringify(hold_cart));
			localStorage.removeItem('pos_cart');
			if($('#holdorders').hasClass('active')){
				holdCartArea();
			}
			getCartProductHTML();
			}
		}
	 
		} else {
		$('#pos_cart_hold').attr('data-target','');
		errorMessage('Warning: Cart is empty!',2000);
		}
		$('#hold-order').modal('hide');	
});
var pos_cart_hold_cnt = 0;
$(document ).delegate( "#pos_cart_hold", "click", function() {
$('#pos_cart_hold').attr('data-target','');
	if(localStorage.getItem("pos_cart") != null){
	 $('#hold_order_msg').val('');
	$('#pos_cart_hold').attr('data-target','#hold-order');	 
		} else {
		$('#pos_cart_hold').attr('data-target','');
		errorMessage('Warning: Cart is empty!',2000);
		}
		pos_cart_hold_cnt++;
		if(pos_cart_hold_cnt == 1){
			$('#pos_cart_hold').trigger('click');
		}
		
	});
	function stoppedTyping(){
	    var holdcart = $('#pos_hold_data').val();
        if(holdcart.length > 0) { 
            document.getElementById('hold_carts').disabled = false; 
        } else { 
            document.getElementById('hold_carts').disabled = true;
        }
    }
	$('#hold_carts').on('click', function(){
	  var holdcart = $('#pos_hold_data').val();
	  var cartdata = "data";
	  var allcartholddata = {
	     reson : holdcart,
		 cartdata: cartdata
	  };
	   var pos_allcartholddata = JSON.stringify(allcartholddata);
	   sessionStorage.setItem("allcartholddata", pos_allcartholddata);
	   
	        if(holdcart != ''){
	                 emptyCart();
					 getCartProductHTML();
					 $('#pos-to-cart').empty();
					 por_order_count = 0;
					 $('#pos-order-count').empty();
					 $('#pos-order-count').append('New Order ('+por_order_count+')');
					 por_add_check = [];
					  cart_pro_total = 0;
					  cart_pro_sub_total = 0;
					// $('#pos-subtotal').empty();
					 //$('#pos-subtotal').append('$0');
					 //$('#pos-total').empty();
					 //$('#pos-total').append('$0');
	         }
			 $('.close').trigger('click');
	});
	
	// click to hold order button code

	$(document).delegate( "#holdorders", "click", function() {
	  holdCartArea();
	});
		
	function holdCartArea(){
		$('.product-area').remove();
	if(localStorage.getItem('hold_cart') != null){
	var hold_cart = JSON.parse(localStorage.getItem('hold_cart'));
	$.each(hold_cart,function(key,val){
	let customer_id = val.data.id;
	let customer_name = val.data.name;
	let text = val.data.message;
	const message = text.substring(0,20);
	
	html = '<div class="product-container product-area" style="position:relative;" data-type="holdorders"><div class="product" data-toggle="modal"><div class="text"><div class="title">'+customer_name+'</div><div class="desc">'+message+'</div><div class="pts-hold-icons" style="display: inline-flex;"><div class="pts-width pts-restore-data" onclick="holdCartRestore('+key+')"><i class="fa fa-undo" aria-hidden="true"></i></div><div class="pts-width pts-delete-data" onclick="holdCartDelete('+key+')"><i class="fa fa-trash" aria-hidden="true"></i></div></div></div></div></div>';
	$('#posproduct').append(html);
	getCartProductHTML();
	});
	}else {
	html = '<div class="product-container product-area">{{ text_no_record_found }}</div>';
	$('#posproduct').append(html);
	}
	}
	
	function holdCartRestore(key){
	if(localStorage.getItem('hold_cart') != null){
		var hold_cart = JSON.parse(localStorage.getItem('hold_cart'));
		localStorage.setItem("pos_cart", JSON.stringify(hold_cart[key].cart_data));
	let resetHoldCartData=[];
		$.each(hold_cart,function(hold_key,val){
			if(parseInt(hold_key) != parseInt(key)){
				resetHoldCartData.push(val);
			}
		
		});
		if(resetHoldCartData.length){
		if(hold_cart[key].data.id){
		sessionStorage.setItem("currentCustomerData", JSON.stringify(hold_cart[key].data.customerData));	
		} else {
		sessionStorage.removeItem('currentCustomerData');
		}
		localStorage.setItem("hold_cart", JSON.stringify(resetHoldCartData));	
		
		} else {
		if(hold_cart[key].data.id){
		sessionStorage.setItem("currentCustomerData", JSON.stringify(hold_cart[key].data.customerData));	
		} else {
		sessionStorage.removeItem('currentCustomerData');
		}
		localStorage.removeItem('hold_cart');
		}
		holdCartArea();
		getCartProductHTML();
	}
	
	}
	
	function holdCartDelete(key){
		if(localStorage.getItem('hold_cart') != null){
		var hold_cart = JSON.parse(localStorage.getItem('hold_cart'));
	let resetHoldCartData=[];
		$.each(hold_cart,function(hold_key,val){
			if(parseInt(hold_key) != parseInt(key)){
				resetHoldCartData.push(val);
			}
		
		});
		if(resetHoldCartData.length){
		if(hold_cart[key].data.id){
		sessionStorage.setItem("currentCustomerData", JSON.stringify(hold_cart[key].data.customerData));	
		} else {
		sessionStorage.removeItem('currentCustomerData');
		}
		localStorage.setItem("hold_cart", JSON.stringify(resetHoldCartData));	
		
		} else {
		if(hold_cart[key].data.id){
		sessionStorage.setItem("currentCustomerData", JSON.stringify(hold_cart[key].data.customerData));	
		} else {
		sessionStorage.removeItem('currentCustomerData');
		}
		localStorage.removeItem('hold_cart');
		}	
		holdCartArea();
		getCartProductHTML();
	}
	}
	
// Point of sale orders
  $(document).delegate( "#pos_orders", "click", function() {
	posOrders();
	});
	
	function posOrders(){
	$('.product-area').remove();
	var pos_orders_data = [];
	let message = '{{ text_record_not_found }}';
	if(localStorage.getItem("pos_orders_data") != null){
	var pos_orders_data = JSON.parse(localStorage.getItem("pos_orders_data"));
	}
		{% if isPosAdmin %}
		{% set col = 3 %}
		{% else %}
		{% set col = 4 %}
		{% endif %}
			html = '<div class="product-list-container product-area" data-type="pos_orders"><div class="row product"><div class="row col-sm-12 pts-filter">';
			html += '<div class="col-sm-{{ col }}"><div class="form-group">  <label class="control-label float-left" for="order-date-start">{{ text_filter_date_start }}</label>  <div class="input-group date"><input type="date" name="order_date_start" value="2022-03-01" placeholder="Date Start" data-date-format="YYYY-MM-DD" id="order-date-start" class="form-control">  <span class="input-group-btn"> </span></div></div></div>';
			html += '<div class="col-sm-{{ col }}"><div class="form-group">  <label class="control-label float-left" for="order-date-end">{{ text_filter_date_end }}</label>  <div class="input-group date"><input type="date" name="order_date_end" value="2022-03-01" placeholder="Date End" data-date-format="YYYY-MM-DD" id="order-date-end" class="form-control">  <span class="input-group-btn"> </span></div></div></div>';
			{% if isPosAdmin %}
			html += '<div class="col-sm-{{ col }}"> <div class="form-group"><label class="control-label float-left">{{ text_filter_agent }}</label> <div class="input-group"><select id="pos_order_agent" name="pos_order_agent" class="form-control">';

			html += '<option value=""></option>';
			if(typeof posData.agents != 'undefined'){
				if(posData.agents.length){
					$.each(posData.agents,function(agentKey,agentData){
						html += '<option value="'+agentData.customer_id+'">'+agentData.agent_name+'</option>';	
					});
				}
			}

			html += '</select><span class="input-group-btn"> </span></div></div></div>';
			{% endif %}
			
			html += '<div class="text-right col-sm-{{ col }} mt-4 mb-2"><button id="order-filter" class="btn btn-primary" onclick="getOrderFiler();">{{ text_filter }}</button></div></div><div class="col-sm-12"> <div class="table-responsive"><table class="table"><thead><tr><td>{{ text_filter_orderid }}</td><td>{{ text_customer_name }}</td><td>{{ text_order_status }}</td><td>{{ text_date_added }} </td><td>{{ text_order_total }}</td><td>{{ text_action }} </td></tr></thead>';
			if(pos_orders_data.length){
			$.each(pos_orders_data,function(key,val){
			html += '<tr ><td>'+val.order_id+'</td><td>'+val.customer+'</td><td>'+val.order_status+'</td><td>'+val.date_added+'</td><td>'+format(val.total,posData.currency_code)+'</td><td><div onclick="getPosOrderDetail('+val.order_id+');" type="button" class="btn btn-primary"><i class="far fa-eye"></i></div></td></tr>';
			});
			} else {
			html += '<tr><td colspan="6">'+message+'</td></tr>';
			}
			html += '</tbody>';
			html += '</table></div></div>';
			html += '</div></div>';
			$('#posproduct').append(html);
			getCartProductHTML();
			
		
	}
	// Point of sale orders
	// Point of sale return orders
	$(document).delegate( "#returnorders", "click", function() {
	  posReturnOrders();
	});
	
		function posReturnOrders(){
	$('.product-area').remove();
	var return_orders = [];
	let message = '{{ text_record_not_found }}';
	if(localStorage.getItem("return_orders") != null){
	var return_orders = JSON.parse(localStorage.getItem("return_orders"));
	}
		{% if isPosAdmin %}
		{% set col = 3 %}
		{% else %}
		{% set col = 4 %}
		{% endif %}
		html = '<div class="product-list-container product-area" data-type="returnorders"><div class="row product"><div class="row col-sm-12 pts-filter">';
		html += '<div class="col-sm-{{ col }}"><div class="form-group">  <label class="control-label float-left" for="return-order-date-start">{{ text_filter_date_start }}</label>  <div class="input-group date"><input type="date" name="return_order_date_start" value="2022-03-01" placeholder="Date Start" data-date-format="YYYY-MM-DD" id="return-order-date-start" class="form-control">  <span class="input-group-btn"> </span></div></div></div>';
		html += '<div class="col-sm-{{ col }}"><div class="form-group">  <label class="control-label float-left" for="return-order-date-end">{{ text_filter_date_end }}</label>  <div class="input-group date"><input type="date" name="return_order_date_end" value="2022-03-01" placeholder="Date End" data-date-format="YYYY-MM-DD" id="return-order-date-end" class="form-control">  <span class="input-group-btn"> </span></div></div></div>';
		{% if isPosAdmin %}
		html += '<div class="col-sm-{{ col }}"> <div class="form-group"><label class="control-label float-left" for="input-date-end">{{ text_filter_agent }}</label> <div class="input-group"><select id="pos_return_agent" name="pos_return_agent" class="form-control">';

		html += '<option value=""></option>';
		if(typeof posData.agents != 'undefined'){
			if(posData.agents.length){
				$.each(posData.agents,function(agentKey,agentData){
					html += '<option value="'+agentData.customer_id+'">'+agentData.agent_name+'</option>';	
				});
			}
		}

		html += '</select><span class="input-group-btn"> </span></div></div></div>';
		{% endif %}
		
		html += '<div class="text-right col-sm-{{ col }} mt-4 mb-2"><button id="return-order-filter" class="btn btn-primary" onclick="getReturnOrderFiler();">{{ text_filter }}</button></div></div><div class="col-sm-12"> <div class="table-responsive"><table class="table"><thead><tr><td>{{ text_return_id }}</td><td>{{ text_filter_orderid }}</td><td>{{ text_customer_name }}</td><td>{{ text_date_added }}</td><td>{{ text_return_status }}</td><td>{{ text_action }}</td></tr></thead>';
			if(return_orders.length){
			$.each(return_orders,function(key,val){
			html += '<tr ><td>'+val.return_id+'</td><td>'+val.order_id+'</td><td>'+val.customer_name+'</td><td>'+val.date_added+'</td><td>'+val.return_status+'</td><td><div onclick="getPosReturnOrderDetail('+val.return_id+');" type="button" class="btn btn-primary"><i class="far fa-eye"></i></div></td></tr>';
			});
			} else {
			html += '<tr><td colspan="6">'+message+'</td></tr>';
			}
			html += '</tbody>';
			html += '</table></div></div>';
			html += '</div></div>';
			$('#posproduct').append(html);
			getCartProductHTML();
			
		
	}
	// Point of sale return orders
	// Point of sale orders detail
	
	function getPosOrderDetail(order_id){
			$('.product-area').remove();
			var pos_orders_data = [];
			var return_product={};
	if(localStorage.getItem("pos_orders_data") != null){
	var pos_orders_data = JSON.parse(localStorage.getItem("pos_orders_data"));
	}
	
	if(localStorage.getItem("return_product") != null){
		var return_products = JSON.parse(localStorage.getItem("return_product"));
		$.each(return_products[order_id],function(retrunkey,returnval){	
		return_product[returnval.product_id]=returnval;
		});	
	}
			if(pos_orders_data){
		$.each(pos_orders_data,function(key,val){
			
			if(parseInt(val.order_id) == parseInt(order_id)){
			html = '<div class="product-list-container product-area" data-type="pos_orders"><div class="row product">';
			
			html += '<div class="col-sm-12 pts-order-summary">';
			html += '<span class="pts-heading">#'+val.order_id+'</span>';
			html += '<span class="pts-title">{{ text_filter_orderid }}:#'+val.order_id+'</span> ';
			html += '<span class="pts-title">{{ text_Date }}: '+val.date_added+'</span> ';
			html += '<span class="pts-title">{{ text_customer_name }}: '+val.customer+'</span> ';
			html += '<span class="pts-title">{{ text_payment_method }}: '+val.payment_method+'</span> ';
			html += '</div>';
			
		
			html += '<form  id="selected_product">';
			html += '<div class=" row col-sm-12"><div class="table-responsive"> <table class="table table-bordered"><thead><tr><td class="text-left"><input type="checkbox" onClick="checkAll(this);" id="product_id" name="product_id" ></td><td class="text-left">{{ text_name }}</td><td class="text-left">{{ text_model }}</td><td class="text-left">{{ text_quantity }}</td><td class="text-left">{{ text_price }}</td><td class="text-left">{{ text_amount }}</td></tr></thead>';
			
			html += '<tbody>';
			$.each(val.products,function(productkey,productval){
			html += '<tr ><td class="text-left">';
			if(typeof return_product[productval.product_id] == 'undefined'){
			html += '<input type="checkbox" id="id_'+productval.product_id+'" name="name_'+productval.product_id+'" value="1">';
			}
			html += '</td><td class="text-left">'+productval.name+'</td><td class="text-left">'+productval.model+'</td><td class="text-left">'+productval.quantity+'</td><td class="text-left">'+format(productval.price,posData.currency_code)+'</td><td class="text-left">'+format(productval.total,posData.currency_code)+'</td></tr>';
			});
			
			html += '</tbody>';
			html += '</table></div></div>';
			
			html += '</form>';
			
			html += '<div class="col-sm-12 text-right mb-2"><div onClick="printPreview('+val.order_id+');" type="button" class="btn btn-primary">{{ text_receipt }}</div><div onClick="returnOrdList('+val.order_id+');" type="button" class="btn btn-primary ml-2">{{ text_return }}</div></div>';
			html +='<div class="col-sm-12" id="return_order_list"></div>';
			html += '</div></div>';
			$('#posproduct').append(html);
			getCartProductHTML();
			}
			});
		}
	}
	// Point of sale orders detail
	
	// Point of sale return orders detail

	function getPosReturnOrderDetail(return_id){
			$('.product-area').remove();
			var pos_orders_data = [];
			var return_orders={};
	if(localStorage.getItem("return_orders") != null){
	var return_orders = JSON.parse(localStorage.getItem("return_orders"));
	}
		
		if(Object.keys(return_orders).length){
		$.each(return_orders,function(key,return_order){
		if(parseInt(return_order.return_id) == parseInt(return_id)){
			html = '<div class="product-list-container product-area" data-type="returnorders"><div class="row product">';
			
			html += '<div class="col-sm-12 pts-order-summary">';
			html += '<span class="pts-heading">#'+return_order.return_id+'</span>';
			html += '<span class="pts-title">Return Id: #'+return_order.return_id+'</span> ';
			html += '<span class="pts-title">Order Id: #'+return_order.order_id+'</span> ';
			html += '<span class="pts-title">Return Date: '+return_order.date_added+'</span> ';
			html += '<span class="pts-title">Order Date: '+return_order.date_ordered+'</span> ';
			html += '<span class="pts-title">Name: '+return_order.product+'</span> ';
			html += '<span class="pts-title">Model: '+return_order.model+'</span> ';
			html += '<span class="pts-title">Quantity: '+return_order.quantity+'</span> ';
			html += '<span class="pts-title">Opened: ';
			if(return_order.opened){
			html += '{{ text_opened }}';
			}else {
			html += '{{ text_unopened }}';
			}
			html += '</span> ';
			html += '<span class="pts-title">Customer Name: '+return_order.customer_name+'</span> ';
			html += '<span class="pts-title">Email: '+return_order.email+'</span>';
			html += '<span class="pts-title">Telephone: '+return_order.telephone+'</span>';
			html += '<span class="pts-title">'+return_order.return_reason+'</span>';
			html += '<span class="pts-title">Comment: '+return_order.comment+'</span>';
			html += '<span class="pts-title">Return Action: '+return_order.return_action+'</span>';
			html += '<span class="pts-title">Return Status: '+return_order.return_status+'</span>';
			html += '</div>';

			html +='<div class="col-sm-12" id="return_order_list"></div>';
			html += '</div></div>';
			$('#posproduct').append(html);
			}
		});
			}
		
	}
	// Point of sale return orders detail
	
	
	
	
	
	// Remove holdorder data
	 function removecartdata(){
	 //alert("hhhh");
	 $('.product-area').remove();
	 var session_data = sessionStorage.removeItem('allcartholddata');
	   html = '<div class="product-area"><div class="holdorder"><h1 style="color: #676767;">Hold Orders</h1></div><div id="no-order-result"><p class="no-order-font">No orders on hold</p></div></div>';
	   $('#posproduct').append(html);
	 }
	// function holddata(){
		// var pos_getcartholddata = JSON.parse(sessionStorage.getItem("allcartholddata"));
		// var data=pos_getcartholddata;
		
		// } 
function uploadImage (e) {
	var node = e;

	$('#form-upload').remove();

	$('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" /></form>');

	$('#form-upload input[name=\'file\']').trigger('click');

	if (typeof timer != 'undefined') {
    	clearInterval(timer);
	}

	timer = setInterval(function() {
		if ($('#form-upload input[name=\'file\']').val() != '') {
			clearInterval(timer);

			$.ajax({
				url: 'index.php?route=tool/upload',
				type: 'post',
				dataType: 'json',
				data: new FormData($('#form-upload')[0]),
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function() {
					$(node).button('loading');
				},
				complete: function() {
					$(node).button('reset');
				},
				success: function(json) {
					$('.text-danger').remove();

					if (json['error']) {
						$(node).parent().find('input').after('<div class="text-danger">' + json['error'] + '</div>');
					}

					if (json['success']) {
						alert(json['success']);

						$(node).parent().find('input').val(json['code']);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	}, 500);
	
	
}
function posFullscreen() {
  var isInFullScreen = (document.fullscreenElement && document.fullscreenElement !== null) ||
        (document.webkitFullscreenElement && document.webkitFullscreenElement !== null) ||
        (document.mozFullScreenElement && document.mozFullScreenElement !== null) ||
        (document.msFullscreenElement && document.msFullscreenElement !== null);

    var posElm = document.documentElement;
    if (!isInFullScreen) {
        if (posElm.requestFullscreen) {
            posElm.requestFullscreen();
        } else if (posElm.mozRequestFullScreen) {
            posElm.mozRequestFullScreen();
        } else if (posElm.webkitRequestFullScreen) {
            posElm.webkitRequestFullScreen();
        } else if (posElm.msRequestFullscreen) {
            posElm.msRequestFullscreen();
        }
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
    }
}

 function getTaxes() {
		tax_data = {};
		$.each(CartObj.getProducts(),function(key,product){
			if(product.tax_class_id){
				tax_rates = taxObj.getRates(product.price, product.tax_class_id);
				$.each(tax_rates,function(key1,tax_rate){
					if(!tax_data[tax_rate.tax_rate_id]){
						tax_data[tax_rate.tax_rate_id] = (tax_rate.amount * product.quantity);
					} else {
						tax_data[tax_rate.tax_rate_id] += (tax_rate.amount * product.quantity);
					}
				
				});
			}
		});
		return tax_data;
	}

	


//function getTotal(total) {
	//$.each(total.taxes,function(key,value){
		//if (value > 0) {
		//		total['totals'].push({
		//			'code'      : 'tax',
		//			'title'      : taxObj.tax.getRateName(88),
		//			'value'      : value,
		//			'sort_order' : 1
		//		});

		//		total.total += value;
		//	}
//	});
//}

// Tax Calculation

function cartDetails(){
let cartProducts = JSON.parse(localStorage.getItem("pos_cart"));
if(localStorage.getItem("pos_cart") == null){
$('#pts-cart-product-detail').empty();
errorMessage('Warning: Cart is empty. please add product to cart!',2000);
}
$('.pts-cart-total').remove();
$('#cart-sub-detail').remove();
$("#pts-cart-detail").attr('data-target','');
if(cartProducts && cartProducts.length >0 ){
$("#pts-cart-detail").attr('data-target','#pts_cart_detail');
if(sessionStorage.getItem("totals") != null){
Totalitem = getTotalProducts();
Totalqty = getTotalProductQty();
var totals = JSON.parse(sessionStorage.getItem("totals"));
		if(typeof totals!= 'undefined'){
			$.each(totals,function(key,val){
					total_css='';
					if(val.code == 'total'){
					 total_css='pts-total-css';
					}
				 var html='';
				    html += '<div class="pts-cart-total">';
					html += '<div class="d-flex flex-row">';
					html += '<div  class="flex-fill text-left pts-title-width '+total_css+'">'+val.title+'</div>';
					
					html +=  '<div  class="flex-fill text-right pts-value-width '+total_css+'">'+format(val.value,posData.currency_code)+'</div>';
					if(val.removable){
					let customVar = {
					'id':val.id,
					'title':val.custom_title
					};
					html +=  '<div custom_id="'+val.id+'" custom_title="'+customVar.title+'" onclick="removeCustomAll(this)" class="flex-fill text-left pts-close-width '+total_css+'"><div class="close" type="button"><span>×</span></div></div>';
					} else {
					html +=  '<div  class="flex-fill text-left pts-close-width '+total_css+'"></div>';
					}
					html += '</div>';
					html += '</div>';
				$('#pts-cart-product-totals').append(html);
			});
			html ='';
			html += '<div id="cart-sub-detail" class="d-flex flex-row flex-wrap align-items-center"><div  class="flex-fill text-left"> <span > </span><span ></span></div><div  class="flex-fill text-left"><span >{{ text_total_items }}:</span><span id="itemCount">'+Totalitem+'</span></div><div  class="flex-fill text-left"> <span >{{ text_total_quantity }}:</span> <span>'+Totalqty+'</span></div> <div  class="flex-fill text-left"><span ></span> </div></div>';
			$('#pts-cart-product-detail').append(html);
		}
		}
		}
		
}
function removeCustomAll(e){
var title = e.attributes.custom_title.value;
var id = e.attributes.custom_id.value;
if(sessionStorage.getItem(title) != null){
 	var data = JSON.parse(sessionStorage.getItem(title));
	var discount_temp = [];	
	$.each(data,function(key,val){
	if(parseInt(key) != parseInt(id) && title != val.custom_title){
		discount_temp.push(val);
	}
	});
	if(discount_temp.length){
		sessionStorage.setItem(title, JSON.stringify(discount_temp));
	} else {
		sessionStorage.removeItem(title);
	}
	getCartProductHTML();
	cartDetails();
	}
}

function addCustomProduct(){
	        $('.pos-custom-pro').remove();
	        html ='';
			html +='<div class="pos-custom-pro">';
			html +='<form id="pts_submit_custom_prod">';
			html +='<div><label for="name" class="control-labe">{{ text_name }}:</label><input type="text" id="custom_prod_name" name="custom_prod_name" class="form-control mb-3"></div>';
			html +='<div><label for="price" class="control-labe">{{ text_price }}:</label><div class="input-group mb-3 input-group-lg"><div class="input-group-prepend"><div class="input-group-text"><input  type="checkbox" id="includedTax" name="includedTax"></div><span  class="input-group-text">'+getCurrencySymbol(posData.currency_code)+'</span></div><input id="custom_prod_price" type="number" name="custom_prod_price" class="form-control form-control-lg><input id="target-price" type="hidden"></div></div>';
			html +='<div><label for="quantity" class="control-labe">{{ text_quantity }}</label><div  class="input-group mb-3 input-group-lg"><input type="number" id="custom_prod_quantity" name="custom_prod_quantity" class="form-control form-control-lg"><input  id="target-quantity" type="hidden"></div></div>';
			html +='<div><label  for="tax_class_id" class="control-labe">{{ text_tax_class }}</label><select id="tax_class_id" name="tax_class_id" class="form-control form-control-lg"><option  value="0">---Select Tax Class---</option></select></div>';
			html +='</form>';
			html +='<div class="text-right"><button class="btn btn-success mt-3" onclick="custom_product_submit();">{{ text_submit }}</button></div>';
			html +='<div id="custmproderror" class="text-center"></div>';
			
			html +='</div>';
			$('#pts-custom-product').append(html);
			//$('#pos-to-cart').append(html);
			
		var tax_class = posData.taxclass;
	      html = '';
     $.each(tax_class, function(key,val) {
	      html += '<option value="'+ val.tax_class_id +'">'+ val.title +'</option>';
	      });
		   $('#tax_class_id').append(html);
			
}

function custom_product_submit(){
setPosTax(taxObj);
	var custom_prod = $('#pts_submit_custom_prod').serializeArray();
	
	var objCustomPorudct = {};
    for (var i = 0; i < custom_prod.length; ++i){
        var name = custom_prod[i].name;
        var value = custom_prod[i].value;
        objCustomPorudct[name] = custom_prod[i].value;
    }
	
		var includedTax = 0;
	if(typeof objCustomPorudct.includedTax != 'undefined'){
		includedTax = 1;
	}
	
	
      var custom_prod_name       = objCustomPorudct.custom_prod_name;
      var custom_prod_price      = objCustomPorudct.custom_prod_price;
      var custom_prod_quantity   = objCustomPorudct.custom_prod_quantity;
	  var custom_prod_tax_class  =  objCustomPorudct.tax_class_id;
	 
	  if(custom_prod_name != '' && custom_prod_price != '' && custom_prod_quantity  != ''){	  
		var custom_product=[];
	 if(localStorage.getItem("custom_product") != null){
		custom_product = JSON.parse(localStorage.getItem("custom_product"));
		}
		
		
	if(!includedTax){
		price =parseFloat(custom_prod_price);
	} else {
	var tax_rates = taxObj.getRates(parseFloat(custom_prod_price), parseInt(custom_prod_tax_class));
	if(tax_rates){
	var percent=0;
	var flat=0;
		$.each(tax_rates,function(ptskey,ptsval){
		if(ptsval.type == 'P'){
		  percent += parseFloat(ptsval.rate);
		}
		
		if(ptsval.type == 'F'){
		  flat += parseFloat(ptsval.rate);
		}	
		});	
		price = parseFloat(custom_prod_price);
		if(flat){
		price = price-flat;	
		} 
		if(percent){
		price = ((price*100)/(100+percent));
		}
	}
	}
	  var custom_product_obj = {
		  product_id: parseInt((Math.floor(Math.random() * 1000000) + 1000000).toString().substring(1)),
		  name:custom_prod_name,
		  model:custom_prod_name,
		  price:price,
		  quantity:custom_prod_quantity,
		  tax_class_id:custom_prod_tax_class
		  };
		  custom_product.push(custom_product_obj);
		  localStorage.setItem('custom_product', JSON.stringify(custom_product));
	var cartProducts = [];
	if(localStorage.getItem("pos_cart") != null){	  
      cartProducts = JSON.parse(localStorage.getItem("pos_cart"));
	}
	var options = [];
	let customer_id=0;
	if(sessionStorage.getItem('currentCustomerData') != null){
	currentCustomerData = JSON.parse(sessionStorage.getItem('currentCustomerData'));
	customer_id=currentCustomerData.customer_id;
	}
	
	var posCartData = 
		{
			'cart_id':parseInt((Math.floor(Math.random() * 10000000000) + 10000000000).toString().substring(1)),
			'customer_id':customer_id,
			'product_id':custom_product_obj.product_id,
			'recurring_id':'0',
			'option':options,
			'quantity':custom_product_obj.quantity
		};
		cartProducts.push(posCartData);
	localStorage.setItem('pos_cart', JSON.stringify(cartProducts));	
	PlaySound();	
	getCartProductHTML();

	      $('#pos_custom_prod').modal('hide');
	  }else{
	     $('#custompro-error').remove();
	     $('#custmproderror').append('<span id="custompro-error" class="error_all">Please fill all fields</span>');
	  }

}
function orderSummery(){
	//var product_id=e.product_id;
let cartProducts = JSON.parse(localStorage.getItem("pos_cart"));
if(localStorage.getItem("pos_cart") == null){
errorMessage('Warning: Cart is empty. please add product to cart!',2000);
}
	 currentCustomerData='';
	 if(sessionStorage.getItem('currentCustomerData') != null){
	  	currentCustomerData = JSON.parse(sessionStorage.getItem('currentCustomerData'));
	  }
	  
	  if(currentCustomerData == 0 ){
		errorMessage('Warning: Pleasee select customer!',2000);
	  }
	  
$("#submit_order").attr('data-target','');
if(cartProducts && cartProducts.length >0 && currentCustomerData != ''){
$("#submit_order").attr('data-target','#pos_checkout');
//data-target="#pos_checkout"
	      $('#creditamnt').html(format(posData.order_total,posData.currency_code));
			$('#cashtotal').html(format(posData.order_total,posData.currency_code));
			$('#customtotal').html(format(posData.order_total,posData.currency_code));
			$('#addcmntcashtotal').html(format(posData.order_total,posData.currency_code));
	    Totalitem = getTotalProducts();
		Totalqty = getTotalProductQty();
	  $('#pts_order_detail').empty();
	  $('#pts_total_count').empty();
	  $('#pts-cart-total-block').empty();
	  if(sessionStorage.getItem('currentCustomerData') != null){
	  	currentCustomerData = JSON.parse(sessionStorage.getItem('currentCustomerData'));
		var customer_name = currentCustomerData.firstname+' '+currentCustomerData.lastname;
		$("#customer_name").text(customer_name);
	  }

	  html='';
	  html += '<div id="cart-product-block" class="overflow-auto">';
	  html += '<div class="table-responsive">';
	  html += '<table  class="table table-hover">';
	  html += '<thead  class="thead-light">';
	  html += '<tr>';
	  html += '<th class="text-left">{{ text_name }}</th>';
	  html += '<th class="text-center">{{ text_quantity }}</th>';
	  html += '<th class="text-right">{{ text_price }}</th>';
	  html += '<th class="text-right">{{ text_dis }}</th>';
	  html += '<th class="text-right">{{ text_disc_price }}</th>';
	  html += '<th class="text-right">{{ text_amount }}</th>';
	  html += '</tr>';
	  html += '</thead>';
	  html += '<tbody id="ord_summary" >';
	  html += '</tbody>';
	  html +='</table>';
	  html += '</div>';
	  html += '</div>';
	  $('#pts_order_detail').append(html);

	  html='';
	  $.each(CartObj.getProducts(),function(key,val){
	  html += '<tr  class="cart-product">';
	  html += '<td  class="text-left">';
	  html += '<div  class="d-flex flex-column align-items-start">';
	  html += '<div  class="flex-fill">'+val.name+'</div>';
	  html += '<div  class="flex-fill">{{ text_model }}:'+val.model+'</div>';
	  if(val.option){
		$.each(val.option,function(key21,option){
			html += '<small style="display:block">'+ option.name +': '+ option.value +'</small>';
		});
	  }
	  html += '</div>';
	  html += '</td>';
	  html += '<td  class="text-center">';
	  html += '<span><span >'+val.quantity+'</span></span>';
	  html += '</td>';
	          del= '';
			  del_close= '';
			 if(val.disc_value){
			   del= '<del>';
			   del_close= '</del>';
			 }
	  if(val.change_price){
	  html += '<td class="text-right"><span>'+del+''+format(taxObj.calculate(val.change_price,val.tax_class_id,posData.config_tax),posData.currency_code)+''+del_close+'</span></td>';
	  }else if(val.disc_value){
	   html += '<td class="text-right"><span>'+del+''+format(taxObj.calculate(val.real_price,val.tax_class_id,posData.config_tax),posData.currency_code)+''+del_close+'</span></td>';
	  }else{
	   html += '<td class="text-right"><span>'+format(taxObj.calculate(val.real_price,val.tax_class_id,posData.config_tax),posData.currency_code)+'</span></td>';
	  }
	  if(val.disc_value){
	  html += '<td  class="text-center"><span>'+val.disc_value+'</span></td>';
	  }else{
	  html += '<td  class="text-center"><span>-</span></td>';
	  }
	  if(val.disc_price){
	  html += '<td  class="text-center">'+format(taxObj.calculate(val.disc_price,val.tax_class_id,posData.config_tax),posData.currency_code)+'</td>';
	  }else{
	  html += '<td  class="text-center">-</td>';
	  }
	  html += '<td class="text-right"><span>'+format((taxObj.calculate(val.price,val.tax_class_id,posData.config_tax)*val.quantity),posData.currency_code)+'</span></td>';
	  html += '</tr>';
	});
	 $('#ord_summary').append(html);
	
	//$('.subtotal').remove();
		var orderItemArr=[];
		getTotal();
		var sub_total={
					'code' : 'sub_total',
                    'title' : '{{ text_sub_total }}',
                    'value' : getSubTotal(),
                    'sort_order' :1
		
		};
sessionStorage.setItem("sub_total", JSON.stringify(sub_total));
var sub_total = JSON.parse(sessionStorage.getItem("sub_total"));
var custom_tax = JSON.parse(sessionStorage.getItem("custom_tax"));
var admin_tax = JSON.parse(sessionStorage.getItem("admin_tax"));
var custom_charge = JSON.parse(sessionStorage.getItem("custom_charge"));
var discount = JSON.parse(sessionStorage.getItem("discount"));
orderItemArr.push(sub_total);
if(sessionStorage.getItem("coupon") != null){
	var coupon = JSON.parse(sessionStorage.getItem("coupon"));
	orderItemArr.push(coupon);
	posData.order_total = parseFloat(posData.order_total)+parseFloat(coupon.value);
}
// Admin Tax
	if(typeof admin_tax != "undefined"){
		$.each(admin_tax,function(tax_key,tax_val){
		orderItemArr.push(tax_val);
		});
	}
// Admin Tax
	var total_tax=0;
	if(custom_tax != null){
	$.each(custom_tax,function(key,TaxValue){
	if(TaxValue.type == 'percentage'){
	tax_value = (parseFloat(getSubTotal())*parseFloat(TaxValue.value))/100;
	} else {
	tax_value = parseFloat(TaxValue.value);
	}
	total_tax+=tax_value;
		let tax = {
					'code' : 'tax',
                    'title' : TaxValue.title,
                    'value' : tax_value,
                    'sort_order' :1
				};
		orderItemArr.push(tax);
		});
	}
	posData.order_total = parseFloat(posData.order_total)+parseFloat(total_tax);
	
	// Additional charges
		var total_charge=0;
	if(custom_charge != null){
	$.each(custom_charge,function(key,ChargeValue){
	if(ChargeValue.type == 'percentage'){
	charge_value = (parseFloat(getSubTotal())*parseFloat(ChargeValue.value))/100;
	} else {
	charge_value = parseFloat(ChargeValue.value);
	}
	total_charge+=charge_value;
		let charge = {
					'code' : 'tax',
                    'title' : ChargeValue.title,
                    'value' : charge_value,
                    'sort_order' :1
				};
		orderItemArr.push(charge);
		});
	}
	posData.order_total = parseFloat(posData.order_total)+parseFloat(total_charge);
	// Additional charges
	// discount code
		var total_discount=0;
	if(discount != null){
	$.each(discount,function(key,discountValue){
	if(discountValue.type == 'percentage'){
	discount_value = (parseFloat(posData.order_total)*parseFloat(discountValue.value))/100;
	} else {
	discount_value = parseFloat(discountValue.value);
	}
	total_discount+=discount_value;
		let discount = {
					'code' : 'tax',
                    'title' : discountValue.title,
                    'value' : '-'+discount_value,
                    'sort_order' :1
				};
		orderItemArr.push(discount);
		});
	}
	posData.order_total = parseFloat(posData.order_total)-parseFloat(total_discount);
	// discount code

		var grand_total={
					'code' : 'total',
                    'title': '{{ text_total }}',
                    'value': posData.order_total,
                    'sort_order': 9
		
		};
sessionStorage.setItem("grand_total", JSON.stringify(grand_total));
var grand_total = JSON.parse(sessionStorage.getItem("grand_total"));
orderItemArr.push(grand_total);
sessionStorage.setItem("totals", JSON.stringify(orderItemArr));
		// if(typeof orderItemArr!= 'undefined')
			// $.each(orderItemArr,function(key,val){
				// html='';
				// html+='<div class="subtotal '+val.title+'">';
				// html+='<div class="text">'+val.title+'</div>';
				// html+='<div class="price" >'+format(val.value,posData.currency_code)+'</div>';
				// html+='</div>';	
				// $('#pts-cart-total-block').append(html);	
			// });
			    // html='';
				// html+='<div id="pts_total_count_order" class="d-flex flex-row">';
	            // html+='<div  class="flex-fill text-left"><span>Total Items:'+Totalitem+'</span> </div>';
	            // html+='<div class="flex-fill text-left"><span>Total Quantity:'+Totalqty+'</span> </div>';
	            // html+='<div  class="flex-fill text-left"><span>Total Saving:</span> $0.00</div>';
	            // html+='</div>';
				//$('#pos-total').append(html);
				// $('#pts_total_count').append(html);
		// 
		if(typeof orderItemArr!= 'undefined'){
			$.each(orderItemArr,function(key,val){
	   html='';
	   html+='<div class="d-flex flex-row justify-content-center total">';
	   html+='<div class="d-flex flex-column align-items-start flex-fill m-1">'+val.title+'</div>';
	   html+=' <div  class="d-flex align-items-center justify-content-end flex-fill text-right m-1"><span>'+format(val.value,posData.currency_code)+'</span>';
	   html+='</div>';
	  html+='</div>';
	  $('#pts-cart-total-block').append(html);
	  });
	             html='';
				 html+='<div id="pts_total_count_order" class="d-flex flex-row">';
	             html+='<div  class="flex-fill text-left"><span>{{ text_total_items }}:'+Totalitem+'</span> </div>';
	             html+='<div class="flex-fill text-left"><span>{{ text_total_quantity }}:'+Totalqty+'</span> </div>';
	            // html+='<div  class="flex-fill text-left"><span>Total Saving:</span> $0.00</div>';
	             html+='</div>';
				 $('#pts-cart-total-block').append(html);
				
	  }
	  		if(typeof orderItemArr!= 'undefined'){
			
			$('.text-centerTotal').remove();
			$.each(orderItemArr,function(key,val){
				 html='';
				 html+='<div id="name" class="flex-grow-1 pts text-center'+val.title+'">'+format(val.value,posData.currency_code)+'</div>';
				 });
				 $('#pts_order_total_amnt').append(html);
			}
	
	store_address();	
}	
}
$('.collapse').collapse('hide');

function store_address(payment_select=false,shipping_select=false){
var pay_select='';
if(payment_select){
var pay_select='checked="checked"';
}
var ship_select='';
if(shipping_select){
var ship_select='checked="checked"';
}
//sessionStorage.setItem("shipping_address",JSON.stringify(shipping_address));
//sessionStorage.setItem("payment_address",JSON.stringify(payment_address));
payment_address= default_store_address;

if(sessionStorage.getItem('payment_address') != null){
payment_address = JSON.parse(sessionStorage.getItem('payment_address'));
}

$('.customer-ship-pay-address').remove();
	var html = '';
		html += '<div class="col-sm-12 card-body customer-ship-pay-address">';
		html += '<div class="row">';
		html += '<div class="col-sm-6">';
		html += '<div  id="pts-address" class="d-flex flex-wrap flex-row">';
		html += '<div  class="flex-fill">';
		html += '<div  class="custom-checkbox">';
		//html += '<input  type="checkbox" id="payment-address" class="custom-control-input" '+pay_select+'>';
		html += '<label class=""><h5>{{ text_payment_address }}</h5>';
		html += '</label>';
		html += '</div>';
		html += '<p><span >{{ text_payment_name }}:</span> '+payment_address.firstname+' '+payment_address.lastname+' </p>';
		html += '<p><span>{{ text_address1 }}</span>'+payment_address.address_1+'</p>';
		html += '<p><span >{{ text_address2 }}</span> '+payment_address.address_2+'</p>';
		html += '<p><span >{{ text_city }}</span> '+payment_address.city+'</p>';
		html += '<p><span >{{ text_postcode }}</span> '+payment_address.postcode+'</p>';
		html += '<p><span >{{ text_region_state }}</span>'+payment_address.zone_code+'</p>';
		html += '<p><span >{{ text_country }}</span>'+payment_address.country+'</p>';
		html += '</div>';
		html += '</div>';
		html += '</div>';
		if(sessionStorage.getItem('shipping_address') != null){
		shipping_address = JSON.parse(sessionStorage.getItem('shipping_address'));
		html += '<div class="col-sm-6">';
		html += '<div  class="flex-fill" id="pts-address">';
		html += '<div  class="custom-checkbox">';
		//html += '<input  type="checkbox" id="shipping-address" class="custom-control-input" '+ship_select+'>';
		html += '<label><h5 >{{ text_shipping_address }}</h5>';
		html += '</label>';
		html += '</div>';
		html += '<p><span >{{ text_payment_name }}</span> '+shipping_address.firstname+' '+shipping_address.lastname+' </p>';
		html += '<p><span>{{ text_address1 }}</span>'+shipping_address.address_1+'</p>';
		html += '<p><span >{{ text_address2 }}</span> '+shipping_address.address_2+'</p>';
		html += '<p><span >{{ text_city }}</span> '+shipping_address.city+'</p>';
		html += '<p><span >{{ text_postcode }}</span> '+shipping_address.postcode+'</p>';
		html += '<p><span >{{ text_region_state }}</span>'+shipping_address.zone_code+'</p>';
		html += '<p><span >{{ text_country }}</span>'+shipping_address.country+'</p>';
		html += '</div>';
		html += '</div>';
		}
		
		html += '</div>';
		html += '</div>';
		$('#collapseOne').append(html);
}

function customer_address(){
if(sessionStorage.getItem("currentCustomerData") != null){
	var customerData = JSON.parse(sessionStorage.getItem("currentCustomerData"));
	
	if(Object.keys(customerData.address).length){
	var html = '';
	$(".customer_address_list").remove();
	//$.each(addressess,function(key,value){
	html += '<form id="customer_select_address" class="customer_address_list"><div class="custom-control custom-radio" id="pts-addaddress">';			
	html += '</div>';
	html += '<div class="form-group"><div class="custom-control custom-checkbox"><input type="checkbox" id="setPaymentAddress" name="setPaymentAddress" class="custom-control-input"><label  for="setPaymentAddress" class="custom-control-label">Set as payment address</label></div></div>';

	html += '<div class="form-group "><div class="custom-control custom-checkbox"><input  type="checkbox" id="setShippingAddress" name="setShippingAddress" class="custom-control-input"><label for="setShippingAddress" class="custom-control-label">Set as shipping address</label></div></div>';
	html += '</form>';				
	html += '<div class="text-right customer_address_list"><button type="submit" onclick="select_address();" class="btn btn-primary">{{ text_select }}</button></div>';
	//});
	
	 $('#customer_addresses').append(html);
	var html1 ='';
	 	$.each(customerData.address,function(key,address){
		html1 += '<div class="customer_address_list"><input  type="radio" name="customer_address" value="'+key+'"required="" class="custom-control-input" id="'+key+'"><label  class="custom-control-label" for="'+key+'">'+address.firstname+','+address.lastname+','+address.address_1+','+address.address_2+','+address.city+','+address.postcode+','+address.zone_code+','+address.country+'</label><div>';
		});
		
	 $('#pts-addaddress').append(html1);
	 }
}

}

function select_address(){
var customer_select_address = $('#customer_select_address').serializeArray();
address_id='';
var customer_address='';
var setPaymentAddress='';
var setShippingAddress='';
if(customer_select_address){
	$.each(customer_select_address,function(key,val){
		if(val.name == 'customer_address' && typeof val.name != "undefined"){
			address_id=val.value;
		}
		if(val.name == 'setPaymentAddress' && typeof val.name != "undefined"){
			setPaymentAddress=val.value;
		}
		if(val.name == 'setShippingAddress' && typeof val.name != "undefined"){
			setShippingAddress=val.value;
		}
	});
}

if(address_id != ''){
	let customerData = JSON.parse(sessionStorage.getItem('currentCustomerData'));
	if(customerData.address != false){
		$.each(customerData.address,function(key,val){
			if(address_id == val.address_id){
				customer_address= {
			   'address_id' : val.address_id,
			   'firstname' : val.firstname,
			   'lastname' : val.lastname,
			   'company' : val.company,
			   'address_1' : val.address_1,
			   'address_2' : val.address_2,
			   'postcode' : val.postcode,
			   'city' : val.city,
			   'zone_id' : val.zone_id,
			   'zone' : val.zone,
			   'zone_code' : val.zone_code,
			   'country_id' : val.country_id,
			   'country' : val.country,
			   'iso_code_2' : val.iso_code_2,
			   'iso_code_3' : val.iso_code_3,
			   'address_format' : val.address_format,
			   'custom_field' : val.custom_field
			};
			}
		});
	}
	//var payment_address = default_store_address;
	//var shipping_address = default_store_address;
	if(setPaymentAddress){	
		payment_address= customer_address;
		sessionStorage.setItem("payment_address",JSON.stringify(customer_address));
	}

	if(setShippingAddress){
	var shipping_address= customer_address;
	sessionStorage.setItem("shipping_address",JSON.stringify(customer_address));
	}
	store_address();
	setPosTax(taxObj);
	orderSummery();
}
$('#addaddress').modal('hide');
	
}
   var country = posData.countries;
	      $.each(country, function( key, val ) {
	      html = '<option value="'+ val.country_id +'">'+ val.name +'</option>';
	      $('#country_id').append(html);
	      });
// Add new address
function submit_address(){
		var customer_id = '';
		var address = $('#pts_submit_add').serializeArray();
		var objAddnewaddress = {};
    for (var i = 0; i < address.length; ++i){
        var name = address[i].name;
        var value = address[i].value;
        objAddnewaddress[name] = address[i].value;
    }
		if(sessionStorage.getItem('currentCustomerData') != null){
		var customerData = JSON.parse(sessionStorage.getItem('currentCustomerData'));
		var customer_id = {name: "customer_id", value: customerData.customer_id };
		}
		address.push(customer_id);
		if(objAddnewaddress.firstname != '' && objAddnewaddress.lastname != '' && objAddnewaddress.address_1 != '' && objAddnewaddress.city != ''){
		setTimeout(function(){
	$.ajax({
       url: 'index.php?route=extension/purpletree_pos/pos/home|addAddress',
	   type: 'post',
	   dataType: 'json',
	   data: address,
       success: function(json) {
	   if(sessionStorage.getItem('currentCustomerData') != null){
		var customerData = JSON.parse(sessionStorage.getItem('currentCustomerData'));
		customerData.address=json.address;
	      sessionStorage.setItem('currentCustomerData', JSON.stringify(customerData));
		
		if(json.address_id){
				shipping_address=default_store_address;
				payment_address=default_store_address;
			if(json.setShippingAddress){
				sessionStorage.setItem("shipping_address",JSON.stringify(json.address[json.address_id]));
				shipping_address = json.address[json.address_id];
			}
			if(json.setPaymentAddress){
				sessionStorage.setItem("payment_address",JSON.stringify(json.address[json.address_id]));
				payment_address = json.address[json.address_id];
			}
			store_address(payment_address,shipping_address);
		}
		}
      }
    });
	},500);
	$('#addnewaddress').modal('hide');
	$("#pts_submit_add").trigger('reset');
		}else{
		$('#addnewaddress-error').remove();
	     $('#addnewaddresserror').append('<span id="addnewaddress-error" class="error_all">Please fill all required fields</span>');
		 //$('#addnewaddrsfirstname').append('<span id="addnewaddress-error" class="error_all">Please fill firstname fields</span>');
		}
   //localStorage.setItem("customer_newaddress", JSON.stringify(customer_newaddress));
   //var customer_newaddress = JSON.parse(localStorage.getItem("customer_newaddress"));
  var customer_newaddres = JSON.parse(sessionStorage.getItem("addnewaddress"));
      //$('#pts_submit_add').trigger('reset');
  
}

	function validaddnewaddress(){
	   
	  
	//$("#country_id").empty();
	// $('.form-horizontal input').keyup(function() {

        // var empty = false;
        // $('.form-horizontal input').each(function() {
            // if ($(this).val() == '') {
                // empty = true;
            // }
        // });

        // if (empty) {
            // $('#submit').attr('disabled', true);
        // } else {
            // $('#submit').attr('disabled', false);
        // }
		
	// });
	
	
	 // $("#cuurrentcust").remove(); 
	// $("#submit").remove();
	 // $("#guestc").remove();
	// var html = '';
	  // if(sessionStorage.getItem('currentCustomerData') != null){
	   // var html = '';
	  	// currentCustomerData = JSON.parse(sessionStorage.getItem('currentCustomerData'));
		// html += '<div id="cuurrentcust">';
		// html += '<button class="btn btn-primary" onClick="submit_address();" id="submit">{{ text_submit }}</button>';
	// html +='<div id="adnewaddress" class="text-left"></div>';
		// html += '</div>';
		//$("#rty").append(html);
		// $("#logincus").append(html);
	  // }
	  //else if((sessionStorage.getItem('currentCustomerData') != null)){
	     // html +='';
		  
	  //}
	  // else{
	   // var html = '';
	     // html += '<div id="guestc">';
		// html += '<button  class="btn btn-primary" data-dismiss="modal"onClick="guest_submit_address();" id="submit" >Submit</button>';
		// html += '</div>';
		 // $("#guestsub").append(html);
		  // }
	
	   //$("#pts_submit_add").trigger('reset');
	}


function guest_submit_address(){
	  // var customer_select_newaddress = $('#pts_submit_add').serializeArray();
	  // var customer_newaddress='';
	  // var setPaymentAddress1='';
	  // var setShippingAddress1=''; 
	 
	  // if(customer_select_newaddress){
	  // $.each(customer_select_newaddress,function(key,val){

		  // if(val.name == 'setPaymentAddress1' && typeof val.name != "undefined"){
			  // setPaymentAddress=val.value;
		  // }
		 // if(val.name == 'setShippingAddress1' && typeof val.name != "undefined"){
			  // setShippingAddress=val.value;
		  // }
	  // });
  // }
	var f_name = $('#firstname').val();
	var la_name = $('#lastname').val();
	var address1 = $('#address1').val();
	var address2 = $('#address2').val();
	var company = $('#company').val();
	var city = $('#city').val();
	var postcode = $('#postcode').val();
	var country = $('#country_id').val();
	var state = $('#zone_id').val();
	// var e = document.getElementById("state");
    // var state = e.value;
    // var e = document.getElementById("country");
    // var country = e.value;

   guest_address = {
			    'firstname' : 'Guest',
				'lastname' : 'User',
				'email' : 'test@gmail.com',
				'telephone' : 123456789,
			  shipping_address :{ 
			   'firstname' : f_name,
			   'lastname' : la_name,
			   'company' : company,
			   'address_1' : address1,
			   'address_2' : address2,
			   'postcode' : postcode,
			   'city' : city,
			   'state' : state,
			   'country' : country,
			   // 'zone_id' : ,
			   // 'zone' :,
			   // 'zone_code' :,
			   // 'country_id' : ,
			   // 'country' : ,
			   // 'iso_code_2' : ,
			   // 'iso_code_3' : ,
			   // 'address_format' : ,
			   // 'custom_field' : ,
			  }
			}

  //localStorage.setItem("guest_address", JSON.stringify(guest_address));
  //var guest_address = JSON.parse(localStorage.getItem("guest_address"));
			//var payment_address= guest_address.shipping_address;
			//var shipping_address= guest_address.shipping_address;
			//store_address(payment_address,shipping_address);
	$("#pts_submit_add").trigger('reset');
}

 $('#country_id').on('change', function() {
	 json = [];
	 var countryID = $(this).val();
	 var zone = posData.zone;
	 $.each(zone, function( key, val ) {
	    if(parseInt(countryID) == parseInt(val.country_id)){
		     json.push(val);
		    }
		    }); 
		   
		 html = '<option value="">{{ text_select }}</option>';
		    if (json.length && json != '') {
				  for (i = 0; i < json.length; i++) {
					  html += '<option value="' + json[i]['zone_id'] + '"';
					
					  if (json[i]['zone_id'] == '{{ zone_id }}') {
						  html += ' selected="selected"';
					  }
					{% if version4103 %}
					  html += '>' + json[i]['code'] + '</option>';
					  {% else %}
					  html += '>' + json[i]['name'] + '</option>';
					  {% endif %}
			  }
			  }
			   else {
				  html += '<option value="0" selected="selected">{{ text_none }}</option>';
			  }
			//$('#zone_id').append(html);
			 $('select[name=\'zone_id\']').html(html);
	 });
$('select[name=\'country_id\']').trigger('change');

function pos_split_payment(){
// will be delete
    var currencyData = globalPOS;
	//var currencyData= JSON.parse(localStorage.getItem("posData"));
	var orderItemArr =[];
	var grand_total={
					'code' : 'total',
                    'title': '{{ text_total }}',
                    'value': posData.order_total,
                    'sort_order': 9
		
		};
	sessionStorage.setItem("grand_total", JSON.stringify(grand_total));
var grand_total = JSON.parse(sessionStorage.getItem("grand_total"));
orderItemArr.push(grand_total);

	$('#split-payment').remove();
	
	html ='';
	html = '<div id="split-payment">';
	html += '<div class="modal-header">';
	if(typeof orderItemArr!= 'undefined'){
	$.each(orderItemArr,function(key,val){
	html += '<h2> Total Amount:'+format(val.value,posData.currency_code)+'</h2>';
	});}
	html += '<button class="close" type="button" data-dismiss="modal" aria-label="Close">&times;</button>';
	html += '</div>';
	html += '<div class="modal-body" style="margin-left: 25px;">';
	html += '<div class="form-group required">';					
	html += '<div  id="pts-split-payment">';

	html +=' </div>';
	html +='<div>';
	html +=' <label for="cash" class="control-labe">By cash</label>';
	html +='<div class="input-group mb-3 input-group-lg">';
	html +='<div class="input-group-prepend">';
	html +='<span  class="input-group-text">'+getCurrencySymbol(posData.currency_code)+'</span>';
	html +='</div>';
	html +='<input id="cash" type="number" formcontrolname="price" class="form-control form-control-lg" value=""><input id="target-price" type="hidden">';
	html +='</div>';
					
	html +=' <label for="card" class="control-labe">By card</label>';
	html +=' <div class="input-group mb-3 input-group-lg">';
	html +=' <div class="input-group-prepend">';
	html +='<span  class="input-group-text">'+getCurrencySymbol(posData.currency_code)+'</span>';
	html +='</div>';
	html +='<input id="card" type="number" formcontrolname="price" class="form-control form-control-lg" value=""><input id="target-price" type="hidden">';
	html +='</div>';
					
	html +='<label for="custom" class="control-labe">By custom payment </label>';
	html +='<div class="input-group mb-3 input-group-lg">';
	html +='<div class="input-group-prepend">';
	html +='<span  class="input-group-text">'+getCurrencySymbol(posData.currency_code)+'</span>';
	html +='</div>';
	html +='<input id="custom" type="number" formcontrolname="price" class="form-control form-control-lg" value=""><input id="target-price" type="hidden">';
	html +='</div>';

	html +='<div class="text-right">';
	html +='<button class="btn btn-primary" data-dismiss="modal" onclick="removeSplit();">Remove amount split</button>';
	html +='<button class="btn btn-primary" id="makesplit" data-dismiss="modal" onclick="makeSplit();"disabled="disabled">Make split</button>';
	html +='</div>';
				   
	html +='</div>';
	html +='</div>'; 
					
	html +='</div>';
	html +='</div>';
 
	$('#splt_pymnt').append(html);
	$('#makesplit').attr('disabled', true);
	
	 //var cash = document.getElementById('cash').value;
	// var card = document.getElementById('card').value;
	// var custom = document.getElementById('custom').value;
	 // var text1 = document.getElementById("price1").value;
    //document.getElementById("pricecash").value = val; 
	//document.getElementById("creditamnt").text = cash;
	// document.getElementById("Name3").value=text1;
           splt=[];
		   cash=0;
		   card=0;
		   custom=0;
		   if(sessionStorage.getItem("split_payment") != null){
		   var setpayments = JSON.parse(sessionStorage.getItem("split_payment"));
		   cash=setpayments.cash;
		   card=setpayments.card;
		   custom=setpayments.custom;
		   }
	        split_payment ={
						 'cash':cash,
						 'card':card,
						 'custom':custom,
						 'splitpay':false
				};
				
		$('#cash').val(cash);		
		$('#card').val(card);		
		$('#custom').val(custom);		
		   
		   	sessionStorage.setItem("split_payment",JSON.stringify(split_payment));
        var split_paymnt = JSON.parse(sessionStorage.getItem("split_payment"));
	
             // splt.push(split_paymnt);
			  
			// split_amount_total = 0;
			// $.each(splt,function(key,val){
			    // split_amount_total = parseFloat(val.cash)+parseFloat(val.card)+parseFloat(val.custom);
				
				// });
				
	// var remaining = parseFloat(posData.order_total-split_amount_total);
  $('#cash').keyup(function(e) {
     setPayment(e);
	 });
	  $('#card').keyup(function(e) {
     setPayment(e);
	 });
	  $('#custom').keyup(function(e) {
     setPayment(e);
	 });
}

	
	 
	 function setPayment(e){
		 var value = e.target.value;
		 pay_price=0;
		 if(value){
		 pay_price=parseFloat(value);
		 }
		 var type = e.target.id;
		 var payment_object = JSON.parse(sessionStorage.getItem('split_payment'));
         if(payment_object !== null){
		 if(type=='cash'){
		 payment_object.cash=pay_price;
		 } else if(type=='card'){
		 payment_object.card=pay_price;
		 } else if(type=='custom'){
		 payment_object.custom=pay_price;
		 }
	     }
		sessionStorage.setItem("split_payment",JSON.stringify(payment_object));
		payment_object = {};
		payment_object = JSON.parse(sessionStorage.getItem('split_payment'));
		grand_total = JSON.parse(sessionStorage.getItem('grand_total'));
		var total =parseFloat(payment_object.cash)+parseFloat(payment_object.card)+parseFloat(payment_object.custom);
		$('#makesplit').attr('disabled', false);
		if(parseFloat(grand_total.value) <= total){
			$('#makesplit').attr('disabled', false);
		} else {
			$('#makesplit').attr('disabled', true);
		}
		var reminder=0;
		if(parseFloat(grand_total.value) < total){
		reminder = total-parseFloat(grand_total.value);
		} 
	}

function makeSplit(){
	payment_object = JSON.parse(sessionStorage.getItem('split_payment'));
	if(payment_object !== null){
	if( payment_object.custom > 0){  
		$('.cash_place_order').hide();
		$('.credit_place_order').hide();
          //errorMessage('Warning: This payment method not allowed in split payment!',1000);
     }else{
	  $('.cash_place_order').show();
	  $('.credit_place_order').show();
	 }
	} else{
	  $('.cash_place_order').show();
	  $('.credit_place_order').show();
	 }
//$("splypymnt").addClass("intro");
$('.splypymnt').css('background', '#ff8c00');
$('.splypymnt').css('border-color', '#ff8c00');
	 //$(".cash_place_order").hide();
	 //$(".credit_place_order").hide();
	 //$(".add_cmnt_place_order").hide();
	 // var cash = document.getElementById('cash').value;
	 // var card = document.getElementById('card').value;
	 // var custom = document.getElementById('custom').value;
	 // var text1 = document.getElementById("price1").value;
    //document.getElementById("pricecash").value = val; 
	//document.getElementById("creditamnt").text = cash;
	// document.getElementById("Name3").value=text1;
           // splt=[];
	        // split_payment ={
						 // 'cash':cash,
						 // 'card':card,
						 // 'custom':custom
				// };
			// document.getElementById("pricecash").value = split_payment.cash;//document.getElementById("creditamnt").value = split_payment.cash;
			
			payment_object = JSON.parse(sessionStorage.getItem('split_payment'));
			if(payment_object){
			 split_payment ={
						 'cash':payment_object.cash,
						 'card':payment_object.card,
						 'custom':payment_object.custom,
						 'splitpay':true
				};
				
		   	sessionStorage.setItem("split_payment",JSON.stringify(split_payment));
			
			
			
			    html ='';

				html +=format(payment_object.card,posData.currency_code);
				 $('#creditamnt').html(html);
				 html='';
				 html +=format(payment_object.cash,posData.currency_code);
				 $('#cashtotal').html(html);
				 $('#addcmntcashtotal').html(html);
				 html ='';
				 html +=format(payment_object.custom,posData.currency_code);
				 $('#customtotal').html(html);
				 
			}
			
			
			// $('#cashtotal').html(split_payment.cash);
			// $('#addcmntcashtotal').html(split_payment.cash);
			// $('#customtotal').html(split_payment.custom);
			
			// sessionStorage.setItem("split_payment",JSON.stringify(split_payment));
        // var split_paymnt = JSON.parse(sessionStorage.getItem("split_payment"));
	
              // splt.push(split_paymnt);
			  
			// split_amount_total = 0;
			// $.each(splt,function(key,val){
			    // split_amount_total = parseFloat(val.cash)+parseFloat(val.card)+parseFloat(val.custom);
				
				// });
				
	// remaining = parseFloat(posData.order_total-split_amount_total);
	// sessionStorage.setItem("remaining",JSON.stringify(remaining));
        // var remaining = JSON.parse(sessionStorage.getItem("remaining"));
      
	  //if( remaining >=0){
		   // $('#makesplit').prop('disabled', true);
		 // }
		 // else{
			 // $('#makesplit').prop('disabled',false);
			  //}
	
}
function removeSplit(){
	payment_object = JSON.parse(sessionStorage.getItem('split_payment'));
	if(payment_object !== null){
	if( payment_object.custom == 0){  
		$('.cash_place_order').show();
		$('.credit_place_order').show();
          //errorMessage('Warning: This payment method not allowed in split payment!',1000);
     }else{
	  $('.cash_place_order').show();
	  $('.credit_place_order').show();
	 }
	} else{
	  $('.cash_place_order').show();
	  $('.credit_place_order').show();
	 }
sessionStorage.removeItem('split_payment');
				split_payment ={
					'cash':0,
					'card':0,
					'custom':0,
					'splitpay':false
				};
sessionStorage.setItem("split_payment",JSON.stringify(split_payment));
//sessionStorage.removeItem('payment_object');
$('.splypymnt').css('background', '');
$('.splypymnt').css('border-color', '');

            $('#creditamnt').html(format(posData.order_total,posData.currency_code));
			$('#cashtotal').html(format(posData.order_total,posData.currency_code));
			$('#customtotal').html(format(posData.order_total,posData.currency_code));
			$('#addcmntcashtotal').html(format(posData.order_total,posData.currency_code));

}
   var custom_pay_order_status = posData.order_status;
     html ='';
 $.each(custom_pay_order_status, function( key, val ) {
	   html +='<option value="'+val.order_status_id+'">'+val.name+'</option>'; 
	 });
	 $('#custom_payment_orderstatus').append(html);
	 
function customPay(){
//will be delete
 //$('#custompayment').modal('show');
	 // if($('#custom').val() == 0){  
         // errorMessage("This payment method not allowed in split payment!");
    // }
	payment_object = JSON.parse(sessionStorage.getItem('split_payment'));
	if(payment_object !== null){
	if( payment_object.custom== 0 && payment_object.custom=='' && payment_object.splitpay){  
	     $('#custompayment').modal('hide');
          errorMessage('Warning: This payment method not allowed in split payment!',1000);
     }else{
	  $('#custompayment').modal('show');
	 }
	}else{
	  $('#custompayment').modal('show');
	 }
	
	$('#cus_pay_title').keyup(function(){
	     
		  var custom_pay_title = false;
         $('#cus_pay_title').each(function() {
             if ($(this).val() == '') {
                 custom_pay_title = true;
             }
         });
		 if (custom_pay_title) {
             $('#custompaysubmit').attr('disabled', true);
         } else {
             $('#custompaysubmit').attr('disabled', false);
         }
		});


     // payment_object = JSON.parse(sessionStorage.getItem('split_payment'));
			// if(payment_object){
				  // html ='';
				 // html +=format(payment_object.custom,posData.currency_code);
				 // $('#customtotal').html(html);
				 
			// }
}

function cashAmnt(){
	//$('#cashtotalamnt').modal('show');
	$('.cash_tender').remove();
	
	payment_object = JSON.parse(sessionStorage.getItem('split_payment'));
	if(payment_object !== null){
	if( payment_object.cash == 0 && payment_object.splitpay ){  
	     $('#cashtotalamnt').modal('hide');
          errorMessage('Warning: This payment method not allowed in split payment!',1000);
     }else{
	  $('#cashtotalamnt').modal('show');
	 }
	} else{
	  $('#cashtotalamnt').modal('show');
	 }
		
	$('#cashtotal').html(format(posData.order_total,posData.currency_code));
	// payment_object = JSON.parse(sessionStorage.getItem('split_payment'));
			// if(payment_object){
				 // html='';
				 // html +=format(payment_object.cash,posData.currency_code);
				 // $('#cashtotal').html(html);
				 // $('#addcmntcashtotal').html(html);
				 
			// }
}
function cardAmnt(){
	payment_object = JSON.parse(sessionStorage.getItem('split_payment'));
	if(payment_object !== null){
	if( payment_object.card == 0 && payment_object.splitpay){  
		$('#totalamnt').modal('hide');
          errorMessage('Warning: This payment method not allowed in split payment!',1000);
     }else{
	  $('#totalamnt').modal('show');
	 }
	} else{
	  $('#totalamnt').modal('show');
	 }
	 
	$('#creditamnt').html(format(posData.order_total,posData.currency_code));
	// payment_object = JSON.parse(sessionStorage.getItem('split_payment'));
			// if(payment_object){
			    // html ='';
				// html +=format(payment_object.card,posData.currency_code);
				 // $('#creditamnt').html(html);
				 
			// }
}


function getCurrencySymbol(currency) {
var currencyData = globalPOS;
//var currencyData= JSON.parse(localStorage.getItem("posData"));
var currencyVal='';
	if(typeof currencyData.currency != 'undefined'){
		$.each(currencyData.currency,function(key,val){
			if(currency == val.code){
				currencyVal= val;
			}
		});
	}
	if(currencyVal){
		symbol_left = currencyVal.symbol_left;
		symbol_right = currencyVal.symbol_right;

		if (symbol_left) {
			symbol= symbol_left;
		}

		if (symbol_right) {
			symbol= symbol_right;
		}
		return symbol;
	}	
}


function inArrayFunc(product_id,arr){
	return new Promise (function(resolve, reject){
	if(arr.length){
		$.each(arr,function(key,val){
			if(parseInt(val.product_id) == parseInt(product_id)){
				resolve('true');
			}
	    });
	}
	resolve('false');
	});
}

$(document).on('click', '.updateptscart', function(e){
	var cart_id=parseInt(e.target.attributes.ptproddata.value);
	var price = $('#changeprice'+cart_id).val();
	var discount = $('#discountValue'+cart_id).val();
	var type =$('#discountType'+cart_id).val();
	var quantity_value =$('#changequantity'+cart_id).val();
	var quantity_unit =$("input[id='quantity_unit"+cart_id+"']:checked").val()
	var quantity_weight =$("input[id='quantity_weight"+cart_id+"']:checked").val();
	var changeprice_checkbox_temp =$("input[id='changeprice_checkbox"+cart_id+"']:checked").val();
		changeprice_checkbox=false;
	if(typeof changeprice_checkbox_temp != 'undefined'){
		changeprice_checkbox=true;
	}
	if(typeof quantity_unit!='undefined'){
	quantity_type=quantity_unit;
	} else {
	quantity_type=quantity_weight;
	}
	var data = {
		'cart_id':cart_id,
		'price':parseFloat(price),
		'discount':parseFloat(discount),	
		'type':parseInt(type),
		'quantity_type':quantity_type,
		'quantity_value':parseInt(quantity_value),
		'changeprice_checkbox':changeprice_checkbox
	};
    updateCartProduct(data);
});

function updateCartProduct(data){
	if(data.quantity_value != ''){
	 var poscart = JSON.parse(localStorage.getItem("pos_cart"));
	 $.each(poscart,function(key,val){
		 if(parseInt(val.cart_id) == parseInt(data.cart_id)){
			 if(data.quantity_value != NaN){
				 if(data.quantity_type == 'unit' && data.quantity_value){
					poscart[key].quantity= data.quantity_value;
				 }
			 }
		 }
	});
	localStorage.setItem("pos_cart", JSON.stringify(poscart));
	}
	var updatecartObj={};
	var cart_id=data.cart_id;
	if(sessionStorage.getItem("updateCartProduct") != null){

	var uCPObj = JSON.parse(sessionStorage.getItem("updateCartProduct"));
	$.each(uCPObj,function(key,val){
	inArrayFunc(cart_id,uCPObj).then(function(val1){
	if(val1 == 'true' ){
	var price=data.price;
	var discount_type = data.type;
    var discount_value = data.discount;
	var quantity_type = data.quantity_type;
	var quantity_value = data.quantity_value;
	var changeprice_checkbox = data.changeprice_checkbox;
	if(cart_id == val.cart_id){
	    if(discount_value != ''){
			 if(parseInt(discount_type) === 1){
				tax_type = 'Fixed';
				tax_title = ' (Fixed '+discount_value+')'
			} 
			if(parseInt(discount_type) === 0){
				tax_type = 'percentage';
				tax_title = ' ('+discount_value+'%)';
			}
			let cart_pro_discount = {
					'type' : tax_type,
                    'title' : tax_title,
                    'value' : discount_value,
                    
				};
				
	if(changeprice_checkbox ==  true && price != ''){
var tax_class_id='';
	if(localStorage.getItem("pos_cart") != null){
	var pos_cart = JSON.parse(localStorage.getItem("pos_cart"));
		if(pos_cart.length){
	$.each(pos_cart, function(kkey,vval){
		if(vval.cart_id == cart_id){
		$.each(CartObj.getProducts(),function(pkey,pval){
		if((cart_id == pval.cart_id) && (vval.product_id == pval.product_id)){
			tax_class_id = parseInt(pval.tax_class_id);
		}
		});
		
		}
	});
	}
	}
	var tax_rates = taxObj.getRates(parseFloat(price), tax_class_id);
						if(tax_rates){
						var percent=0;
						var flat=0;
							$.each(tax_rates,function(ptskey,ptsval){
							if(ptsval.type == 'P'){
							  percent += parseFloat(ptsval.rate);
							}
							
							if(ptsval.type == 'F'){
							  flat += parseFloat(ptsval.rate);
							}	
							});	
							price = parseFloat(price);
							if(flat){
							price = price-flat;	
							} 
							if(percent){
							price = ((price*100)/(100+percent));
							}
						}
	}
						
			var updateCartProduct = {
			 cart_id:cart_id,
			'price':price,
			'discount':cart_pro_discount,
			'quantity_type':quantity_type,
			'quantity_value':quantity_value,
			'changeprice_checkbox':changeprice_checkbox
			}
			
			uCPObj[cart_id] = updateCartProduct;
			
	updateCartProduct_temp = JSON.parse(sessionStorage.getItem('updateCartProduct'));
	if(!isNaN(updateCartProduct.price)){
		uCPObj[cart_id].price=updateCartProduct.price;
		uCPObj[cart_id].changeprice_checkbox=updateCartProduct.changeprice_checkbox;
	} else {
	if(typeof updateCartProduct_temp[cart_id] != 'undefined'){
	uCPObj[cart_id].price=updateCartProduct_temp[cart_id].price;
	uCPObj[cart_id].changeprice_checkbox=updateCartProduct_temp[cart_id].changeprice_checkbox;
	}
	}
	if(!isNaN(updateCartProduct.quantity_value)){
		uCPObj[cart_id].quantity_type=updateCartProduct.quantity_type;
		uCPObj[cart_id].quantity_value=updateCartProduct.quantity_value;
	} else {
	if(typeof updateCartProduct_temp[cart_id] != 'undefined'){
		uCPObj[cart_id].quantity_type=updateCartProduct_temp[cart_id].quantity_type;
		uCPObj[cart_id].quantity_value=updateCartProduct_temp[cart_id].quantity_value;
		}
	
	}

	if(!isNaN(updateCartProduct.discount.value)){
		uCPObj[cart_id].discount.type=updateCartProduct.discount.type;
		uCPObj[cart_id].discount.title=updateCartProduct.discount.title;
		uCPObj[cart_id].discount.value=updateCartProduct.discount.value;
	} else {
	if(typeof updateCartProduct_temp[cart_id] != 'undefined'){
		uCPObj[cart_id].discount.type=updateCartProduct_temp[cart_id].discount.type;
		uCPObj[cart_id].discount.title=updateCartProduct_temp[cart_id].discount.title;
		uCPObj[cart_id].discount.value=updateCartProduct_temp[cart_id].discount.value;
		}
	}
		}
	}
		} else {
			var price=data.price;
			var discount_type = data.type;
			var discount_value = data.discount;
			var quantity_type = data.quantity_type;
			var quantity_value = data.quantity_value;
			var changeprice_checkbox = data.changeprice_checkbox;
			//console.log(price);
	    if(discount_value != ''){
			 if(parseInt(discount_type) === 1){
				discount_type = 'Fixed';
				discount_title = ' (Fixed '+discount_value+')'
			}
			if(parseInt(discount_type) === 0){
				discount_type = 'percentage';
				discount_title = ' ('+discount_value+'%)';
			}
			let cart_pro_discount = {
					'type' : discount_type,
                    'title' : discount_title,
                    'value' : discount_value,
                    
				};
	
	if(changeprice_checkbox ==  true && price != ''){
	var tax_class_id='';
	if(localStorage.getItem("pos_cart") != null){
	var pos_cart = JSON.parse(localStorage.getItem("pos_cart"));
		if(pos_cart.length){
	$.each(pos_cart, function(kkey,vval){
		if(vval.cart_id == cart_id){
		$.each(CartObj.getProducts(),function(pkey,pval){
		if((cart_id == pval.cart_id) && (vval.product_id == pval.product_id)){
			tax_class_id = parseInt(pval.tax_class_id);
		}
		});
		
		}
	});
	}
	}
	var tax_rates = taxObj.getRates(parseFloat(price), tax_class_id);
						if(tax_rates){
						var percent=0;
						var flat=0;
							$.each(tax_rates,function(ptskey,ptsval){
							if(ptsval.type == 'P'){
							  percent += parseFloat(ptsval.rate);
							}
							
							if(ptsval.type == 'F'){
							  flat += parseFloat(ptsval.rate);
							}	
							});	
							price = parseFloat(price);
							if(flat){
							price = price-flat;	
							} 
							if(percent){
							price = ((price*100)/(100+percent));
							}
						}
	}
			var updateCartProduct = {
			 cart_id:cart_id,
			'price':parseFloat(price),
			'discount':cart_pro_discount,
			'quantity_type':quantity_type,
			'quantity_value':quantity_value,
			'changeprice_checkbox':changeprice_checkbox
			}
//console.log('B');
		uCPObj[cart_id]=updateCartProduct;
	updateCartProduct_temp = JSON.parse(sessionStorage.getItem('updateCartProduct'));
	if(!isNaN(updateCartProduct.price)){
		uCPObj[cart_id].price=updateCartProduct.price;
		uCPObj[cart_id].changeprice_checkbox=updateCartProduct.changeprice_checkbox;
	} else {
	if(typeof updateCartProduct_temp[cart_id] != 'undefined'){
		uCPObj[cart_id].price=updateCartProduct_temp[cart_id].price;
		uCPObj[cart_id].changeprice_checkbox=updateCartProduct_temp[cart_id].changeprice_checkbox;
		}
	}
	if(!isNaN(updateCartProduct.quantity_value)){
		uCPObj[cart_id].quantity_type=updateCartProduct.quantity_type;
		uCPObj[cart_id].quantity_value=updateCartProduct.quantity_value;
	} else {
	if(typeof updateCartProduct_temp[cart_id] != 'undefined'){
		uCPObj[cart_id].quantity_type=updateCartProduct_temp[cart_id].quantity_type;
		uCPObj[cart_id].quantity_value=updateCartProduct_temp[cart_id].quantity_value;
		}
	}

	if(!isNaN(updateCartProduct.discount.value)){
		uCPObj[cart_id].discount.type=updateCartProduct.discount.type;
		uCPObj[cart_id].discount.title=updateCartProduct.discount.title;
		uCPObj[cart_id].discount.value=updateCartProduct.discount.value;
	} else {
	if(typeof updateCartProduct_temp[cart_id] != 'undefined'){
		uCPObj[cart_id].discount.type=updateCartProduct_temp[cart_id].discount.type;
		uCPObj[cart_id].discount.title=updateCartProduct_temp[cart_id].discount.title;
		uCPObj[cart_id].discount.value=updateCartProduct_temp[cart_id].discount.value;
		}
	}
		//uCPObj.push(updateCartProduct);
		
		}
			
		
		}
			sessionStorage.setItem("updateCartProduct", JSON.stringify(uCPObj));
			getCartProductHTML();
		});
	});
	} else {
	var price = data.price;
	var discount_type = data.type;
    var discount_value = data.discount;
	var quantity_type = data.quantity_type;
	var quantity_value = data.quantity_value;
	var changeprice_checkbox = data.changeprice_checkbox;
	    if(discount_value != ''){
			if(parseInt(discount_type) === 1){
				tax_type = 'Fixed';
				tax_title = ' (Fixed '+discount_value+')'
			} 
			if(parseInt(discount_type) === 0){
				tax_type = 'percentage';
				tax_title = ' ('+discount_value+'%)';
			}
			let cart_pro_discount = {
					'type' : tax_type,
                    'title' : tax_title,
                    'value' : discount_value,
                    
				};
				
	if(changeprice_checkbox ==  true && price != ''){
var tax_class_id='';
	if(localStorage.getItem("pos_cart") != null){
	var pos_cart = JSON.parse(localStorage.getItem("pos_cart"));
		if(pos_cart.length){
	$.each(pos_cart, function(kkey,vval){
		if(vval.cart_id == cart_id){
		$.each(CartObj.getProducts(),function(pkey,pval){
		if((cart_id == pval.cart_id) && (vval.product_id == pval.product_id)){
			tax_class_id = parseInt(pval.tax_class_id);
		}
		});
		
		}
	});
	}
	}
	var tax_rates = taxObj.getRates(parseFloat(price), tax_class_id);
						if(tax_rates){
						var percent=0;
						var flat=0;
							$.each(tax_rates,function(ptskey,ptsval){
							if(ptsval.type == 'P'){
							  percent += parseFloat(ptsval.rate);
							}
							
							if(ptsval.type == 'F'){
							  flat += parseFloat(ptsval.rate);
							}	
							});	
							price = parseFloat(price);
							if(flat){
							price = price-flat;	
							} 
							if(percent){
							price = ((price*100)/(100+percent));
							}
						}
	}
				
			var updateCartProduct = {
			 cart_id:cart_id,
			'price':price,
			'discount':cart_pro_discount,
			'quantity_type':quantity_type,
			'quantity_value':quantity_value,
			'changeprice_checkbox':changeprice_checkbox
			}
			updatecartObj[cart_id] = updateCartProduct;

	sessionStorage.setItem("updateCartProduct", JSON.stringify(updatecartObj));
	}
	
			
}
getCartProductHTML();

}


function printPreview(order_id) {
if(posData['module_purpletree_pos_receipt_store_detail'] != null && posData['module_purpletree_pos_receipt_store_detail'] != ''){
	store_information = posData['module_purpletree_pos_receipt_store_detail'];
} else {
	 html  = '<div class="d-block">'+store_receipt_information.store_name+'</div>';
	 html += '<div class="d-block">'+store_receipt_information.address+'</div>';
	 html += '<div class="d-block">'+store_receipt_information.zone_code+'</div>';
	 html += '<div class="d-block">'+store_receipt_information.country+'</div>';
     store_information = html;
}
   $('#pts-receipt-print').remove();
	  html ='';
     html +='<div id="pts-receipt-print" style="width: 400px; font-size: 16px;">';
	 html +='<div  class="d-flex flex-column"><div class="d-flex align-items-center justify-content-center" style="margin-bottom: 15px;"><img class="img-responsive" alt="Store Logo" src="'+posData.logo+'" style="max-width: 100%; height: auto;"></div>';
	 html +='<div class="d-flex flex-column text-center" style="border-bottom: 2px dashed;"><div >{{ text_receipt }}</div>';
	 html +='<div class="text-left receipttitle" style="font-size: 20px; outline: 0px; border: 0px;"><pre style="font-size: 16px; outline: 0px; border: 0px;">'+store_information+'</pre></div>';
	 html +='</div>';
	 html +='</div>';
	 html +='<div  class="d-flex flex-column" style="border-bottom: 2px dashed;">';
	 var pos_orders_data = [];
	if(localStorage.getItem("pos_orders_data") != null){
	var pos_orders_data = JSON.parse(localStorage.getItem("pos_orders_data"));
	}
	let total_item = 0;
	let total_quantity = 0;
       $.each(pos_orders_data,function(key,val){ 
	   if(parseInt(val.order_id) == parseInt(order_id)){
	 html +='<div class="d-flex flex-row"><div  class="flex-fill p-1 text-left"><span>{{ text_order_receipt }} #</span>'+val.order_id+'</div><div class="flex-fill p-1 text-right">'+val.date_added+'</div></div>';
	 
	 html +='<div class="d-flex flex-row"><div  class="flex-fill p-1 text-left">Cashier:'+val.customer+'</div><div class="flex-fill p-1 text-right">'+val.payment_method+'</div></div>';
	  
	 html +='</div>';
	 html +='<div  class="d-flex flex-column" style="border-bottom: 1px dashed;">';
	 html+='<div class="d-flex flex-row" style="border-bottom: 2px dashed;">';
	 html +='<div  class="flex-fill p-1 text-left" >{{ text_item }}</div><div  class="flex-fill p-1">{{ text_quantity }}</div>';
	 html +='<div  class="flex-fill p-1 text-left">{{ text_rate }}</div>';
	 html +='<div  class="flex-fill p-1 text-right" >{{ text_amount }}</div>';
	 html +='</div>';
	 var pos_orders_data = [];
	if(localStorage.getItem("pos_orders_data") != null){
	var pos_orders_data = JSON.parse(localStorage.getItem("pos_orders_data"));
	}
      $.each(val.products,function(productkey,productval){
	   total_item++;
	   total_quantity += parseInt(productval.quantity);
	 html +='<div class="d-flex flex-column"><div class="d-flex flex-wrap"><div  class="flex-fill text-left" id="'+productval.order_id+'">'+productval.name+'</div></div><div  class="d-flex flex-wrap"><div class="flex-fill text-right" id="'+productval.order_id+'">'+productval.quantity+'</div>';
	 html +='<div class="flex-fill text-right"id="'+productval.order_id+'">'+format(productval.price,posData.currency_code)+'</div>';
	 html +='<div class="flex-fill text-right" id="'+productval.order_id+'">'+format(productval.total,posData.currency_code)+'</div></div></div>'; 
	 });
	 }
	 });  
	 html +='</div>';
	  var order_total = posData.ordertotal;
	  $.each(order_total,function(key,val){
	   if(parseInt(val.order_id) == parseInt(order_id)){
	 html +='<div  class="d-flex flex-column"><div class="d-flex flex-row"><div  class="flex-fill p-1 text-left" style="max-width: 50%; word-break: break-all;">'+val.title+'</div><div class="flex-fill p-1 text-right" style="max-width: 50%; word-break: break-all;" value="'+val.order_id+'">'+format(val.value,posData.currency_code)+'</div></div></div>';
	   } 
    });
	 
	 html +='</div>';
	 html +='<div  class="d-flex flex-row"><div class="flex-fill text-left"><span >{{ text_total_items }}:</span>'+total_item+'</div></div><div  class="d-flex flex-row"><div class="flex-fill text-left"><span >{{ text_total_quantity }}:</span>'+total_quantity+'</div></div>';

	
	 html +='<div style="margin-top: 10px;"><pre  class="text-left" style="font-size: 16px; outline: 0px; border: 0px;">'+posData['module_purpletree_pos_receipt_detail']+'</pre></div>';
	 html +='</div>';
     var printContents = html;
     var originalContents = document.body.innerHTML;
     document.body.innerHTML = printContents;
     window.print();	 	 
     //document.body.onfocus = doneyet;
     document.body.innerHTML = originalContents;
}
function returnOrdList(order_id){
	var selected_product = $('#selected_product').serializeArray();

	$('.returnordlist').remove();
	
	if(!selected_product.length){
	 alert('Please select product.');
	}
	if(selected_product.length){
	if((selected_product[0].name == 'product_id' && selected_product.length >1) || (selected_product[0].name != 'product_id' && selected_product.length>=1) ){
	html ='';
	html += '<div class="returnordlist">'
	html +='<form id="pts_return_product" class="form-horizontal">';
	var pos_orders_data = [];
	if(localStorage.getItem("pos_orders_data") != null){
	var pos_orders_data = JSON.parse(localStorage.getItem("pos_orders_data"));
	}
	if(pos_orders_data){
	$.each(pos_orders_data,function(key,val){	
	if(parseInt(order_id) == parseInt(val.order_id)){
	
	html += '<div class="pts-product-list">';
	html += '	<table class="table table-bordered">';
	html += '<thead><tr><td>{{ text_name }}</td><td>{{ text_quantity }}</td><td>{{ text_return_reason }}</td><td>{{ text_return_opened }}</td></tr></thead>';		
	html += '<tbody>';	
	html += '<input type="hidden" id="detail_order_id" name="detail_order_id" value="'+val.order_id+'">';	
	$.each(val.products,function(key,val1){	
		$.each(selected_product,function(key,val2){	
		if(val2.name != 'product_id'){
		let product_id=val2.name.replace("name_", "");
		if(parseInt(val1.product_id) == parseInt(product_id)){
		
		html += '<input type="hidden" name="data['+val1.product_id+'][order_id]" value="'+val.order_id+'">';	
		html += '<input type="hidden" name="data['+val1.product_id+'][customer_id]" value="'+val.customer_id+'">';	
		html += '<input type="hidden" name="data['+val1.product_id+'][firstname]" value="'+val.firstname+'">';	
		html += '<input type="hidden" name="data['+val1.product_id+'][lastname]" value="'+val.lastname+'">';	
		html += '<input type="hidden" name="data['+val1.product_id+'][email]" value="'+val.email+'">';	
		html += '<input type="hidden" name="data['+val1.product_id+'][telephone]" value="'+val.telephone+'">';	
		
		html += '<input type="hidden" name="data['+val1.product_id+'][product]" value="'+val1.name+'">';	
		html += '<input type="hidden" name="data['+val1.product_id+'][model]" value="'+val1.model+'">';	
		html += '<input type="hidden" name="data['+val1.product_id+'][date_ordered]" value="'+val.date_ordered+'">';	
		
		html += '<tr><td class="text-left">'+val1.name+'<input type="hidden" value="'+val1.product_id+'" name="data['+val1.product_id+'][product_id]" /></td><td class="text-left"><div class="input-group mb-3"><input type="number" min="1" name="data['+val1.product_id+'][quantity]" id="quantity" formcontrolname="quantity" class="form-control" value="'+val1.quantity+'"></div></td>';
		html +='<td class="text-right"><select id="return-reason" name="data['+val1.product_id+'][return_reason_id]" class="form-control return-reason"></select></td>';
		html +='<td class="text-left"><select  name="data['+val1.product_id+'][opened]" class="form-control"><option value="1">{{ text_return_opened }}</option><option value="0">{{ text_return_unopened }}</option></select></td></tr>';
		}
		}
		});
	});
	
	html += '</tbody>';
	html += '</table>';
	html += '</div>';
	html += '<div class="form-group text-left ml-2">';
	html += '<label class="col-sm-4 pts-return-order">{{ text_comment }}</label><div class="col-sm-4"><textarea name="comment" id="comment" cols="30" rows="1" class="form-control"></textarea></div>';
	html += '<div class="text-right mt-2"><div onClick="returnOrderSubmit();" id="return_submit" class="btn btn-primary">{{ text_submit }}</div></div>';
	html += '</div>';
	}
	});
	}
	html +='</form>';
	html += '</div>';
	$('#return_order_list').append(html);
	
	var returnreason = posData.returnreason;
          html = '';
    $.each(returnreason, function(key,val) {
	      html += '<option value="'+ val.return_reason_id +'">'+ val.name +'</option>';
	      });
		   $('.return-reason').append(html);
		   } else {
		   alert('Please select product.');
		   }
	}	   
}
function returnOrderSubmit(){
		var returnData = $('#pts_return_product').serializeArray();
		var detail_order_id = $('#detail_order_id').val();
	$.ajax({
      url: 'index.php?route=extension/purpletree_pos/pos/home|returnorder',
	  type: 'post',
      dataType: 'json',
	  data: returnData, 
      success: function(json) {
	  if(json.success){
	  if(Object.keys(json.returnProductData).length){
	  var return_products=[];
	  if(localStorage.getItem("return_product") != null){
		return_products = JSON.parse(localStorage.getItem("return_product"));
		if(typeof return_products[detail_order_id] == 'undefined' ){
		return_products = Object.assign(return_products, json.returnProductData);
		} else {
		temp_return_products={};
		temp_returnProductData={};
		$.each(return_products[detail_order_id],function(ptskey,ptsval){
		temp_return_products[ptsval.product_id]=ptsval;
		});
		
		$.each(json.returnProductData[detail_order_id],function(ptskey1,ptsval1){
		temp_returnProductData[ptsval1.product_id]=ptsval1;
		});
		
		return_products[detail_order_id] = Object.assign(temp_return_products, temp_returnProductData);

		}
	   } else {
	   return_products = json.returnProductData;
	   }
	  localStorage.setItem("return_product", JSON.stringify(return_products));
	  }
	  alert(json.message);
	  getPosOrderDetail(detail_order_id);
	  } else {
		alert(json.message);
	  }
	  
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
	
}
$(document ).delegate( "#salereports", "click", function() {
	   sale_report();
});

{% if isPosAdmin %}
function sale_report(){
let message = '{{ text_record_not_found }}';
$('#posproduct').empty();
	$('.sale-report').remove();
	html ='';
	html = '<div class="col-sm-12 sale-report product-area"><div data-type="salereports"><div class="row product"><div class="row col-sm-12 pts-filter">';
	
html += '<div class="col-sm-3"><div class="form-group">  <label class="control-label float-left" for="input-filter-date-start">{{ text_filter_date_start }}</label>  <div class="input-group date"><input type="date" name="sale_report_date_start" value="2022-03-01" placeholder="Date Start" data-date-format="YYYY-MM-DD" id="input-filter-date-start" class="form-control">  <span class="input-group-btn"> </span></div></div></div>';

html += '<div class="col-sm-3"> <div class="form-group"><label class="control-label float-left" for="input-filter-date-end">{{ text_filter_date_end }}</label> <div class="input-group"> <input type="date" name="sale_report_date_end" value="2022-03-24" placeholder="Date End" data-date-format="YYYY-MM-DD" id="input-filter-date-end" class="form-control"> <span class="input-group-btn"> </span></div></div></div>';

html += '<div class="col-sm-3"> <div class="form-group"><label class="control-label float-left" for="input-date-end">{{ text_filter_agent }}</label> <div class="input-group"><select id="pos_agent" name="pos_agent" class="form-control">';

html += '<option value=""></option>';
if(typeof posData.agents != 'undefined'){
	if(posData.agents.length){
		$.each(posData.agents,function(agentKey,agentData){
			html += '<option value="'+agentData.customer_id+'">'+agentData.agent_name+'</option>';	
		});
	}
}

html += '</select><span class="input-group-btn"> </span></div></div></div>';

html += '<div class="text-right col-sm-3 mt-4 mb-1"><button onClick="filter_report();" class="btn btn-primary">{{ text_filter }}</button></div>';

html += '</div><div class="pts-product-list col-sm-12"><div class="table-responsive">	<table class="table table-bordered">';
html += '<thead><tr><td><label>{{ text_filter_date_start }}</label></td><td><label>{{ text_filter_date_end }}</label></td><td><label>{{ text_number_order }}</label></td><td><label>{{ text_number_product }}</label></td><td><label>{{ text_tax }}</label></td><td><label>{{ text_order_total }}</label></td></tr></thead>';
html += '<tbody>';
	var filter_report = [];
	if(localStorage.getItem("filter_report") != null){
	   filter_report = JSON.parse(localStorage.getItem("filter_report"));
	}
	let grand_total = 0;
	if(Object.keys(filter_report).length){
	$.each(filter_report,function(key,val){
	grand_total += parseFloat(val.g_total);
		html += '<tr><td><label>'+val.date_start+'</label></td><td><label>'+val.date_end+'</label></td><td><label>'+val.orders+'</label></td><td><label>'+val.products+'</label></td><td><label>'+val.tax+'</label></td><td><label>'+val.total+'</label></td><tr>';
	});
	html += '<tr><td colspan="5" class="text-right">{{ text_grand_total }}</td><td><label>'+format(grand_total,posData.currency_code)+'</label></td><tr>';
	} else {
	html += '<tr><td colspan="6">'+message+'</td></tr>';
	}
html += '</tbody></table></div></div>';
html += '<div class="col-sm-12 text-right mb-1"><button onClick="print_report();" class="btn btn-primary">{{ text_print_report }}</button></div>';
html += '</div></div></div>';
	$('#posproduct').append(html);
}
{% endif %}

{% if isPosAgent %}
function sale_report(){
	$('#posproduct').empty();
	$('.sale-report').remove();
	let message = 'Record not found!';
	html ='';
	html = '<div class="col-sm-12 sale-report product-area"><div data-type="salereports"><div class="row product"><div class="row col-sm-12 pts-filter">';
	
	html += '<div class="col-sm-4"><div class="form-group">  <label class="control-label float-left" for="input-filter-date-start">{{ text_filter_date_start }}</label>  <div class="input-group date"><input type="date" name="sale_report_date_start" value="2022-03-01" placeholder="Date Start" data-date-format="YYYY-MM-DD" id="input-filter-date-start" class="form-control">  <span class="input-group-btn"> </span></div></div></div>';

	html += '<div class="col-sm-4"> <div class="form-group"><label class="control-label float-left" for="input-filter-date-end">{{ text_filter_date_end }}</label> <div class="input-group"> <input type="date" name="sale_report_date_end" value="2022-03-24" placeholder="Date End" data-date-format="YYYY-MM-DD" id="input-filter-date-end" class="form-control"> <span class="input-group-btn"> </span></div></div></div>';

	html += '<div class="text-right col-sm-4 mt-4 mb-1"><button onClick="filter_report();" class="btn btn-primary">{{ text_filter }}</button></div>';

	html += '</div><div class="pts-product-list col-sm-12">	<table class="table table-bordered">';
	html += '<thead><tr><td><label>{{ text_filter_date_start }}</label></td><td><label>{{ text_filter_date_end }}</label></td><td><label>{{ text_number_order }}</label></td><td><label>{{ text_number_product }}</label></td><td><label>{{ text_tax }}</label></td><td><label>{{ text_order_total }}</label></td></tr></thead>';
	html += '<tbody>';
	var filter_report = [];
	if(localStorage.getItem("filter_report") != null){
	   filter_report = JSON.parse(localStorage.getItem("filter_report"));
	}
	if(Object.keys(filter_report).length){
	let grand_total=0;
	$.each(filter_report,function(key,val){
	grand_total += parseFloat(val.g_total);
		html += '<tr><td><label>'+val.date_start+'</label></td><td><label>'+val.date_end+'</label></td><td><label>'+val.orders+'</label></td><td><label>'+val.products+'</label></td><td><label>'+val.tax+'</label></td><td><label>'+val.total+'</label></td><tr>';
	});
	html += '<tr><td colspan="5" class="text-right">{{ text_grand_total }}</td><td><label>'+format(grand_total,posData.currency_code)+'</label></td><tr>';
	} else {
	html += '<tr><td colspan="6">'+message+'</td></tr>';
	}
	html += '</tbody></table></div>';
	html += '<div class="col-sm-12 text-right mb-1"><button onClick="print_report();" class="btn btn-primary">{{ text_print_report }}</button></div>';
	html += '</div></div></div>';
	$('#posproduct').append(html);
}
{% endif %}

function print_report(){
html = '<div class="pts-product-list col-sm-12 text-center"> <h1>{{ text_sales_report }} </h1></div>';
html += '<div class="pts-product-list col-sm-12">	<table class="table table-bordered">';
html += '<thead><tr><td><label>{{ text_filter_date_start }}</label></td><td><label>{{ text_filter_date_end }}</label></td><td><label>{{ text_number_order }}</label></td><td><label>{{ text_number_product }}</label></td><td><label>{{ text_tax }}</label></td><td><label>{{ text_order_total }}</label></td></tr></thead>';
html += '<tbody>';
	var filter_report = [];
	if(localStorage.getItem("filter_report") != null){
	   filter_report = JSON.parse(localStorage.getItem("filter_report"));
	}
	let grand_total=0;
	if(Object.keys(filter_report).length){
	$.each(filter_report,function(key,val){
		grand_total += parseFloat(val.g_total);
		html += '<tr><td><label>'+val.date_start+'</label></td><td><label>'+val.date_end+'</label></td><td><label>'+val.orders+'</label></td><td><label>'+val.products+'</label></td><td><label>'+val.tax+'</label></td><td><label>'+val.total+'</label></td><tr>';
	}); 
	html += '<tr><td colspan="5" class="text-right">{{ text_grand_total }}</td><td><label>'+format(grand_total,posData.currency_code)+'</label></td><tr>';
	} else {
	html += '<tr><td colspan="6">'+message+'</td></tr>';
	}
html += '</tbody></table></div>';

     var printContents = html;
     var originalContents = document.body.innerHTML;
     document.body.innerHTML = printContents;
     window.print();	 	 
     //document.body.onfocus = doneyet;
     document.body.innerHTML = originalContents;
}

function filter_report(){
		var filter_date_start = $('#input-filter-date-start').val();
		var filter_date_end = $('#input-filter-date-end').val();
		let filter_data = {
			'filter_date_start':filter_date_start,
			'filter_date_end':filter_date_end,
		}
		{% if isPosAdmin %}
		var agent_id = $('#pos_agent').val();
			filter_data.filter_agent_id = agent_id;
		{% endif %}
		
		{% if isPosAgent %}
			filter_data.filter_agent_id = posData.agent_id;
		{% endif %}
		
	$.ajax({
       url: 'index.php?route=extension/purpletree_pos/pos/report|filterReport',
	   type: 'post',
	   dataType: 'json',
	   data: filter_data,
       success: function(json) {
			html ='';
			if(json.reports.length){
			localStorage.setItem("filter_report", JSON.stringify(json.reports));
			
			} else {
			localStorage.removeItem('filter_report');
			}
		sale_report();
       }
    });  
}

$("#barcode").keyup(function(event) {
    if (event.keyCode === 13) {
        $("#barcodebutton").click();
    }
});

function getOrderFiler(){
	let start_date = $('#order-date-start').val();
	let end_date   = $('#order-date-end').val();
	let agent_id   = $('#pos_order_agent').val();

	let order_filter = {
		'filter_date_start':start_date,
		'filter_date_end':end_date
	}
	{% if isPosAdmin %}
		order_filter.agent_id = agent_id;
	{% endif %}
	
	$.ajax({
      url: 'index.php?route=extension/purpletree_pos/pos/home|getPosOrders',
	  type: 'post',
      dataType: 'json',
	  data: order_filter, 
      success: function(json) {
	  if(Object.keys(json.orders).length){
	  localStorage.setItem("pos_orders_data", JSON.stringify(json.orders));
	  localStorage.setItem("return_product", JSON.stringify(json.return_products));
	  } else {
	    localStorage.removeItem('pos_orders_data');
	    localStorage.removeItem('return_product');
	  }
	   posOrders();
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
	
};

function getReturnOrderFiler(){
	let start_date = $('#return-order-date-start').val();
	let end_date   = $('#return-order-date-end').val();
	let agent_id   = $('#pos_return_agent').val();

	let order_filter = {
		'filter_date_start':start_date,
		'filter_date_end':end_date
	}
	{% if isPosAdmin %}
		order_filter.agent_id = agent_id;
	{% endif %}
		
	$.ajax({
      url: 'index.php?route=extension/purpletree_pos/pos/home|getPosReturnOrders',
	  type: 'post',
      dataType: 'json',
	  data: order_filter, 
      success: function(json) {
	  if(Object.keys(json.returnorders).length){
	  localStorage.setItem("return_orders", JSON.stringify(json.returnorders));
	  } else {
	    localStorage.removeItem('return_orders');
	  }
	  posReturnOrders();
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
	
};

function checkAll(e){
$('input[name^=\'name_\']').prop('checked',e.checked);
}
function updateSummaryTable() {
  document.getElementById("tbl_total").innerText = document.getElementById("yousubtotal").innerText;
  document.getElementById("tbl_discount").innerText = document.getElementById("yousaveddiscount").innerText;
  document.getElementById("tbl_reward").innerText = document.getElementById("youreward").innerText;
}

// Run once on load and every time the values update
updateSummaryTable();

</script>

</body>
</html>