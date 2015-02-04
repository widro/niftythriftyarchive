var CheckoutShipping = new Class({
	initialize : function() {
		this.shipping_same_as_choice = $$('input[name=address_billing_same_as]');
		this.shipping_same_as_choice.each(function(input, index) {
			input.addEvent('change', function(e) {
				this.showBillingChoice(input);
			}.bind(this));
		}.bind(this));
	},
	
	showBillingChoice : function(input) {
		$$('div.address_billing_same_as_content').each(function(div) {
			div.setStyle('display', 'none');
		});

		if ($(input.get('id') + '_content'))
			$(input.get('id') + '_content')
					.setStyle('display', 'block');
	},
	
	
});

var CheckoutOrder = new Class({
	initialize : function() {
		this.credits_choice = $$('input[name=credits_choice]');
		this.credits_choice.each(function(choice, index) {
			choice.addEvent('change', function() {
				if (choice.get('value') == 'all') {
					this.hideAmountField();
				} else {
					this.showAmountField();
				}
			}.bind(this));

		}.bind(this));
		
		$$('div.shipment div.method input[name=order_shipping_method]').addEvent('change', function(){
			this.changeShippingMethod();		
		}.bind(this));
	},

	showAmountField : function() {
		$('credits_choice_amount_content').setStyle('display', 'block');
	},

	hideAmountField : function() {
		$('credits_choice_amount_content').setStyle('display', 'none');
	},
	
	changeShippingMethod: function(){
		var input_checked = null;
		$$('div.shipment div.method input[name=order_shipping_method]').each(function(input){
			if(input.get('checked') == true)
				input_checked = input;
		});

		var new_value =input_checked.getNext().getElements('span.value')[0].get('text');
		$$('div.sidebar_checkout div.resume_row.amount_shipping span.value')[0].set('text', new_value);
		this.updateTotalPrice();
	},
	
	updateTotalPrice: function(){
		this.field_amount = $$('div.sidebar_checkout div.resume_row.amount span.value')[0].get('text');
		this.field_amount_vat = $$('div.sidebar_checkout div.resume_row.amount_vat span.value')[0].get('text');
		this.field_amount_shipping = $$('div.sidebar_checkout div.resume_row.amount_shipping span.value')[0].get('text');
		
		if($$('div.sidebar_checkout div.resume_row.amount_coupon span.value').length > 0)
			this.field_amount_coupon = $$('div.sidebar_checkout div.resume_row.amount_coupon span.value')[0].get('text');
		else
			this.field_amount_coupon = '0';
		
		if($$('div.sidebar_checkout div.resume_row.amount_credits span.value').length > 0)
			this.field_amount_credits = $$('div.sidebar_checkout div.resume_row.amount_credits span.value')[0].get('text');
		else
			this.field_amount_credits = '0';
		
		this.field_amount = parseFloat(replaceAll(this.field_amount, ',', ''));
		this.field_amount_vat = parseFloat(replaceAll(this.field_amount_vat, ',', ''));
		this.field_amount_shipping = parseFloat(replaceAll(this.field_amount_shipping, ',', ''));
		this.field_amount_coupon = parseFloat(replaceAll(this.field_amount_coupon, ',', ''));
		this.field_amount_credits = parseFloat(replaceAll(this.field_amount_credits, ',', ''));
		
		var total = this.field_amount+this.field_amount_vat+this.field_amount_shipping-this.field_amount_coupon-this.field_amount_credits;
		if(total<0)
			total = 0;	
		$$('div.sidebar_checkout div.resume_row.amount_total span.value')[0].set('text', number_format(total,'2'));	
	}
});

window.addEvent('domready', function() {
	new CheckoutOrder();
	new CheckoutShipping();
});



function replaceAll(txt, replace, with_this) {
	return txt.replace(new RegExp(replace, 'g'),with_this);
}

function number_format (number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');    }
    return s.join(dec);
}