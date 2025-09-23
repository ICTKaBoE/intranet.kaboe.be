window.renderOptgroupItem = (data, escape) =>
	"<div>" +
	(data.optgroupName ? `${data.optgroupName} - ` : "") +
	`${data.name}</div>`;
