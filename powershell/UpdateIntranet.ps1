Clear-Host;
Write-Host "!!! DO NOT CLOSE THIS WINDOW !!!" -backgroundColor red;
Write-Host PS Version: $PSVersionTable.PSVersion.Major

#Controleer of het script uitgevoerd wordt in PS7. Zoja, voer het uit in PS5.1
if ($PSVersionTable.PSVersion.Major -ge 6) {
    Write-Host "Dit script vereist Windows PowerShell 5.1. Het wordt nu automatisch herstart..." -ForegroundColor Yellow
    $ps51 = "$env:SystemRoot\System32\WindowsPowerShell\v1.0\powershell.exe"
    & $ps51 -File $PSCommandPath @args
    exit
}

$version = $null;
$githubLink = "https://github.com/ICTKaBoE/intranet.kaboe.be.git";
$githubLocation = "C:\intranet-update";
$contentLocation = "$($githubLocation)\v<VERSION>"
$ftpOldLocation = "/www.old";
$ftpDefaultLocation = "/www";
$ftpNewLocation = "/www-v<VERSION>";

$ftpServer = "ftp.kaboebe.webhosting.be";
$ftpUser = "kaboebe@kaboebe";
$ftpPass = "PianomanPA125!";
$ftpClient = $null;

$modules = "Transferetto";
$removes = ".git", ".vscode", "downloads", "powershell", ".gitattributes", ".gitignore", "frontend\shared\default\images\informat", "info.php", "out.json";
$copyDirs = "frontend\shared\default\images", "files", "logs";
$copyFiles = ".htaccess";

$dbUpdateLink = "https://kaboe.be/update_db.php?v=<VERSION>";

function InstallRequiredModule {
    foreach ($module in $modules) {
        Write-Host "Installing Required Module '$($module)'... " -NoNewline;

        if (Get-Module -ListAvailable -Name $module) {
            Write-Host "Already Installed" -ForegroundColor Yellow;
        }
        else {
            Install-Module -Name $module -AllowClobber -Force;
            Write-Host "Done" -ForegroundColor Green;
        }

        Write-Host "Importing Module '$($module)'... " -NoNewLine
        Import-Module $module -Force;
        Write-Host "OK" -ForegroundColor Green;
    }    
}

function RequestVersion {
    $version = Read-Host "Version";
    $version = $version.ToLower();
    if ($version.Contains("v")) {
        $version = $version.Replace("v", "");
    }

    return $version;
}

function TestFTP {
    ConnectFTP;
    DisconnectFTP;
}

function DownloadFromGit {
    Write-Host "Downloading content... " -NoNewline;

    git.exe init $script:githubLocation | Out-Null;
    cd $script:githubLocation;
    git.exe clone --branch "v$($version)" --single-branch $githubLink "v$($version)" | Out-Null;

    Write-Host "Done" -ForegroundColor Green;
}

function RemoveLocal {
    Write-Host "Removing directory... " -NoNewline
    $ext = $contentLocation.Replace("<VERSION>", $version);

    if ((Test-Path -Path $ext) -eq $true) {
        Remove-Item $ext -Force -Recurse -Confirm:$false;
        Write-Host "Done" -ForegroundColor Green;
    }
    else {
        Write-Host "Does not exists" -ForegroundColor DarkYellow;
    }
}

function RemoveExtraContent {
    $content = $contentLocation.Replace("<VERSION>", $version);

    foreach ($remove in $removes) {
        $path = "$($content)\$($remove)";
        Write-Host "Removing $($remove)... " -NoNewline;

        if ((Test-Path $path) -eq $true) {
            Remove-Item -Path $path -Recurse -Force -Confirm:$false;
            Write-Host "Done" -ForegroundColor Green;
        }
        else {
            Write-Host "Does not exists" -ForegroundColor DarkYellow;
        }
    }
}

function ConnectFTP {
    Write-Host "Connecting FTP... " -NoNewline;
    $script:ftpClient = Connect-FTP -Server $ftpServer -Username $ftpUser -Password $ftpPass -EncryptionMode None -ValidateAnyCertificate;
    Write-Host "Done" -ForegroundColor Green;
}

function DeleteOldVersion {
    Write-Host "Deleting old version... " -NoNewline;
    Remove-FTPDirectory -Client $script:ftpClient -RemotePath $ftpOldLocation;
    Write-Host "Done" -ForegroundColor Green;
}

function UploadToRoot {
    $content = $contentLocation.Replace("<VERSION>", $version);
    $dest = $ftpNewLocation.Replace("<VERSION>", $version);

    Write-Host "Uploading to '$($dest)'... " -NoNewline;
    Send-FTPDirectory -Client $script:ftpClient -LocalPath $content -RemotePath $dest -FolderSyncMode Mirror -RemoteExists Overwrite;
    Write-Host "Done" -ForegroundColor Green;
}
    
function CopyKeep {
    $newLocation = $ftpNewLocation.Replace("<VERSION>", $version);
    
    foreach ($copy in $copyDirs) {
        Write-Host "Copying $($copy) from old version to new version... " -NoNewline;
        Move-FTPDirectory -Client $script:ftpClient -RemoteSource "$($ftpDefaultLocation)\$($copy)" -RemoteDestination "$($newLocation)\$($copy)" -RemoteExists Overwrite | Out-Null;
        Write-Host "Done" -ForegroundColor Green;
    }
    
    foreach ($copy in $copyFiles) {
        Write-Host "Copying $($copy) from old version to new version... " -NoNewline;
        Move-FTPFile -Client $script:ftpClient -RemoteSource "$($ftpDefaultLocation)\$($copy)" -RemoteDestination "$($newLocation)\$($copy)" -RemoteExists Overwrite | Out-Null;
        Write-Host "Done" -ForegroundColor Green;
    }
}

function SwitchVersion {
    $newLocation = $ftpNewLocation.Replace("<VERSION>", $version);

    Write-Host "Switching to version $($version)... " -NoNewline;
    Rename-FTPFile -Client $script:ftpClient -Path $ftpDefaultLocation -DestinationPath $ftpOldLocation;
    Rename-FTPFile -Client $script:ftpClient -Path $newLocation -DestinationPath $ftpDefaultLocation;
    Write-Host "Done" -ForegroundColor Green;
}


function DisconnectFTP {
    Write-Host "Disconnecting FTP... " -NoNewline;
    Disconnect-FTP -Client $script:ftpClient;
    Write-Host "Done" -ForegroundColor Green;
}

function GenerateDBUpdateLink {
	$link = $dbUpdateLink.Replace("<VERSION>", $version);
	Write-Host "Don't Forget To Update DB: $($link)";
}

function StartUpdate {
    try {
        $version = RequestVersion;
        InstallRequiredModule;
        TestFTP;
        DownloadFromGit;
        Pause;
        RemoveExtraContent;
        Pause;
        ConnectFTP;
        DeleteOldVersion;
        UploadToRoot;
        CopyKeep;
	Write-Host "Don't Forget To Disable The CRON-jobs!"
        Pause;
        SwitchVersion;
	GenerateDBUpdateLink;
	Write-Host "Don't Forget To Enable The CRON-jobs!"
    }
    catch {
        Write-Error "`nError: $($_)";
    }
    
    DisconnectFTP;
    # RemoveLocal;
}

StartUpdate;
Pause;