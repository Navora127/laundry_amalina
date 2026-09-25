$mysqlExe = 'C:\Program Files\MySQL\MySQL Server 8.4\bin\mysqld.exe'
$processes = Get-Process mysqld -ErrorAction SilentlyContinue | Where-Object { $_.Path -eq $mysqlExe }

if (-not $processes) {
    Write-Host 'MySQL 8.4 tidak sedang berjalan.'
    exit 0
}

$processes | Stop-Process -Force
Write-Host 'MySQL 8.4 dihentikan.'
