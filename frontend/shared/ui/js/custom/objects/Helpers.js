export default class Helpers {
	static toggleWait = () => {
		this.toggleModal('wait');
	};

	static toggleModal = (id) => {
		let modal = bootstrap.Modal.getOrCreateInstance(`#modal-${id}`);
		modal.toggle();
	};

	static redirect = (link) => {
		let url = window.location.href;
		url += link;

		window.location.href = url;
	};

	static setCookie = (cname, cvalue, exdays = 30) => {
		const d = new Date();
		d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
		let expires = "expires=" + d.toUTCString();
		document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	};

	static getCookie = (cname) => {
		let name = cname + "=";
		let ca = document.cookie.split(';');
		for (let i = 0; i < ca.length; i++) {
			let c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
			}
		}
		return "";
	};

	static addButtonToPageTitle = (button) => {
		document.getElementById("pagetitle-buttons").appendChild(button.write());
	};

	static loadIcon = (name) => {
		let icon = $.ajax({
			url: `/frontend/shared/ui/icons/${name}.svg`,
			async: false
		});

		return icon.responseText;
	};

	static getObjectValue = (from, ...selectors) =>
		[...selectors].map(s =>
			s
				.replace(/\[([^\[\]]*)\]/g, '.$1.')
				.split('.')
				.filter(t => t !== '')
				.reduce((prev, cur) => prev && prev[cur], from)
		);

	static formatValue = (value, type, format) => {
		if (undefined === format) return value;

		switch (type) {
			case 'double': {
				value = parseFloat(value);
				if (format.precision) value.toFixed(format.precision);
				if (format.prefix) value = `${format.prefix}${value}`;
				if (format.suffix) value = `${value}${format.suffix}`;

				return value;
			} break;

			default:
				return value;
		}
	};
}