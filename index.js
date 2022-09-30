if (document.documentElement.className != "js")
	throw 0;

let
	/* Variables */
	bookWidth,
	fontSize = 1,
	offset,
	freeScroll = 0,
	page,
	pages,
	pageCalc,
	pageTurning,
	theme = 0,
	touchDevice = "ontouchstart" in window,
	touchStartX,
	touchDeltaX = 0,

	/* Elements */
	book = document.getElementById("book"),
	contents = document.getElementById("contents"),
	pageNum = document.getElementById("pagenum"),

	/* Control Functions */
	swapInfo = (el) => {
		let text = el.textContent;
		el.textContent = el.title;
		el.title = text;
	}
	setScroll = (e) => {
		e.preventDefault();
		if (freeScroll ^= 1)
			pageNum.textContent = pageNum.alt;
		else
			calcDims();
	},
	setTheme = () => {
		document.body.className = `theme${theme = (theme + 1) % 3}`;
	},
	setSize = (val) => {
		contents.style.fontSize = (val < 0 && fontSize < .51 ? .5 : fontSize += val / 20) +"em";
		calcDims();
		turn(page);
	},
	jumpPage = () => {
		touchStartX = null;
		let to = prompt(pageNum.title.replace("(P)", "")) - 1;
		if (!isNaN(to) && to > -1)
			jump(to);
	},

	/* Pagination Functions */
	calcDims = () => {
		if (pageTurning)
			return;
		let pos = page && pages ? (page - .5) / pages : 0;
		bookWidth = book.offsetWidth;
		offset = bookWidth / 6;
		pages = Math.round(book.scrollWidth / bookWidth) - 1;
		page = pos ? Math.ceil(pos * pages) : Math.round(scrollX / bookWidth);
		pageNum.textContent = `${page + 1}/${pages}`;
		console.log("bookWidth", bookWidth, "| offset", offset, "| pages", pages, "| page", page + 1);
	},
	jump = (to) => turn(to, false),
	turn = (to, smooth = true) => {
		page = Math.max(to, 0);
		page = Math.min(page, pages - 1);
		turnTimeout();
		scrollTo({
			left: page * bookWidth,
			behavior: smooth ? "smooth" : "auto"
		});
		pageNum.textContent = `${page + 1}/${pages}`;
	},
	turnTimeout = () => { // prevent "onscroll"; prevent "onresize", triggered by keyboard popup
		if (pageTurning)
			clearTimeout(pageTurning);
		pageTurning = setTimeout(() => {
			clearTimeout(pageTurning);
			pageTurning = 0;
		}, 400);
	};

/* Keyboard Navigation */
document.addEventListener("keydown", (e) => {
	if (freeScroll || e.altKey || e.ctrlKey) return;
	switch (e.key) {
		case "ArrowUp":
		case "ArrowLeft":
		case "PageUp":
			page--;
			break;
		case "ArrowDown":
		case "ArrowRight":
		case "PageDown":
			page++;
			break;
		case "p":
			jumpPage();
			break;
		case "t":
			setTheme();
			break;
		case "-":
			setSize(-1);
			break;
		case "+":
		case "=":
			setSize(+1);
			break;
		default:
			return;
	}
	e.preventDefault();
	turn(page);
});

/* Touch Navigation */
book.addEventListener("touchstart", (e) => {
	if (freeScroll) return;
	e.preventDefault();
	touchStartX = e.changedTouches[0].screenX;
}, 1);
book.addEventListener("touchmove", (e) => {
	if (freeScroll) return;
	e.preventDefault();
	touchDeltaX = touchStartX - event.changedTouches[0].screenX;
	scrollTo(page * bookWidth + touchDeltaX, 0);
}, 1);
book.addEventListener("touchend", (e) => {
	if (freeScroll) return;
	e.preventDefault();
	touchStartX = null;
	if (touchDeltaX < -offset)
		page--;
	else if (touchDeltaX > offset)
		page++;
	else if (e.target.id != "contents" && Math.abs(touchDeltaX) < 10)
		e.target.click();
	touchDeltaX = 0;
	turn(page);
}, 1);

/* Wheel Navigation */
window.addEventListener("wheel", (e) => {
	if (freeScroll) return;
	e.preventDefault();
	if (e.deltaY < 0)
		page--;
	else if (e.deltaY > 0)
		page++;
	turn(page);
}, { passive: false });

/* Listeners */
window.addEventListener("resize", calcDims);
window.addEventListener("scroll", () => {
	if (freeScroll) return;
	if (pageTurning)
		return turnTimeout();
	scrolledTo = Math.min(Math.round(scrollX / bookWidth), pages - 1);
	if (scrolledTo > -1)
		pageNum.textContent = `${scrolledTo + 1}/${pages}`;
	if (pageCalc)
		clearTimeout(pageCalc);
	pageCalc = setTimeout(() => {
		if (pageTurning)
			return;
		page = Math.min(Math.round(scrollX / bookWidth), pages - 1);
	}, 200);
}, { passive: true });

/* Initialization */
setTimeout(calcDims, 400);