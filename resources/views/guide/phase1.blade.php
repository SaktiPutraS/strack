@extends('guide.layouts.base')

@section('page-title', '📞 Phase 1: Initial Contact')
@section('page-subtitle', 'Kontak Pertama dengan Klien')

@section('phase-content')
                    <div class="flow-node start">
                        <span class="node-label">Start</span>
                        <div class="node-title">Pertanyaan Pembuka</div>
                        <div class="node-content">
                            <div class="node-code">"Halo, ada yang bisa dibantu?"</div>
                            <p style="margin-top: 0.75rem; font-size: 0.85rem; color: var(--text-light);">
                                ⏱️ Tunggu respons awal dari klien
                            </p>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node decision">
                        <span class="node-label">Decision</span>
                        <div class="node-title">Identifikasi Jenis Kebutuhan</div>
                        <div class="node-content">
                            <div class="node-code">"Kalo boleh tau ini untuk kebutuhan apa ya kak?"</div>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="branches">
                        <div class="branch success">
                            <div class="branch-icon">A</div>
                            <div class="branch-title">Tugas Kuliah/Sekolah</div>
                            <div class="branch-desc">Website untuk tugas, skripsi, atau project kampus</div>
                        </div>
                        <div class="branch warning">
                            <div class="branch-icon">B</div>
                            <div class="branch-title">Bisnis/Pribadi</div>
                            <div class="branch-desc">Company profile, toko online, landing page</div>
                        </div>
                        <div class="branch">
                            <div class="branch-icon">C</div>
                            <div class="branch-title">Hanya Hosting</div>
                            <div class="branch-desc">Project sudah jadi, butuh hosting saja</div>
                        </div>
                        <div class="branch danger">
                            <div class="branch-icon">D</div>
                            <div class="branch-title">Perbaikan/Fix Error</div>
                            <div class="branch-desc">Website bermasalah, perlu diperbaiki</div>
                        </div>
                    </div>

                    <div class="info-box">
                        <div class="info-box-title">
                            💡 Tips Fase Identifikasi
                        </div>
                        <div class="info-box-content">
                            <strong>Tujuan fase ini:</strong> Mengarahkan percakapan ke jalur yang tepat agar pertanyaan selanjutnya relevan. Jangan
                            langsung tanya harga sebelum tau jenis kebutuhannya!
                        </div>
                    </div>
                </div>
            </div>
@endsection
