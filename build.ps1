$ErrorActionPreference = 'Stop'

$ModuleName = 'iiromanesti_ai_seo'
$Version = '0.1.0'
$ArchiveName = "$ModuleName-v$Version.zip"
$DistDir = 'dist'
$BuildDir = Join-Path $DistDir 'build'
$PackageDir = Join-Path $BuildDir $ModuleName
$ArchivePath = Join-Path $DistDir $ArchiveName

if (Test-Path $BuildDir) {
    Remove-Item $BuildDir -Recurse -Force
}
New-Item -ItemType Directory -Path $PackageDir -Force | Out-Null
New-Item -ItemType Directory -Path $DistDir -Force | Out-Null

Copy-Item README.md, index.php, iiromanesti_ai_seo.php -Destination $PackageDir
New-Item -ItemType Directory -Path (Join-Path $PackageDir 'classes') -Force | Out-Null
Copy-Item classes/ApiClient.php, classes/Logger.php, classes/index.php -Destination (Join-Path $PackageDir 'classes')

if (Test-Path $ArchivePath) {
    Remove-Item $ArchivePath -Force
}
Compress-Archive -Path $PackageDir -DestinationPath $ArchivePath -CompressionLevel Optimal

Remove-Item $BuildDir -Recurse -Force
Write-Host "Created $ArchivePath"
