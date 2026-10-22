$mysqlExe = 'C:\Program Files\MySQL\MySQL Server 8.4\bin\mysqld.exe'

$processes = Get-Process mysqld -ErrorAction SilentlyContinue | Where-Object { $_.Path -eq $mysqlExe }

if (-not $processes) {
    Write-Host 'MySQL 8.4 tidak sedang berjalan.'
    exit 0
}

# Hentikan tanpa -Force supaya mysqld menutup koneksi dan flush buffer dengan rapi.
$processes | Stop-Process
Start-Sleep -Milliseconds 800

$remaining = Get-Process mysqld -ErrorAction SilentlyContinue | Where-Object { $_.Path -eq $mysqlExe }

if ($remaining) {
    Write-Host 'Proses belum selesai, memaksa berhenti...'
    $remaining | Stop-Process -Force
    Start-Sleep -Milliseconds 500
}

Write-Host 'MySQL 8.4 dihentikan.'