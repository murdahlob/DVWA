<?php

header ("X-XSS-Protection: 0");

// Is there any input?
if( array_key_exists( "name", $_GET ) && $_GET[ 'name' ] != NULL ) {
	// Feedback for end user
	// NOTE: user-supplied $_GET[name] is echoed into HTML without encoding (reflected-XSS sink).
	$html .= '<pre>Hello ' . $_GET[ 'name' ] . '</pre>';
}

?>
