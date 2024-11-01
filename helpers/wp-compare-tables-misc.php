<?php

// PARSE GET VALUES
function tabels_return_get ($field) {
	if ( isset($_GET[$field]) && trim($_GET[$field]) != '' )
		return trim($_GET[$field]);
	return false;
}

// PARSE POST VALUES
function tabels_return_post ($field) {
	if ( isset($_POST[$field]) && trim($_POST[$field]) != '' )
		return trim($_POST[$field]);
	return false;
}