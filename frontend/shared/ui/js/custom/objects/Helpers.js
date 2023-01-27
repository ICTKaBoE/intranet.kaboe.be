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

	static loadIcon = (name) => $.ajax({
		url: `/frontend/shared/ui/icons/${name}.svg`,
		async: false
	}).responseText;

	static getObjectValue = (from, ...selectors) =>
		[...selectors].map(s =>
			s
				.replace(/\[([^\[\]]*)\]/g, '.$1.')
				.split('.')
				.filter(t => t !== '')
				.reduce((prev, cur) => prev && prev[cur], from)
		);

	static formatValue = (value, type, format, originalData = null) => {
		if (undefined === format) return value;

		switch (type) {
			case 'double': {
				value = parseFloat(value);
				if (format.precision) value.toFixed(format.precision);
				if (format.prefix) value = `${format.prefix}${value}`;
				if (format.suffix) value = `${value}${format.suffix}`;
			} break;

			default: value = value;
		}

		if (format.textColor) {
			let textColor = "";
			if (typeof format.textColor == 'object') {
				if (format.textColor.condition) {
					let baseOnValue = this.getObjectValue(originalData, format.textColor.condition.baseOnValue)[0];

					if (format.textColor.condition.ifTrue) {
						if (baseOnValue === format.textColor.condition.ifTrue.check) textColor = format.textColor.condition.ifTrue.result;
						else if (baseOnValue === format.textColor.condition.ifFalse.check) textColor = format.textColor.condition.ifFalse.result;
					}
				}
			} else {
				textColor = this.getObjectValue(originalData, format.textColor)[0];
				if (textColor === undefined) textColor = format.textColor;
			}

			value = `<span class="text-${textColor}">${value}</span>`;
		}

		return value;
	};

	static request = ({ url, method = 'GET', data = {}, initiator, done = () => { }, fail = () => { }, always = () => { } }) => {
		return $.ajax({
			url: url,
			method: method,
			data: data || {},
			cache: false,
			processData: false,
			contentType: false
		})
			.done(returnData => {
				done(returnData);
			})
			.fail(returnData => {
				fail(returnData);
			})
			.always(returnData => {
				always(returnData);
			});
	};

	static sleep = (ms) => {
		const date = Date.now();
		let currentDate = null;

		do {
			currentDate = Date.now();
		} while (currentDate - date < ms);
	};
}