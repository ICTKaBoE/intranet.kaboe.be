document.getElementById("email").onkeyup = (e) => {
	let v = document.getElementById("email").value;

	if (v.includes("@")) document.getElementById("password").parentElement.classList.add("d-none");
	else document.getElementById("password").parentElement.classList.remove("d-none");
};