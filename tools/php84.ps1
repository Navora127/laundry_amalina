$package = Get-ChildItem -LiteralPath (Join-Path $env:LOCALAPPDATA 'Microsoft\WinGet\Packages') -Directory -Filter 'PHP.PHP.8.4_*' -ErrorAction Stop |
    Sort-Object LastWriteTime -Descending |
    Select-Object -First 1

if ($null -eq $package) {
    throw 'PHP 8.4 tidak ditemukan. Jalankan: winget install --id PHP.PHP.8.4 --source winget'
}

& (Join-Path $package.FullName 'php.exe') @args
exit $LASTEXITCODE
