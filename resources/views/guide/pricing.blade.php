@extends('guide.layouts.base')

@section('page-title', '💰 Pricing Guide')
@section('page-subtitle', 'Referensi Harga Jasa Website')

@section('phase-content')
                                <li>Berapa rata-rata nilai project yang close?</li>
                            </ul>
                            Data ini akan membantu Anda improve proses sales!
                        </div>
                    </div>
                </div>
            </div>

            <!-- PRICE GUIDE -->
            <div id="pricing" class="phase-section">
                <div class="flowchart-container">
                    <div style="text-align: center; margin-bottom: 2rem;">
                        <h2 style="font-size: 2rem; color: var(--dark); margin-bottom: 0.5rem;">💰 Price Guidelines</h2>
                        <p style="color: var(--text-light);">Berdasarkan analisis 20 percakapan real</p>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                        <!-- Website Statis -->
                        <div
                            style="background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); border-radius: 16px; padding: 2rem; border: 3px solid #0284c7;">
                            <h3 style="color: #0369a1; margin-bottom: 1rem; font-size: 1.5rem;">📄 Website Statis</h3>
                            <div class="price-table">
                                <div class="price-row">
                                    <span class="price-label">Landing Page Sederhana</span>
                                    <span class="price-value">350k - 500k</span>
                                </div>
                                <div class="price-row">
                                    <span class="price-label">Company Profile (tanpa admin)</span>
                                    <span class="price-value">700k - 850k</span>
                                </div>
                                <div class="price-row">
                                    <span class="price-label">Company Profile (dengan admin)</span>
                                    <span class="price-value">1.5jt - 1.7jt</span>
                                </div>
                            </div>
                            <div style="margin-top: 1rem; padding: 1rem; background: rgba(255,255,255,0.7); border-radius: 8px; font-size: 0.85rem;">
                                <strong>Include:</strong> Design, Development, Free Revisi 2x
                            </div>
                        </div>

                        <!-- Web Aplikasi -->
                        <div
                            style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 16px; padding: 2rem; border: 3px solid #10b981;">
                            <h3 style="color: #047857; margin-bottom: 1rem; font-size: 1.5rem;">⚙️ Web Aplikasi</h3>
                            <div class="price-table">
                                <div class="price-row">
                                    <span class="price-label">CRUD Sederhana (1-2 menu)</span>
                                    <span class="price-value">300k - 400k</span>
                                </div>
                                <div class="price-row">
                                    <span class="price-label">Web App Medium (3-5 menu)</span>
                                    <span class="price-value">600k - 800k</span>
                                </div>
                                <div class="price-row">
                                    <span class="price-label">Web App Kompleks (5+ menu)</span>
                                    <span class="price-value">1jt - 1.7jt</span>
                                </div>
                            </div>
                            <div style="margin-top: 1rem; padding: 1rem; background: rgba(255,255,255,0.7); border-radius: 8px; font-size: 0.85rem;">
                                <strong>Include:</strong> Database, CRUD, Auth System, Dashboard
                            </div>
                        </div>

                        <!-- Tugas Skripsi -->
                        <div
                            style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 16px; padding: 2rem; border: 3px solid #f59e0b;">
                            <h3 style="color: #b45309; margin-bottom: 1rem; font-size: 1.5rem;">🎓 Tugas Skripsi</h3>
                            <div class="price-table">
                                <div class="price-row">
                                    <span class="price-label">Paket Web Only</span>
                                    <span class="price-value">1.5jt</span>
                                </div>
                                <div class="price-row">
                                    <span class="price-label">Paket Web + Skripsi (All-in)</span>
                                    <span class="price-value">2.5jt</span>
                                </div>
                            </div>
                            <div style="margin-top: 1rem; padding: 1rem; background: rgba(255,255,255,0.7); border-radius: 8px; font-size: 0.85rem;">
                                <strong>Include:</strong> Web Development, Pendampingan Bimbingan, Revisi Unlimited sampai ACC, Source Code, Dokumentasi
                            </div>
                        </div>

                        <!-- Hosting & Domain -->
                        <div
                            style="background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); border-radius: 16px; padding: 2rem; border: 3px solid #6366f1;">
                            <h3 style="color: #4338ca; margin-bottom: 1rem; font-size: 1.5rem;">🌐 Hosting & Domain</h3>
                            <div class="price-table">
                                <div class="price-row">
                                    <span class="price-label">.my.id</span>
                                    <span class="price-value">100k/tahun</span>
                                </div>
                                <div class="price-row">
                                    <span class="price-label">.id atau .com</span>
                                    <span class="price-value">300k/tahun</span>
                                </div>
                                <div class="price-row">
                                    <span class="price-label">Subdomain (namaweb.saktify.my.id)</span>
                                    <span class="price-value">50k (one-time)</span>
                                </div>
                            </div>
                            <div style="margin-top: 1rem; padding: 1rem; background: rgba(255,255,255,0.7); border-radius: 8px; font-size: 0.85rem;">
                                <strong>Note:</strong> Biaya perpanjangan domain sama setiap tahun
                            </div>
                        </div>

                        <!-- Services Lain -->
                        <div
                            style="background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%); border-radius: 16px; padding: 2rem; border: 3px solid #ec4899;">
                            <h3 style="color: #be185d; margin-bottom: 1rem; font-size: 1.5rem;">🔧 Services Lain</h3>
                            <div class="price-table">
                                <div class="price-row">
                                    <span class="price-label">Hosting Only (project sudah jadi)</span>
                                    <span class="price-value">50k - 100k</span>
                                </div>
                                <div class="price-row">
                                    <span class="price-label">Fix Error (tergantung complexity)</span>
                                    <span class="price-value">100k - 300k</span>
                                </div>
                                <div class="price-row">
                                    <span class="price-label">Revisi Kode (per revisi)</span>
                                    <span class="price-value">~100k</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="info-box" style="margin-top: 2rem;">
                        <div class="info-box-title">
                            💡 Pricing Tips
                        </div>
                        <div class="info-box-content">
                            <strong>Faktor yang mempengaruhi harga:</strong>
                            <ol style="margin-left: 1.5rem; margin-top: 0.5rem; line-height: 1.8;">
                                <li><strong>Complexity:</strong> Jumlah fitur, integrasi, custom requirements</li>
                                <li><strong>Timeline:</strong> Urgent = +20-30% fee</li>
                                <li><strong>Client Type:</strong> Bisnis biasanya willing to pay more than students</li>
                                <li><strong>Revisions:</strong> Unlimited revisions = higher price</li>
                                <li><strong>Value Add-ons:</strong> Panel admin, SEO, training, maintenance</li>
                            </ol>
                        </div>
                    </div>

                    <div class="info-box" style="margin-top: 1rem;">
                        <div class="info-box-title">
                            🎯 Positioning Strategy
                        </div>
                        <div class="info-box-content">
                            <strong>Jangan bersaing di harga!</strong> Focus on:
                            <ul style="margin-left: 1.5rem; margin-top: 0.5rem; line-height: 1.8;">
                                <li><strong>Quality:</strong> Portofolio yang bagus justify higher price</li>
                                <li><strong>Service:</strong> Fast response, regular updates, professional communication</li>
                                <li><strong>Support:</strong> After-sales support, tutorial, maintenance</li>
                                <li><strong>Results:</strong> Focus on outcome, bukan output</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
@endsection
