$url = "https://dev.intranet.kaboe.be/api/v1.0/computernotification/$($env:COMPUTERNAME)";
$viewUrl = "https://dev.intranet.kaboe.be/public/computernotification?id={id}";
$processedIds = "C:\KaBoE\Scripts\computernotifications/processed.txt";

if (!(Test-Path -Path $processedIds)) {
    New-Item -Path $processedIds -ItemType File -Force;
}

$processedIdsArray = [string[]](Get-Content -Path $processedIds);

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

$code = @'
    [DllImport("user32.dll")]
    public static extern bool BlockInput(bool fBlockIt);
'@;

$userInput = Add-Type -MemberDefinition $code -Name Blocker -Namespace UserInput -PassThru;

Write-Host "Requesting notifications for $($env:COMPUTERNAME)...";
$notifications = Invoke-RestMethod -Method Get -Uri $url -Headers $headers;

foreach ($notification in $notifications.items) {
    if ($null -ne $processedIdsArray -and $processedIdsArray.Contains([string]$notification.id) -eq $true) { Continue; } 
    Add-Content -Path $processedIds -Value $notification.id;  
    $app = Start-Process -FilePath "${Env:ProgramFiles(x86)}\Google\Chrome\Application\chrome.exe" -ArgumentList "--start-kiosk `"$($viewUrl.Replace("{id}", $notification.id))`"" -PassThru;

    $null = $userInput::BlockInput($true);

    while ((Get-Date) -lt (Get-Date -Date $notification.end)) {
        Write-Host "$(Get-Date -Format "yyyy-MM-dd HH:mm:ss") --- $(Get-Date -Date $notification.end -Format "yyyy-MM-dd HH:mm:ss")... " -NoNewline;
        Write-Host "Showing message & Blocking input..." -ForegroundColor Red;
        Start-Sleep -Seconds 1;
    }

    Stop-Process -Id $app.Id;
    $null = $userInput::BlockInput($false);

}