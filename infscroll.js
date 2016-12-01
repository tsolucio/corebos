'use strict';
function getPageId(n) {
    return 'article-page-' + n;
}
function getDocumentHeight() {
    var body = document.body;
    var html = document.documentElement;
    return Math.max(body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight);
}
;
function getScrollTop() {
    return window.pageYOffset !== undefined ? window.pageYOffset : (document.documentElement || document.body.parentNode || document.body).scrollTop;
}
function getArticleImage() {
    var hash = Math.floor(Math.random() * Number.MAX_SAFE_INTEGER);
    var image = new Image();
    image.className = 'article-list__item__image article-list__item__image--loading';
    image.src = 'http://api.adorable.io/avatars/250/' + hash;
    image.onload = function () {
        image.classList.remove('article-list__item__image--loading');
    };
    return image;
}

function getAccounts(page){
	return new Promise(function(resolve, reject) {
		var url = 'infscroll.php?page='+page;
		var req = new XMLHttpRequest();
		req.open('GET', url, true);  // make call asynchronous

		req.onload = function() {
			// check the status
			if (req.status == 200) {
				// Resolve the promise with the response text
				resolve(req.response);
			} else {
				// Otherwise reject with the status text which will hopefully be a meaningful error
				reject(Error(req.statusText));
			}
		};

		// Handle errors
		req.onerror = function() {
			reject(Error("Network/Script Error"));
		};

		// Make the request
		req.send();
	});
}
function getArticle(page) {
    //var articleImage = getArticleImage();
    return getAccounts(page).then(function(response) {
    	var rsp = document.createElement('div');
    	rsp.innerHTML=response;
    var article = document.createElement('article');
    article.className = 'article-list__item';
    article.appendChild(rsp);
    return article;
});
}
function getArticlePage(page) {
    var articlesPerPage = arguments.length <= 1 || arguments[1] === undefined ? 2 : arguments[1];
    var pageElement = document.createElement('div');
    pageElement.id = getPageId(page);
    pageElement.className = 'article-list__page';
    var ps = new Array();
    while (articlesPerPage--) {
        var prm = getArticle(page).then(function(response) {
        pageElement.appendChild(response);
        });
        ps.push(prm);
    }
    return Promise.all(ps).then(function(resp) {
    	return pageElement;
    });
}
function addPaginationPage(page) {
    var pageLink = document.createElement('a');
    pageLink.href = '#' + getPageId(page);
    pageLink.innerHTML = page;
    var listItem = document.createElement('li');
    listItem.className = 'article-list__pagination__item';
    listItem.appendChild(pageLink);
    articleListPagination.appendChild(listItem);
    if (page === 2) {
        articleListPagination.classList.remove('article-list__pagination--inactive');
    }
}
function fetchPage(page) {
	return getArticlePage(page).then(function(response){
    articleList.appendChild(response);
	});
}
function addPage(page) {
    return fetchPage(page).then(function(response){
    addPaginationPage(page);
    });
}
var articleList = document.getElementById('article-list');
var articleListPagination = document.getElementById('article-list-pagination');
var page = 0;
addPage(++page);
window.onscroll = function () {
    if (getScrollTop() < getDocumentHeight() - window.innerHeight)
        return;
    addPage(++page);
};