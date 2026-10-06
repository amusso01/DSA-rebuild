// Contact form (components-2026/page/contact-form.php): keeps the progress steps and the steps'
// error messages in sync with the Multi Step for Contact Form 7 plugin. The plugin (jQuery) switches
// steps by moving .cf7mls_current_fs between its fieldsets; this module only watches the classes.
export default function contactForm() {
	const cards = document.querySelectorAll('.contact-form__card')
	if (!cards.length) return

	cards.forEach((card) => {
		const form = card.querySelector('.wpcf7-form')
		if (!form) return

		const steps = card.querySelectorAll('.contact-form__step')
		let queued = false

		const sync = () => {
			queued = false
			const fieldsets = [...form.querySelectorAll('.fieldset-cf7mls')]
			const current = fieldsets.findIndex((fieldset) => fieldset.classList.contains('cf7mls_current_fs'))

			// no fieldsets (plugin inactive): keep the first step from the PHP
			if (current !== -1) {
				steps.forEach((step, i) => {
					step.classList.toggle('is-current', i === current)
					step.classList.toggle('is-done', i < current)
					if (i === current) step.setAttribute('aria-current', 'step')
					else step.removeAttribute('aria-current')
				})
			}

			// each step's .form-error (in the CF7 form) shows while the step has an invalid field;
			// it's role="alert", so screen readers announce it
			fieldsets.forEach((fieldset) => {
				const error = fieldset.querySelector('.form-error')
				if (error) error.hidden = !fieldset.querySelector('.wpcf7-not-valid')
			})

			// sent: CF7's message replaces the steps (_contact-form.scss)
			card.classList.toggle('is-sent', form.classList.contains('sent'))
		}

		// the plugin changes classes after its AJAX validation: one sync per frame is enough.
		// sync() only touches the hidden attribute inside the form, so it doesn't retrigger this.
		new MutationObserver(() => {
			if (queued) return
			queued = true
			requestAnimationFrame(sync)
		}).observe(form, { attributes: true, attributeFilter: ['class'], subtree: true })

		sync()
	})
}
