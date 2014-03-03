<?php
/**
 * @author Azuka Okuleye
 * Call using the name of the XTEAM generated code folder e.g.
 * php xteam.php 20140227_1414_43
 * Copy this file and distribution_package\XTEAM_scaffold_code\XTEAM_scaffold.zip
 * into distribution_package\assortedxadl_model
 */

$code   = $argv[1];
$folder = 'XTEAM_scaffold_'.time(); // Unique folder name

$zip = new ZipArchive;

// Extract zip file into new folder
if ($zip->open('XTEAM_scaffold.zip') === TRUE) {
    $zip->extractTo(__DIR__);
    $zip->close();
    rename('XTEAM_scaffold', $folder);
} else {
    exit(1);
}

// Load Visual C++ project file
$project = __DIR__ . '/' . $folder . '/XTEAM_Simulation/XTEAM_Simulation.vcproj';
$xml     = simplexml_load_file($project);

// Add source files
foreach (glob($code.'\\XTEAM_Simulation\\simulation_code\\*.cpp') as $file)
{
	$node = $xml->Files->Filter[0]->addChild('File');
	$node->addAttribute('RelativePath', '..\\..\\'.$code.'\\XTEAM_Simulation\\simulation_code\\'.basename($file));
}
// Add header files
foreach (glob($code.'\\XTEAM_Simulation\\simulation_code\\*.h') as $file)
{
	$node = $xml->Files->Filter[1]->addChild('File');
	$node->addAttribute('RelativePath', '..\\..\\'.$code.'\\XTEAM_Simulation\\simulation_code\\'.basename($file));
}

// Save project configuration
file_put_contents($project, $xml->asXML());

// See http://msdn.microsoft.com/en-us/library/xee0c8y7.aspx for devenv command line switches
$command = '"C:\\Program Files (x86)\\Microsoft Visual Studio 9.0\\Common7\\IDE\\devenv.exe" ';
$command .= escapeshellarg(__DIR__ . '/' . $folder . '/XTEAM_Simulation.sln');
$command .= ' /build';

// Create output folder and use it as the csv file output location
mkdir(__DIR__ . '/' . $folder . '/Output');
chdir(__DIR__ . '/' . $folder . '/Output');

// Build solution
system($command);
// Run simulation
system(escapeshellarg(__DIR__ . '/' . $folder . '/Debug/XTEAM_Simulation.exe'));
// Finally, launch explorer window with the output files
exec('explorer.exe ' . escapeshellarg(__DIR__ . '\\' . $folder . '\\Output'));
exit;
