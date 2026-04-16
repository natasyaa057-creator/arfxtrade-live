<?php
declare(strict_types=1);
$judul_halaman = 'Tentang Saya';
$deskripsi_meta = 'Profil lengkap ARFXTRADE - pengalaman trading, latar belakang, visi misi, dan timeline perjalanan karir trader profesional.';
require_once __DIR__ . '/includes/kepala.php';
?>

<section class="py-5 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="text-center mb-5" data-aos="fade-up">
                    <h1 class="display-5 fw-bold mb-3">Tentang <span class="teks-emas">ARFXTRADE</span></h1>
                    <p class="lead text-secondary">Trader Profesional dengan Fokus Disiplin & Manajemen Risiko</p>
                </div>
                
                <div class="kartu-gelap p-5 mb-5" data-aos="fade-up" data-aos-delay="100">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center mb-4 mb-md-0">
                            <div style="width: 200px; height: 200px; background: #000000; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; padding: 15px; box-sizing: border-box;">
                                <img src="aset/gambar/foto-profil.jpg" class="img-fluid rounded-circle" 
                                     style="width: 200px; height: 200px; object-fit: cover;" 
                                     alt="Foto ARFXTRADE" 
                                     onerror="this.src='<?= aman_html(basis_url('aset/image/logo_arfx.jpeg')) ?>'; this.style.width='100%'; this.style.height='100%'; this.style.borderRadius='50%'; this.style.objectFit='contain'; this.style.padding='0'; this.style.backgroundColor='transparent'; this.style.display='block';">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h3 class="fw-bold mb-3">Profil Singkat</h3>
                            <p class="text-secondary mb-3">
                                Saya adalah trader profesional dengan pengalaman lebih dari 5 tahun di pasar forex dan komoditas. 
                                Fokus utama saya adalah membangun konsistensi trading melalui disiplin, manajemen risiko yang ketat, 
                                dan analisis probabilitas yang mendalam.
                            </p>
                            <p class="text-secondary">
                                Filosofi trading saya berpusat pada perlindungan modal sebagai prioritas utama, 
                                diikuti dengan pertumbuhan yang berkelanjutan dan berkelanjutan.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="row g-4 mb-5">
                    <div class="col-md-6" data-aos="fade-right">
                        <div class="kartu-gelap p-4 h-100">
                            <h4 class="fw-bold teks-emas mb-3">Visi</h4>
                            <p class="text-secondary">
                                Menjadi trader yang konsisten dan dapat diandalkan, serta membantu sesama trader 
                                untuk mengembangkan kemampuan trading mereka melalui edukasi dan kolaborasi.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6" data-aos="fade-left">
                        <div class="kartu-gelap p-4 h-100">
                            <h4 class="fw-bold teks-emas mb-3">Misi</h4>
                            <p class="text-secondary">
                                Menerapkan disiplin trading yang ketat, mengutamakan manajemen risiko, 
                                dan terus belajar untuk meningkatkan probabilitas keberhasilan trading.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="kartu-gelap p-5" data-aos="fade-up">
                    <h3 class="fw-bold mb-4">Timeline Perjalanan Karir</h3>
                    <div class="timeline">
                        <div class="timeline-item mb-4">
                            <div class="d-flex">
                                <div class="timeline-marker teks-emas me-3">
                                    <i class="fa-solid fa-circle fa-sm"></i>
                                </div>
                                <div>
                                    <h5 class="fw-semibold mb-1">2019 - Awal Perjalanan</h5>
                                    <p class="text-secondary mb-0">Memulai trading dengan modal kecil, fokus belajar analisis teknikal dasar.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="timeline-item mb-4">
                            <div class="d-flex">
                                <div class="timeline-marker teks-emas me-3">
                                    <i class="fa-solid fa-circle fa-sm"></i>
                                </div>
                                <div>
                                    <h5 class="fw-semibold mb-1">2020 - Pengembangan Strategi</h5>
                                    <p class="text-secondary mb-0">Mengembangkan sistem trading personal dan mulai fokus pada manajemen risiko.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="timeline-item mb-4">
                            <div class="d-flex">
                                <div class="timeline-marker teks-emas me-3">
                                    <i class="fa-solid fa-circle fa-sm"></i>
                                </div>
                                <div>
                                    <h5 class="fw-semibold mb-1">2021 - Konsistensi Pertama</h5>
                                    <p class="text-secondary mb-0">Mencapai konsistensi profit bulanan dan mulai membagikan analisis publik.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="timeline-item mb-4">
                            <div class="d-flex">
                                <div class="timeline-marker teks-emas me-3">
                                    <i class="fa-solid fa-circle fa-sm"></i>
                                </div>
                                <div>
                                    <h5 class="fw-semibold mb-1">2022 - Ekspansi Pasar</h5>
                                    <p class="text-secondary mb-0">Memperluas trading ke komoditas dan crypto, mengembangkan strategi multi-asset.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="timeline-item mb-4">
                            <div class="d-flex">
                                <div class="timeline-marker teks-emas me-3">
                                    <i class="fa-solid fa-circle fa-sm"></i>
                                </div>
                                <div>
                                    <h5 class="fw-semibold mb-1">2023 - Profesional</h5>
                                    <p class="text-secondary mb-0">Menjadi trader profesional penuh waktu dengan klien dan portofolio yang berkembang.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="d-flex">
                                <div class="timeline-marker teks-emas me-3">
                                    <i class="fa-solid fa-circle fa-sm"></i>
                                </div>
                                <div>
                                    <h5 class="fw-semibold mb-1">2024 - Platform Digital</h5>
                                    <p class="text-secondary mb-0">Meluncurkan platform personal branding untuk berbagi pengetahuan dan kolaborasi.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-5" data-aos="fade-up">
                    <div class="kartu-gelap p-4">
                        <h4 class="fw-bold teks-emas mb-3">Filosofi Trading</h4>
                        <blockquote class="blockquote">
                            <p class="mb-0 fst-italic text-secondary">
                                "Trading bukan tentang menang setiap hari, tapi tentang konsistensi dalam jangka panjang. 
                                Fokus pada probabilitas, bukan pada hasil individual. Lindungi modal Anda seperti 
                                melindungi nyawa Anda."
                            </p>
                            <footer class="blockquote-footer mt-3">
                                <cite title="Source Title">ARFXTRADE</cite>
                            </footer>
                        </blockquote>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/kaki.php'; ?>








