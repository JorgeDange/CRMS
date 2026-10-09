@echo off
REM ==========================================
REM ADG - Script de desenvolvimento
REM Configura PATH para PHP 8.4 do Herd e executa comandos
REM ==========================================

set HERD_PHP="C:\Users\JORGE DANGE\.config\herd\bin\php84\php.exe"
set PROJECT_DIR="C:\Users\JORGE DANGE\Documents\PROJECTOS DE CLIENTES\Otimize\CRM\prototipo\crm-prototipo\adg"

cd /d %PROJECT_DIR%

if "%1"=="" (
    echo.
    echo ==========================================
    echo  ADG - Comandos disponiveis
    echo ==========================================
    echo.
    echo  dev test        - Executa todos os testes
    echo  dev test-filtro - Executa testes com filtro (ex: dev test-filtro PipelineTest)
    echo  dev serve       - Inicia servidor em http://127.0.0.1:8099
    echo  dev build       - Compila assets (npm run build)
    echo  dev dev         - Inicia Vite em modo dev (npm run dev)
    echo  dev pint        - Formata codigo com Pint
    echo  dev pint-test   - Verifica formatacao sem alterar
    echo  dev migrate     - Executa migrations
    echo  dev migrate-fresh - Recria BD e roda seeders
    echo  dev backup      - Executa backup (spatie/laravel-backup)
    echo  dev backup-clean - Limpa backups antigos
    echo  dev schedule    - Mostra agendamentos
    echo  dev logs        - Mostra logs do Laravel
    echo  dev queue       - Inicia queue worker
    echo  dev shell       - Abre tinker
    echo.
    echo Exemplo: dev test-filtro PipelineTest
    goto :eof
)

if "%1"=="test" (
    echo Executando testes...
    "%HERD_PHP%" artisan test %2 %3 %4 %5 %6 %7 %8 %9
    goto :eof
)

if "%1"=="test-filtro" (
    echo Executando testes com filtro: %2
    "%HERD_PHP%" artisan test --filter="%2" %3 %4 %5 %6 %7 %8 %9
    goto :eof
)

if "%1"=="serve" (
    echo Iniciando servidor em http://127.0.0.1:8099 ...
    "%HERD_PHP%" artisan serve --no-reload --port=8099
    goto :eof
)

if "%1"=="build" (
    echo Compilando assets...
    npm run build
    goto :eof
)

if "%1"=="dev" (
    echo Iniciando Vite dev server...
    npm run dev
    goto :eof
)

if "%1"=="pint" (
    echo Formatando codigo com Pint...
    "%HERD_PHP%" vendor/bin/pint
    goto :eof
)

if "%1"=="pint-test" (
    echo Verificando formatacao...
    "%HERD_PHP%" vendor/bin/pint --test
    goto :eof
)

if "%1"=="migrate" (
    echo Executando migrations...
    "%HERD_PHP%" artisan migrate %2 %3 %4 %5 %6 %7 %8 %9
    goto :eof
)

if "%1"=="migrate-fresh" (
    echo Recriando banco e rodando seeders...
    "%HERD_PHP%" artisan migrate:fresh --seed
    goto :eof
)

if "%1"=="backup" (
    echo Executando backup...
    "%HERD_PHP%" artisan backup:run %2 %3 %4 %5 %6 %7 %8 %9
    goto :eof
)

if "%1"=="backup-clean" (
    echo Limpando backups antigos...
    "%HERD_PHP%" artisan backup:clean
    goto :eof
)

if "%1"=="schedule" (
    echo Agendamentos configurados:
    "%HERD_PHP%" artisan schedule:list
    goto :eof
)

if "%1"=="logs" (
    echo Mostrando logs (Ctrl+C para sair)...
    type nul > storage\logs\laravel.log 2>nul
    powershell -Command "Get-Content storage\logs\laravel.log -Wait -Tail 50"
    goto :eof
)

if "%1"=="queue" (
    echo Iniciando queue worker...
    "%HERD_PHP%" artisan queue:work --tries=3 --timeout=60
    goto :eof
)

if "%1"=="shell" (
    echo Abrindo Laravel Tinker...
    "%HERD_PHP%" artisan tinker
    goto :eof
)

echo Comando desconhecido: %1
echo Digite "dev" sem argumentos para ver comandos disponiveis.
echo.
pause