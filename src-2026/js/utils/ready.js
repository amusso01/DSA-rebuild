// Run fn once the DOM is parsed. Never assign window.onload:
// the legacy dist/bundle.js already does and only the last assignment wins.
export function ready(fn) {
	if (document.readyState !== 'loading') {
		fn()
	} else {
		document.addEventListener('DOMContentLoaded', fn)
	}
}
