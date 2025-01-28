$getSyncUrl = "https://dev.api.kaboe.be/ps/sync";
$postSyncUrl = "$($getSyncUrl)/update/<ID>";
$baseOU = "OU=COLTD,DC=coltd,DC=be";
$server = "SRV-DC01.coltd.be";

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

function StartSync {
    Clear-Host;
    $items = GatherItems;

    if ($items.items.Length -gt 0) {
        LoopItems -items $items.items;
    }
}

function GatherItems {
    $items = Invoke-RestMethod -Method Get -Uri $script:getSyncUrl -Headers $script:headers;

    return $items;
}

function GetExistingItem {
    param (
        $item
    );

    $i = Get-ADUser -Server $script:server -SearchBase $script:baseOU -Filter "EmployeeID -eq '$($item.employeeId)' -or EmployeeID -eq 'P$($item.employeeId)'";
    return $i;
}

function LoopItems {
    param (
        $items
    );

    foreach ($item in $items) {
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
        }

        if ($updateDatabase -eq $true) {
            UpdateDatabase -item $item -itemError $lastError -ignoreError $ignoreError;
        }
    }

}

function CreateItem {
    param(
        $item
    );

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

            UpdateItem -item $item;
            SetItemMembership -item $item;
            EnableItem -item $item;       
        }
    }
    else {
        throw "Cannot create user as no password is set!";
        # $script:lastError = "Cannot create user as no password is set!";
    }
}
else {
    throw "User already exists!";
    # $script:lastError = "User already exists!";
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

    $i = GetExistingItem -item $item;

    if ([string]::IsNullOrEmpty($i)) {
        throw "User not found in OU '$($script:baseOU)'";
        # $script:lastError = "User not found in OU '$($script:baseOU)'!";
    }
    else {
        if ($null -ne $item.givenName) {
            $i | Set-ADUser -GivenName $item.givenName -Server $script:server -Confirm:$false;
        }
        if ($null -ne $item.surname) {
            $i | Set-ADUser -Surname $item.surname -Server $script:server -Confirm:$false;
        }
        if ($null -ne $item.displayName) {
            $i | Set-ADUser -DisplayName $item.displayName -Server $script:server -Confirm:$false;
            $i | Rename-ADObject -NewName $item.displayName -Server $script:server -Confirm:$false;
            $i = GetExistingItem -item $item;
        }
        if ($null -ne $item.companyName) {
            $i | Set-ADUser -Company $item.companyName -Server $script:server -Confirm:$false;
        }
        if ($null -ne $item.department) {
            $i | Set-ADUser -Department $item.department -Server $script:server -Confirm:$false;
        }
        if ($null -ne $item.jobTitle) {
            $i | Set-ADUser -Title $item.jobTitle -Server $script:server -Confirm:$false;
        }
        if ($null -ne $item.otherAttributes) {
            $otherAttributes = @{};
            $item.otherAttributes.PSObject.properties | Foreach { $otherAttributes[$_.Name] = $_.Value };

            if ($otherAttributes.Length -gt 0) {
                $i | Set-ADUser -Replace $otherAttributes -Server $script:server -Confirm:$false;
            }
        }
        if ($null -ne $item.memberOf) {
            SetItemMembership -item $item;
        }
        if ($null -ne $item.password) {
            $i | Set-ADAccountPassword -NewPassword (ConvertTo-SecureString -AsPlainText $item.password -Force) -Server $script:server;
            $i | Set-ADUser -ChangePasswordAtLogon $false -PasswordNeverExpires ($item.type -eq "S") -Server $script:server;
        }
        if ($null -ne $item.ou) {
            $i | Move-ADObject -TargetPath $item.ou -Server $script:server -Confirm:$false;
        }
    }
}

function EnableItem {
    param(
        $item
    );

    $i = GetExistingItem -item $item;

    if ([string]::IsNullOrEmpty($i)) {
        throw "User not found in OU '$($script:baseOU)'!";
        # $script:lastError = "User not found in OU '$($script:baseOU)'!";
    }
    else {
        $i | Enable-ADAccount -Server $script:server -Confirm:$false;
        $i = GetExistingItem -item $item; 

        if ($i.Enabled -ne $true) {
            throw "Could not enable user!";
            # $script:lastError = "Could not enable user!";
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

    $i = GetExistingItem -item $item;

    if ([string]::IsNullOrEmpty($i)) {
        throw "User not found in OU '$($script:baseOU)'!";
        # $script:lastError = "User not found in OU '$($script:baseOU)'!";
    }
    else {
        $i | Disable-ADAccount -Server $script:server -Confirm:$false;
        $i = GetExistingItem -item $item;
        
        if ($i.Enabled -ne $false) {
            throw "Could not disable user!";
            # $script:lastError = "Could not disable user!";
        }
    }
}

function UpdateDatabase {
    param(
        $item,
        $itemError = $null,
        $ignoreError = $false
    );

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

StartSync;