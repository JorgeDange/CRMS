param(
    [string]$Repo = 'C:\Users\JORGE DANGE\Documents\PROJECTOS DE CLIENTES\Otimize\CRM\prototipo\crm-prototipo',
    [string]$OutDir
)

$ErrorActionPreference = 'Stop'
if (-not $OutDir) { $OutDir = Join-Path $Repo 'adg\resources\css\prototype' }
New-Item -ItemType Directory -Force -Path $OutDir | Out-Null
$fontsOut = Join-Path $Repo 'adg\public\fonts'
New-Item -ItemType Directory -Force -Path $fontsOut | Out-Null

# ---------------------------------------------------------------- parsers ----
function Parse-Css([string]$css, [int]$pos, [int]$end) {
    $nodes = New-Object System.Collections.ArrayList
    $selStart = $pos
    while ($pos -lt $end) {
        $ch = $css[$pos]
        if ($ch -eq '{') {
            $sel = $css.Substring($selStart, $pos - $selStart).Trim()
            $depth = 1; $j = $pos + 1
            while ($j -lt $end -and $depth -gt 0) {
                $c2 = $css[$j]
                if ($c2 -eq '{') { $depth++ }
                elseif ($c2 -eq '}') { $depth-- }
                $j++
            }
            $bodyStart = $pos + 1
            $bodyEnd = [Math]::Max($j - 1, $bodyStart)
            $body = $css.Substring($bodyStart, $bodyEnd - $bodyStart)
            if ($sel.StartsWith('@')) {
                $children = Parse-Css $body 0 $body.Length
                # Body tambem aqui: @font-face/@page tem declaracoes, nao filhos regras
                [void]$nodes.Add([pscustomobject]@{ Kind = 'At'; Sel = $sel; Children = $children; Body = $body })
            } else {
                [void]$nodes.Add([pscustomobject]@{ Kind = 'Rule'; Sel = $sel; Body = $body })
            }
            $pos = $j; $selStart = $j
        }
        elseif ($ch -eq '}') { return ,$nodes }
        else { $pos++ }
    }
    return ,$nodes
}

function Select-KeptSelectors([string]$sel, [string]$keepRe) {
    $kept = @()
    foreach ($p in ($sel -split ',')) {
        $p2 = $p.Trim()
        if ($p2 -and ($p2 -match $keepRe)) { $kept += $p2 }
    }
    if ($kept.Count -eq 0) { return $null }
    return ($kept -join ', ')
}

# Resolve um caminho relativo a $baseDir (dir do css de origem, relativo ao repo)
# para um caminho absoluto a partir da raiz web (public/).
function Resolve-WebPath([string]$baseDir, [string]$rel) {
    $stack = New-Object System.Collections.Generic.List[string]
    foreach ($seg in (($baseDir + '/' + $rel) -split '/')) {
        if ($seg -eq '' -or $seg -eq '.') { continue }
        if ($seg -eq '..') { if ($stack.Count -gt 0) { $stack.RemoveAt($stack.Count - 1) }; continue }
        [void]$stack.Add($seg)
    }
    return '/' + ($stack -join '/')
}

function Rewrite-Urls([string]$text, [string]$srcDir = '') {
    # fontes: copiadas para public/fonts pelo rodape do script
    $text = $text -replace 'url\((\s*)(["'']?)\.\./fonts/', 'url($1$2/fonts/'
    # restantes relativos (ex.: ../img/flags do vendors.min.css) -> absolutos
    if ($srcDir) {
        $text = [regex]::Replace($text, 'url\((\s*)(["'']?)((?:\.\./)+[^)''"]*)(["'']?)\s*\)', {
            param($m)
            $path = Resolve-WebPath $srcDir $m.Groups[3].Value
            return 'url(' + $m.Groups[1].Value + $m.Groups[2].Value + $path + $m.Groups[4].Value + ')'
        })
    }
    return $text
}

function Write-Nodes($nodes, [System.Text.StringBuilder]$sb, [string]$keepRe, [switch]$AllFontFaces, [string]$srcDir = '') {
    foreach ($n in $nodes) {
        if ($n.Kind -eq 'Rule') {
            $k = Select-KeptSelectors $n.Sel $keepRe
            if ($k) { [void]$sb.Append($k).Append('{').Append((Rewrite-Urls $n.Body $srcDir)).Append("}`n") }
        } else {
            $sel = $n.Sel
            if ($sel -match '^@font-face') {
                $fam = if ($n.Body -match 'font-family:\s*([^;}]+)') { $Matches[1].Trim([char[]]@(' ', "'", '"')) } else { '' }
                if ($AllFontFaces -or $fam -match 'feather|bootstrap-icons|Font Awesome') {
                    [void]$sb.Append('@font-face{').Append((Rewrite-Urls $n.Body $srcDir)).Append("}`n")
                }
            }
            elseif ($sel -match '^@(media|supports|layer)') {
                $inner = New-Object System.Text.StringBuilder
                Write-Nodes $n.Children $inner $keepRe -AllFontFaces:$AllFontFaces -srcDir $srcDir
                if ($inner.Length -gt 0) {
                    [void]$sb.Append($sel).Append("{`n").Append($inner).Append("}`n")
                }
            }
            # @keyframes / @charset / outros: ignorados de proposito
        }
    }
}

function Extract-Css([string]$inFile, [string]$outFile, [string]$keepRe, [switch]$AllFontFaces) {
    $raw = [System.IO.File]::ReadAllText($inFile)
    $raw = [regex]::Replace($raw, '/\*.*?\*/', '', 'Singleline')   # comentarios
    $raw = [regex]::Replace($raw, '@import[^;]+;', '')              # imports remotos (fonts ja carregadas)
    $nodes = Parse-Css $raw 0 $raw.Length
    $srcDir = Split-Path $inFile -Parent
    if ($srcDir.StartsWith($Repo)) {
        $srcDir = $srcDir.Substring($Repo.Length).TrimStart('\', '/') -replace '\\', '/'
    } else { $srcDir = '' }
    $sb = New-Object System.Text.StringBuilder
    Write-Nodes $nodes $sb $keepRe -AllFontFaces:$AllFontFaces -srcDir $srcDir
    $header = "/* EXTRAIDO do prototipo ? gerado por tools/extract-prototype-css.ps1 (PROMPT_MASTER 4.6) */`n"
    [System.IO.File]::WriteAllText($outFile, $header + $sb.ToString(), (New-Object System.Text.UTF8Encoding($false)))
    $ruleCount = ([regex]::Matches($sb.ToString(), "\{")).Count
    Write-Host ("{0} -> {1} regras, {2} KB" -f (Split-Path $inFile -Leaf), $ruleCount, [math]::Round((Get-Item $outFile).Length / 1KB, 1))
}

function Flatten-Rules($nodes, [string]$media, [System.Collections.Specialized.OrderedDictionary]$map) {
    foreach ($n in $nodes) {
        if ($n.Kind -eq 'Rule') {
            $key = "$media|$($n.Sel)"
            if (-not $map.Contains($key)) { $map[$key] = $n.Body }
        }
        elseif ($n.Sel -match '^@(media|supports)') {
            Flatten-Rules $n.Children $n.Sel $map
        }
        # @font-face / @keyframes nao existem no bootstrap
    }
}

function Load-FlatMap([string]$file) {
    $raw = [System.IO.File]::ReadAllText($file)
    $raw = [regex]::Replace($raw, '/\*.*?\*/', '', 'Singleline')
    $raw = [regex]::Replace($raw, '@import[^;]+;', '')
    $map = [ordered]@{}
    Flatten-Rules (Parse-Css $raw 0 $raw.Length) '' $map
    return $map
}

# O bootstrap do prototipo (5.2.0-beta1) vem customizado com as cores do ADG (SEC.3).
# Sobrepoemos ao bootstrap 5.3 do npm apenas as regras que diferem + as regras com
# cores da marca - igual ao que o prototipo faz, sem trocar a base (SEC.5).
function Extract-BootstrapDiff([string]$protoFile, [string]$npmFile, [string]$outFile, [string]$brandRe) {
    $proto = Load-FlatMap $protoFile
    $npm = Load-FlatMap $npmFile
    $sb = New-Object System.Text.StringBuilder
    $kept = 0
    foreach ($key in $proto.Keys) {
        $body = $proto[$key]
        $isBrand = $body -match $brandRe
        $isDiff = (-not $npm.Contains($key)) -or ($npm[$key] -ne $body)
        if (-not ($isBrand -or $isDiff)) { continue }
        $idx = $key.IndexOf('|')
        $media = $key.Substring(0, $idx)
        $sel = $key.Substring($idx + 1)
        $decl = $sel + '{' + $body + '}'
        if ($media) { $decl = $media + '{' + $decl + '}' }
        [void]$sb.Append($decl).Append("`n")
        $kept++
    }
    $header = "/* BOOTSTRAP diff do prototipo - gerado por tools/extract-prototype-css.ps1 (PROMPT_MASTER 3/4/5) */`n"
    [System.IO.File]::WriteAllText($outFile, $header + $sb.ToString(), (New-Object System.Text.UTF8Encoding($false)))
    Write-Host ("bootstrap-diff -> {0} regras, {1} KB" -f $kept, [math]::Round((Get-Item $outFile).Length / 1KB, 1))
}

# ------------------------------------------------------------- whitelists ----
# theme.min.css: chrome do layout (nxl/header/page-header/auth) + componentes
# Bootstrap re-estilizados pelo template + utilitarios usados no markup copiado.
$themeKeep = `
    '\.nxl-|\.page-header|\.auth-cover|\.hamburger|\.custom-control|\.avatar|\.user-avtar|' + `
    '::-webkit-scrollbar|\bhtml\b|\bbody\b|\.minimenu|\.main-content|\.content-area|\.content-sidebar|' + `
    '\.dropdown|\.modal|\.offcanvas|\.btn|\.form-|\.input-group|\.card|\.badge|\.table|\.alert|' + `
    '\.breadcrumb|\.nav\b|\.nav-|\.list-group|\.progress|\.pagination|\.tooltip|\.accordion|\.popover|' + `
    '\.text-muted|\.text-dark|\.text-primary|\.text-success|\.text-danger|\.text-warning|\.text-info|' + `
    '\.text-teal|\.text-purple|\.text-indigo|' + `
    '\.text-uppercase|\.text-center|\.text-end|\.text-truncate|\.text-reset|\.text-decoration|' + `
    '\.fs-\d|\.wd-\d|\.ht-\d|\.fw-|\.font-body|\.c-pointer|\.footer|\.m-b-10|\.hstack|\.vstack|' + `
    '\.border-bottom-dashed|\.bg-soft-|\.bg-gray|\.border-gray|\.text-gray|\.search-form|' + `
    '\.notifications-|\.select-language|\.language[_-]|\.text-spacing|\.scroll-menu|' + `
    '\.bg-primary|\.bg-success|\.bg-danger|\.bg-warning|\.bg-secondary|\.rounded-circle|\.img-fluid|' + `
    '\.stretch|\.border-dashed|\.team-progress|' + `
    '\.calendar-|\.menu-navi|\.single-task-list|' + `
    '(^a\b|a:hover|a:not\(|^:focus$|^b$|^strong$)'

# tui-theme.min.css (assets/vendors/css): sidebar do calendario (lnb-calendars,
# checkbox da legenda), #renderRange e botoes das views de apps-calendar.html.
$tuiKeep = `
    '\.lnb-|#lnb\b|#renderRange|\.calendar-icon|\.calendar-bar|\.calendar-name|' + `
    '\.weekday-grid|\.schedule-time|\.render-range|\.tui-full-calendar-checkbox-round'

# vendors.min.css: apenas iconografia (feather, bootstrap-icons, FA) + fontes.
# '^i$' = regra base i{font-size:16px;font-family:feather} — sem ela as classes
# .feather-* tem content mas nenhum font-family (icones nao desenham).
$vendorsKeep = `
    '\.feather-|\[class\^=.feather|\.fa-|\[class\^=.fa|\[class\*=.fa|\bfa\b|\.fas\b|\.far\b|\.fab\b|' + `
    '\.bi-|\[class\^=.bi|\bbi\b|\.hamburger|^i$'

# ------------------------------------------------------------------- run ----
$brandRe = '#3454d1|#eff0f6|#283c50|#17c666|#ea4d4d|#ffa21d|#3dc7be|#64748b|#4b5563|#f0f2f8|#e5e7eb|' + `
    '52, ?84, ?209|23, ?198, ?102|234, ?77, ?77|255, ?162, ?29|61, ?199, ?190|40, ?60, ?80|' + `
    '100, ?116, ?139|75, ?85, ?99|240, ?242, ?248|229, ?231, ?235'

Extract-Css (Join-Path $Repo 'assets\css\theme.min.css')        (Join-Path $OutDir 'theme-extract.css')   $themeKeep
Extract-Css (Join-Path $Repo 'assets\vendors\css\tui-theme.min.css') (Join-Path $OutDir 'tui-theme-extract.css') $tuiKeep
Extract-Css (Join-Path $Repo 'assets\vendors\css\vendors.min.css') (Join-Path $OutDir 'icons-extract.css') $vendorsKeep -AllFontFaces
Extract-BootstrapDiff (Join-Path $Repo 'assets\css\bootstrap.min.css') `
    (Join-Path $Repo 'adg\node_modules\bootstrap\dist\css\bootstrap.min.css') `
    (Join-Path $OutDir 'bootstrap-diff.css') $brandRe

# fontes do prototipo -> public/fonts (url rewrite feito acima)
Copy-Item (Join-Path $Repo 'assets\vendors\fonts\*') $fontsOut -Force
Write-Host ("fontes copiadas: {0}" -f (Get-ChildItem $fontsOut).Count)
