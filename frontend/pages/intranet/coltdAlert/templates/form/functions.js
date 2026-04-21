$(document).ready(() => {
	let contentLength = document.getElementById("content_length");
	let content = document.getElementById("content");

	contentLength.innerHTML = `0/${content.maxLength}`;

	content.onkeyup = (v) => {
		contentLength.innerHTML = `${content.value.length}/${content.maxLength}`;
	};
});
