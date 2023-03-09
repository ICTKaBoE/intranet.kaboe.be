import Button from "./Button.js";
import Helpers from "./Helpers.js";

export default class TaskBoard {
	static INSTANCES = {};

	constructor(element) {
		this.taskboard = element;
		this.id = this.taskboard.id || false;

		this.source = this.taskboard.dataset.source || false;
		this.update = this.taskboard.dataset.update || false;
		this.defaultTab = this.taskboard.dataset.defaultTab || false;
		this.data = null;

		this.init();
	}

	static ScanAndCreate() {
		$("*[role='taskboard']").each((ids, el) => {
			TaskBoard.INSTANCES[el.id] = new TaskBoard(el);
		});
	}

	init = async () => {
		this.createStructure();
		await this.getData();

		Helpers.toggleWait();

		setTimeout(() => {
			this.createTabs();
			this.createContent();
			Helpers.toggleWait();
		}, 500);
	};

	reInit = async () => {
		Helpers.toggleWait();
		setTimeout(async () => {
			this.clear();
			await this.getData();

			this.createTabs();
			this.createContent();
			Helpers.toggleWait();
		}, 500);
	};

	createStructure = () => {
		this.navTabContainer = document.createElement("ul");
		this.navTabContainer.classList.add("nav", "nav-bordered");
		this.navTabContainer.setAttribute("data-bs-toggle", "tabs");

		this.tabContentContainer = document.createElement("div");
		this.tabContentContainer.classList.add("tab-content");

		this.taskboard.appendChild(this.navTabContainer);
		this.taskboard.appendChild(this.tabContentContainer);
	};

	getData = () => {
		if (!this.source) return;

		return $.get(this.source).done(data => {
			this.data = data;
		});
	};

	clear = () => {
		this.navTabContainer.innerHTML = "";
		this.tabContentContainer.innerHTML = "";
	};

	createTabs = () => {
		if (!this.data.tabs) return;
		let tabCount = 0;

		this.data.tabs.forEach(tab => {
			let tabItem = document.createElement("li");
			tabItem.classList.add("nav-item");

			let tabLink = document.createElement("a");
			tabLink.classList.add("nav-link");
			tabLink.setAttribute("data-bs-toggle", "tab");
			tabLink.href = `#tab-${tab.id}`;
			tabLink.innerHTML = tab.name;
			if (!this.defaultTab && tabCount === 0) tabLink.classList.add("active");
			else if (this.defaultTab == tab.id) tabLink.classList.add("active");

			tabItem.appendChild(tabLink);
			this.navTabContainer.appendChild(tabItem);

			tabCount++;
		});
	};

	createContent = () => {
		if (!this.data.tabs) return;
		let tabCount = 0;

		this.data.tabs.forEach(tab => {
			let tabPane = document.createElement("div");
			tabPane.classList.add("tab-pane");
			tabPane.id = `tab-${tab.id}`;
			if (!this.defaultTab && tabCount === 0) tabPane.classList.add("active", "show");
			else if (this.defaultTab == tab.id) tabPane.classList.add("active", "show");

			this.createColumns(tabPane);

			this.tabContentContainer.appendChild(tabPane);

			tabCount++;
		});
	};

	createColumns = (pane) => {
		if (!this.data.columns) return;
		let columnCount = 0;

		let div = document.createElement("div");
		div.classList.add("row");

		this.data.columns.forEach(col => {
			let column = document.createElement("div");
			column.classList.add("col-12", "col-md-6", "col-lg");

			let title = document.createElement("h2");
			title.classList.add("mb-3");
			title.innerHTML = col.name;
			column.appendChild(title);

			this.createJobs(column, pane.id, col.id);

			div.appendChild(column);
			columnCount++;
		});

		pane.appendChild(div);
	};

	createJobs = (column, tabId, type) => {
		if (!this.data.jobs) return;

		let jobContainer = document.createElement("div");
		jobContainer.classList.add("mb-4");

		let jobSubContainer = document.createElement("div");
		jobSubContainer.classList.add("row", "row-cards");

		this.data.jobs[parseInt(tabId.replace("tab-", ""))][type].forEach(job => {
			this.createJob(jobSubContainer, job);
		});

		jobContainer.appendChild(jobSubContainer);
		column.appendChild(jobContainer);
	};

	createJob = (container, job) => {
		let cardContainer = document.createElement("div");
		cardContainer.classList.add("col-12");

		let card = document.createElement("div");
		card.classList.add("card");

		if (job.priority) {
			let status = document.createElement("div");
			status.classList.add("card-status-top", "bg-" + (job.priority === "high" ? "red" : job.priority === "medium" ? "orange" : "green"));
			card.appendChild(status);
		}

		if (job.title || job.actions.length !== 0) {
			let cardHeader = document.createElement("div");
			cardHeader.classList.add("card-header");

			let title = document.createElement("h3");
			title.classList.add("card-title");
			title.innerHTML = job.title;

			cardHeader.appendChild(title);

			if (job.actions.length !== 0) {
				let cardActions = document.createElement("div");
				cardActions.classList.add("card-actions", "btn-actions");

				if (job.actions.includes("setInProgress")) {
					let btnSetInProgress = new Button({
						type: 'icon-title',
						text: 'Zet In Uitvoering',
						icon: 'rotate',
						classes: ['btn-action'],
						onclick: "setInProgress",
						params: [job.id]
					});

					cardActions.appendChild(btnSetInProgress.write());
				}

				if (job.actions.includes("setWaiting")) {
					let btnSetWaiting = new Button({
						type: 'icon-title',
						text: 'Zet In Afwachting',
						icon: 'hourglass-empty',
						classes: ['btn-action'],
						onclick: "setWaiting",
						params: [job.id]
					});

					cardActions.appendChild(btnSetWaiting.write());
				}

				if (job.actions.includes("setCompleted")) {
					let btnSetCompleted = new Button({
						type: 'icon-title',
						text: 'Zet Afgewerkt',
						icon: 'check',
						classes: ['btn-action'],
						onclick: "setCompleted",
						params: [job.id]
					});

					cardActions.appendChild(btnSetCompleted.write());
				}

				cardHeader.appendChild(cardActions);
			}

			card.appendChild(cardHeader);
		}

		if (job.body) {
			let cardBody = document.createElement("div");
			cardBody.classList.add("card-body");
			cardBody.innerHTML = job.body;

			card.appendChild(cardBody);
		}

		if (job.location) {
			let cardBody = document.createElement("div");
			cardBody.classList.add("card-body");
			cardBody.innerHTML = job.location;

			card.appendChild(cardBody);
		}

		if (job.executeBy) {
			let cardBody = document.createElement("div");
			cardBody.classList.add("card-body");
			cardBody.innerHTML = job.executeBy;

			card.appendChild(cardBody);
		}

		if (job.postDate) {
			let cardBody = document.createElement("div");
			cardBody.classList.add("card-body");
			cardBody.innerHTML = job.postDate;

			card.appendChild(cardBody);
		}

		if (job.finishedByDate) {
			let cardBody = document.createElement("div");
			cardBody.classList.add("card-body");
			cardBody.innerHTML = job.finishedByDate;

			card.appendChild(cardBody);
		}

		if (job.finishedAt) {
			let cardBody = document.createElement("div");
			cardBody.classList.add("card-body");
			cardBody.innerHTML = job.finishedAt;

			card.appendChild(cardBody);
		}

		cardContainer.appendChild(card);
		container.appendChild(cardContainer);
	};
}