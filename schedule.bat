@ECHO off
:Label1
echo This utility will create a scheduled task which runs
echo DeskPRO's inbuilt task scheduler every minute.
echo.
echo To continue; we need to know the full path to where you
echo have installed PHP on your system. An example might be
echo C:\Program Files\PHP\php-win.exe
echo.
echo Please note that you may have a php.exe in the same folder
echo You should use php-win.exe which surpresses the display
echo of a command line window.
echo.
set /p php="PHP Path: "
echo.
"%php%" -r "echo(\"installed\");" > data\tmp\php.txt
set /p info= < data\tmp\php.txt
del data\tmp\php.txt
IF NOT "%info%"=="installed" (
echo. 
echo.
echo.
echo * The path to PHP entered was invalid *
echo.
echo.
GOTO Label1
)
schtasks /create /tn DeskPRO /sc MINUTE /tr "\"%php%\" -q \"%~dp0cron.php\""
echo %DATE% %TIME% > data\tmp\schedule.txt
echo.
echo It will take 60 seconds for the scheduled task to begin
echo.
echo Please note that although your scheduled task has now been
echo created; it will only run when this windows user account is
echo logged in. To use DeskPRO in production you will probably
echo need to modify the scheduled task from the Windows Task Scheduler 
echo to specify the username and password of the windows account
echo the task should run under if this user is not logged in.
echo.
"%php%" "app\src\Application\InstallBundle\Install\WinSetup.php" "%php%"
pause