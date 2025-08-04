@extends('layouts.app')
@section('title', 'Daftar Harga Joki Tugas 2025')

@section('content')
    <!-- Search Container -->
    <div class="search-container">
        <div style="position: relative;">
            <input type="text" id="searchInput" class="search-input" placeholder="Cari project, teknologi, atau fitur...">
            <i class="bi bi-search search-icon"></i>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div class="loading" id="loading">
        <i class="bi bi-arrow-repeat"></i>
        <p>Mencari...</p>
    </div>

    <!-- Desktop Table View -->
    <div class="table-container d-none d-lg-block">
        <table class="price-list-table" id="priceTable">
            <thead>
                <tr>
                    <th style="width: 50px;"><i class="bi bi-hash me-2"></i>No</th>
                    <th><i class="bi bi-folder me-2"></i>Jenis Project</th>
                    <th><i class="bi bi-file-text me-2"></i>Deskripsi</th>
                    <th><i class="bi bi-code-slash me-2"></i>Teknologi</th>
                    <th><i class="bi bi-cash me-2"></i>Harga Biasa</th>
                    <th><i class="bi bi-mortarboard me-2"></i>Harga TA</th>
                    <th><i class="bi bi-clock me-2"></i>Timeline</th>
                </tr>
            </thead>
            <tbody>
                <!-- Static Websites -->
                <tr class="category-row">
                    <td colspan="7"><i class="bi bi-globe me-2"></i>STATIC WEBSITES</td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Landing Page Sederhana</td>
                    <td>1 halaman, HTML+CSS, responsive basic, contact form</td>
                    <td><span class="tech-badge">HTML</span><span class="tech-badge">CSS</span><span class="tech-badge">JS</span></td>
                    <td><span class="price">75.000-125.000</span></td>
                    <td><span class="price">150.000-200.000</span></td>
                    <td><span class="timeline-badge">1-2 hari</span></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Website Company Profile</td>
                    <td>3-5 halaman (Home, About, Services, Portfolio, Contact)</td>
                    <td><span class="tech-badge">HTML</span><span class="tech-badge">CSS</span><span class="tech-badge">JS</span></td>
                    <td><span class="price">150.000-250.000</span></td>
                    <td><span class="price">300.000-400.000</span></td>
                    <td><span class="timeline-badge">2-3 hari</span></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Landing Page Premium</td>
                    <td>1 halaman dengan animasi, parallax, modern design</td>
                    <td><span class="tech-badge">HTML</span><span class="tech-badge">CSS</span><span class="tech-badge">JS</span></td>
                    <td><span class="price">200.000-300.000</span></td>
                    <td><span class="price">400.000-500.000</span></td>
                    <td><span class="timeline-badge">2-3 hari</span></td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>Website Sekolah/Organisasi</td>
                    <td>5-8 halaman, galeri foto, berita, profil</td>
                    <td><span class="tech-badge">HTML</span><span class="tech-badge">CSS</span><span class="tech-badge">JS</span></td>
                    <td><span class="price">250.000-350.000</span></td>
                    <td><span class="price">450.000-600.000</span></td>
                    <td><span class="timeline-badge">3-4 hari</span></td>
                </tr>

                <!-- Basic Dynamic - PHP -->
                <tr class="category-row">
                    <td colspan="7"><i class="bi bi-server me-2"></i>BASIC DYNAMIC - PHP</td>
                </tr>
                <tr>
                    <td>5</td>
                    <td>CRUD Sederhana</td>
                    <td>1 tabel, tambah/edit/hapus/lihat data, no login</td>
                    <td><span class="tech-badge">PHP</span><span class="tech-badge">MySQL</span></td>
                    <td><span class="price">200.000-280.000</span></td>
                    <td><span class="price">400.000-500.000</span></td>
                    <td><span class="timeline-badge">2-3 hari</span></td>
                </tr>
                <tr>
                    <td>6</td>
                    <td>CRUD + Login</td>
                    <td>CRUD + sistem login, session management</td>
                    <td><span class="tech-badge">PHP</span><span class="tech-badge">MySQL</span></td>
                    <td><span class="price">300.000-380.000</span></td>
                    <td><span class="price">500.000-650.000</span></td>
                    <td><span class="timeline-badge">3-4 hari</span></td>
                </tr>
                <tr>
                    <td>7</td>
                    <td>CRUD + Login + Admin</td>
                    <td>Multi-user, admin panel, user management</td>
                    <td><span class="tech-badge">PHP</span><span class="tech-badge">MySQL</span></td>
                    <td><span class="price">380.000-450.000</span></td>
                    <td><span class="price">650.000-800.000</span></td>
                    <td><span class="timeline-badge">4-5 hari</span></td>
                </tr>
                <tr>
                    <td>8</td>
                    <td>Sistem Absensi Sederhana</td>
                    <td>Input absensi, laporan per hari/bulan</td>
                    <td><span class="tech-badge">PHP</span><span class="tech-badge">MySQL</span></td>
                    <td><span class="price">350.000-420.000</span></td>
                    <td><span class="price">600.000-750.000</span></td>
                    <td><span class="timeline-badge">3-4 hari</span></td>
                </tr>
                <tr>
                    <td>9</td>
                    <td>Sistem Inventory Basic</td>
                    <td>Barang masuk/keluar, stok, laporan</td>
                    <td><span class="tech-badge">PHP</span><span class="tech-badge">MySQL</span></td>
                    <td><span class="price">400.000-480.000</span></td>
                    <td><span class="price">700.000-850.000</span></td>
                    <td><span class="timeline-badge">4-5 hari</span></td>
                </tr>

                <!-- Laravel Framework -->
                <tr class="category-row">
                    <td colspan="7"><i class="bi bi-layers me-2"></i>LARAVEL FRAMEWORK</td>
                </tr>
                <tr>
                    <td>10</td>
                    <td>CRUD Laravel Basic</td>
                    <td>Eloquent ORM, Blade templates, authentication</td>
                    <td><span class="tech-badge">Laravel</span><span class="tech-badge">MySQL</span></td>
                    <td><span class="price">450.000-550.000</span></td>
                    <td><span class="price">800.000-1.000.000</span></td>
                    <td><span class="timeline-badge">4-5 hari</span></td>
                </tr>
                <tr>
                    <td>11</td>
                    <td>Laravel + Admin Panel</td>
                    <td>Dashboard, role management, middleware</td>
                    <td><span class="tech-badge">Laravel</span><span class="tech-badge">MySQL</span></td>
                    <td><span class="price">600.000-750.000</span></td>
                    <td><span class="price">1.000.000-1.300.000</span></td>
                    <td><span class="timeline-badge">5-7 hari</span></td>
                </tr>
                <tr>
                    <td>12</td>
                    <td>Laravel E-commerce</td>
                    <td>Product, cart, order, payment integration</td>
                    <td><span class="tech-badge">Laravel</span><span class="tech-badge">MySQL</span></td>
                    <td><span class="price">800.000-1.200.000</span></td>
                    <td><span class="price">1.500.000-2.000.000</span></td>
                    <td><span class="timeline-badge">8-12 hari</span></td>
                </tr>
                <tr>
                    <td>13</td>
                    <td>Laravel API + Frontend</td>
                    <td>RESTful API, JSON responses, AJAX</td>
                    <td><span class="tech-badge">Laravel</span><span class="tech-badge">MySQL</span><span class="tech-badge">JS</span></td>
                    <td><span class="price">700.000-900.000</span></td>
                    <td><span class="price">1.200.000-1.500.000</span></td>
                    <td><span class="timeline-badge">6-8 hari</span></td>
                </tr>

                <!-- Intermediate Projects -->
                <tr class="category-row">
                    <td colspan="7"><i class="bi bi-gear-wide-connected me-2"></i>INTERMEDIATE PROJECTS</td>
                </tr>
                <tr>
                    <td>14</td>
                    <td>Sistem Perpustakaan</td>
                    <td>Buku, anggota, peminjaman, denda, search</td>
                    <td><span class="tech-badge">PHP/Laravel</span><span class="tech-badge">MySQL</span></td>
                    <td><span class="price">400.000-550.000</span></td>
                    <td><span class="price">800.000-1.100.000</span></td>
                    <td><span class="timeline-badge">4-6 hari</span></td>
                </tr>
                <tr>
                    <td>15</td>
                    <td>Sistem Penjualan</td>
                    <td>Produk, customer, transaksi, laporan</td>
                    <td><span class="tech-badge">PHP/Laravel</span><span class="tech-badge">MySQL</span></td>
                    <td><span class="price">450.000-600.000</span></td>
                    <td><span class="price">900.000-1.200.000</span></td>
                    <td><span class="timeline-badge">5-6 hari</span></td>
                </tr>
                <tr>
                    <td>16</td>
                    <td>Sistem Klinik</td>
                    <td>Pasien, dokter, obat, rekam medis</td>
                    <td><span class="tech-badge">PHP/Laravel</span><span class="tech-badge">MySQL</span></td>
                    <td><span class="price">500.000-650.000</span></td>
                    <td><span class="price">1.000.000-1.300.000</span></td>
                    <td><span class="timeline-badge">5-7 hari</span></td>
                </tr>
                <tr>
                    <td>17</td>
                    <td>Sistem Restoran</td>
                    <td>Menu, order, meja, kasir, laporan</td>
                    <td><span class="tech-badge">PHP/Laravel</span><span class="tech-badge">MySQL</span></td>
                    <td><span class="price">450.000-580.000</span></td>
                    <td><span class="price">900.000-1.150.000</span></td>
                    <td><span class="timeline-badge">4-6 hari</span></td>
                </tr>
                <tr>
                    <td>18</td>
                    <td>Sistem Rental</td>
                    <td>Mobil/motor, customer, booking, return</td>
                    <td><span class="tech-badge">PHP/Laravel</span><span class="tech-badge">MySQL</span></td>
                    <td><span class="price">480.000-620.000</span></td>
                    <td><span class="price">950.000-1.200.000</span></td>
                    <td><span class="timeline-badge">5-6 hari</span></td>
                </tr>
                <tr>
                    <td>19</td>
                    <td>Sistem Hotel</td>
                    <td>Kamar, tamu, reservasi, check in/out</td>
                    <td><span class="tech-badge">PHP/Laravel</span><span class="tech-badge">MySQL</span></td>
                    <td><span class="price">550.000-700.000</span></td>
                    <td><span class="price">1.100.000-1.400.000</span></td>
                    <td><span class="timeline-badge">6-7 hari</span></td>
                </tr>
                <tr>
                    <td>20</td>
                    <td>Sistem Penggajian</td>
                    <td>Karyawan, absensi, gaji, slip gaji</td>
                    <td><span class="tech-badge">PHP/Laravel</span><span class="tech-badge">MySQL</span></td>
                    <td><span class="price">500.000-650.000</span></td>
                    <td><span class="price">1.000.000-1.300.000</span></td>
                    <td><span class="timeline-badge">5-7 hari</span></td>
                </tr>
                <tr>
                    <td>21</td>
                    <td>Sistem SPK (Decision Support)</td>
                    <td>Kriteria, alternatif, perhitungan AHP/SAW</td>
                    <td><span class="tech-badge">PHP/Laravel</span><span class="tech-badge">MySQL</span></td>
                    <td><span class="price">550.000-750.000</span></td>
                    <td><span class="price">1.100.000-1.500.000</span></td>
                    <td><span class="timeline-badge">6-8 hari</span></td>
                </tr>

                <!-- Desktop Application - Delphi -->
                <tr class="category-row">
                    <td colspan="7"><i class="bi bi-pc-display me-2"></i>DESKTOP APPLICATION - DELPHI</td>
                </tr>
                <tr>
                    <td>22</td>
                    <td>Aplikasi CRUD Desktop</td>
                    <td>Form input, database, laporan</td>
                    <td><span class="tech-badge">Delphi</span><span class="tech-badge">MySQL/SQLite</span></td>
                    <td><span class="price">400.000-550.000</span></td>
                    <td><span class="price">800.000-1.100.000</span></td>
                    <td><span class="timeline-badge">4-6 hari</span></td>
                </tr>
                <tr>
                    <td>23</td>
                    <td>Sistem Kasir Desktop</td>
                    <td>POS system, printer, barcode</td>
                    <td><span class="tech-badge">Delphi</span><span class="tech-badge">MySQL</span></td>
                    <td><span class="price">600.000-800.000</span></td>
                    <td><span class="price">1.200.000-1.600.000</span></td>
                    <td><span class="timeline-badge">6-8 hari</span></td>
                </tr>
                <tr>
                    <td>24</td>
                    <td>Sistem Inventory Desktop</td>
                    <td>Stock management, supplier, reporting</td>
                    <td><span class="tech-badge">Delphi</span><span class="tech-badge">MySQL</span></td>
                    <td><span class="price">550.000-750.000</span></td>
                    <td><span class="price">1.100.000-1.500.000</span></td>
                    <td><span class="timeline-badge">6-8 hari</span></td>
                </tr>
                <tr>
                    <td>25</td>
                    <td>Aplikasi Keuangan Desktop</td>
                    <td>Cashflow, budget, laporan keuangan</td>
                    <td><span class="tech-badge">Delphi</span><span class="tech-badge">MySQL</span></td>
                    <td><span class="price">650.000-850.000</span></td>
                    <td><span class="price">1.300.000-1.700.000</span></td>
                    <td><span class="timeline-badge">7-9 hari</span></td>
                </tr>

                <!-- Advanced Features -->
                <tr class="category-row">
                    <td colspan="7"><i class="bi bi-lightning me-2"></i>ADVANCED FEATURES</td>
                </tr>
                <tr>
                    <td>26</td>
                    <td>Sistem dengan Dashboard</td>
                    <td>Chart, grafik, analytics, export laporan</td>
                    <td><span class="tech-badge">Laravel/PHP</span><span class="tech-badge">Chart.js</span></td>
                    <td><span class="price">600.000-800.000</span></td>
                    <td><span class="price">1.200.000-1.600.000</span></td>
                    <td><span class="timeline-badge">6-8 hari</span></td>
                </tr>
                <tr>
                    <td>27</td>
                    <td>Sistem dengan Upload File</td>
                    <td>Multi-file upload, validasi, download</td>
                    <td><span class="tech-badge">Laravel/PHP</span><span class="tech-badge">MySQL</span></td>
                    <td><span class="price">450.000-600.000</span></td>
                    <td><span class="price">900.000-1.200.000</span></td>
                    <td><span class="timeline-badge">4-6 hari</span></td>
                </tr>
                <tr>
                    <td>28</td>
                    <td>Sistem dengan Email</td>
                    <td>Notifikasi email, reset password</td>
                    <td><span class="tech-badge">Laravel/PHP</span><span class="tech-badge">PHPMailer</span></td>
                    <td><span class="price">500.000-650.000</span></td>
                    <td><span class="price">1.000.000-1.300.000</span></td>
                    <td><span class="timeline-badge">5-7 hari</span></td>
                </tr>
                <tr>
                    <td>29</td>
                    <td>Sistem dengan API</td>
                    <td>Integrasi API external, JSON handling</td>
                    <td><span class="tech-badge">Laravel/PHP</span><span class="tech-badge">cURL</span></td>
                    <td><span class="price">550.000-750.000</span></td>
                    <td><span class="price">1.100.000-1.500.000</span></td>
                    <td><span class="timeline-badge">5-7 hari</span></td>
                </tr>
                <tr>
                    <td>30</td>
                    <td>Sistem Real-time</td>
                    <td>Chat, notifikasi real-time, WebSocket</td>
                    <td><span class="tech-badge">Laravel</span><span class="tech-badge">Pusher/Socket.io</span></td>
                    <td><span class="price">700.000-900.000</span></td>
                    <td><span class="price">1.400.000-1.800.000</span></td>
                    <td><span class="timeline-badge">7-9 hari</span></td>
                </tr>

                <!-- Complex Projects -->
                <tr class="category-row">
                    <td colspan="7"><i class="bi bi-diagram-3 me-2"></i>COMPLEX PROJECTS</td>
                </tr>
                <tr>
                    <td>31</td>
                    <td>Sistem Akademik Lengkap</td>
                    <td>Multi-role, nilai, jadwal, raport</td>
                    <td><span class="tech-badge">Laravel</span><span class="tech-badge">MySQL</span></td>
                    <td><span class="price">800.000-1.200.000</span></td>
                    <td><span class="price">1.600.000-2.400.000</span></td>
                    <td><span class="timeline-badge">8-12 hari</span></td>
                </tr>
                <tr>
                    <td>32</td>
                    <td>Sistem HRD</td>
                    <td>Karyawan, payroll, cuti, performance</td>
                    <td><span class="tech-badge">Laravel</span><span class="tech-badge">MySQL</span></td>
                    <td><span class="price">900.000-1.300.000</span></td>
                    <td><span class="price">1.800.000-2.600.000</span></td>
                    <td><span class="timeline-badge">9-13 hari</span></td>
                </tr>
                <tr>
                    <td>33</td>
                    <td>ERP Sederhana</td>
                    <td>Multi-module, inventory, finance, sales</td>
                    <td><span class="tech-badge">Laravel</span><span class="tech-badge">MySQL</span></td>
                    <td><span class="price">1.000.000-1.500.000</span></td>
                    <td><span class="price">2.000.000-3.000.000</span></td>
                    <td><span class="timeline-badge">10-15 hari</span></td>
                </tr>
                <tr>
                    <td>34</td>
                    <td>Sistem E-learning</td>
                    <td>Course, quiz, video, sertifikat</td>
                    <td><span class="tech-badge">Laravel</span><span class="tech-badge">MySQL</span></td>
                    <td><span class="price">1.200.000-1.600.000</span></td>
                    <td><span class="price">2.400.000-3.200.000</span></td>
                    <td><span class="timeline-badge">12-16 hari</span></td>
                </tr>
                <tr>
                    <td>35</td>
                    <td>Marketplace</td>
                    <td>Multi-vendor, payment, review, rating</td>
                    <td><span class="tech-badge">Laravel</span><span class="tech-badge">MySQL</span></td>
                    <td><span class="price">1.500.000-2.000.000</span></td>
                    <td><span class="price">3.000.000-4.000.000</span></td>
                    <td><span class="timeline-badge">15-20 hari</span></td>
                </tr>

                <!-- UI/UX Design Services -->
                <tr class="category-row">
                    <td colspan="7"><i class="bi bi-palette me-2"></i>UI/UX DESIGN SERVICES</td>
                </tr>
                <tr>
                    <td>36</td>
                    <td>Wireframe/Mockup</td>
                    <td>Low-fidelity design, user flow</td>
                    <td><span class="tech-badge">Figma</span></td>
                    <td><span class="price">150.000-250.000</span></td>
                    <td><span class="price">300.000-400.000</span></td>
                    <td><span class="timeline-badge">2-3 hari</span></td>
                </tr>
                <tr>
                    <td>37</td>
                    <td>UI Design Complete</td>
                    <td>High-fidelity, responsive design</td>
                    <td><span class="tech-badge">Figma</span></td>
                    <td><span class="price">300.000-500.000</span></td>
                    <td><span class="price">600.000-800.000</span></td>
                    <td><span class="timeline-badge">4-6 hari</span></td>
                </tr>
                <tr>
                    <td>38</td>
                    <td>Design System</td>
                    <td>Component library, style guide</td>
                    <td><span class="tech-badge">Figma</span></td>
                    <td><span class="price">400.000-600.000</span></td>
                    <td><span class="price">800.000-1.000.000</span></td>
                    <td><span class="timeline-badge">5-7 hari</span></td>
                </tr>

                <!-- Skripsi/TA Services -->
                <tr class="category-row">
                    <td colspan="7"><i class="bi bi-mortarboard me-2"></i>SKRIPSI/TUGAS AKHIR SERVICES</td>
                </tr>
                <tr>
                    <td>39</td>
                    <td>Bab 4: Implementasi</td>
                    <td>Coding, screenshot, penjelasan kode</td>
                    <td><span class="tech-badge">Sesuai project</span></td>
                    <td>-</td>
                    <td><span class="price">500.000-800.000</span></td>
                    <td><span class="timeline-badge">3-5 hari</span></td>
                </tr>
                <tr>
                    <td>40</td>
                    <td>Bab 5: Testing</td>
                    <td>Unit test, black box, white box, UAT</td>
                    <td><span class="tech-badge">Sesuai project</span></td>
                    <td>-</td>
                    <td><span class="price">400.000-600.000</span></td>
                    <td><span class="timeline-badge">2-4 hari</span></td>
                </tr>
                <tr>
                    <td>41</td>
                    <td>Bab 6: Kesimpulan</td>
                    <td>Kesimpulan, saran, dokumentasi</td>
                    <td><span class="tech-badge">Sesuai project</span></td>
                    <td>-</td>
                    <td><span class="price">200.000-300.000</span></td>
                    <td><span class="timeline-badge">1-2 hari</span></td>
                </tr>
                <tr>
                    <td>42</td>
                    <td>Complete TA Package</td>
                    <td>Project + Bab 4,5,6 + Dokumentasi</td>
                    <td><span class="tech-badge">Sesuai project</span></td>
                    <td>-</td>
                    <td><span class="price">+1.000.000</span></td>
                    <td><span class="timeline-badge">+7-10 hari</span></td>
                </tr>

                <!-- Add-On Services -->
                <tr class="category-row">
                    <td colspan="7"><i class="bi bi-plus-square me-2"></i>ADD-ON SERVICES</td>
                </tr>
                <tr>
                    <td>A1</td>
                    <td>Mobile Responsive</td>
                    <td>Optimasi untuk mobile/tablet</td>
                    <td>-</td>
                    <td><span class="price">100.000</span></td>
                    <td><span class="price">150.000</span></td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>A2</td>
                    <td>Admin Panel Advanced</td>
                    <td>Dashboard lengkap dengan statistik</td>
                    <td>-</td>
                    <td><span class="price">150.000</span></td>
                    <td><span class="price">200.000</span></td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>A3</td>
                    <td>Export Data</td>
                    <td>Excel, PDF, CSV export</td>
                    <td>-</td>
                    <td><span class="price">75.000</span></td>
                    <td><span class="price">100.000</span></td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>A4</td>
                    <td>Email Notification</td>
                    <td>Auto-send email untuk events tertentu</td>
                    <td>-</td>
                    <td><span class="price">100.000</span></td>
                    <td><span class="price">150.000</span></td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>A5</td>
                    <td>SMS Gateway</td>
                    <td>Integrasi SMS notification</td>
                    <td>-</td>
                    <td><span class="price">150.000</span></td>
                    <td><span class="price">200.000</span></td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>A6</td>
                    <td>Payment Gateway</td>
                    <td>Midtrans, QRIS, e-wallet</td>
                    <td>-</td>
                    <td><span class="price">200.000</span></td>
                    <td><span class="price">300.000</span></td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>A7</td>
                    <td>Multi-language</td>
                    <td>Support 2+ bahasa</td>
                    <td>-</td>
                    <td><span class="price">150.000</span></td>
                    <td><span class="price">200.000</span></td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>A8</td>
                    <td>Advanced Security</td>
                    <td>CAPTCHA, 2FA, encryption</td>
                    <td>-</td>
                    <td><span class="price">200.000</span></td>
                    <td><span class="price">300.000</span></td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>A9</td>
                    <td>SEO Optimization</td>
                    <td>Meta tags, sitemap, speed optimization</td>
                    <td>-</td>
                    <td><span class="price">150.000</span></td>
                    <td><span class="price">200.000</span></td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>A10</td>
                    <td>Deployment/Hosting</td>
                    <td>Setup di hosting + domain</td>
                    <td>-</td>
                    <td><span class="price">100.000</span></td>
                    <td><span class="price">150.000</span></td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>A11</td>
                    <td>Unit Testing</td>
                    <td>PHPUnit, automated testing</td>
                    <td>-</td>
                    <td><span class="price">200.000</span></td>
                    <td><span class="price">Included</span></td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>A12</td>
                    <td>API Documentation</td>
                    <td>Postman, swagger documentation</td>
                    <td>-</td>
                    <td><span class="price">150.000</span></td>
                    <td><span class="price">200.000</span></td>
                    <td>-</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="d-lg-none" id="mobileView">
        <!-- Category: Static Websites -->
        <div class="mb-4 mobile-category">
            <div class="luxury-card border-0 mb-3">
                <div class="card-header bg-purple text-white p-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-globe me-2"></i>STATIC WEBSITES
                    </h5>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12 mobile-item" data-search="landing page sederhana html css js responsive contact form">
                    <div class="card luxury-card border-0">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold mb-0 text-purple">1. Landing Page Sederhana</h6>
                                <span class="timeline-badge">1-2 hari</span>
                            </div>
                            <p class="text-muted mb-2 fs-7">1 halaman, HTML+CSS, responsive basic, contact form</p>
                            <div class="mb-2">
                                <span class="tech-badge">HTML</span>
                                <span class="tech-badge">CSS</span>
                                <span class="tech-badge">JS</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted d-block">Harga Biasa</small>
                                    <span class="price fs-7">75.000-125.000</span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">Harga TA</small>
                                    <span class="price fs-7">150.000-200.000</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mobile-item" data-search="website company profile html css js home about services portfolio contact">
                    <div class="card luxury-card border-0">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold mb-0 text-purple">2. Website Company Profile</h6>
                                <span class="timeline-badge">2-3 hari</span>
                            </div>
                            <p class="text-muted mb-2 fs-7">3-5 halaman (Home, About, Services, Portfolio, Contact)</p>
                            <div class="mb-2">
                                <span class="tech-badge">HTML</span>
                                <span class="tech-badge">CSS</span>
                                <span class="tech-badge">JS</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted d-block">Harga Biasa</small>
                                    <span class="price fs-7">150.000-250.000</span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">Harga TA</small>
                                    <span class="price fs-7">300.000-400.000</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mobile-item" data-search="landing page premium animasi parallax modern design html css js">
                    <div class="card luxury-card border-0">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold mb-0 text-purple">3. Landing Page Premium</h6>
                                <span class="timeline-badge">2-3 hari</span>
                            </div>
                            <p class="text-muted mb-2 fs-7">1 halaman dengan animasi, parallax, modern design</p>
                            <div class="mb-2">
                                <span class="tech-badge">HTML</span>
                                <span class="tech-badge">CSS</span>
                                <span class="tech-badge">JS</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted d-block">Harga Biasa</small>
                                    <span class="price fs-7">200.000-300.000</span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">Harga TA</small>
                                    <span class="price fs-7">400.000-500.000</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mobile-item" data-search="website sekolah organisasi galeri foto berita profil html css js">
                    <div class="card luxury-card border-0">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold mb-0 text-purple">4. Website Sekolah/Organisasi</h6>
                                <span class="timeline-badge">3-4 hari</span>
                            </div>
                            <p class="text-muted mb-2 fs-7">5-8 halaman, galeri foto, berita, profil</p>
                            <div class="mb-2">
                                <span class="tech-badge">HTML</span>
                                <span class="tech-badge">CSS</span>
                                <span class="tech-badge">JS</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted d-block">Harga Biasa</small>
                                    <span class="price fs-7">250.000-350.000</span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">Harga TA</small>
                                    <span class="price fs-7">450.000-600.000</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category: Basic Dynamic PHP -->
        <div class="mb-4 mobile-category">
            <div class="luxury-card border-0 mb-3">
                <div class="card-header bg-purple text-white p-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-server me-2"></i>BASIC DYNAMIC - PHP
                    </h5>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12 mobile-item" data-search="crud sederhana php mysql tabel tambah edit hapus lihat data">
                    <div class="card luxury-card border-0">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold mb-0 text-purple">5. CRUD Sederhana</h6>
                                <span class="timeline-badge">2-3 hari</span>
                            </div>
                            <p class="text-muted mb-2 fs-7">1 tabel, tambah/edit/hapus/lihat data, no login</p>
                            <div class="mb-2">
                                <span class="tech-badge">PHP</span>
                                <span class="tech-badge">MySQL</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted d-block">Harga Biasa</small>
                                    <span class="price fs-7">200.000-280.000</span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">Harga TA</small>
                                    <span class="price fs-7">400.000-500.000</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mobile-item" data-search="crud login php mysql sistem session management">
                    <div class="card luxury-card border-0">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold mb-0 text-purple">6. CRUD + Login</h6>
                                <span class="timeline-badge">3-4 hari</span>
                            </div>
                            <p class="text-muted mb-2 fs-7">CRUD + sistem login, session management</p>
                            <div class="mb-2">
                                <span class="tech-badge">PHP</span>
                                <span class="tech-badge">MySQL</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted d-block">Harga Biasa</small>
                                    <span class="price fs-7">300.000-380.000</span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">Harga TA</small>
                                    <span class="price fs-7">500.000-650.000</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mobile-item" data-search="crud login admin php mysql multi user admin panel user management">
                    <div class="card luxury-card border-0">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold mb-0 text-purple">7. CRUD + Login + Admin</h6>
                                <span class="timeline-badge">4-5 hari</span>
                            </div>
                            <p class="text-muted mb-2 fs-7">Multi-user, admin panel, user management</p>
                            <div class="mb-2">
                                <span class="tech-badge">PHP</span>
                                <span class="tech-badge">MySQL</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted d-block">Harga Biasa</small>
                                    <span class="price fs-7">380.000-450.000</span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">Harga TA</small>
                                    <span class="price fs-7">650.000-800.000</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mobile-item" data-search="sistem absensi sederhana php mysql input laporan hari bulan">
                    <div class="card luxury-card border-0">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold mb-0 text-purple">8. Sistem Absensi Sederhana</h6>
                                <span class="timeline-badge">3-4 hari</span>
                            </div>
                            <p class="text-muted mb-2 fs-7">Input absensi, laporan per hari/bulan</p>
                            <div class="mb-2">
                                <span class="tech-badge">PHP</span>
                                <span class="tech-badge">MySQL</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted d-block">Harga Biasa</small>
                                    <span class="price fs-7">350.000-420.000</span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">Harga TA</small>
                                    <span class="price fs-7">600.000-750.000</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mobile-item" data-search="sistem inventory basic php mysql barang masuk keluar stok laporan">
                    <div class="card luxury-card border-0">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold mb-0 text-purple">9. Sistem Inventory Basic</h6>
                                <span class="timeline-badge">4-5 hari</span>
                            </div>
                            <p class="text-muted mb-2 fs-7">Barang masuk/keluar, stok, laporan</p>
                            <div class="mb-2">
                                <span class="tech-badge">PHP</span>
                                <span class="tech-badge">MySQL</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted d-block">Harga Biasa</small>
                                    <span class="price fs-7">400.000-480.000</span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">Harga TA</small>
                                    <span class="price fs-7">700.000-850.000</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category: Laravel Framework -->
        <div class="mb-4 mobile-category">
            <div class="luxury-card border-0 mb-3">
                <div class="card-header bg-purple text-white p-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-layers me-2"></i>LARAVEL FRAMEWORK
                    </h5>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12 mobile-item" data-search="crud laravel basic eloquent orm blade templates authentication mysql">
                    <div class="card luxury-card border-0">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold mb-0 text-purple">10. CRUD Laravel Basic</h6>
                                <span class="timeline-badge">4-5 hari</span>
                            </div>
                            <p class="text-muted mb-2 fs-7">Eloquent ORM, Blade templates, authentication</p>
                            <div class="mb-2">
                                <span class="tech-badge">Laravel</span>
                                <span class="tech-badge">MySQL</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted d-block">Harga Biasa</small>
                                    <span class="price fs-7">450.000-550.000</span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">Harga TA</small>
                                    <span class="price fs-7">800.000-1.000.000</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add more Laravel items as needed -->
            </div>
        </div>

        <!-- Add more categories following the same pattern -->
    </div>

    <!-- No Results Message -->
    <div class="no-results" id="noResults" style="display: none;">
        <i class="bi bi-search"></i>
        <h5>Tidak ada hasil ditemukan</h5>
        <p>Coba gunakan kata kunci yang berbeda</p>
    </div>

    <!-- Scroll to Top Button -->
    <button class="scroll-top" id="scrollTop">
        <i class="bi bi-arrow-up"></i>
    </button>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const desktopTable = document.getElementById('priceTable');
            const mobileView = document.getElementById('mobileView');
            const noResults = document.getElementById('noResults');
            const loading = document.getElementById('loading');
            const scrollTop = document.getElementById('scrollTop');

            // Get elements for both desktop and mobile
            const desktopRows = desktopTable ? desktopTable.querySelectorAll('tbody tr') : [];
            const mobileItems = document.querySelectorAll('.mobile-item');
            const mobileCategories = document.querySelectorAll('.mobile-category');

            let searchTimeout;

            // Responsive search functionality
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();

                clearTimeout(searchTimeout);
                loading.style.display = 'block';

                searchTimeout = setTimeout(() => {
                    let visibleCount = 0;

                    if (window.innerWidth >= 992) {
                        // Desktop table search
                        let currentCategory = null;

                        desktopRows.forEach(row => {
                            if (row.classList.contains('category-row')) {
                                currentCategory = row;
                                row.style.display = 'none';
                                return;
                            }

                            const text = row.textContent.toLowerCase();
                            const isVisible = query === '' || text.includes(query);

                            row.style.display = isVisible ? '' : 'none';

                            if (isVisible) {
                                visibleCount++;
                                if (currentCategory) {
                                    currentCategory.style.display = '';
                                }
                            }
                        });

                        // Show/hide desktop table
                        if (visibleCount === 0 && query !== '') {
                            desktopTable.parentElement.style.display = 'none';
                            noResults.style.display = 'block';
                        } else {
                            desktopTable.parentElement.style.display = 'block';
                            noResults.style.display = 'none';
                        }
                    } else {
                        // Mobile card search
                        mobileCategories.forEach(category => {
                            const items = category.querySelectorAll('.mobile-item');
                            let categoryHasVisible = false;

                            items.forEach(item => {
                                const searchData = item.getAttribute('data-search') || '';
                                const text = (item.textContent + ' ' + searchData).toLowerCase();
                                const isVisible = query === '' || text.includes(query);

                                item.style.display = isVisible ? '' : 'none';

                                if (isVisible) {
                                    visibleCount++;
                                    categoryHasVisible = true;
                                }
                            });

                            // Show/hide category header
                            category.style.display = categoryHasVisible || query === '' ? '' : 'none';
                        });

                        // Show/hide mobile view
                        if (visibleCount === 0 && query !== '') {
                            mobileView.style.display = 'none';
                            noResults.style.display = 'block';
                        } else {
                            mobileView.style.display = 'block';
                            noResults.style.display = 'none';
                        }
                    }

                    loading.style.display = 'none';
                }, 300);
            });

            // Scroll to top functionality
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    scrollTop.classList.add('show');
                } else {
                    scrollTop.classList.remove('show');
                }
            });

            scrollTop.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // Hover effects for desktop
            if (desktopRows.length > 0) {
                desktopRows.forEach(row => {
                    if (!row.classList.contains('category-row')) {
                        row.addEventListener('mouseenter', function() {
                            this.style.backgroundColor = 'rgba(139, 92, 246, 0.08)';
                        });

                        row.addEventListener('mouseleave', function() {
                            this.style.backgroundColor = '';
                        });
                    }
                });
            }

            // Mobile card touch effects
            mobileItems.forEach(item => {
                item.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                    this.style.transition = 'transform 0.1s ease';
                }, {
                    passive: true
                });

                item.addEventListener('touchend', function() {
                    this.style.transform = 'scale(1)';
                    this.style.transition = 'transform 0.2s ease';
                }, {
                    passive: true
                });
            });

            // Handle resize to refresh search
            let resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    // Trigger search refresh
                    const event = new Event('input');
                    searchInput.dispatchEvent(event);
                }, 100);
            });

            // Add CSS for mobile responsive design
            const style = document.createElement('style');
            style.textContent = `
        .bg-purple {
            background: linear-gradient(135deg, #8B5CF6, #A855F7) !important;
        }

        .mobile-item {
            transition: all 0.2s ease;
        }

        .mobile-item:active {
            transform: scale(0.98);
        }

        @media (max-width: 991px) {
            .page-title {
                font-size: 1.8rem !important;
                flex-direction: column;
                gap: 10px;
            }

            .search-input {
                padding: 12px 15px;
                font-size: 14px;
            }

            .price {
                font-size: 0.8rem !important;
                padding: 4px 8px !important;
            }

            .tech-badge {
                font-size: 0.75rem !important;
                padding: 2px 6px !important;
            }

            .timeline-badge {
                font-size: 0.75rem !important;
                padding: 2px 6px !important;
            }
        }

        @media (max-width: 576px) {
            .page-header {
                padding: 20px 15px !important;
            }

            .search-container {
                padding: 15px !important;
            }

            .page-title {
                font-size: 1.5rem !important;
            }
        }
    `;
            document.head.appendChild(style);
        });
    </script>
@endpush
