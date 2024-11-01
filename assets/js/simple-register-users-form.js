function ValidateSRUF() {
	
	// Variable declaration
	var error = false;
	
	var username = document.forms["frontsruf"]["sruf_username"].value;
	var subject = document.forms["frontsruf"]["sruf_subject"].value;
	var email = document.forms["frontsruf"]["sruf_email"].value;
	var comments = document.forms["frontsruf"]["sruf_comments"].value;
	
	// Form field validation
	if (typeof username !== 'undefined') {
		if(username.length == 0){
			var error = true;
			jQuery('#username_error').fadeIn(500);
			return false;
		}else{
			jQuery('#username_error').fadeOut(500);
		}
	}
	
	if (typeof subject !== 'undefined') {
		if(subject.length == 0){
			var error = true;
			jQuery('#subject_error').fadeIn(500);
			return false;
		}else{
			jQuery('#subject_error').fadeOut(500);
		}
	}
	
	if(typeof email !== 'undefined') {
		var atpos = email.indexOf("@");
		var dotpos = email.lastIndexOf(".");
		if(email.length == 0) {
			var error = true;
			jQuery('#email_error').fadeIn(500);
			return false;
		} else if (atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length) {
			var error = true;
			jQuery('#email_error').text('Not a valid e-mail address.');
			jQuery('#email_error').fadeIn(500);
			return false;
		} else {
			jQuery('#email_error').fadeOut(500);
		}
	}	
	
	if (typeof comments !== 'undefined') {
		if(comments.length == 0){
			var error = true;
			jQuery('#comments_error').fadeIn(500);
			return false;
		}else{
			jQuery('#comments_error').fadeOut(500);
		}
	}		


}
