document.getElementById("username").onkeyup = (e) => {
	let v = document.getElementById("username").value;

	if (v.includes("@")) document.getElementById("password").parentElement.classList.add("d-none");
	else document.getElementById("password").parentElement.classList.remove("d-none");
};