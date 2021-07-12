var adminAjax = (function (i = null, ii = null){
	if(i == ''){ return; }
	var d = new FormData();
	d.append('visit_from', 'web');
	d.append('token', token);
	if(ii != null){
		var a = ii.toString().split(',');
		$.each(a,function(v){
		  var [o, t] = a[v].toString().split('::'); d.append(o, t);
		});
	}
	var ob = jQuery.ajax({
		url: i,
		type: 'POST',
		enctype: 'multipart/form-data',
		contentType: 'application/json; charset=UTF-8',
		processData: false,
		contentType: false,
		data: d,
		cache: false,
		async: false,
		success: function (response) {
		},
	}).responseText;
	return jQuery.parseJSON(ob);
});

var get_this_category = (function (item_id = null){
	if(item_id == ''){
		return false;
	}
	var data = new FormData();
	data.append('item_id', item_id);
	var object = $.ajax({
		url: "AdminApis/get_categories",
		type: 'POST',
		enctype: 'multipart/form-data',
		contentType: 'application/json; charset=UTF-8',
		processData: false,
		contentType: false,
		data: data,
		cache: false,
		async: false,
		success: function (response) {
		},
	}).responseText;
	var returndata = jQuery.parseJSON(object);
	if(returndata.status == 'ok'){
		return returndata.details[0];
	}
});