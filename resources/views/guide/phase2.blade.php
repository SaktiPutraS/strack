@extends('guide.layouts.base')

@section('page-title', '📋 Phase 2: Requirement Gathering')
@section('page-subtitle', 'Menggali Kebutuhan Detail Klien')

@section('phase-content')

<!-- Legend -->
<div class="legend">
    <div class="legend-item">
        <div class="legend-color start"></div>
        <span class="legend-text">Start/Pembuka</span>
    </div>
    <div class="legend-item">
        <div class="legend-color decision"></div>
        <span class="legend-text">Decision/Keputusan</span>
    </div>
    <div class="legend-item">
        <div class="legend-color process"></div>
        <span class="legend-text">Process/Pertanyaan</span>
    </div>
    <div class="legend-item">
        <div class="legend-color end"></div>
        <span class="legend-text">End/Closing</span>
    </div>
</div>

<!-- Path A: Tugas Kuliah -->
<h2 style="color: var(--primary); margin: 2rem 0 1rem;">Path A: Tugas Kuliah/Sekolah</h2>
                    <div class="flow-node start">
                        <span class="node-label">Start Phase 2A</span>
                        <div class="node-title">Path: Tugas Kuliah/Sekolah</div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node process">
                        <span class="node-label">Q1</span>
                        <div class="node-title">Cek Deadline</div>
                        <div class="node-content">
                            <div class="node-code">"Deadlinenya kapan kak?"</div>
                            <p style="margin-top: 0.75rem; font-size: 0.85rem;">
                                🎯 <strong>Tujuan:</strong> Menentukan urgency dan feasibility<br>
                                ⚡ &lt; 3 hari = Urgent (pertimbangkan fee tambahan)<br>
                                ✅ 3-14 hari = Normal<br>
                                🌟 &gt; 14 hari = Fleksibel untuk revisi
                            </p>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node process">
                        <span class="node-label">Q2</span>
                        <div class="node-title">Minta Dokumen Requirement</div>
                        <div class="node-content">
                            <div class="node-code">"Apakah ada deskripsi/ketentuan lengkap dari dosen/kampus yang bisa di-share?"</div>
                            <div style="margin-top: 1rem; display: grid; gap: 0.5rem;">
                                <div style="padding: 0.5rem; background: rgba(16,185,129,0.1); border-radius: 6px; font-size: 0.85rem;">
                                    ✅ <strong>Jika ADA:</strong> Minta dokumen → Analisis requirement
                                </div>
                                <div style="padding: 0.5rem; background: rgba(239,68,68,0.1); border-radius: 6px; font-size: 0.85rem;">
                                    ❌ <strong>Jika TIDAK ADA:</strong> Lanjut gali detail manual
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node process">
                        <span class="node-label">Q3</span>
                        <div class="node-title">Cek Ketentuan Teknis</div>
                        <div class="node-content">
                            <div class="node-code">"Apakah ada ketentuan untuk developmentnya menggunakan bahasa/framework tertentu?"</div>
                            <p style="margin-top: 0.75rem; font-size: 0.85rem;">
                                Contoh: PHP, Laravel, React, HTML/CSS Basic, dll
                            </p>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node decision">
                        <span class="node-label">Q4</span>
                        <div class="node-title">Hosting atau Lokal?</div>
                        <div class="node-content">
                            <div class="node-code">"Apakah nanti webnya perlu di-hosting atau hanya dijalankan di lokal laptop saja?"</div>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="branches">
                        <div class="branch success">
                            <div class="branch-icon">H</div>
                            <div class="branch-title">Perlu Hosting</div>
                            <div class="branch-desc">Lanjut ke Q5: Pilihan Domain</div>
                        </div>
                        <div class="branch warning">
                            <div class="branch-icon">L</div>
                            <div class="branch-title">Hanya Lokal</div>
                            <div class="branch-desc">Skip hosting, langsung ke Q7</div>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node process">
                        <span class="node-label">Q5</span>
                        <div class="node-title">Pilihan Domain (Jika Hosting)</div>
                        <div class="node-content">
                            <div class="node-code">"Untuk domainnya kakak mau pakai yang mana?"</div>
                            <div class="price-table" style="margin-top: 1rem;">
                                <div class="price-row">
                                    <span class="price-label">.my.id</span>
                                    <span class="price-value">Rp 100k/tahun</span>
                                </div>
                                <div class="price-row">
                                    <span class="price-label">.id atau .com</span>
                                    <span class="price-value">Rp 300k/tahun</span>
                                </div>
                                <div class="price-row">
                                    <span class="price-label">Subdomain gratis</span>
                                    <span class="price-value">Hosting saja</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node process">
                        <span class="node-label">Q6</span>
                        <div class="node-title">Cek Ketersediaan Domain</div>
                        <div class="node-content">
                            <div class="node-code">"Nama domainnya mau apa kak? Biar saya cek ketersediaannya"</div>
                            <p style="margin-top: 0.75rem; font-size: 0.85rem;">
                                ⚠️ Jika tidak tersedia, tawarkan alternatif
                            </p>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node process">
                        <span class="node-label">Q7</span>
                        <div class="node-title">Source Code atau Link Saja?</div>
                        <div class="node-content">
                            <div class="node-code">"Apakah kakak perlu file project (source code) atau hanya link web siap pakai?"</div>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node process">
                        <span class="node-label">Q8 - CRITICAL</span>
                        <div class="node-title">Gali Detail Fitur Website</div>
                        <div class="node-content">
                            <div class="node-code">"Untuk fitur webnya, apa saja yang dibutuhkan?"</div>
                            <div style="margin-top: 1rem; background: rgba(99,102,241,0.05); padding: 1rem; border-radius: 8px; font-size: 0.85rem;">
                                <strong>Pertanyaan follow-up:</strong>
                                <ul style="margin-left: 1.5rem; margin-top: 0.5rem; line-height: 1.8;">
                                    <li>Menu/halaman apa saja?</li>
                                    <li>Apakah ada login system?</li>
                                    <li>Apakah perlu dashboard admin?</li>
                                    <li>Apakah ada database? Berapa tabel?</li>
                                    <li>Fitur khusus apa? (upload, download, notifikasi, dll)</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node decision">
                        <span class="node-label">Kategorisasi</span>
                        <div class="node-title">Tentukan Kategori & Estimasi Harga</div>
                        <div class="node-content">
                            <div class="price-table">
                                <div class="price-row">
                                    <span class="price-label">Web Statis Sederhana</span>
                                    <span class="price-value">250k - 400k</span>
                                </div>
                                <div class="price-row">
                                    <span class="price-label">Web dengan Dashboard Admin</span>
                                    <span class="price-value">600k - 1.5jt</span>
                                </div>
                                <div class="price-row">
                                    <span class="price-label">Web Aplikasi Kompleks</span>
                                    <span class="price-value">1.5jt - 2.5jt</span>
                                </div>
                                <div class="price-row">
                                    <span class="price-label">Paket Skripsi (All-in)</span>
                                    <span class="price-value">1.5jt - 2.5jt</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node process">
                        <span class="node-label">Q9</span>
                        <div class="node-title">Tanya Budget Klien</div>
                        <div class="node-content">
                            <div class="node-code">"Dari kakak ada budget berapa untuk project ini?"</div>
                            <div style="margin-top: 1rem; background: rgba(245,158,11,0.1); padding: 1rem; border-radius: 8px; font-size: 0.85rem;">
                                <strong>⚖️ Strategi Nego:</strong><br>
                                • Budget &lt; estimasi → Tawarkan scope lebih kecil<br>
                                • Budget = estimasi → Konfirmasi dan lanjut<br>
                                • Budget &gt; estimasi → Tawarkan fitur tambahan
                            </div>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node end">
                        <span class="node-label">Next Phase</span>
                        <div class="node-title">Lanjut ke Fase 3: Penawaran Harga</div>
                    </div>

                    <div class="info-box">
                        <div class="info-box-title">
                            🎓 Special: Paket Skripsi
                        </div>
                        <div class="info-box-content">
                            Untuk mahasiswa yang masih tahap pengajuan judul atau baru mulai skripsi, tawarkan <strong>Paket Skripsi All-in (1.5jt -
                                2.5jt)</strong> yang include:
                            <ul style="margin-left: 1.5rem; margin-top: 0.5rem;">
                                <li>Pembuatan website</li>
                                <li>Pendampingan selama bimbingan</li>
                                <li>Revisi unlimited sampai ACC</li>
                            </ul>
                            Lebih menguntungkan untuk long-term project!
                        </div>
                    </div>
                </div>
            </div>


<!-- Path B: Bisnis -->
<h2 style="color: var(--primary); margin: 2rem 0 1rem;">Path B: Bisnis/Company Profile</h2>
                    <div class="flow-node start">
                        <span class="node-label">Start Phase 2B</span>
                        <div class="node-title">Path: Bisnis/Pribadi</div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node decision">
                        <span class="node-label">Q1 - CRITICAL</span>
                        <div class="node-title">Statis atau Dinamis?</div>
                        <div class="node-content">
                            <div class="node-code">"Kalo boleh tau websitenya akan dikelola sendiri (misal ada perubahan konten didalamnya) atau website
                                statis (tidak ada perubahan kedepannya)?"</div>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="branches">
                        <div class="branch success">
                            <div class="branch-icon">D</div>
                            <div class="branch-title">Dikelola Sendiri (Dinamis)</div>
                            <div class="branch-desc">Perlu Panel Admin → Start 1.5jt</div>
                        </div>
                        <div class="branch warning">
                            <div class="branch-icon">S</div>
                            <div class="branch-title">Website Statis</div>
                            <div class="branch-desc">Tidak perlu Panel Admin → Start 700k</div>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node process">
                        <span class="node-label">Q2</span>
                        <div class="node-title">Info Harga Awal</div>
                        <div class="node-content">
                            <div style="display: grid; gap: 1rem;">
                                <div style="background: rgba(16,185,129,0.1); padding: 1rem; border-radius: 8px;">
                                    <strong>Jika Dinamis (Panel Admin):</strong>
                                    <div class="node-code" style="margin-top: 0.5rem;">
                                        "Baik kak, berarti nanti saya buatkan panel admin untuk manage konten ya. Company Profile + Panel Admin start dari
                                        1.5jt, sudah include hosting & domain (.com/.id) selama 1 tahun."
                                    </div>
                                </div>
                                <div style="background: rgba(245,158,11,0.1); padding: 1rem; border-radius: 8px;">
                                    <strong>Jika Statis:</strong>
                                    <div class="node-code" style="margin-top: 0.5rem;">
                                        "Untuk website statis, saya bisa tawarkan di kisaran Rp700-750k, sudah include hosting & domain."
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node process">
                        <span class="node-label">Q3</span>
                        <div class="node-title">Konsep atau Referensi?</div>
                        <div class="node-content">
                            <div class="node-code">"Dari kakak sudah ada konsep websitenya atau referensi? Atau bebas dari saya?"</div>
                            <div style="margin-top: 1rem; background: rgba(99,102,241,0.05); padding: 1rem; border-radius: 8px; font-size: 0.85rem;">
                                • <strong>Ada Referensi:</strong> Minta link/screenshot → Analisis tingkat kesulitan<br>
                                • <strong>Bebas:</strong> Anda yang design, lebih fleksibel (bisa charge lebih)
                            </div>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node process">
                        <span class="node-label">Q4</span>
                        <div class="node-title">Cek Deadline</div>
                        <div class="node-content">
                            <div class="node-code">"Apakah ada deadline khusus dari kakak?"</div>
                            <p style="margin-top: 0.5rem; font-size: 0.85rem;">
                                ⏱️ Estimasi normal: 7-14 hari kerja
                            </p>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node process">
                        <span class="node-label">Q5</span>
                        <div class="node-title">Pilihan Domain</div>
                        <div class="node-content">
                            <div class="node-code">"Untuk domainnya, kakak mau pakai yang mana?"</div>
                            <div class="price-table" style="margin-top: 1rem;">
                                <div class="price-row">
                                    <span class="price-label">.my.id</span>
                                    <span class="price-value">+100k/tahun</span>
                                </div>
                                <div class="price-row">
                                    <span class="price-label">.id atau .com</span>
                                    <span class="price-value">+300k/tahun</span>
                                </div>
                            </div>
                            <p style="margin-top: 0.75rem; font-size: 0.85rem; color: var(--text-light);">
                                💡 Untuk bisnis, rekomendasikan .com atau .id untuk kredibilitas
                            </p>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node decision">
                        <span class="node-label">Q6</span>
                        <div class="node-title">Nego Budget</div>
                        <div class="node-content">
                            <div class="node-code">"Boleh di-share kak budgetnya berapa? Biar saya bisa sesuaikan fitur dan layanannya"</div>
                            <div style="margin-top: 1rem; background: rgba(139,92,246,0.1); padding: 1rem; border-radius: 8px; font-size: 0.85rem;">
                                <strong>🎯 Strategi Pricing Bisnis:</strong><br>
                                • Budget rendah → Tawarkan statis atau fitur minimal<br>
                                • Budget cukup → Full features dengan panel admin<br>
                                • Budget tinggi → Tambahkan value (SEO, training, support)
                            </div>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node end">
                        <span class="node-label">Next Phase</span>
                        <div class="node-title">Lanjut ke Fase 3: Penawaran Harga</div>
                    </div>

                    <div class="info-box">
                        <div class="info-box-title">
                            💼 Tips untuk Klien Bisnis
                        </div>
                        <div class="info-box-content">
                            <strong>Perbedaan pendekatan:</strong>
                            <ul style="margin-left: 1.5rem; margin-top: 0.5rem;">
                                <li><strong>Lebih profesional:</strong> Kirim portofolio, tunjukkan kredibilitas</li>
                                <li><strong>Value-driven:</strong> Fokus pada ROI dan manfaat bisnis</li>
                                <li><strong>After-sales:</strong> Tawarkan maintenance package</li>
                                <li><strong>Upselling:</strong> SEO, Google My Business, Social Media Integration</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>


<!-- Path C: Hosting Only -->
<h2 style="color: var(--primary); margin: 2rem 0 1rem;">Path C: Hosting Only</h2>
                    <div class="flow-node start">
                        <span class="node-label">Start Phase 2C</span>
                        <div class="node-title">Path: Hosting Saja</div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node process">
                        <span class="node-label">Q1</span>
                        <div class="node-title">Minta Project File</div>
                        <div class="node-content">
                            <div class="node-code">"Boleh dikirim projectnya kak untuk saya cek dan test di lokal"</div>
                            <p style="margin-top: 0.75rem; font-size: 0.85rem;">
                                🎯 <strong>Tujuan:</strong> Memastikan project bisa jalan sebelum hosting
                            </p>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node process">
                        <span class="node-label">Q2</span>
                        <div class="node-title">Cek Deadline</div>
                        <div class="node-content">
                            <div class="node-code">"Deadlinenya kapan kak?"</div>
                            <p style="margin-top: 0.5rem; font-size: 0.85rem;">
                                ⚡ Hosting biasanya cepat (1-2 hari) kalau project lancar
                            </p>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node decision">
                        <span class="node-label">Testing</span>
                        <div class="node-title">Test di Lokal</div>
                        <div class="node-content">
                            <div style="text-align: center; padding: 1rem 0;">
                                <div style="font-size: 3rem;">⚙️</div>
                                <p style="margin-top: 0.5rem; font-weight: 600;">Testing project di environment lokal...</p>
                            </div>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="branches">
                        <div class="branch success">
                            <div class="branch-icon">✓</div>
                            <div class="branch-title">Project Berhasil Jalan</div>
                            <div class="branch-desc">Lanjut konfirmasi ke klien</div>
                        </div>
                        <div class="branch danger">
                            <div class="branch-icon">✗</div>
                            <div class="branch-title">Project Bermasalah</div>
                            <div class="branch-desc">Informasikan issue, tawarkan fix (biaya tambahan)</div>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node process">
                        <span class="node-label">Q3</span>
                        <div class="node-title">Konfirmasi Hasil Testing</div>
                        <div class="node-content">
                            <div class="node-code">"Project sudah berhasil jalan di lokal saya. Apakah jadi kak?"</div>
                            <div style="margin-top: 1rem; background: rgba(239,68,68,0.1); padding: 1rem; border-radius: 8px; font-size: 0.85rem;">
                                ⚠️ <strong>Jika bermasalah:</strong><br>
                                "Maaf kak, setelah saya cek ada beberapa issue: [detail masalah]. Apakah mau saya bantu fix? Estimasi biaya: [X]k"
                            </div>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node process">
                        <span class="node-label">Q4</span>
                        <div class="node-title">Pilih Nama Domain</div>
                        <div class="node-content">
                            <div class="node-code">"Nama domainnya mau apa kak?"</div>
                            <p style="margin-top: 0.5rem; font-size: 0.85rem;">
                                • Cek ketersediaan domain<br>
                                • Tawarkan alternatif jika tidak tersedia
                            </p>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node process">
                        <span class="node-label">Q5</span>
                        <div class="node-title">Info Harga Hosting</div>
                        <div class="node-content">
                            <div class="price-table">
                                <div class="price-row">
                                    <span class="price-label">Hosting + Domain .my.id</span>
                                    <span class="price-value">Rp 100k/tahun</span>
                                </div>
                                <div class="price-row">
                                    <span class="price-label">Hosting + Domain .id/.com</span>
                                    <span class="price-value">Rp 300k/tahun</span>
                                </div>
                                <div class="price-row">
                                    <span class="price-label">Subdomain (namaweb.saktify.my.id)</span>
                                    <span class="price-value">Rp 50k (one-time)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node end">
                        <span class="node-label">Next Phase</span>
                        <div class="node-title">Lanjut ke Fase 4: Konfirmasi & Closing</div>
                    </div>

                    <div class="info-box">
                        <div class="info-box-title">
                            🔧 Tips Handling Project Bermasalah
                        </div>
                        <div class="info-box-content">
                            <strong>Jika project error/tidak bisa jalan:</strong>
                            <ol style="margin-left: 1.5rem; margin-top: 0.5rem; line-height: 1.8;">
                                <li>Jelaskan dengan jujur masalah yang ditemukan</li>
                                <li>Tawarkan solusi: fix error dengan biaya tambahan (100k-300k)</li>
                                <li>Jika terlalu kompleks, tolak dengan sopan dan suggest rebuild</li>
                                <li>Jangan terima project yang di luar kapasitas!</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>


@endsection
