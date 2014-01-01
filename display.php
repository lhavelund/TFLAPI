<?php

/* 	This code is public domain.
	Written by Lasse Havelund <lasse@havelund.org>
	as an attempt to use the TFL API with PHP,
	uploaded to Github to discourage anyone from
	being as terrible at coding as I am. Enjoy!

	v1.0.2
*/

// Acquire data

$data = file_get_contents('http://countdown.api.tfl.gov.uk/interfaces/ura/instant_V1?StopPointName=Darlaston%20Road&returnlist=StopID,StopPointName,LineID,DestinationName,DirectionID,EstimatedTime');

// Remove newlines
$arrayfied = explode("\n", $data);

$stops = array();

// Push stops to array
foreach($arrayfied as $line) {
	$indiv = str_getcsv($line);
	array_push($stops, $indiv);
}


// Set sorting algo

function sortShitOut($a, $b) {
	if($a[6] == $b[6]) {
		return 0;
	}
	return ($a[6] < $b[6]) ? -1 : 1;
}

// Sort stops

usort($stops, "sortShitOut");

?>
<!doctype html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Buses from Darlaston Road</title>
	<!-- Include bootstrap -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap-theme.min.css">
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
	<style type="text/css">
		body { max-width: 700px !important; margin: 0 auto; padding: 10px; }
		#github { position: absolute; top: 0; right: 0; border: 0; }
		@media screen and (max-width: 768px) { #github { display: none; }}
	</style>	
	</head>
	<body>
		<div id="github">
			<a href="https://github.com/lhavelund"><img src="https://s3.amazonaws.com/github/ribbons/forkme_right_red_aa0000.png" alt="Fork me on GitHub" /></a>
			</div>
			<h1>Buses from Darlaston Road</h1>
			<p>This website uses live data from the <a href="http://tfl.gov.uk">Transport for London</a> API.</p>
			<?php // We have our data! Now for magic..
				echo '<h3>Buses towards Tooting Broadway</h3>';
				echo '<table class="table"><tr><th id="col1">Line</th><th id="col2">Destination</th><th id="col3">Arrival</th></tr>';
				foreach($stops as $entries) { // Display buses
					if($entries[1] == 'Darlaston Road' && $entries[4] == 1) {	// 1 = Towards Tooting
						echo '<tr>' . "\n";
						echo '<td>' . $entries[3] . '</td>';
						echo '<td>' . $entries[5] . '</td>';
					
						// Time calculation

						$now = time();
						$realTimeStamp = 0.001 * $entries[6]; 		// Times are multiplied by 1,000 for some reason.
						$timeDiff = $realTimeStamp - $now; 			// Get difference between now and due time.
						$timeDiffMins = round($timeDiff/60, 0); 	// Round to nearest minute
						if($timeDiffMins == 0) { 					// If less than half a minute (in theory)...
							$timeDiffMins = 'due';					// ... set as due.
						}
						else {
							$timeDiffMins = $timeDiffMins . 'min';	// If not due, add "min" suffix to indicate minutes
						}
						echo '<td>' . $timeDiffMins . '</td>';
						echo '</tr>';
					} else {
						continue;
					}
				}	
				echo '</table>';

				/* 	
					Keep in mind --
					this is terrible practice. I'm aware of it. I should
					most definitely re-write this at some point to
					actually cycle through in a single foreach instead
					of doing everything I just did over again. This is
					retarded, and I know. Shoot me.
				*/

				echo '<h3>Buses towards Kingston</h3>';
				echo '<table class="table"><tr><th id="col1">Line</th><th id="col2">Destination</th><th id="col3">Arrival</th></tr>';
				foreach($stops as $entries) { // Display buses
					if($entries[1] == 'Darlaston Road' && $entries[4] == 2) {	// 2 = Towards Kingston
						echo '<tr>' . "\n";
						echo '<td>' . $entries[3] . '</td>';
						echo '<td>' . $entries[5] . '</td>';
					
						// Time calculation

						$now = time();
						$realTimeStamp = 0.001 * $entries[6];
						$timeDiff = $realTimeStamp - $now;
						$timeDiffMins = round($timeDiff/60, 0);
						if($timeDiffMins == 0) {
							$timeDiffMins = 'due';
						}
						else {
							$timeDiffMins = $timeDiffMins . 'min';
						}
						echo '<td>' . $timeDiffMins . '</td>';
						echo '</tr>';
					} else {
						continue;
					}
				}	
				echo '</table>';
?>
</body>
</html>