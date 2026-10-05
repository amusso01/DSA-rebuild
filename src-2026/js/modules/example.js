// Module pattern: find the root element, return early if it isn't on this page.
export function init() {
	const root = document.querySelector('[data-2026="example"]')
	if (!root) return

	root.classList.add('is-ready')
}
