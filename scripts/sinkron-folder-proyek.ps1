<#
    sinkron-folder-proyek.ps1 - Samakan isi folder kerja lokal dengan data strack.

    Yang dikerjakan:
      1. Ambil daftar proyek dari strack (lewat SSH ke hosting).
      2. Cocokkan tiap folder di "D:\Project Saktify" dengan proyeknya.
         Hasil pencocokan disimpan di file peta supaya tidak ditanya berulang.
      3. Laporkan:
         - proyek yang masih jalan (PROGRESS/WAITING/LEAD) tapi BELUM punya folder
         - folder yang proyeknya sudah FINISHED/CANCELLED, siap diarsipkan
         - folder yang belum dipetakan
      4. Dengan -Arsipkan: folder yang sudah selesai di-RAR ke "E:\Backup Joki",
         isinya diuji dulu, baru folder aslinya dihapus.

    Tanpa -Arsipkan skrip ini TIDAK mengubah apa pun, hanya melapor.

    Contoh pakai:
      ./scripts/sinkron-folder-proyek.ps1                 # laporan saja
      ./scripts/sinkron-folder-proyek.ps1 -Arsipkan       # laporan + arsipkan (dikonfirmasi satu per satu)
      ./scripts/sinkron-folder-proyek.ps1 -TanpaTanya     # tanpa tanya jawab (untuk dijadwalkan)
#>

param(
    [string]$ProjectDir = "D:\Project Saktify",
    [string]$BackupDir  = "E:\Backup Joki",
    [string]$SshHost    = "saktify",
    [string]$RemotePath = "~/domains/strack.my.id/public_html",
    [string]$PetaFile   = "",
    [string]$DaftarFile = "",
    [string]$RarExe     = "C:\Program Files\WinRAR\Rar.exe",
    [switch]$Arsipkan,
    [switch]$TanpaTanya
)

$ErrorActionPreference = "Stop"
try { [Console]::OutputEncoding = [System.Text.Encoding]::UTF8 } catch {}

# Status yang dianggap masih berjalan (wajib punya folder kerja).
$StatusAktif = @('PROGRESS', 'WAITING', 'LEAD')
# Status yang dianggap sudah tutup (foldernya boleh diarsipkan).
$StatusSelesai = @('FINISHED', 'CANCELLED')

if ($PetaFile -eq "") { $PetaFile = Join-Path $ProjectDir "_peta-folder-proyek.json" }

function Judul($teks) { Write-Host "`n==> $teks" -ForegroundColor Cyan }
function Info($teks)  { Write-Host $teks }
function Baik($teks)  { Write-Host $teks -ForegroundColor Green }
function Awas($teks)  { Write-Host $teks -ForegroundColor Yellow }
function Salah($teks) { Write-Host $teks -ForegroundColor Red }

# ── Pencocokan nama ────────────────────────────────────────────────────────────

# Buang tanda baca, huruf kecil semua, sisakan kata yang berarti.
function Kata($teks) {
    if ($null -eq $teks) { return @() }

    $bersih = ($teks -replace '[^\p{L}\p{Nd}]+', ' ').ToLower().Trim()
    $buang = @('project', 'proyek', 'dan', 'the', 'web', 'file', 'cust', 'pt', 'cv')

    $hasil = @()
    foreach ($k in ($bersih -split '\s+')) {
        if ($k.Length -ge 3 -and $buang -notcontains $k) { $hasil += $k }
    }

    return $hasil
}

# Dua kata dianggap sama bila identik atau salah satu awalan yang lain
# ("analisi" vs "analisis", "revisi" vs "revisian").
function KataCocok($a, $b) {
    if ($a -eq $b) { return $true }
    if ($a.Length -ge 4 -and $b.StartsWith($a)) { return $true }
    if ($b.Length -ge 4 -and $a.StartsWith($b)) { return $true }
    return $false
}

# Skor kemiripan folder dengan sebuah proyek. Nama klien diberi bobot lebih
# besar karena itu penanda paling kuat di nama folder.
function Skor($kataFolder, $proyek) {
    $kataKlien = Kata $proyek.klien
    $kataJudul = Kata $proyek.judul

    $nilai = 0
    foreach ($kf in $kataFolder) {
        foreach ($kk in $kataKlien) { if (KataCocok $kf $kk) { $nilai += 3; break } }
        foreach ($kj in $kataJudul) { if (KataCocok $kf $kj) { $nilai += 1; break } }
    }

    # Proyek yang masih jalan sedikit diunggulkan bila skornya seri.
    if ($StatusAktif -contains $proyek.status) { $nilai += 0.5 }

    return $nilai
}

function BarisProyek($p) {
    return ("#{0} {1} - {2} [{3}]" -f $p.id, $p.klien, $p.judul, $p.status)
}

# Usulan nama folder untuk proyek yang belum punya: Project_Klien_Judul.
function UsulNamaFolder($p) {
    $potong = {
        param($teks)
        $bersih = ($teks -replace '[^\p{L}\p{Nd}]+', ' ').Trim()
        $bagian = @()
        foreach ($k in ($bersih -split '\s+')) {
            if ($k -ne '') { $bagian += ($k.Substring(0, 1).ToUpper() + $k.Substring(1)) }
        }
        return ($bagian -join '_')
    }

    $klien = & $potong $p.klien
    $judul = & $potong $p.judul

    return ("Project_{0}_{1}" -f $klien, $judul)
}

# ── Peta folder -> proyek ──────────────────────────────────────────────────────

function BacaPeta($path) {
    $peta = @{}

    if (-not (Test-Path $path)) { return $peta }

    $isi = Get-Content $path -Raw -Encoding UTF8
    if ($isi.Trim() -eq "") { return $peta }

    $obj = $isi | ConvertFrom-Json
    foreach ($prop in $obj.PSObject.Properties) {
        $peta[$prop.Name] = $prop.Value
    }

    return $peta
}

function TulisPeta($path, $peta) {
    $obj = New-Object PSObject
    foreach ($nama in ($peta.Keys | Sort-Object)) {
        Add-Member -InputObject $obj -MemberType NoteProperty -Name $nama -Value $peta[$nama]
    }

    ($obj | ConvertTo-Json -Depth 5) | Out-File -FilePath $path -Encoding utf8
}

# ── 1. Ambil daftar proyek ─────────────────────────────────────────────────────

Judul "Ambil daftar proyek dari strack"

if ($DaftarFile -ne "") {
    Info "Membaca daftar dari file: $DaftarFile"
    $mentah = Get-Content $DaftarFile -Raw -Encoding UTF8
} else {
    Info "SSH ke $SshHost ..."
    $mentah = (ssh $SshHost "cd $RemotePath && php artisan proyek:daftar --json") -join "`n"
    if ($LASTEXITCODE -ne 0) { throw "Gagal mengambil daftar proyek lewat SSH." }
}

$proyek = $mentah | ConvertFrom-Json
if ($null -eq $proyek -or $proyek.Count -eq 0) { throw "Daftar proyek kosong." }

$proyekById = @{}
foreach ($p in $proyek) { $proyekById[[string]$p.id] = $p }

Baik ("{0} proyek terbaca ({1} masih jalan)." -f $proyek.Count, @($proyek | Where-Object { $StatusAktif -contains $_.status }).Count)

# ── 2. Cocokkan folder ─────────────────────────────────────────────────────────

Judul "Periksa folder di $ProjectDir"

if (-not (Test-Path $ProjectDir)) { throw "Folder kerja tidak ditemukan: $ProjectDir" }

$folders = Get-ChildItem -Path $ProjectDir -Directory | Sort-Object Name
Info ("{0} folder ditemukan." -f $folders.Count)

$peta = BacaPeta $PetaFile
$petaBerubah = $false

$belumDipetakan = @()

foreach ($f in $folders) {
    if ($peta.ContainsKey($f.Name)) { continue }

    $kataFolder = Kata $f.Name

    $kandidat = @()
    foreach ($p in $proyek) {
        $nilai = Skor $kataFolder $p
        if ($nilai -ge 3) { $kandidat += [PSCustomObject]@{ Skor = $nilai; Proyek = $p } }
    }
    $kandidat = @($kandidat | Sort-Object -Property Skor -Descending | Select-Object -First 5)

    if ($TanpaTanya) {
        $belumDipetakan += [PSCustomObject]@{ Nama = $f.Name; Kandidat = $kandidat }
        continue
    }

    Write-Host ""
    Write-Host ("Folder belum dipetakan: {0}" -f $f.Name) -ForegroundColor Yellow

    if ($kandidat.Count -eq 0) {
        Info "  Tidak ada proyek yang mirip."
    } else {
        for ($i = 0; $i -lt $kandidat.Count; $i++) {
            Info ("  [{0}] {1}  (skor {2})" -f ($i + 1), (BarisProyek $kandidat[$i].Proyek), $kandidat[$i].Skor)
        }
    }

    Info "  [a] abaikan folder ini selamanya   [l] lewati sekarang   [id] ketik nomor proyek strack"
    $jawab = (Read-Host "  Pilih").Trim()

    if ($jawab -eq 'l' -or $jawab -eq '') {
        $belumDipetakan += [PSCustomObject]@{ Nama = $f.Name; Kandidat = $kandidat }
        continue
    }

    if ($jawab -eq 'a') {
        $peta[$f.Name] = [PSCustomObject]@{ project_id = $null; abaikan = $true; catatan = 'bukan proyek strack' }
        $petaBerubah = $true
        continue
    }

    $pilih = $null
    if ($jawab -match '^\d+$') {
        $angka = [int]$jawab
        if ($angka -ge 1 -and $angka -le $kandidat.Count) {
            $pilih = $kandidat[$angka - 1].Proyek
        } elseif ($proyekById.ContainsKey([string]$angka)) {
            $pilih = $proyekById[[string]$angka]
        }
    }

    if ($null -eq $pilih) {
        Salah "  Pilihan tidak dikenali, folder dilewati."
        $belumDipetakan += [PSCustomObject]@{ Nama = $f.Name; Kandidat = $kandidat }
        continue
    }

    $peta[$f.Name] = [PSCustomObject]@{
        project_id = $pilih.id
        abaikan    = $false
        catatan    = ("{0} - {1}" -f $pilih.klien, $pilih.judul)
    }
    $petaBerubah = $true
    Baik ("  Dipetakan ke " + (BarisProyek $pilih))
}

if ($petaBerubah) {
    TulisPeta $PetaFile $peta
    Info "`nPeta disimpan: $PetaFile"
}

# ── 3. Susun laporan ───────────────────────────────────────────────────────────

$idTerpakai = @{}
$siapArsip = @()
$petaYatim = @()

foreach ($nama in $peta.Keys) {
    $entri = $peta[$nama]
    if ($entri.abaikan -eq $true) { continue }

    $id = [string]$entri.project_id
    if (-not $proyekById.ContainsKey($id)) {
        $petaYatim += $nama
        continue
    }

    $idTerpakai[$id] = $true

    $adaFolder = Test-Path (Join-Path $ProjectDir $nama)
    if (-not $adaFolder) { continue }

    $p = $proyekById[$id]
    if ($StatusSelesai -contains $p.status) {
        $siapArsip += [PSCustomObject]@{ Nama = $nama; Proyek = $p }
    }
}

$aktifTanpaFolder = @()
foreach ($p in $proyek) {
    if ($StatusAktif -notcontains $p.status) { continue }
    if ($idTerpakai.ContainsKey([string]$p.id)) { continue }
    $aktifTanpaFolder += $p
}

Judul "Proyek masih jalan tapi BELUM punya folder"
if ($aktifTanpaFolder.Count -eq 0) {
    Baik "Tidak ada. Semua proyek yang masih jalan sudah punya folder."
} else {
    foreach ($p in ($aktifTanpaFolder | Sort-Object status, id)) {
        Awas ("  " + (BarisProyek $p))
        Info ("     usul folder: " + (UsulNamaFolder $p))
    }
}

Judul "Folder yang proyeknya sudah selesai (siap diarsipkan)"
if ($siapArsip.Count -eq 0) {
    Info "Tidak ada."
} else {
    foreach ($s in $siapArsip) {
        Info ("  {0}  ->  {1}" -f $s.Nama, (BarisProyek $s.Proyek))
    }
}

if ($belumDipetakan.Count -gt 0) {
    Judul "Folder yang belum dipetakan"
    foreach ($b in $belumDipetakan) {
        Awas ("  " + $b.Nama)
        if ($b.Kandidat.Count -eq 0) {
            Info "     tidak ada proyek yang mirip"
        } else {
            foreach ($k in $b.Kandidat) {
                Info ("     mirip: " + (BarisProyek $k.Proyek) + (" (skor {0})" -f $k.Skor))
            }
        }
    }
    Info "`nJalankan skrip ini tanpa -TanpaTanya untuk memetakannya."
}

if ($petaYatim.Count -gt 0) {
    Judul "Entri peta yang proyeknya tidak ada lagi di strack"
    foreach ($n in $petaYatim) { Awas ("  " + $n) }
}

# ── 4. Arsipkan ────────────────────────────────────────────────────────────────

if (-not $Arsipkan) {
    Judul "Selesai (mode laporan)"
    Info "Tidak ada yang diubah. Tambahkan -Arsipkan untuk memindahkan folder yang sudah selesai."
    exit 0
}

if ($siapArsip.Count -eq 0) {
    Judul "Selesai"
    Info "Tidak ada folder yang perlu diarsipkan."
    exit 0
}

Judul "Arsipkan folder yang sudah selesai"

if (-not (Test-Path $RarExe)) { throw "Rar.exe tidak ditemukan di: $RarExe" }
if (-not (Test-Path $BackupDir)) { throw "Folder backup tidak ditemukan: $BackupDir" }

$berhasil = 0
$dilewati = 0

foreach ($s in $siapArsip) {
    $sumber = Join-Path $ProjectDir $s.Nama

    Write-Host ""
    Info ("{0}  ->  {1}" -f $s.Nama, (BarisProyek $s.Proyek))

    if (-not $TanpaTanya) {
        $ya = (Read-Host "  Arsipkan dan hapus folder aslinya? (y/t)").Trim().ToLower()
        if ($ya -ne 'y' -and $ya -ne 'ya') {
            Info "  Dilewati."
            $dilewati++
            continue
        }
    }

    # Jangan pernah menimpa arsip lama.
    $target = Join-Path $BackupDir ($s.Nama + ".rar")
    if (Test-Path $target) {
        $target = Join-Path $BackupDir ("{0}_{1}.rar" -f $s.Nama, (Get-Date -Format 'yyyyMMdd'))
    }
    if (Test-Path $target) {
        $target = Join-Path $BackupDir ("{0}_{1}.rar" -f $s.Nama, (Get-Date -Format 'yyyyMMdd_HHmmss'))
    }

    Info "  Membuat arsip: $target"

    Push-Location $ProjectDir
    try {
        & $RarExe a -r -idq -- $target $s.Nama
        $kodeRar = $LASTEXITCODE
    } finally {
        Pop-Location
    }

    if ($kodeRar -ne 0 -or -not (Test-Path $target)) {
        Salah "  Gagal membuat arsip (kode $kodeRar). Folder asli TIDAK disentuh."
        $dilewati++
        continue
    }

    $ukuran = (Get-Item $target).Length
    if ($ukuran -lt 1024) {
        Salah "  Arsip mencurigakan (hanya $ukuran byte). Folder asli TIDAK disentuh."
        $dilewati++
        continue
    }

    Info "  Menguji isi arsip ..."
    & $RarExe t -idq -- $target
    if ($LASTEXITCODE -ne 0) {
        Salah "  Uji arsip GAGAL. Folder asli TIDAK disentuh."
        $dilewati++
        continue
    }

    Remove-Item -Path $sumber -Recurse -Force
    Baik ("  Selesai. {0} MB, folder asli dihapus." -f [math]::Round($ukuran / 1MB, 1))

    $peta[$s.Nama] = [PSCustomObject]@{
        project_id = $s.Proyek.id
        abaikan    = $false
        catatan    = ("{0} - {1}" -f $s.Proyek.klien, $s.Proyek.judul)
        diarsipkan = (Get-Date -Format 'yyyy-MM-dd')
        arsip      = (Split-Path $target -Leaf)
    }
    $petaBerubah = $true
    $berhasil++
}

if ($petaBerubah) { TulisPeta $PetaFile $peta }

Judul "Selesai"
Info ("{0} folder diarsipkan, {1} dilewati." -f $berhasil, $dilewati)
