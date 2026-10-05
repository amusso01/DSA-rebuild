// Header (header-new.php): desktop dropdowns, mobile panel with accordion-js submenus.
// Markup classes (.ac / .ac-trigger / .ac-panel) come from inc/function-navigation.php.
import Accordion from 'accordion-js'

// below the include-media 'tablet' breakpoint (1140px), same as _navigation.scss
const MOBILE_QUERY = '(max-width: 1139px)'

export default function headerNavigation() {
	const header = document.querySelector('[data-2026="header"]')
	if (!header) return

	const menu = header.querySelector('.main-nav__menu')
	const hamburger = header.querySelector('.hamburger')
	const parents = menu ? [...menu.querySelectorAll(':scope > .menu-item-has-children')] : []
	const mobile = window.matchMedia(MOBILE_QUERY)
	let accordion = null

	/* Desktop dropdowns
	–––––––––––––––––––––––– */
	const setOpen = (item, open) => {
		item.classList.toggle('is-open', open)
		item.querySelector('.submenu-toggle')?.setAttribute('aria-expanded', String(open))
	}
	const closeDropdowns = (except) => {
		parents.forEach((item) => {
			if (item !== except) setOpen(item, false)
		})
	}

	parents.forEach((item) => {
		item.querySelector('.submenu-toggle')?.addEventListener('click', () => {
			if (mobile.matches) return // accordion-js owns the click on mobile
			const open = !item.classList.contains('is-open')
			closeDropdowns(item)
			setOpen(item, open)
		})

		item.addEventListener('focusout', (e) => {
			if (!mobile.matches && !item.contains(e.relatedTarget)) setOpen(item, false)
		})
	})

	document.addEventListener('click', (e) => {
		if (!mobile.matches && menu && !menu.contains(e.target)) closeDropdowns()
	})

	/* Mobile panel
	–––––––––––––––––––––––– */
	const setPanel = (open) => {
		header.classList.toggle('is-nav-open', open)
		document.body.classList.toggle('noscroll', open)
		if (hamburger) {
			hamburger.setAttribute('aria-expanded', String(open))
			hamburger.setAttribute('aria-label', open ? 'Close menu' : 'Open menu')
		}
	}

	hamburger?.addEventListener('click', () => setPanel(!header.classList.contains('is-nav-open')))

	document.addEventListener('keydown', (e) => {
		if (e.key !== 'Escape') return

		if (header.classList.contains('is-nav-open')) {
			setPanel(false)
			hamburger?.focus()
			return
		}

		const open = parents.find((item) => item.classList.contains('is-open'))
		if (open) {
			setOpen(open, false)
			open.querySelector('.submenu-toggle')?.focus()
		}
	})

	/* Mobile accordion (accordion-js)
	–––––––––––––––––––––––– */
	const enableMobile = () => {
		closeDropdowns()
		if (!accordion && menu) {
			accordion = new Accordion(menu, { duration: 300 })
		}
	}

	const disableMobile = () => {
		setPanel(false)
		if (!accordion) return

		accordion.destroy()
		accordion = null

		// destroy() opens every item inside a requestAnimationFrame:
		// reset to the closed desktop state once it has run
		requestAnimationFrame(() =>
			requestAnimationFrame(() => {
				if (accordion) return // back on mobile already
				parents.forEach((item) => {
					item.classList.remove('is-active')
					item.querySelector('.ac-panel')?.removeAttribute('style')
					setOpen(item, false)
				})
			})
		)
	}

	mobile.addEventListener('change', () => (mobile.matches ? enableMobile() : disableMobile()))
	if (mobile.matches) enableMobile()
}
