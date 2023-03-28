$users = "$($PSScriptRoot)\allusers.csv";
$delimiter = ";";

try {
	if (!(Get-Module -ListAvailable -Name "ActiveDirectory")) {
		Install-Module -Name ActiveDirectory -AllowClobber -Scope CurrentUser -Force;
	}

	Import-Csv -Path $users -Delimiter $delimiter | Foreach {
		$email = $_.safeUpn;
		$user = Get-ADUser -Filter { EmailAddress -eq $email };

		if ($user -ne $Null) {
			$user | Set-ADUser -Add @{uid = $_.insz };
		}
		else {
			Write-Host "$($_.fullName) not found...";
		}
	}
}
catch {
	Write-Host $_;
}