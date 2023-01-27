import Helpers from "./Helpers.js";

export default class NoteScreen {
	static INSTANCES = {};

	constructor(element) {
		this.notescreen = element;
		this.id = this.notescreen.id || false;

		this.source = this.notescreen.dataset.source || false;
		this.data = null;

		this.pages = {};
		this.pageKeys = [];

		this.activePage = 0;
		this.activeArticle = 0;

		this.init();
	}

	static ScanAndCreate() {
		$("*[role='notescreen']").each((ids, el) => {
			NoteScreen.INSTANCES[el.id] = new NoteScreen(el);
		});
	}

	init = async () => {
		this.createStructure();
		await this.getData();
		this.fillData();

		this.activePage = 0;
		this.activeArticle = 0;
		this.loopPages();
	};

	reInit = async () => {
		await this.getData();
		this.fillData();

		this.activePage = 0;
		this.activeArticle = 0;
		this.loopPages();
	};

	createStructure = () => {
		this.pagesContainer = document.createElement("div");
		this.pagesContainer.classList.add("col-3", "list-group");

		this.articleContainer = document.createElement("div");
		this.articleContainer.classList.add("col-9");

		this.notescreen.appendChild(this.pagesContainer);
		this.notescreen.appendChild(this.articleContainer);
	};

	getData = () => {
		if (!this.source) return;

		return $.get(this.source).done(data => {
			this.data = data;
		});
	};

	fillData = () => {
		if (!this.data.pages || !this.data.articles) return;
		this.pages = {};
		this.pageKeys = [];

		this.pagesContainer.innerHTML = "";
		this.articleContainer.innerHTML = "";

		this.data.pages.forEach(element => {
			this.pages[`page_${element.id}`] = {
				id: element.id,
				articles: [],
			};

			this.pageKeys.push(`page_${element.id}`);
			this.createPage(element);
		});

		this.data.articles.forEach(element => {
			this.pages[`page_${element.notescreenPageId}`].articles.push({
				id: element.id,
				timeout: element.displayTime
			});

			this.createArticle(element);
		});
	};

	createPage = (data) => {
		let page = document.createElement("div");
		page.id = `page_${data.id}`;
		page.classList.add("list-group-item", "list-group-item-action");
		page.innerHTML = data.name;

		this.pagesContainer.appendChild(page);
	};

	createArticle = (data) => {
		let article = document.createElement("div");
		article.id = `article_${data.id}`;
		article.classList.add("card", "d-none");

		let articleTitleContainer = document.createElement("div");
		articleTitleContainer.classList.add("card-header");
		article.appendChild(articleTitleContainer);

		let articleTitle = document.createElement("h3");
		articleTitle.classList.add("card-title");
		articleTitle.innerHTML = data.title;
		articleTitleContainer.appendChild(articleTitle);

		let articleBodyContainer = document.createElement("div");
		articleBodyContainer.classList.add("card-body");
		articleBodyContainer.innerHTML = data.content;

		article.appendChild(articleBodyContainer);

		this.articleContainer.appendChild(article);
	};

	loopPages = () => {
		if (this.activePage === this.pageKeys.length) this.reInit();
		else {
			let page = this.pages[this.pageKeys[this.activePage]];
			let pageTimeout = 500;
			page.articles.map(a => a.timeout).forEach(t => pageTimeout += t);

			this.activatePage(page.id);
			this.loopArticles(page);

			setTimeout(() => {
				this.activePage++;
				this.loopPages();
			}, pageTimeout);
		}
	};

	loopArticles = (page) => {
		if (page.articles.length === 0) return;

		if (this.activeArticle === page.articles.length) this.activateArticle(0);
		else {
			let article = page.articles[this.activeArticle];

			this.activateArticle(article.id);

			setTimeout(() => {
				this.activeArticle++;
				this.loopArticles(page);
			}, article.timeout);
		}
	};

	activatePage = (pageId) => {
		$(`[id^=page_][id=page_${pageId}]`).addClass("active");
		if (this.data.settings) {
			$(`[id^=page_][id=page_${pageId}]`)
				.css("background-color", this.data.settings.color)
				.css("color", "white")
				.css("border-left-color", this.data.settings.color);
		}

		$(`[id^=page_][id!=page_${pageId}]`)
			.removeClass("active")
			.css("background-color", "")
			.css("color", "")
			.css("border-left-color", "");
	};

	activateArticle = (articleId) => {
		$(`[id^=article_][id!=article_${articleId}]`).addClass("d-none");
		$(`[id^=article_][id=article_${articleId}]`).removeClass("d-none");
	};
}