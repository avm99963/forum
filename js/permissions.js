function doit() {
	if (this.getAttribute("class") == "select") {
		var select = true;
	} else {
		var select = false;
	}

	var role = this.getAttribute("data-role");

	var checkboxes = document.querySelectorAll("input[type='checkbox'][data-role='"+role+"']");

	for (var i = 0; i < checkboxes.length; i++) {
		if (checkboxes[i].disabled === false) {
			checkboxes[i].checked = select;
		}
	}
}

function init() {
	var select = document.querySelectorAll(".select");
	var deselect = document.querySelectorAll(".deselect");

	for (var i = 0; i < select.length; i++) {
		select[i].addEventListener("click", doit);
	}

	for (var i = 0; i < deselect.length; i++) {
		deselect[i].addEventListener("click", doit);
	}
}

window.addEventListener("load", init);