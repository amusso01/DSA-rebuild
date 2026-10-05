import { CountUp } from 'countup.js'
window.onload = function () {
	// let t = new i('countUpNum', 39.9, {
	// 	decimalPlaces: 1,
	// 	duration: 4,
	// 	enableScrollSpy: !0,
	// })
	// t.error ? console.error(t.error) : t.start()

	const animatedGrid = document.querySelector('.countUpAnimationItem')
	const animatedItems = animatedGrid.querySelectorAll('svg')

	let counter = new CountUp('countUpNum', 39.9, {
		decimalPlaces: 1,
		duration: 4.5,
		enableScrollSpy: true,
		scrollSpyDelay: 400,
		onStartCallback: () => {
			const duration = counter.duration //get duration of the countUp instance
			const interval = (duration - 800) / animatedItems.length
			let index = 0
			if (animatedGrid.classList.contains('completed')) {
				animatedGrid.classList.remove('completed')
				animatedItems.forEach((element) => {
					element.classList.remove('filled')
				})
			}
			setInterval(() => {
				if (index < animatedItems.length) {
					animatedItems[index].classList.add('filled')
					index++
				} else {
					clearInterval(this)
				}
			}, interval)
		},
		onCompleteCallback: () => {
			animatedGrid.classList.add('completed')
		},
	})
	if (!counter.error) {
		counter.start()
	} else {
		console.error(counter.error)
	}
}
