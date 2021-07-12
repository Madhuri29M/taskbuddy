$(document).ready(function() {	
		getPushNotification(notification_url);
		setInterval(function(){ getPushNotification(notification_url); }, 10000);
});
function getPushNotification(notification_url) {	
	// alert();
	if (!Notification) {
		$('body').append('<h4 style="color:red">*Browser does not support Web Notification</h4>');
		return;
	}
	if (Notification.permission !== "granted") {		
		Notification.requestPermission();
	} else {
		$.ajax({
			url : notification_url,
			type: "post",
			dataType : 'json',
			data: {
              '_token' : csrf_for_noti,
          	},
			success: function(response) {
				var response = response;
				if(response.result == true) {
					var notificationDetails = response.notif;
					for (var i = notificationDetails.length - 1; i >= 0; i--) {
						var notificationUrl = notificationDetails[i]['url'];
						var notificationObj = new Notification(notificationDetails[i]['title'], {
							icon: notificationDetails[i]['icon'],
							body: notificationDetails[i]['message'],
						});
						notificationObj.onclick = function () {
							window.open(notificationUrl); 
							notificationObj.close();     
						};
						setTimeout(function(){
							notificationObj.close();
						}, 10000);
					};
				} else {
				}
			},
			error: function(jqXHR, textStatus, errorThrown)	{}
		}); 
	}
};