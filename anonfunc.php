<?php
// Anonymous functions example.

// createFunction returns a brand new anonymous function
function createFunction() {
	return function($arg1) {
		echo("Yay! $arg1\n");
	};
}

// Get anonymous function and store it within $myFunction's variable
$myFunction = createFunction();
// Call function
$myFunction("coucou");

