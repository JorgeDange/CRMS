<# 
.SYNOPSIS
    ADG - Script de desenvolvimento PowerShell
    Configura PATH para PHP 8.4 do Herd e executa comandos comuns

.DESCRIPTION
    Script para facilitar execução de comandos comuns do projeto ADG
    Usa PHP 8.4 do Herd (C:\Users\JORGE DANGE\.config\herd\bin\php84\php.exe)

.EXAMPLE
    .\dev.ps1 test
    .\dev.ps1 test-filtro PipelineTest
    .\dev.ps1 serve
    .\dev.ps1 build
    .\dev.ps1 dev
    .\dev.ps1 pint
    .\dev.ps1 migrate-fresh
    .\dev.ps1 backup
    .\dev.ps1 schedule
    .\dev.ps1 logs
#>

param(
    [Parameter(Mandatory=$false, Position=0)]
    [ValidateSet('test', 'test-filtro', 'serve', 'build', 'dev', 'pint', 'pint-test', 'migrate', 'migrate-fresh', 'backup', 'backup-clean', 'schedule', 'logs', 'queue', 'shell')]
    [string]$Command = '',

    [Parameter(Mandatory=$false, Position=1)]
    [string]$Filter = '',

    [Parameter(ValueFromRemainingArguments=$true)]
    [string[]]$ExtraArgs = @()
)

$HerdPhp = "C:\Users\JORGE DANGE\.config\herd\bin\php84\php.exe"
$ProjectDir = "C:\Users\JORGE DANGE\Documents\PROJECTOS DE CLIENTES\Otimize\CRM\prototipo\crm-prototipo\adg"

# Verifica se PHP existe
if (-not (Test-Path $HerdPhp)) {
    Write-Error "PHP do Herd não encontrado em: $HerdPhp"
    exit 1
}

Set-Location $ProjectDir

if (-not $Command) {
    Write-Host ""
    Write-Host "==========================================" -ForegroundColor Cyan
    Write-Host "  ADG - Comandos disponíveis" -ForegroundColor Cyan
    Write-Host "==========================================" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "  dev test           - Executa todos os testes" -ForegroundColor Green
    Write-Host "  dev test-filtro <filtro> - Executa testes com filtro" -ForegroundColor Green
    Write-Host "  dev serve          - Inicia servidor em http://127.0.0.1:8099" -ForegroundColor Green
    Write-Host "  dev build          - Compila assets (npm run build)" -ForegroundColor Green
    Write-Host "  dev dev            - Inicia Vite em modo dev" -ForegroundColor Green
    Write-Host "  dev pint           - Formata código com Pint" -ForegroundColor Green
    Write-Host "  dev pint-test      - Verifica formatação sem alterar" -ForegroundColor Green
    Write-Host "  dev migrate        - Executa migrations" -ForegroundColor Green
    Write-Host "  dev migrate-fresh  - Recria BD e roda seeders" -ForegroundColor Green
    Write-Host "  dev backup         - Executa backup (spatie/laravel-backup)" -ForegroundColor Green
    Write-Host "  dev backup-clean   - Limpa backups antigos" -ForegroundColor Green
    Write-Host "  dev schedule       - Mostra agendamentos" -ForegroundColor Green
    Write-Host "  dev logs           - Mostra logs do Laravel (tail -f)" -ForegroundColor Green
    Write-Host "  dev queue          - Inicia queue worker" -ForegroundColor Green
    Write-Host "  dev shell          - Abre tinker" -ForegroundColor Green
    Write-Host ""
    Write-Host "Exemplo: .\dev.ps1 test-filtro PipelineTest" -ForegroundColor Yellow
    exit 0
}

switch ($Command) {
    'test' {
        Write-Host "Executando testes..." -ForegroundColor Cyan
        & $HerdPhp artisan test @ExtraArgs
    }
    'test-filtro' {
        if (-not $Filter) { Write-Error "Filtro obrigatório: .\dev.ps1 test-filtro <filtro>"; exit 1 }
        Write-Host "Executando testes com filtro: $Filter" -ForegroundColor Cyan
        & $HerdPhp artisan test --filter="$Filter" @ExtraArgs
    }
    'serve' {
        Write-Host "Iniciando servidor em http://127.0.0.1:8099 ..." -ForegroundColor Cyan
        & $HerdPhp artisan serve --no-reload --port=8099
    }
    'build' {
        Write-Host "Compilando assets..." -ForegroundColor Cyan
        npm run build
    }
    'dev' {
        Write-Host "Iniciando Vite dev server..." -ForegroundColor Cyan
        npm run dev
    }
    'pint' {
        Write-Host "Formatando código com Pint..." -ForegroundColor Cyan
        & $HerdPhp vendor/bin/pint
    }
    'pint-test' {
        Write-Host "Verificando formatação..." -ForegroundColor Cyan
        & $HerdPhp vendor/bin/pint --test
    }
    'migrate' {
        Write-Host "Executando migrations..." -ForegroundColor Cyan
        & $HerdPhp artisan migrate @ExtraArgs
    }
    'migrate-fresh' {
        Write-Host "Recriando banco e rodando seeders..." -ForegroundColor Cyan
        & $HerdPhp artisan migrate:fresh --seed
    }
    'backup' {
        Write-Host "Executando backup..." -ForegroundColor Cyan
        & $HerdPhp artisan backup:run @ExtraArgs
    }
    'backup-clean' {
        Write-Host "Limpando backups antigos..." -ForegroundColor Cyan
        & $HerdPhp artisan backup:clean
    }
    'schedule' {
        Write-Host "Agendamentos configurados:" -ForegroundColor Cyan
        & $HerdPhp artisan schedule:list
    }
    'logs' {
        Write-Host "Mostrando logs (Ctrl+C para sair)..." -ForegroundColor Cyan
        Get-Content storage\logs\laravel.log -Wait -Tail 50
    }
    'queue' {
        Write-Host "Iniciando queue worker..." -ForegroundColor Cyan
        & $HerdPhp artisan queue:work --tries=3 --timeout=60
    }
    'shell' {
        Write-Host "Abrindo Laravel Tinker..." -ForegroundColor Cyan
        & $HerdPhp artisan tinker
    }
    default {
        Write-Error "Comando desconhecido: $Command"
        Write-Host "Digite '.\dev.ps1' sem argumentos para ver comandos disponíveis."
        Write-Host ""
        Write-Host "Pressione Enter para sair..." -ForegroundColor Yellow
        Read-Host | Out-Null
        exit 1
    }
}