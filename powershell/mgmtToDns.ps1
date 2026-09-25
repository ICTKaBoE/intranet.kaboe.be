$getUrl = "https://api.kaboe.be/ps/management/";
$server = "SRV-DC01.coltd.be";
$what = "cctv", "printer";

$today = Get-Date -Format "yyyy-MM-dd";
$now = Get-Date -Format "yyyy-MM-dd HH:mm:ss";
$logPath = "$($PSScriptRoot)\Logs\MGMT To DNS\$($today)";
$logFile = "$($now.Replace(":", "-")).log";

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

$headers = @{
    "X-Authorization" = "Basic " + [System.Convert]::ToBase64String([System.Text.Encoding]::ASCII.GetBytes("admin:PianomanPA"))
};

function GatherItems {
    WriteLogFile -type "info" -message "Gathering items...";

    $items = @();
    foreach ($w in $what) {
        $get = "$($script:getUrl)$($w)";
        $items += (Invoke-RestMethod -Method Get -Uri $get -Headers $script:headers).items;
    }

    return $items;
}

function GetExistingItem {
    param (
        $item
    );

    $zoneName = $item.ip -replace '^(\d+)\.(\d+)\.(\d+)\.(\d+)$', '$3.$2.$1.in-addr.arpa';
    $name = $item.ip -replace '^(\d+)\.(\d+)\.(\d+)\.(\d+)$', '$4';
    WriteLogFile -type "info" -message "Trying to search for IP '$($item.ip)' in Reverse Lookup Zone '$($zoneName)'...";

    $i = Get-DnsServerResourceRecord -ComputerName $script:server -ZoneName $zoneName -Name $name -RRType Ptr -ErrorAction Ignore;
    return $i;
}

function LoopItems {
    param(
        $items
    )

    foreach ($item in $items) {
        if ($item.ip -ne $null) {
            EmptyLineLogFile;
            WriteLogFile -type "info" -message "$($item.name) - $($item.ip)";
        
            $i = GetExistingItem -item $item;

            if ($i -eq $null) {
                WriteLogFile -type "warn" -message "Not found - Create PTR";
                $zoneName = $item.ip -replace '^(\d+)\.(\d+)\.(\d+)\.(\d+)$', '$3.$2.$1.in-addr.arpa';
                $name = $item.ip -replace '^(\d+)\.(\d+)\.(\d+)\.(\d+)$', '$4';
                $fullName = $item.name;
                if (-not $fullName.Contains(".coltd.be")) {
                    $fullName = "$($fullName).coltd.be";
                }

                Add-DnsServerResourceRecordPtr -ComputerName $script:server -Name $name -ZoneName $zoneName -AllowUpdateAny -TimeToLive 01:00:00 -PtrDomainName $fullName;
            }
            else {
                WriteLogFile -type "info" -message "Found - Updating record";

                $zoneName = $item.ip -replace '^(\d+)\.(\d+)\.(\d+)\.(\d+)$', '$3.$2.$1.in-addr.arpa';
                $name = $item.ip -replace '^(\d+)\.(\d+)\.(\d+)\.(\d+)$', '$4';
                $fullName = $item.name;
                if (-not $fullName.Contains(".coltd.be")) {
                    $fullName = "$($fullName).coltd.be";
                }

                $new = $i.Clone();
                $new.RecordData.PtrDomainName = $fullName;
                Set-DnsServerResourceRecord -ComputerName $script:server -ZoneName $zoneName -OldInputObject $i -NewInputObject $new -PassThru;
            }
        }
    }
}

function CreateLogFile {
    param(
        [string]$path,
        [string]$file
    )

    if ((Test-Path -Path $path) -eq $false) {
        New-Item -Path $path -ItemType Directory;
    }

    if ((Test-Path -Path $path) -eq $false) {
        New-Item -Path $path -Name $file -ItemType File;
    }

    $path = "$($path)\$($file)";

    Set-Content -Path $path -Value "[START OF LOG - $($script:now)]";
}

function WriteLogFile {
    param(
        [string]$type,
        [string]$message
    )

    $path = "$($script:logPath)\$($script:logFile)";
    $type = $type.ToUpper();

    Add-Content -Path $path -Value "[$(Get-Date -Format "yyyy-MM-dd HH:mm:ss")]`t[$($type)]`t`t$($message)";
}

function CloseLogFile {
    $path = "$($script:logPath)\$($script:logFile)";

    Add-Content -Path $path -Value "[END OF LOG - $(Get-Date -Format "yyyy-MM-dd HH:mm:ss")]";
}

function EmptyLineLogFile {
    $path = "$($script:logPath)\$($script:logFile)";

    Add-Content -Path $path -Value "";
}

Clear-Host;

CreateLogFile -path $logPath -file $logFile;

$items = GatherItems;

if ($items.Length -gt 0) {
    WriteLogFile -type "warn" -message "Item count: $($items.Length) - Start running loop..."; 
    EmptyLineLogFile;
    LoopItems -items $items;
}
else {
    WriteLogFile -type "info" -message "Item count: $($items.Length) - Nothing to do, exit...";
}

CloseLogFile;