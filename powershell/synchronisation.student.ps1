$getSyncStudentUrl = "https://intranet.kaboe.be/api/v1.0/sync/student";
$updateSyncStudentUrl = "$($getSyncStudentUrl)/<ID>";
$updateSyncTimeUrl = "$($getSyncStudentUrl)/time";

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
	"authentication" = "Basic " + [System.Convert]::ToBase64String([System.Text.Encoding]::ASCII.GetBytes("admin:PianomanPA"))
};

$students = $null;
$updateDatabase = $true;
$lastAdSyncError = "";
$lastAdSyncErrorIgnore = $false;

function StartSync {
	Clear-Host;
	UpdateSyncTime;
	$students = GatherStudents;

	if ($students.items.Length -gt 0) {
		LoopStudents -studens = $students;
	}
}

function GatherStudents {
	Write-Host "Gathering all students..." -NoNewline;
	$students = Invoke-RestMethod -Method Get -Uri $getSyncStudentUrl -Headers $headers;
	Write-Host "OK" -ForegroundColor Green;

	return $students;
}

function LoopStudents {
	param (
		$students
	);

	foreach ($student in $students.items) {
		$script:lastAdSyncError = "";
		$Script:lastAdSyncErrorIgnore = $false;
		$script:updateDatabase = $true;

		$actions = $student.action -split "`r`n";
		$actions = $actions -split [System.Environment]::NewLine;
		$actions = $actions.Split([System.Environment]::NewLine, [System.StringSplitOptions]::RemoveEmptyEntries);
		
		foreach ($action in $actions) {
			$userObject = GetUserObject -student $student;

			switch ($action) {
				"C" {  
					CreateStudent -student $student -userObject $userObject;
				}
				"U" {
					UpdateDetails -student $student -userObject $userObject;
				}
				"UP" {
					UpdatePassword -student $student -userObject $userObject;
				}
				"M" {
					MoveStudent -student $student -userObject $userObject
				}
				"A" {
					EnableStudent -userObject $userObject;
				}
				"DA" {
					DisableStudent -userObject $userObject;
				}
				"D" {
					DeleteStudent -userObject $userObject;
				}
				Default {
					Write-Host "NOTHING TO DO" -ForegroundColor Blue;
				}
			}
		}
		
		if ($script:updateDatabase) { 
			UpdateDatabase -student $student -lastAdSyncError $script:lastAdSyncError;
		}
	}
}

function CreateStudent {
	param (
		$student,
		$userObject = $null
	);

	Write-Host "CREATE ";

	if ($null -eq $userObject) {
		if ($student.password) {
			New-ADUser -Name $student.displayName -GivenName $student.firstName -Surname $student.name -DisplayName $student.displayName -EmailAddress $student.email -SamAccountName $student.samAccountName -UserPrincipalName $student.email -Description $student.description -Company $student.companyName -EmployeeID $student.uid -Path $student.ou;

			while ($null -eq $userObject) {
				Start-Sleep -Seconds 1;
			}

			Write-Host "OK" -ForegroundColor green;
		}
		else {
			$Script:lastAdSyncError += "`nCANNOT CREATE USER AS NO PASSWORD IS FOUND IN DB!";
			Write-Host "CANNOT CREATE USER AS NO PASSWORD IS FOUND IN DB!" -ForegroundColor red;
		}
	}
	else {
		$Script:lastAdSyncErrorIgnore = $true;
		$Script:lastAdSyncError += "`nUser already exists!";
		Write-Host "User already exists!" -ForegroundColor red;
	}
}

function UpdateDetails {
	param (
		$student,
		$userObject
	);

	Write-Host "UPDATE DETAILS " -NoNewline; 

	$userObject | Set-ADUser -Description $student.description -Company $student.companyName;

	if ($student.memberOf) {
		$userObject | Get-ADPrincipalGroupMembership | ForEach-Object {
			if ($_.Name -ne "Domain Users") {
				$_ | Remove-ADGroupMember -Members $student.samAccountName -Confirm:$false;
			}
		}

		$membersOf = $student.memberOf -split "`r`n";
		$membersOf = $membersOf -split [System.Environment]::NewLine;
		$membersOf = $membersOf.Split([System.Environment]::NewLine, [System.StringSplitOptions]::RemoveEmptyEntries);

		foreach ($memberOf in $membersOf) {
			Add-ADGroupMember -Identity $memberOf -Members $student.samAccountName;
		}
	}
	
	Write-Host "OK" -ForegroundColor green;
}

function UpdatePassword {
	param (
		$student,
		$userObject
	);

	Write-Host "UPDATE PASSWORD " -NoNewline; 

	if ($student.password) {
		$userObject | Set-ADAccountPassword -NewPassword (ConvertTo-SecureString -AsPlainText $student.password -Force);
		$userObject | Set-ADUser -ChangePasswordAtLogon $false;
		Write-Host "OK" -ForegroundColor green;
	}
	else {
		$script:lastAdSyncError += "`nNO PASSWORD SET/FOUND IN DB!";
		Write-Host "NO PASSWORD SET/FOUND IN DB" -ForegroundColor red;
	}
}
	
function MoveStudent {
	param (
		$student,
		$userObject
	);

	Write-Host "MOVE " -NoNewline; 
	$userObject | Move-ADObject -TargetPath $student.ou;
	Write-Host "OK" -ForegroundColor green;
}

function EnableStudent {
	param (
		$userObject
	);

	Write-Host "ENABLE " -NoNewline;
	$userObject | Enable-ADAccount;
	Write-Host "OK" -ForegroundColor green;
}

function DisableStudent {	
	param (
		$userObject
	);

	Write-Host "DISABLE " -NoNewline;
	$userObject | Disable-ADAccount;
	Write-Host "OK" -ForegroundColor green;
}

function DeleteStudent {
	param (
		$userObject
	);

	Write-Host "DELETE " -NoNewline; 
	$userObject | Remove-ADUser;
	Write-Host "OK" -ForegroundColor green;
}

function GetUserObject {
	param (
		$student,
		$output = $true
	);

	if ($output) { Write-Host "Searching student $($student.displayName)... " -NoNewline; }
	$uob = Get-ADUser -Filter "EmployeeID -eq $($student.uid)";

	if ($null -eq $uob) {
		if ($output) { Write-Host "USER NOT FOUND!" -ForegroundColor Red; }
	}
	else {
		if ($output) { Write-Host "OK" -ForegroundColor Green; }
	}

	return $uob;
}

function UpdateDatabase {
	param (
		$student,
		$lastAdSyncTime = (Get-Date -Format "yyyy-MM-dd HH:mm:ss"),
		$lastAdSyncError = $null
	);

	$updateBody = @{
		action                  = "N"
		lastAdSyncSuccessAction = $student.action
		lastAdSyncTime          = $lastAdSyncTime
		lastAdSyncError         = $lastAdSyncError.trim()
	};

	if ($updateBody.lastAdSyncError.Length -gt 0 -and $false -eq $Script:lastAdSyncErrorIgnore) {
		$updateBody.lastAdSyncSuccessAction = "N";
		$updateBody.action = $student.action;
	}

	Write-Host "Updating database... " -NoNewline;
	Invoke-RestMethod -Method Post -Uri $updateSyncStudentUrl.Replace("<ID>", $student.id) -Body $updateBody -Headers $script:headers | Out-Null;
	Write-Host "OK" -ForegroundColor green;
}

function UpdateSyncTime {
	Write-Host "Updating sync time... " -NoNewline;
	Invoke-RestMethod -Method Post -Uri $updateSyncTimeUrl -Headers $script:headers | Out-Null;
	Write-Host "OK" -ForegroundColor green;
}

Start-Transcript -Path "C:\KaBoE\Scripts\Logs\synchronisation.student\$(Get-Date -Format 'ddMMyyyyHHmmss').log";
StartSync;
Stop-Transcript;