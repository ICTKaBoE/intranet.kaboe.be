$getSyncUrl = "https://dev.api.kaboe.be/ps/sync";
$postSyncUrl = "$($getSyncUrl)/update/<ID>";
$baseOU = "OU=COLTD,DC=coltd,DC=be";
$server = "SRV-DC01.coltd.be";

$today = Get-Date -Format "yyyy-MM-dd";
$now = Get-Date -Format "yyyy-MM-dd HH:mm:ss";
$logPath = "$($PSScriptRoot)\Logs\Sync\$($today)";
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
    $items = Invoke-RestMethod -Method Get -Uri $script:getSyncUrl -Headers $script:headers;

    return $items;
}

function GetExistingItem {
    param (
        $item
    );

    WriteLogFile -type "info" -message "Trying to search for user with EmployeeID '$($item.employeeId)'...";

    $i = Get-ADUser -Server $script:server -SearchBase $script:baseOU -Filter "EmployeeID -eq '$($item.employeeId)' -or EmployeeID -eq 'P$($item.employeeId)'";
    return $i;
}

function LoopItems {
    param (
        $items
    );

    foreach ($item in $items) {
        WriteLogFile -type "info" -message "$($item.linked.employee.formatted.fullNameReversed) - $($item.employeeId)";

        $lastError = $null;
        $updateDatabase = $true;
        $ignoreError = $false;
        
        try {
            switch ($item.action) {
                "C" { 
                    CreateItem -item $item;
                }
                "U" {
                    UpdateItem -item $item;
                }
                "E" {
                    EnableItem -item $item;
                }
                "D" {
                    DisableItem -item $item;
                }
                Default {
                    $updateDatabase = $false;
                    $ignoreError = $true;
                }
            }
        }
        catch {
            $lastError = $_;
            WriteLogFile -type "error" -message $lastError;
        }

        if ($updateDatabase -eq $true) {
            UpdateDatabase -item $item -itemError $lastError -ignoreError $ignoreError;
        }

        EmptyLineLogFile;
    }
}

function CreateItem {
    param(
        $item
    );

    WriteLogFile -type "warn" -message "Creating user...";
    $i = GetExistingItem -item $item;

    if ($null -eq $i) {
        if ($item.password) {
            New-ADUser `
                -EmployeeID $item.employeeId `
                -Name $item.displayName `
                -GivenName $item.givenName `
                -Surname $item.surname `
                -DisplayName $item.displayName `
                -EmailAddress $item.emailAddress `
                -SamAccountName $item.samAccountName `
                -UserPrincipalName $item.userPrincipalName `
                -Path $item.ou `
                -Server $script:server -Confirm:$false;

            while ($null -eq $i) {
                Start-Sleep -Seconds 1;
                $i = GetExistingItem -item $item;
            }

            $item.displayName = $null;
            $item.givenName = $null;
            $item.surname = $null;
            $item.ou = $null;

            UpdateItem -item $item;
            EnableItem -item $item;       
        }
        else {
            throw "Cannot create user as no password is set!";
        }
    }
    else {
        throw "User already exists!";
    }
}

function SetItemMembership {
    param($item);

    $i = GetExistingItem -item $item;

    if ($item.memberOf) {
        foreach ($memberOf in $item.memberOf) {
            Add-ADGroupMember -Identity $memberOf -Members $i.SamAccountName -Server $script:server -ErrorAction SilentlyContinue | Out-Null;
        }
    }
}

function UpdateItem {
    param(
        $item
    );

    WriteLogFile -type "warn" -message "Updating user...";
    $i = GetExistingItem -item $item;

    if ([string]::IsNullOrEmpty($i)) {
        throw "User not found in OU '$($script:baseOU)'";
    }
    else {
        if ($null -ne $item.givenName) {
            WriteLogFile -type "warn" -message "Updating user GivenName...";
            $i | Set-ADUser -GivenName $item.givenName -Server $script:server -Confirm:$false;
        }

        if ($null -ne $item.surname) {
            WriteLogFile -type "warn" -message "Updating user Surname...";
            $i | Set-ADUser -Surname $item.surname -Server $script:server -Confirm:$false;
        }

        if ($null -ne $item.displayName) {
            WriteLogFile -type "warn" -message "Updating user DisplayName...";
            $i | Set-ADUser -DisplayName $item.displayName -Server $script:server -Confirm:$false;
            $i | Rename-ADObject -NewName $item.displayName -Server $script:server -Confirm:$false;
            $i = GetExistingItem -item $item;
        }

        if ($null -ne $item.companyName) {
            WriteLogFile -type "warn" -message "Updating user Company...";
            $i | Set-ADUser -Company $item.companyName -Server $script:server -Confirm:$false;
        }

        if ($null -ne $item.department) {
            WriteLogFile -type "warn" -message "Updating user Department...";
            $i | Set-ADUser -Department $item.department -Server $script:server -Confirm:$false;
        }

        if ($null -ne $item.jobTitle) {
            WriteLogFile -type "warn" -message "Updating user Title...";
            $i | Set-ADUser -Title $item.jobTitle -Server $script:server -Confirm:$false;
        }

        if ($null -ne $item.otherAttributes) {
            $otherAttributes = @{};
            $item.otherAttributes.PSObject.properties | Foreach { $otherAttributes[$_.Name] = $_.Value };

            if ($otherAttributes.Length -gt 0) {
                WriteLogFile -type "warn" -message "Updating user Extension Attributes...";
                $i | Set-ADUser -Replace $otherAttributes -Server $script:server -Confirm:$false;
            }
        }

        if ($null -ne $item.memberOf) {
            WriteLogFile -type "warn" -message "Set user membership...";
            SetItemMembership -item $item;
        }

        if ($null -ne $item.password) {
            WriteLogFile -type "warn" -message "Updating user Password...";
            $i | Set-ADAccountPassword -NewPassword (ConvertTo-SecureString -AsPlainText $item.password -Force) -Server $script:server;
            $i | Set-ADUser -ChangePasswordAtLogon $false -PasswordNeverExpires ($item.type -eq "S") -Server $script:server;
        }

        if ($null -ne $item.ou) {
            WriteLogFile -type "warn" -message "Moving user to '$($item.ou)'...";
            $i | Move-ADObject -TargetPath $item.ou -Server $script:server -Confirm:$false;
        }

        if ($null -ne $item.thumbnailPhoto) {
            WriteLogFile -type "warn" -message "Updating user ThumbnailPhoto...";
            Invoke-WebRequest -Uri $item.thumbnailPhoto -OutFile "$($PSScriptRoot)\temp.jpg";
            $photo = [byte[]](Get-Content "$($PSScriptRoot)\temp.jpg" -Encoding Byte);
            Remove-Item -Path "$($PSScriptRoot)\temp.jpg" -Force -Confirm:$false;
            
            $i | Set-ADUser -Replace @{thumbnailPhoto = $photo } -Server $script:server;
        }
    }
}

function EnableItem {
    param(
        $item
    );

    WriteLogFile -type "warn" -message "Enable user...";
    $i = GetExistingItem -item $item;

    if ([string]::IsNullOrEmpty($i)) {
        throw "User not found in OU '$($script:baseOU)'!";
    }
    else {
        $i | Enable-ADAccount -Server $script:server -Confirm:$false;
        $i = GetExistingItem -item $item; 

        if ($i.Enabled -ne $true) {
            throw "Could not enable user!";
        }
        else {
            UpdateItem -item $item;
        }
    }
}

function DisableItem {
    param(
        $item
    );

    WriteLogFile -type "warn" -message "Disable user...";
    $i = GetExistingItem -item $item;

    if ([string]::IsNullOrEmpty($i)) {
        throw "User not found in OU '$($script:baseOU)'!";
    }
    else {
        $i | Disable-ADAccount -Server $script:server -Confirm:$false;
        $i = GetExistingItem -item $item;
        
        if ($i.Enabled -ne $false) {
            throw "Could not disable user!";
        }
    }
}

function UpdateDatabase {
    param(
        $item,
        $itemError = $null,
        $ignoreError = $false
    );
    
    WriteLogFile -type "warn" -message "Updating database...";

    $body = @{
        action     = $null
        lastAction = $item.action
        lastError  = $itemError
        lastSync   = (Get-Date -Format "yyyy-MM-dd HH:mm:ss")
    };
    
    if ($itemError.Length -gt 0 -and $false -eq $ignoreError) {
        $body.action = $item.action;
    }
        
    $postUri = $script:postSyncUrl.Replace("<ID>", $item.id);
    Invoke-RestMethod -Method Post -Uri $postUri -Body $body -Headers $script:headers;
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
    $type = $type.ToUpper();

    Add-Content -Path $path -Value "[END OF LOG - $(Get-Date -Format "yyyy-MM-dd HH:mm:ss")]";
}

function EmptyLineLogFile {
    $path = "$($script:logPath)\$($script:logFile)";
    $type = $type.ToUpper();

    Add-Content -Path $path -Value "";
}

Clear-Host;

CreateLogFile -path $logPath -file $logFile;

$items = GatherItems;

if ($items.items.Length -gt 0) {
    WriteLogFile -type "warn" -message "Item count: $($items.items.Length) - Start running loop..."; 
    EmptyLineLogFile;
    LoopItems -items $items.items;
}
else {
    WriteLogFile -type "info" -message "Item count: $($items.items.Length) - Nothing to do, exit...";
}

CloseLogFile;