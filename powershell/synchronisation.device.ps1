if ((Get-Module -ListAvailable -Name PdqStuff) -eq $false) {
	Install-Module -Name PdqStuff;
	Import-Module -Name PdqStuff;
}

$computers = PDQInventory.exe GetAllComputers;
$computerInfo = @();

foreach ($computer in $computers) {
	$c = Get-PdqInventoryComputerData -Name $computer -Table Computers | Select Name, Chassis, OSName, OS, OSServicePack, OSArchitecture, Manufacturer, Model, Memory, BiosManufacturer, BiosVersion;

	if ($c.OSName.Contains("Server") -eq $false -and $c.OS.Contains("Unidentified") -eq $false) {
        $c;    
    }
}