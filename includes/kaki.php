<?php declare(strict_types=1); ?>
<footer class="footer py-4 mt-5">
	<div class="container d-flex flex-column flex-md-row align-items-center justify-content-between">
		<div class="mb-2 mb-md-0">&copy; <?= date('Y') ?> <span class="teks-emas fw-semibold">ARFXTRADE</span>. Semua hak cipta.</div>
		<div class="small text-secondary">Dibangun dengan Bootstrap 5 • Tema Gold-Black</div>
	</div>
</footer>

<a href="#" class="tombol-naik" id="tombolNaik" aria-label="Kembali ke atas"><i class="fa-solid fa-arrow-up"></i></a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script src="<?= aman_html(basis_url('aset/js/tema.js')) ?>"></script>
<script src="<?= aman_html(basis_url('aset/js/ui.js')) ?>"></script>
<script src="<?= aman_html(basis_url('aset/js/notifikasi_admin.js')) ?>"></script>
<script>
	AOS.init({ duration: 700, once: true, easing: 'ease-out' });
</script>

<!-- Tawk.to Live Chat Integration -->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/65f8a8b31ec1082f0e8b8b8a/1hq8q8q8q';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>

</body>
</html>





