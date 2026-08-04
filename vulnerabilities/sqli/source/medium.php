<?php

if( isset( $_POST[ 'Submit' ] ) ) {
	// Get input
	$id = $_POST[ 'id' ];

	switch ($_DVWA['SQLI_DB']) {
		case MYSQL:
			$id = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $id);
			$query  = "SELECT first_name, last_name FROM users WHERE user_id = $id;";
			$result = mysqli_query($GLOBALS["___mysqli_ston"], $query) or die( '<pre>' . mysqli_error($GLOBALS["___mysqli_ston"]) . '</pre>' );

			// Get results
			while( $row = mysqli_fetch_assoc( $result ) ) {
				// Display values
				$first = $row["first_name"];
				$last  = $row["last_name"];

				// Feedback for end user
				$html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
			}

			break;
		case SQLITE:
			global $sqlite_db_connection;

			$id = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $id);
			$query  = "SELECT first_name, last_name FROM users WHERE user_id = $id;";
			#print $query;
			try {
				$results = $sqlite_db_connection->query($query);
			} catch (Exception $e) {
				echo 'Caught exception: ' . $e->getMessage();
				exit();
			}

			if ($results) {
				while ($row = $results->fetchArray()) {
					// Get values
					$first = $row["first_name"];
					$last  = $row["last_name"];

					// Feedback for end user
					$html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
				}
			} else {
				echo "Error in fetch ".$sqlite_db->lastErrorMsg();
			}

			break;
		case PGSQL:
			// Custom PostgreSQL Implementation
			$host = $_DVWA[ 'PGSQL_SERVER' ];
			$db   = $_DVWA[ 'PGSQL_DB' ];
			$user = $_DVWA[ 'PGSQL_USER' ];
			$pass = $_DVWA[ 'PGSQL_PASSWORD' ];

			// Connect to PostgreSQL
			$pg_conn = @pg_connect("host=$host dbname=$db user=$user password=$pass");

			if (!$pg_conn) {
				die("<pre>PostgreSQL Connection failed: " . pg_last_error() . "</pre>");
			}

			// Medium level protection: escape strings
			$id = pg_escape_string($pg_conn, $id);

			// Intentionally Vulnerable query: No quotes around $id
			$query  = "SELECT first_name, last_name FROM users WHERE user_id = $id;";
			$result = @pg_query($pg_conn, $query);

			if ($result) {
				// Output results
				while( $row = pg_fetch_assoc( $result ) ) {
					$first = $row["first_name"];
					$last  = $row["last_name"];
					$html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
				}
			} else {
				// Error based injection feedback
				echo "<pre>PostgreSQL Error: " . pg_last_error($pg_conn) . "</pre>";
			}

			pg_close($pg_conn);
			break;
	}
}

// This is used later on in the index.php page
// Setting it here so we can close the database connection in here like in the rest of the source scripts

switch ($_DVWA['SQLI_DB']) {
	case MYSQL:
		$query  = "SELECT COUNT(*) FROM users;";
		$result = mysqli_query($GLOBALS["___mysqli_ston"],  $query ) or die( '<pre>' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '</pre>' );
		$number_of_rows = mysqli_fetch_row( $result )[0];

		mysqli_close($GLOBALS["___mysqli_ston"]);
		break;
	case SQLITE:
		global $sqlite_db_connection;

		$query  = "SELECT COUNT(*) FROM users;";
		$results = $sqlite_db_connection->query($query);
		$row = $results->fetchArray();
		$number_of_rows = $row["COUNT(*)"];

		$sqlite_db_connection->close();
		break;
	case PGSQL:
		// Custom PostgreSQL Implementation
		$host = $_DVWA[ 'PGSQL_SERVER' ];
		$db   = $_DVWA[ 'PGSQL_DB' ];
		$user = $_DVWA[ 'PGSQL_USER' ];
		$pass = $_DVWA[ 'PGSQL_PASSWORD' ];

		// Connect to PostgreSQL
		$pg_conn = @pg_connect("host=$host dbname=$db user=$user password=$pass");

		if (!$pg_conn) {
			die("<pre>PostgreSQL Connection failed: " . pg_last_error() . "</pre>");
		}

		$query  = "SELECT COUNT(*) FROM users;";
		$result = @pg_query($pg_conn, $query);
		$row = pg_fetch_assoc( $result );
		$number_of_rows = $row['count'];

		pg_close($pg_conn);
		break;
}

?>
