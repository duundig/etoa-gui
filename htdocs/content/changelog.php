<?PHP
	$changelogFile = "../Changelog_public.md";
	if (is_file($changelogFile)) {
		echo '<div style="text-align:left;">';
		$Parsedown = new Parsedown();
		echo $Parsedown->text(file_get_contents($changelogFile)); 
		echo '</div>';
	} else {
		echo "<h1>Changelog</h1>";
		error_msg("Changelog nicht verfügbar!",1);
	}
?>
