@echo off
title Clinic System - One-Click Update
color 0B

echo ===================================================
echo             CLINIC SYSTEM UPDATER
echo ===================================================
echo.

:: Navigate to the folder where this batch file is located
cd /d "%~dp0"

:: Check if Git is installed
where git >nul 2>nul
if %errorlevel% neq 0 (
    color 0C
    echo [ERROR] Git is not installed or not recognized in PATH!
    echo Please install Git on this PC.
    echo.
    pause
    exit /b
)

:: Ensure this repository path is allowed in Git safe.directory
git config --global --add safe.directory "%CD%" >nul 2>nul

echo Fetching latest updates from GitHub...
echo.

git pull origin legacy

if %errorlevel% equ 0 (
    echo.
    color 0A
    echo ===================================================
    echo   [SUCCESS] Clinic System successfully updated!
    echo ===================================================
) else (
    echo.
    color 0C
    echo ===================================================
    echo   [WARNING] Update encountered an issue.
    echo   (Check error messages above)
    echo ===================================================
)

echo.
pause
