$mysqlRoot = 'C:\Program Files\MySQL\MySQL Server 8.4'
$config = Join-Path $PSScriptRoot 'mysql.ini'
$logDir = Join-Path (Split-Path $PSScriptRoot -Parent) 'storage\mysql\logs'

if (-not (Test-Path -LiteralPath $config)) {
    throw 'tools/mysql.ini belum tersedia. Salin tools/mysql.ini.example dan sesuaikan path.'
}

$listener = Get-NetTCPConnection -State Listen -LocalPort 3307 -ErrorAction SilentlyContinue
if ($listener) {
    Write-Host 'MySQL sudah berjalan di port 3307.'
    exit 0
}

New-Item -ItemType Directory -Path $logDir -Force | Out-Null
$process = Start-Process `
    -FilePath (Join-Path $mysqlRoot 'bin\mysqld.exe') `
    -ArgumentList "--defaults-file=$config", '--console' `
    -WindowStyle Hidden `
    -RedirectStandardOutput (Join-Path $logDir 'stdout.log') `
    -RedirectStandardError (Join-Path $logDir 'stderr.log') `
    -PassThru

Write-Host "MySQL 8.4 mulai berjalan pada port 3307 (PID $($process.Id))."
