window.onload = choosePic;
var myPix = new Array('themes/login/images/undraw_creative_team.png', 'themes/login/images/undraw_teamwork.png');

function choosePic() {
	let randomNum = Math.floor((Math.random() * myPix.length));
	var element =  document.getElementById('myPicture');
	if (typeof(element) != 'undefined' && element != null) {
		document.getElementById('myPicture').src = myPix[randomNum];
	}
}

function showPassword() {
	const password = document.getElementById('password');
	const btn = document.getElementById('btnid');
	if (password.type === 'password') {
		btn.innerHTML = `
			<svg class="slds-button__icon" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#hide"></use>
			</svg>`;
		password.type = 'text';
	} else {
		btn.innerHTML = `
			<svg class="slds-button__icon" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#preview"></use>
			</svg>`;
		password.type = 'password';
	}
}