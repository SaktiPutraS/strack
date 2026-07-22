<#
    deploy.ps1 - Deploy strack ke hosting (Hostinger) lewat git + SSH.

    Alur:
      1. (opsional) commit semua perubahan lokal bila -Message diberikan
      2. push branch saat ini ke origin (GitHub)
      3. SSH ke hosting: git pull --ff-only + bersihkan cache Laravel

    Prasyarat:
      - SSH key sudah terpasang (uji: `ssh saktify`)
      - Hosting adalah clone git dari remote yang sama (branch main)

    Contoh pakai:
      ./scripts/deploy.ps1                        # push commit yang sudah ada, lalu deploy
      ./scripts/deploy.ps1 -Message "perbaiki sidebar"   # commit semua + push + deploy
      ./scripts/deploy.ps1 -SkipPush              # hanya jalankan pull di hosting
#>

param(
    [string]$Message = "",
    [switch]$SkipPush,
    [string]$SshHost = "saktify",
    [string]$RemotePath = "~/domains/strack.my.id/public_html",
    [string]$Branch = "main"
)

$ErrorActionPreference = "Stop"

function Step($text) { Write-Host "`n==> $text" -ForegroundColor Cyan }

# Pastikan dijalankan dari root project
$repoRoot = Split-Path -Parent $PSScriptRoot
Set-Location $repoRoot

if (-not $SkipPush) {
    if ($Message -ne "") {
        Step "Commit perubahan lokal: $Message"
        git add -A
        git commit -m $Message
    }

    Step "Push ke origin/$Branch"
    git push origin $Branch
    if ($LASTEXITCODE -ne 0) { throw "git push gagal. Batalkan deploy." }
}

Step "Deploy di hosting ($SshHost)"

# Perintah remote:
#  - buang perubahan composer.lock sepele agar pull tidak menolak
#  - fast-forward pull dari origin/main
#  - bersihkan cache lalu cache ulang view
$remote = @"
set -e
cd $RemotePath
echo '--- git checkout composer.lock (buang perubahan sepele) ---'
git checkout -- composer.lock 2>/dev/null || true
echo '--- git pull --ff-only ---'
git pull --ff-only origin $Branch
echo '--- bersihkan cache Laravel ---'
php artisan optimize:clear
php artisan view:cache
echo '--- HEAD sekarang ---'
git log --oneline -1
"@

ssh $SshHost $remote
if ($LASTEXITCODE -ne 0) { throw "Deploy di hosting gagal." }

Step "Selesai. Hosting sudah update."
