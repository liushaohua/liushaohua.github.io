var HYQValidHelper = {

	_RegEmail : /^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/,//邮件正则表达式
	_RegCellphone : /^(13[0-9]|15[0|3|6|7|8|9]|18[8|9])\d{8}$/,//手机号正则表达式
	_mailAjaxValidApi:'',
	_errTemplate:'<div class="hyq-f-err"><i class="hyq-ic ic-vlid-err"></i> <label>{{___MSG___}}</label>',
	_isEmpty : function(obj) {
		for (var name in obj) {
			return false;
		}
		return true;
	},
	_radioRequired:function(domName){
		var flag = false;
		$("input[name='"+domName+"']").each(function(i,item){
			flag = $(item).prop('checked')&&flag;
		});

		return flag;
	},
	_requiredValid : function(domNode) {
		if($.trim($(domNode).val()=="")){
			return false;
		}
		return true;
	},
	_emailValid : function(email) {
		return this._RegEmail.test(email);
	},
	_mobileValid : function(mobile) {
		return this._RegCellphone.test(mobile);
	},
	_matchValid : function(valA,valB) {
		return valA==valB;
	},
	createNew : function(form,errors) {

		var v = {};
		v.form = form;
		v.errors=errors;
 

		v.showError = function(errKey) {
			if (v.errors[errKey]) {
				var err = v.errors[errKey];
				if(err.whyNode==null){ 
					var why = HYQValidHelper._errTemplate.replace("{{___MSG___}}",err.why);
					err.whyNode = $(why);
					err.who.after(err.whyNode);
				}
			}
		}

		v.removeError = function(errKey) {
			if (v.errors[errKey]) {
				var err = v.errors[errKey];
				$(err.whyNode).remove();
				 err.whyNode =null;
			}
		}

		v.addErrItem = function(key, who, why) {
			if(v.errors){
				v.errors[key] = {
					why : why,
					node : who,
					whyNode:null
				};
			}
		}
		v.removeErrItem = function(key){
			try{
				delete v.errors[key];
			}catch(e){

			}
		}
		v.addErrItems=function(errors){
			v.errors = errors;
		}
		return v;
	}
}