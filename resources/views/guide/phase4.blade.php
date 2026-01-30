@extends('guide.layouts.base')

@section('page-title', '⚙️ Phase 4: Development')
@section('page-subtitle', 'Proses Pengerjaan Project')

@section('phase-content')

<!-- Legend -->
<div class="legend">
    <div class="legend-item">
        <div class="legend-color start"></div>
        <span class="legend-text">Start/Pembuka</span>
    </div>
    <div class="legend-item">
        <div class="legend-color process"></div>
        <span class="legend-text">Process/Tahapan</span>
    </div>
    <div class="legend-item">
        <div class="legend-color decision"></div>
        <span class="legend-text">Decision/Keputusan</span>
    </div>
    <div class="legend-item">
        <div class="legend-color end"></div>
        <span class="legend-text">End/Selesai</span>
    </div>
</div>

                    <div class="flow-node start">
                        <span class="node-label">Fase 4</span>
                        <div class="node-title">Konfirmasi & Closing</div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node decision">
                        <span class="node-label">Q1</span>
                        <div class="node-title">Konfirmasi Final</div>
                        <div class="node-content">
                            <div class="node-code">"Bagaimana kak? Apakah sudah oke untuk harganya?"</div>
                            <p style="margin-top: 0.5rem; font-size: 0.85rem; font-style: italic;">
                                Atau: "Apakah berminat/jadi kak?"
                            </p>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="branches">
                        <div class="branch success">
                            <div class="branch-icon">✓</div>
                            <div class="branch-title">Jadi / Deal</div>
                            <div class="branch-desc">Lanjut ke SOP pembayaran</div>
                        </div>
                        <div class="branch warning">
                            <div class="branch-icon">💭</div>
                            <div class="branch-title">Ragu / Mau Pikir</div>
                            <div class="branch-desc">Set follow-up reminder</div>
                        </div>
                        <div class="branch danger">
                            <div class="branch-icon">✗</div>
                            <div class="branch-title">Tidak Jadi</div>
                            <div class="branch-desc">Closing dengan baik</div>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node process">
                        <span class="node-label">SOP</span>
                        <div class="node-title">Informasikan Alur Kerja (Jika Deal)</div>
                        <div class="node-content">
                            <div class="node-code" style="white-space: pre-line;">
                                "Baik kak, untuk prosesnya sebagai berikut:

                                Pengerjaan akan dimulai setelah DP minimal 30% dari total biaya diterima.

                                Selama proses pengerjaan, progres akan saya informasikan melalui link demo agar Kakak dapat memantau hasilnya secara
                                langsung.

                                Setelah website selesai dan disetujui, proses pelunasan dapat dilakukan, dan website akan langsung saya aktivasi untuk
                                penggunaan penuh.

                                Rekening:
                                🟦 BCA : 6042125799
                                🟧 Seabank : 901551898940
                                🟥 CIMB : 760967985900

                                a.n Sakti Putra S"
                            </div>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node success" style="border-color: var(--success); background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);">
                        <span class="node-label">Payment</span>
                        <div class="node-title">DP Diterima ✓</div>
                        <div class="node-content">
                            <div class="node-code">"Baik kak, DP sudah diterima. Saya langsung mulai pengerjaan ya 🙏"</div>
                        </div>
                    </div>

                    <div class="arrow">↓</div>

                    <div class="flow-node end">
                        <span class="node-label">Start Work</span>
                        <div class="node-title">🚀 Mulai Pengerjaan Project</div>
                    </div>

                    <div style="margin-top: 3rem;">
                        <div class="flow-nod

@endsection
