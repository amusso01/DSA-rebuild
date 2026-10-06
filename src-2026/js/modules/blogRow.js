// Blog row (components-2026/sections/blog-row.php): "Load more" fetches the next 9 cards
// from the REST route in inc/function-blog.php and appends them to the grid.
// Only sections in "Show all" mode have data-blog-rest.
export default function blogRow() {
	const sections = document.querySelectorAll('.blog-row[data-blog-rest]')
	if (!sections.length) return

	sections.forEach((section) => {
		const button = section.querySelector('.blog-row__load')
		const grid = section.querySelector('.blog-row__grid')
		const shown = section.querySelector('.blog-row__shown')
		if (!button || !grid) return

		button.addEventListener('click', async () => {
			if (button.classList.contains('is-loading')) return

			const page = Number(button.dataset.page) + 1
			// URL API: works with pretty (/wp-json/…) and plain (?rest_route=…) permalinks
			const url = new URL(section.dataset.blogRest)
			url.searchParams.set('page', page)

			// spinner in the button (_blog-row.scss). aria-disabled, not disabled: the button keeps focus
			button.classList.add('is-loading')
			button.setAttribute('aria-disabled', 'true')
			grid.setAttribute('aria-busy', 'true')

			try {
				const response = await fetch(url)
				if (!response.ok) throw new Error(`Load more: ${response.status}`)

				const data = await response.json()
				const first = grid.children.length

				grid.insertAdjacentHTML('beforeend', data.html)
				button.dataset.page = page
				if (shown) shown.textContent = data.shown

				// keyboard users carry on from the first new card
				grid.children[first]?.querySelector('.blog-card__link')?.focus()

				if (!data.has_more) button.parentElement.remove()
			} catch (error) {
				// the button stays: the next click tries again
				console.error(error)
			} finally {
				button.classList.remove('is-loading')
				button.removeAttribute('aria-disabled')
				grid.removeAttribute('aria-busy')
			}
		})
	})
}
