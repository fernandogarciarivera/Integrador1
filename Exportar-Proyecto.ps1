<#
.SYNOPSIS
    Exporta archivos del proyecto a un archivo de texto plano.

.DESCRIPTION
    Recorre recursivamente el directorio especificado, incluye archivos con extensiones definidas,
    y omite aquellos que coincidan con la lista de exclusion.

.PARAMETER OutputFile
    Nombre del archivo de salida (por defecto: exportacion_proyecto.txt)

.PARAMETER SourceDir
    Directorio raiz a escanear (por defecto: ./src)

.PARAMETER IncludeExtensions
    Lista de extensiones a incluir (separadas por coma). Por defecto: php,js,css,scss,blade.php,json,yml,yaml,conf,ini,sh,bat,vue,ts,tsx,jsx,html,xml,sql,composer,env

.PARAMETER ExcludePatterns
    Lista de patrones de exclusion (carpetas o archivos) separados por coma.
    Soporta comodines (*). Por defecto: node_modules,vendor,storage,.git,.idea,.vscode,package-lock.json,.lock,*.log,*.cache,*.tmp,*.bak,public/build,public/hot

.EXAMPLE
    .\Exportar-Proyecto.ps1

.EXAMPLE
    .\Exportar-Proyecto.ps1 -OutputFile "dump.txt" -ExcludePatterns "node_modules,vendor,storage,.git,*.log"

.EXAMPLE
    .\Exportar-Proyecto.ps1 -SourceDir "." -IncludeExtensions "php,js,json" -ExcludePatterns "node_modules,vendor"
#>

param(
    [string]$OutputFile = "exportacion_proyecto.txt",
    [string]$SourceDir = ".\src",
    [string]$IncludeExtensions = "php,
                        js,
                        css,
                        scss,
                        blade.php,
                        json,
                        yml,
                        yaml,
                        conf,
                        ini,
                        sh,
                        bat,
                        vue,
                        ts,
                        tsx,
                        jsx,
                        html,
                        xml,
                        sql",

    [string]$ExcludePatterns = "node_modules,
                storage,
                .git,
                .idea,
                .vscode,
                package-lock.json,
                composer.lock,
                .env,
                *.log,
                *.cache,
                *.tmp,
                *.bak,
                public/build,
                public/hot,
                vendor"
                )

# Convertir listas a arrays
$extensions = $IncludeExtensions -split ',' | ForEach-Object { $_.Trim() } | Where-Object { $_ -ne '' }
$excludeList = $ExcludePatterns -split ',' | ForEach-Object { $_.Trim() } | Where-Object { $_ -ne '' }

# Resolver la ruta de origen
try {
    $sourcePath = (Resolve-Path -Path $SourceDir -ErrorAction Stop).Path
}
catch {
    Write-Host "ERROR: No se puede resolver la ruta de origen: $SourceDir" -ForegroundColor Red
    exit 1
}

# Crear el archivo de salida (sobrescribe)
$header = "====================================================`n"
$header += " EXPORTACION DE ARCHIVOS DEL PROYECTO`n"
$header += " Fecha: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')`n"
$header += " Directorio origen: $SourceDir`n"
$header += " Extensiones incluidas: $IncludeExtensions`n"
$header += " Exclusiones: $ExcludePatterns`n"
$header += "`n"
$header += "====================================================`n`n"
$header | Out-File -FilePath $OutputFile -Encoding utf8

# Funciones
function Test-ExcludedPath {
    param([string]$Path)
    $pathToCheck = $Path -replace '/','\'
    foreach ($pattern in $excludeList) {
        $trimmed = $pattern.Trim()
        if ($trimmed -eq '') { continue }
        if ($trimmed -notmatch '[*?]') {
            $trimmed = "*$trimmed*"
        }
        if ($pathToCheck -like $trimmed) {
            return $true
        }
    }
    return $false
}

function Get-RelativePath {
    param([string]$FullPath, [string]$BasePath)
    $full = [System.IO.Path]::GetFullPath($FullPath)
    $base = [System.IO.Path]::GetFullPath($BasePath).TrimEnd('\','/')
    if ($full.StartsWith($base, [System.StringComparison]::OrdinalIgnoreCase)) {
        $relative = $full.Substring($base.Length).TrimStart('\','/')
        if ($relative -eq '') { return $full }
        return $relative
    }
    return $full
}

function Test-IncludedFile {
    param([System.IO.FileInfo]$File)
    $fileName = $File.Name
    $extension = $File.Extension.TrimStart('.')

    foreach ($ext in $extensions) {
        if ($ext -ieq 'blade.php') {
            if ($fileName -like '*.blade.php') {
                return $true
            }
            continue
        }
        if ($extension -ieq $ext) {
            return $true
        }
    }
    return $false
}

# Obtener todos los archivos
$allFilesRaw = Get-ChildItem -Path $sourcePath -Recurse -File -ErrorAction SilentlyContinue
Write-Host "Archivos totales encontrados (sin filtrar): $($allFilesRaw.Count)" -ForegroundColor Cyan

if ($allFilesRaw.Count -eq 0) {
    Write-Host "ERROR: No se encontraron archivos en el directorio: $sourcePath" -ForegroundColor Red
    exit 1
}

# Mostrar extensiones unicas para ayuda
$uniqueExtensions = $allFilesRaw | ForEach-Object { $_.Extension.TrimStart('.') } | Where-Object { $_ -ne '' } | Sort-Object -Unique
Write-Host "Extensiones encontradas en el directorio:" -ForegroundColor Yellow
$uniqueExtensions | ForEach-Object { Write-Host "   .$_" -ForegroundColor Gray }

# Filtrar por extension
$allFiles = $allFilesRaw | Where-Object { Test-IncludedFile -File $_ }
Write-Host "Archivos que coinciden con extensiones especificadas: $($allFiles.Count)" -ForegroundColor Green

if ($allFiles.Count -eq 0) {
    Write-Host "ADVERTENCIA: No se encontraron archivos con las extensiones especificadas." -ForegroundColor Yellow
    Write-Host "Extensiones actuales: $IncludeExtensions" -ForegroundColor Yellow
    Write-Host "Sugerencia: usa las extensiones que aparecen arriba." -ForegroundColor Yellow
    Write-Host "Ejemplo: -IncludeExtensions '$($uniqueExtensions -join ',')'" -ForegroundColor Gray
    exit 0
}

# Procesar archivos
$processed = 0
$errors = 0

Write-Host "Procesando archivos..." -ForegroundColor Cyan
Write-Host "Archivo de salida: $OutputFile" -ForegroundColor Cyan
Write-Host "Exclusiones: $ExcludePatterns" -ForegroundColor Yellow
Write-Host ""

foreach ($file in $allFiles) {
    $relativePath = Get-RelativePath -FullPath $file.FullName -BasePath $sourcePath

    if (Test-ExcludedPath -Path $relativePath) {
        Write-Host "Excluido: $relativePath" -ForegroundColor Gray
        continue
    }

    try {
        $content = Get-Content -Path $file.FullName -Raw -ErrorAction Stop
        if ($content -eq $null) { $content = "" }

        "========== $relativePath ==========" | Out-File -FilePath $OutputFile -Append -Encoding utf8
        $content | Out-File -FilePath $OutputFile -Append -Encoding utf8
        "" | Out-File -FilePath $OutputFile -Append -Encoding utf8
        "" | Out-File -FilePath $OutputFile -Append -Encoding utf8

        $processed++
        Write-Host "Procesado: $relativePath" -ForegroundColor Green
    }
    catch {
        $errors++
        Write-Host "Error al leer: $relativePath - $_" -ForegroundColor Red
    }
}

# Resumen
$footer = "`n====================================================`n"
$footer += " RESUMEN`n"
$footer += "====================================================`n"
$footer += " Archivos procesados: $processed`n"
$footer += " Errores: $errors`n"
$footer += " Total encontrados (con extensiones): $($allFiles.Count)`n"
$footer += " Fecha finalizacion: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')`n"
$footer += "====================================================`n"
$footer | Out-File -FilePath $OutputFile -Append -Encoding utf8

Write-Host ""
Write-Host "====================================================" -ForegroundColor Cyan
Write-Host "EXPORTACION COMPLETADA" -ForegroundColor Green
Write-Host "Archivo generado: $OutputFile" -ForegroundColor Yellow
Write-Host "Archivos procesados: $processed" -ForegroundColor Yellow
if ($errors -gt 0) {
    Write-Host "Errores: $errors" -ForegroundColor Red
}
Write-Host "====================================================" -ForegroundColor Cyan
Write-Host ""