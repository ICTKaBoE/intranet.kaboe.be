Clear-Host;
$ErrorActionPreference = "SilentlyContinue";
Start-Transcript -Path "C:\KaBoE\Scripts\Logs\syncAdManagementComputer\$(Get-Date -Format 'ddMMyyyyHHmmss').log";

if (-not (Get-Module -ListAvailable -Name PdqStuff)) {
	Write-Host "Installing Module 'PdqStuff'...";
	Install-PackageProvider -Name NuGet -Force;
	Install-Module -Name PdqStuff -Force;
	Import-Module -Name PdqStuff;
}

Write-Host "Gathering all computers...";
$computers = PDQInventory.exe GetAllComputers;

$computerInfo = @();
$url = "https://intranet.kaboe.be/api/v1.0/sync/ad/management/computer";

add-type @"
    using System.Net;
    using System.Security.Cryptography.X509Certificates;
    public class TrustAllCertsPolicy : ICertificatePolicy {
        public bool CheckValidationResult(
            ServicePoint srvPoint, X509Certificate certificate,
            WebRequest request, int certificateProblem) {
            return true;
        }
    }
"@;
[System.Net.ServicePointManager]::CertificatePolicy = New-Object TrustAllCertsPolicy;

foreach ($computer in $computers) {
	Write-Host "Gathering computer info for $($computer)...";
	$c = Get-PdqInventoryComputerData -Name $computer -Table Computers | Select Name, Chassis, OSName, OS, OSServicePack, OSArchitecture, Manufacturer, Model, SerialNumber, Memory, BiosManufacturer, BiosVersion;

	if ($c.OSName.Contains("Server") -eq $false -and $c.OS.Contains("Unidentified") -eq $false) {
		Add-Member -InputObject $c -NotePropertyName "CPU" -NotePropertyValue "";
		Add-Member -InputObject $c -NotePropertyName "DiskSize" -NotePropertyValue 0;

		Write-Host "Gathering CPU Information for $($computer)...";
		$cpus = Get-PdqInventoryComputerData -Name $computer -Table CPUs -AllowNull;

		if ($null -ne $cpus) {
			foreach ($cpu in $cpus) {
				$c.CPU += $cpu.ProcessorSummary + ", " 
			} 
		}
        
		Write-Host "Gathering Hard Drive Information for $($computer)...";
		$hardDrives = Get-PdqInventoryComputerData -Name $computer -Table DiskDrives -AllowNull;

		if ($null -ne $hardDrives) {
			foreach ($hardDrive in $hardDrives) {
				if ($hardDrive.DiskDeviceId.Contains("PHYSICALDRIVE0")) {
					$c.DiskSize += $hardDrive.Size;   
				}
			} 
		}

		$computerInfo += $c;
	}
}

$headers = @{
	"authentication" = "Basic " + [System.Convert]::ToBase64String([System.Text.Encoding]::ASCII.GetBytes("admin:PianomanPA"))
};

Write-Host "Pushing info to server...";
foreach ($computer in $computerInfo) {
	$schoolId = 5;
	if ($computer.Name.Contains("MEI")) { $schoolId = 1; }
	elseif ($computer.Name.Contains("WEG")) { $schoolId = 2; }
	elseif ($computer.Name.Contains("BSA")) { $schoolId = 3; }
	elseif ($computer.Name.Contains("STJ")) { $schoolId = 4; }

	$type = "L";
	if ($computer.Chassis -eq "Mini Tower" -or $computer.Chassis -eq "Desktop") { $type = "D"; }

	$osType = "W";
	if ($computer.OSName -contains "Linux") { $osType = "L"; }
	elseif ($computer.OSName -contains "CentOS") { $osType = "C"; }

	$memory = [math]::round($computer.Memory / 1GB, 2);
	$disksize = [math]::round($computer.DiskSize / 1GB, 2);

	$body = @{
		"schoolId"               = $schoolId
		"buildingId"             = $null
		"roomId"                 = $null
		"name"                   = $computer.Name
		"type"                   = $type
		"osType"                 = $osType
		"osNumber"               = $computer.OS
		"osBuild"                = $computer.OSServicePack
		"osArchitecture"         = $computer.OSArchitecture
		"systemManufacturer"     = $computer.Manufacturer
		"systemModel"            = $computer.Model
		"systemMemory"           = "$($memory) GB"
		"systemProcessor"        = $computer.cpu.replace(", ", "")
		"systemSerialnumber"     = $computer.SerialNumber
		"systemBiosManufacturer" = $computer.BiosManufacturer
		"systemBiosVersion"      = $computer.BiosVersion
		"systemDrive"            = "$($disksize) GB"
	};

	Write-Host "Posting $($computer.Name) to server... " -NoNewline;
	$result = Invoke-RestMethod -Method Post -Uri $url -Headers $headers -Body $body;

	if ($result.PSObject.Properties.name -contains "message") {
		Write-Host $result.message;
	}
 else {
		Write-Host $result.validation;
	}
}

Write-Host "Done!";
Stop-Transcript;