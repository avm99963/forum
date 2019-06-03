var num_attachments = 0;

function init() {
	document.querySelector("#attach_text").addEventListener("click", attachFile);
}

function attachFile() {
	if (num_attachments + 1 > max_file_uploads) {
		alert("Solo puedes adjuntar hasta "+max_file_uploads+" archivos adjuntos.");
		return false;
	}
	document.querySelector("#attachments").insertAdjacentHTML("beforeend", "<p class=\"attachment_container\"><input type=\"file\" name=\"attachment[]\"> <span class=\"remove_attachment\">x</span></p>");
	document.querySelector(".attachment_container:last-child .remove_attachment").addEventListener("click", removeFile);
	num_attachments++;
}

function removeFile() {
	this.parentNode.parentNode.removeChild(this.parentNode);
	num_attachments--;
}

window.addEventListener("load", init);