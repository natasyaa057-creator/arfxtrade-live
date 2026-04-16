// UI interaksi umum: tombol scroll-to-top
(function(){
	const tombol = document.getElementById('tombolNaik');
	if (!tombol) return;
	window.addEventListener('scroll', function(){
		if (window.scrollY > 240) {
			tombol.classList.add('muncul');
		} else {
			tombol.classList.remove('muncul');
		}
	});
	tombol.addEventListener('click', function(e){
		e.preventDefault();
		window.scrollTo({ top: 0, behavior: 'smooth' });
	});
})();







