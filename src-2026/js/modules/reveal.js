// Reveal (common/_reveal.scss): every data-reveal element fades up once when it scrolls into view,
// the heroes included. The inline head script (dsa_2026_reveal_script() in inc/function-assets.php)
// hides them from the first paint with html.reveal-on, and shows everything if this hasn't run within 3s.
const html = document.documentElement

let observer = null

export default function reveal() {
	// no head script, or its 3s fallback already showed everything
	if (!html.classList.contains('reveal-on')) return
	html.classList.add('reveal-ready')

	if (!('IntersectionObserver' in window)) {
		html.classList.remove('reveal-on')
		return
	}
	// with reduced motion the CSS hides nothing
	if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return

	const elements = document.querySelectorAll('[data-reveal]')
	if (!elements.length) return

	// reveals when the top passes 90% of the viewport. threshold 0, not a ratio: an element taller
	// than the viewport would never reach one. The huge top margin counts everything above the viewport
	// as intersecting, so an element jumped over (anchor link, End key) is shown, not left as a hole.
	observer = new IntersectionObserver(onIntersect, { rootMargin: '100000px 0px -10% 0px', threshold: 0 })

	// web fonts first: text that swaps font mid-fade rewraps, and the block changes size
	fontsReady().then(() => elements.forEach((element) => observer.observe(element)))
}

// Content inserted later (Blog row Load more): html.reveal-on already hides it.
export function revealIn(container) {
	if (!observer) return
	container.querySelectorAll('[data-reveal]:not(.is-revealed)').forEach((element) => observer.observe(element))
}

function fontsReady() {
	// layout starts the font downloads; without it fonts.ready may resolve before they begin
	void document.body.offsetHeight
	// capped: on a slow connection the content shows after 1s, in the fallback font
	return Promise.race([document.fonts.ready, new Promise((resolve) => setTimeout(resolve, 1000))])
}

function onIntersect(entries) {
	entries.forEach((entry) => {
		if (!entry.isIntersecting) return

		entry.target.classList.add('is-revealed')
		observer.unobserve(entry.target)
	})
}
