var validator = {
	success : true
	, args : []
	, init : function(args) {
		validator.args = args;
		validator.success = true;
	}
	, execute : function() {
		var args = validator.args;
		for (var i = 0; i < args.length; i++) {
			validator.validField(args[i]);
		}
		return validator.success;
	}
	, validField : function(eleField) {
		var ele = $(eleField[0]);
		var field = eleField[1];
		var parent = ele.getParent('.ynresume-form-wrapper');
		var error = parent.getElements('.error')[0];
		var value = ele.get('value');
		value = value.trim();
		switch (field) {
			
			case 'require':
				if (isEmpty(value)) {
					validator.success = false;
					var text = error.get('text');
					error.set('text', text+en4.core.language.translate(field+'_valid', eleField[2])+'\t');
					
				}
				break;
				
			case 'require-select':
				if (isEmpty(value) || value == '0000') {
					validator.success = false;
					var text = error.get('text');
					error.set('text', text+en4.core.language.translate(field+'_valid', eleField[2])+'\t');
					
				}
				break;
				
			case 'year':
				if (!isEmpty(value) && !isYear(value)) {
					validator.success = false;
					var text = error.get('text');
					error.set('text', text+en4.core.language.translate(field+'_valid', eleField[2])+'\t');
				}
				break;
				
			case 'year-before':
				var after_val = $(eleField[2]).get('value');
				if (isYear(value) && isYear(after_val) && after_val < value) {
					validator.success = false;
					var text = error.get('text');
					error.set('text', text+en4.core.language.translate(field+'_valid', eleField[3], eleField[4])+'\t');
				}
				break;
				
			case 'month-year-before':
				var start_month = $(eleField[2]).get('value');
				var start_year = $(eleField[0]).get('value');
				var end_month = $(eleField[4]).get('value');
				var end_year = $(eleField[3]).get('value');
				start_month = (start_month > 0) ? parseInt(start_month)-1 : 0;
				end_month = (start_month > 0) ? parseInt(end_month)-1 : 0;
				if (!isEmpty(start_year) && !isEmpty(end_year) && isYear(start_year) && isYear(end_year)) {
					var start_date = new Date(parseInt(start_year), start_month, 1);
					var end_date = new Date(parseInt(end_year), end_month, 1);
					if (start_date > end_date) {
						validator.success = false;
						var text = error.get('text');
						error.set('text', text+en4.core.language.translate(field+'_valid', eleField[5], eleField[6])+'\t');
					}
				}
				break;
				
			case 'month-year-before-current':
				var month = $(eleField[2]).get('value');
				var year = $(eleField[0]).get('value');
				month = (month > 0) ? parseInt(month)-1 : 0;
				var date = new Date(parseInt(year), month, 1);
				var current = new Date();
				if (date > current) {
					validator.success = false;
					var text = error.get('text');
					error.set('text', text+en4.core.language.translate(field+'_valid')+'\t');
				}
				break;
			
			case 'email':
				if(!validateEmail(value))
				{
					validator.success = false;
					var text = error.get('text');
					error.set('text', text+en4.core.language.translate(field+'_valid', eleField[2])+'\t');
				}
				break;
		}
	}
};

function validateEmail(email) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
} 

function isEmpty(s){
    return !s.trim().length;    
}

function isBlank(s){
    return isEmpty(s.trim());    
}

function isYear(s) {
	var _thisYear = new Date().getFullYear();
	if (s.length != 4) return false;
	if (!s.match(/\d{4}/)) return false;
	if (parseInt(s) > _thisYear || parseInt(s) < 1900) {
	 	return false;
	}
	return true;
}
