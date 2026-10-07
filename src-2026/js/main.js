import { ready } from './utils/ready'
import reveal from './modules/reveal'
import headerNavigation from './modules/headerNavigation'
import blogRow from './modules/blogRow'
import contactForm from './modules/contactForm'

// One call per component module; each returns early when its element isn't on the page.
// Libraries are imported normally inside the modules and bundled here. Use a lazy import()
// only for a large library needed on one page (see "Decisions" in restructure.md).
ready(() => {
	reveal() // first: hides below-the-fold content as early as possible
	headerNavigation()
	blogRow()
	contactForm()
})
