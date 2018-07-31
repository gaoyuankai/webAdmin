/**
 * jQuery Validation Plugin Additional Methods
 *
 * Copyright (c) 2015 Zhixiang Wang
 */
(function( factory ) {
	if ( typeof define === "function" && define.amd ) {
		define( ["jquery", "./jquery.validate"], factory );
	} else {
		factory( jQuery );
	}
}(function( $ ) {

	(function() {

		function stripHtml(value) {
			// remove html tags and space chars
			return value.replace(/<.[^<>]*?>/g, " ").replace(/&nbsp;|&#160;/gi, " ")
			// remove punctuation
			.replace(/[.(),;:!?%#$'\"_+=\/\-“”’]*/g, "");
		}

		$.validator.addMethod("maxWords", function(value, element, params) {
			return this.optional(element) || stripHtml(value).match(/\b\w+\b/g).length <= params;
		}, $.validator.format("Please enter {0} words or less."));

		$.validator.addMethod("minWords", function(value, element, params) {
			return this.optional(element) || stripHtml(value).match(/\b\w+\b/g).length >= params;
		}, $.validator.format("Please enter at least {0} words."));

		$.validator.addMethod("rangeWords", function(value, element, params) {
			var valueStripped = stripHtml(value),
				regex = /\b\w+\b/g;
			return this.optional(element) || valueStripped.match(regex).length >= params[0] && valueStripped.match(regex).length <= params[1];
		}, $.validator.format("Please enter between {0} and {1} words."));

	})();

	// phone number
	$.validator.addMethod('phoneNumber', function (value, element, param) {
		return this.optional(element) || value.match(/^([\d|+])([\d|\-]{6,18})(\d)$/);
	}, "Phone Number is invalid.");
	$.validator.addMethod('hasOne', function (value, element, param) {
		if(param){
			var name = element.getAttributeNode("name").value;
			var dom = $('[name="'+name+'"]'), len = dom.length;
			var num = 0;
			for(var i = 0; i < len; i++){
				if(dom.eq(i).val()){
					num++;
				}
			}
			if(num > 0){
				return true;
			} else {
				return false;
			}
		}
	},'Data is invalid');

}));