<!-- Dashboard Information Section -->
<div class="row mb-4">
    <div class="col-12">
        <!-- Description Card -->
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i> Deskripsi Aplikasi
                </h3>
            </div>
            <div class="card-body">
                <p class="text-justify">
                    Aplikasi <strong>TEFA (Teaching Factory)</strong> adalah platform pembelajaran terintegrasi untuk jurusan 
                    <strong>Desain Komunikasi Visual (DKV)</strong> yang dirancang khusus untuk mendukung pembelajaran 
                    berbasis Teaching Factory. Aplikasi ini memfasilitasi manajemen proyek, penilaian, absensi, dan kolaborasi 
                    antara guru dan siswa dalam lingkungan pembelajaran yang menyerupai industri kreatif modern.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <!-- Learning Objectives Card -->
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-graduation-cap"></i> Tujuan Pembelajaran
                </h3>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i>
                        <strong>Kompetensi Praktis:</strong> Mengembangkan keterampilan desain grafis dan komunikasi visual
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i>
                        <strong>Kolaborasi Tim:</strong> Belajar bekerja dalam tim profesional pada proyek nyata
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i>
                        <strong>Manajemen Proyek:</strong> Memahami siklus hidup proyek dari perencanaan hingga eksekusi
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i>
                        <strong>Industri Readiness:</strong> Persiapan kerja di industri kreatif dengan standar profesional
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i>
                        <strong>Inovasi & Kreativitas:</strong> Mendorong inovasi dan pemikiran kreatif dalam setiap proyek
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <!-- Help Desk Card -->
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-headset"></i> Help Desk
                </h3>
            </div>
            <div class="card-body">
                <p class="mb-3">
                    <strong>Butuh bantuan?</strong> Tim support kami siap membantu Anda:
                </p>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-envelope text-info"></i>
                        <strong>Email:</strong> 
                        <a href="mailto:support@tefanex.com">support@tefanex.com</a>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone text-info"></i>
                        <strong>Telepon:</strong> 
                        <a href="https://web.whatsapp.com/send?phone=6282126574516" target="_blank">082126574516 (Asep Hidayat)</a>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone text-info"></i>
                        <strong>Telepon:</strong> 
                        <a href="https://web.whatsapp.com/send?phone=6282295166507" target="_blank">082295166507 (Gita Permatasari)</a>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-clock text-info"></i>
                        <strong>Jam Operasional:</strong> Senin - Jumat, 08:00 - 17:00 WIB
                    </li>
                    @if(Route::has('faq.index'))
                    <li class="mb-2">
                        <i class="fas fa-question-circle text-info"></i>
                        <strong>FAQ:</strong> 
                        <a href="{{ route('faq.index') }}">Lihat Pertanyaan Umum</a>
                    </li>
                    @endif
                    @if(Route::has('tutorial.index'))
                    <li class="mb-2">
                        <i class="fas fa-life-ring text-info"></i>
                        <strong>Tutorial:</strong> 
                        <a href="{{ route('tutorial.index') }}">Akses Panduan Video</a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
