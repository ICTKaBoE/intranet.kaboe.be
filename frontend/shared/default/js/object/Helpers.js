import Select from "./Select.js";

export default class Helpers {
	static toggleWait = () => {
		this.toggleModal('wait');
	};

	static toggleModal = (id) => {
		let modal = bootstrap.Modal.getOrCreateInstance(`#modal-${id}`, { focus: true });
		modal.toggle();
	};

	static closeAllModals = () => {
		$(".modal").modal('hide');
	};

	static redirect = (link) => {
		let url = window.location.href;

		if (String(link).startsWith('http')) url = link;
		else url += link;

		window.location.href = url;
	};

	static addFloatingButton = (...buttons) => {
		for (let button of buttons) document.getElementById("floating-buttons").appendChild(button.write());
	};

	static generateId = (prefix = null, length = 12) => {
		let result = '';
		const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		const charactersLength = characters.length;
		let counter = 0;
		while (counter < length) {
			result += characters.charAt(Math.floor(Math.random() * charactersLength));
			counter += 1;
		}

		return prefix + result;
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

	static getObjectValue = (from, ...selectors) =>
		[...selectors].map(s =>
			s
				.replace(/\[([^\[\]]*)\]/g, '.$1.')
				.split('.')
				.filter(t => t !== '')
				.reduce((prev, cur) => prev && prev[cur], from)
		);

	static flattenObject = (obj) => {
		let result = {};

		for (const i in obj) {
			if ((typeof obj[i]) === 'object' && !Array.isArray(obj[i])) {
				const temp = this.flattenObject(obj[i]);
				for (const j in temp) {
					result[i + '.' + j] = temp[j];
				}
			} else {
				result[i] = obj[i];
			}
		}
		return result;
	};

	static formatValue = (value, type, format, originalData = null) => {
		if (undefined === format) return value;

		switch (type) {
			case 'double': {
				value = parseFloat(value);
				if (format.precision) value.toFixed(format.precision);
				if (format.prefix) value = `${format.prefix}${value}`;
				if (format.suffix) value = `${value}${format.suffix}`;
			} break;
			case 'password': {
				let str = "";
				let replace = format?.replace || "*";
				let length = value.length;

				for (let i = 0; i < length; i++) str += replace;
				value = str;
			}

			default: value = value;
		}

		if (format.length) {
			value = value.substring(0, format.length) + (value.length >= format.length ? "..." : "");
		}

		return value;
	};

	static request = ({ url, method = 'GET', data = null, initiator, done = () => { }, fail = () => { }, always = () => { } }) => {
		let properties = {
			url: url,
			method: method,
			cache: false,
			processData: false,
			contentType: false
		};

		if (method === 'POST') properties.data = data;

		return $.ajax(properties)
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

	static cleanRowIndexes = (rows) => {
		let result = [];

		for (let i in rows) {
			if (rows.hasOwnProperty(i)) result.push(rows[i]);
		}

		return result;
	};

	static isValidUrl = (url) => {
		return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
	};

	static sleep = (ms) => {
		const date = Date.now();
		let currentDate = null;

		do {
			currentDate = Date.now();
		} while (currentDate - date < ms);
	};

	static CheckAllLoaded = (callback) => {
		let intv = setInterval(() => {
			allLoaded();
		}, 100);

		let allLoaded = () => {
			if (Select.Loaded()) {
				clearInterval(intv);
				callback();
			}
		};
	};
};;;