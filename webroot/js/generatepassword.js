function suggestPassword(passwd_form) {
// restrict the password to just letters and numbers to avoid problems:
// "editors and viewers regard the password as multiple words and
// things like double click no longer work"
//text_pma_pw2
	var pwchars = "abcdefhjmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWYXZ";
	var passwordlength = 6; // do we want that to be dynamic? no, keep it simple :)
	var passwd = passwd_form.text;
	passwd.value = '';
for ( i = 0; i < passwordlength; i++ ) {
		passwd.value += pwchars.charAt( Math.floor( Math.random() * pwchars.length ) )
}
window.document.getElementById("password").value = passwd.value;
//passwd_form.getElementById("password").value = passwd.value;
//passwd_form.text.value = passwd.value;
//passwd_form.text_pma_pw2.value = passwd.value;
//alert(passwd_form.password.value);
return true;
} 

function issuePassword(passwd_form) {
// restrict the password to just letters and numbers to avoid problems:
// "editors and viewers regard the password as multiple words and
// things like double click no longer work"
//text_pma_pw2
	var pwchars = "abcdefhjmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWYXZ";
	var passwordlength = 8; // do we want that to be dynamic? no, keep it simple :)
	var passwd = passwd_form.text;
	passwd.value = '';
for ( i = 0; i < passwordlength; i++ ) {
		passwd.value += pwchars.charAt( Math.floor( Math.random() * pwchars.length ) )
}
window.document.getElementById("password").value = passwd.value;
//passwd_form.getElementById("password").value = passwd.value;
//passwd_form.text.value = passwd.value;
//passwd_form.text_pma_pw2.value = passwd.value;
//alert(passwd_form.password.value);
return true;
} 
