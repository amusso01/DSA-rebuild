import { ready } from './utils/ready'
import * as example from './modules/example'

// Eager modules: small and used site-wide. Each init() must be safe on any page.
const modules = [example]

ready(() => {
	modules.forEach((module) => module.init())

	// Heavy, page-specific modules: load lazily so they don't weigh down main.js, e.g.
	// if (document.querySelector('[data-2026="gallery"]')) {
	// 	import('./modules/gallery').then((m) => m.init())
	// }
})
