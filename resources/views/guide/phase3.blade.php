@extends('guide.layouts.base')

@section('page-title', '💼 Phase 3: Quotation & Deal')
@section('page-subtitle', 'Penawaran Harga dan Negosiasi')

@section('phase-content')
                    <div class="flow-node start">
                        <span class="node-label">Fase 3</span>
                        <div class="node-title">Penawaran Harga & Estimasi</div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node process">
                        <span class="node-label">Prepare</span>
                        <div class="node-title">Susun Penawaran Lengkap</div>
                        <div class="node-content">
                            <div style="background: rgba(99,102,241,0.05); padding: 1.25rem; border-radius: 8px;">
                                <strong>Template Penawaran:</strong>
                                <div class="node-code" style="margin-top: 0.75rem; white-space: pre-line;">
                                    Baik kak, dari informasi yang kakak berikan, estimasi dari saya sebagai berikut:

                                    1. Biaya Development: Rp [X]
                                    2. Hosting & Domain ([jenis domain]): Rp [Y]

                                    Total: Rp [X+Y]

                                    Estimasi waktu pengerjaan: [X] hari

                                    Harga sudah include:
                                    - [list included items]
                                    - Free revisi [X] kali / sampai sesuai

                                    Jika kakak ingin melihat beberapa portofolio saya, dapat diakses melalui https://saktify.my.id/
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node decision">
                        <span class="node-label">Response</span>
                        <div class="node-title">Reaksi Klien terhadap Harga</div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="branches">
                        <div class="branch success">
                            <div class="branch-icon">✓</div>
                            <div class="branch-title">Setuju Langsung</div>
                            <div class="branch-desc">Lanjut ke Fase 4: Closing</div>
                        </div>
                        <div class="branch warning">
                            <div class="branch-icon">💭</div>
                            <div class="branch-title">Minta Nego</div>
                            <div class="branch-desc">Masuk ke proses negosiasi</div>
                        </div>
                        <div class="branch">
                            <div class="branch-icon">?</div>
                            <div class="branch-title">Ragu/Mau Pikir</div>
                            <div class="branch-desc">Follow-up strategy</div>
                        </div>
                        <div class="branch danger">
                            <div class="branch-icon">✗</div>
                            <div class="branch-title">Tolak/Tidak Jadi</div>
                            <div class="branch-desc">Closing dengan baik</div>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node decision">
                        <span class="node-label">Negosiasi</span>
                        <div class="node-title">Strategi Nego (Jika Diminta)</div>
                        <div class="node-content">
                            <div class="node-code">"Boleh di-share kak budget di kakak berapa? Biar saya coba sesuaikan"</div>
                            <div style="margin-top: 1rem;">
                                <div class="price-table">
                                    <div class="price-row">
                                        <span class="price-label">Gap Kecil (10-20%)</span>
                                        <span class="price-value">✅ Bisa negotiate</span>
                                    </div>
                                    <div class="price-row">
                                        <span class="price-label">Gap Sedang (20-40%)</span>
                                        <span class="price-value">⚠️ Kurangi scope</span>
                                    </div>
                                    <div class="price-row">
                                        <span class="price-label">Gap Besar (&gt;40%)</span>
                                        <span class="price-value">❌ Tolak sopan</span>
                                    </div>
                                    <div class="price-row">
                                        <span class="price-label">Urgent (&lt;3 hari)</span>
                                        <span class="price-value">💰 +20-30% fee</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node process">
                        <span class="node-label">Examples</span>
                        <div class="node-title">Contoh Strategi Nego</div>
                        <div class="node-content">
                            <div style="display: grid; gap: 1rem;">
                                <div style="background: rgba(16,185,129,0.1); padding: 1rem; border-radius: 8px; font-size: 0.85rem;">
                                    <strong>✅ Scenario 1: Gap Kecil</strong><br>
                                    Estimasi: 1jt | Budget: 850k<br>
                                    <em>"Oke kak, saya bisa kasih di 850k. Deal?"</em>
                                </div>
                                <div style="background: rgba(245,158,11,0.1); padding: 1rem; border-radius: 8px; font-size: 0.85rem;">
                                    <strong>⚠️ Scenario 2: Gap Sedang</strong><br>
                                    Estimasi: 1jt | Budget: 600k<br>
                                    <em>"Dengan budget 600k, saya bisa buatkan versi lebih sederhana tanpa panel admin. Bagaimana?"</em>
                                </div>
                                <div style="background: rgba(239,68,68,0.1); padding: 1rem; border-radius: 8px; font-size: 0.85rem;">
                                    <strong>❌ Scenario 3: Gap Besar</strong><br>
                                    Estimasi: 1jt | Budget: 300k<br>
                                    <em>"Maaf kak, dengan budget tersebut saya belum bisa ambil project kakak. Paling mentok saya bisa kasih 700k dengan
                                        scope lebih kecil 🙏"</em>
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
                            💡 Golden Rules Negosiasi
                        </div>
                        <div class="info-box-content">
                            <ol style="margin-left: 1.5rem; line-height: 1.8;">
                                <li><strong>Jangan undersell:</strong> Kualitas kerja = harga yang fair</li>
                                <li><strong>Always have a bottom line:</strong> Ketahui batas minimal Anda</li>
                                <li><strong>Value over price:</strong> Fokus pada value yang diberikan</li>
                                <li><strong>Be ready to walk away:</strong> Tidak semua project harus diambil</li>
                                <li><strong>Maintain professionalism:</strong> Tolak dengan sopan jika tidak cocok</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

@endsection
