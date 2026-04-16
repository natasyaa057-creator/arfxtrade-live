// Toggle Dark Mode (dengan localStorage)
(function () {
	const KUNCI = 'tema_arfxtrade';
	const kelasGelap = 'tema-gelap';

	function terapkanTema(dark) {
		const html = document.documentElement;
		if (dark) {
			html.classList.add(kelasGelap);
			localStorage.setItem(KUNCI, 'gelap');
		} else {
			html.classList.remove(kelasGelap);
			localStorage.setItem(KUNCI, 'terang');
		}
	}

	function inisialisasiToggle() {
		const simpanan = localStorage.getItem(KUNCI);
		const pakaiGelap = simpanan ? simpanan === 'gelap' : true; // default gelap
		terapkanTema(pakaiGelap);
		const saklar = document.querySelector('#toggleTema');
		if (saklar) {
			saklar.checked = pakaiGelap;
			saklar.addEventListener('change', function () {
				terapkanTema(this.checked);
			});
		}
	}

	document.addEventListener('DOMContentLoaded', inisialisasiToggle);
})();







