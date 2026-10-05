import { ready } from './utils/ready'
import headerNavigation from './modules/headerNavigation'

// One call per component module; each returns early when its element isn't on the page.
// Libraries are imported normally inside the modules and bundled here. Use a lazy import()
// only for a large library needed on one page (see "Decisions" in restructure.md).
ready(() => {
	headerNavigation()
})
