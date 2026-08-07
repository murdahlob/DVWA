<?php

if( isset( $_SESSION [ 'id' ] ) ) {
	// Get input
	$id = $_SESSION[ 'id' ];

	switch ($_DVWA['SQLI_DB']) {
		case MYSQL:
			// Check database
			$query  = "SELECT first_name, last_name FROM users WHERE user_id = '$id' LIMIT 1;";
			$result = mysqli_query($GLOBALS["___mysqli_ston"], $query ) or die( '<pre>Something went wrong.</pre>' );

			// Get results
			while( $row = mysqli_fetch_assoc( $result ) ) {
				// Get values
				$first = $row["first_name"];
				$last  = $row["last_name"];

				// Feedback for end user
				$html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
			}

			((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);		
			break;
		case SQLITE:
			global $sqlite_db_connection;

			$query  = "SELECT first_name, last_name FROM users WHERE user_id = '$id' LIMIT 1;";
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
			$host = $_DVWA[ 'PGSQL_SERVER' ];
			$db   = $_DVWA[ 'PGSQL_DB' ];
			$user = $_DVWA[ 'PGSQL_USER' ];
			$pass = $_DVWA[ 'PGSQL_PASSWORD' ];
			
			// Connect to PostgreSQL 
			$pg_conn = @pg_connect("host=$host dbname=$db user=$user password=$pass");
			
			if (!$pg_conn) {
				die("<pre>Something went wrong.</pre>");
			}

			// Intentionally Vulnerable query: Uses quotes and LIMIT 1
			$query  = "SELECT first_name, last_name FROM users WHERE user_id = $id LIMIT 1;";
			var_dump ($query);
			$result = @pg_query($pg_conn, $query);

			if ($result) {
				// Output results
				while( $row = pg_fetch_assoc( $result ) ) {
					$first = $row["first_name"];
					$last  = $row["last_name"];
					$html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
				}
			} else {
				// Generic error message to prevent error-based SQLi
				echo "<pre>Something went wrong.</pre>";
			}
			
			pg_close($pg_conn);
			break;
	}
}

?>
