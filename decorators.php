<?php

// Force command line argument
if(!isset($argv[1])) {
	echo("Error: Argument expected: user-name\nExit.\n");
	exit(1);
}

// Get username from command line interface
$USER = $argv[1];
// This array is used as "database" to match users with rights levels
$rights = array(
	['michel', 'henri'],   // rights level 0: users
	['patrick', 'didier'], // rights level 1: admin
	['arount']             // rights level 2: root
);


/*
	Gives right level (integer from 0 to 2) associated to
	$current_user.
*/
function _userRights($current_user)
{
	foreach($GLOBALS['rights'] as $right_level => $users) {
		if(in_array($current_user, $users) === true) {
			return $right_level;
		}
	}
	return false;
}


/*
	Parse right level's name ('user', 'admin' or 'root') into
	integer.
*/
function _rightToInt($right)
{
	if($right == 'user') {
		return 0;
	}
	elseif($right == 'admin') {
		return 1;
	}
	else {
		return 2;
	}
}


/*
	Authentification decorator.
	Restrict call to $funcname to users with rights > $expected_right
	$expected_right is passed as string.
*/
function authDecorator($funcname, $expected_right)
{
	// Get current user and get his rights level
	$user = $GLOBALS['USER'];
	$user_right = _userRights($user);
	// Parse function's right level into associated integer
	$int_right = _rightToInt($expected_right);

	// Set globals to bypass PHP's scope limitations
	// That's really not a good practice.
	$GLOBALS['__funcname'] = $funcname;
	$GLOBALS['__expectedright'] = $expected_right;

	// Check current user's right VS function's one.
	if($int_right <= $user_right) {
		// If access granted, build a function that only calls $funcname
		$funcname = function() {
			call_user_func($GLOBALS['__funcname']);
		};
	}
	else {
		// If access refused build a function calling 'showError'
		$funcname = function() {
			call_user_func('showError');
		};
	}

	// Returns new anonym function
	return $funcname;
}



//
// "Front-end" functions.
//

/* Display error. */
function showError()
{
	$required_right = $GLOBALS['__expectedright'];
	unset($GLOBALS['__expectedright']);
	echo("$required_right's right level required.\n");
}

/* Says Coucou! */
function sayHello()
{
	echo("Coucou\n");
}

/* List accessible pages */
function showPages()
{
	echo("Pages: page_1.php, page_2.php\n");
}

/* Show administrator pages */
function showRoot()
{
	echo("Root pages: admin.php\n");
}



//
// Now, let's play a little bit.
// Front-end functions are decorated with authDecorator and then called.
// Because functions are now decorated they will require a defined right level to be
// called.
// If level is too low the user will be redirected to an error message.
//

$hellofunc = authDecorator('sayHello', 'user');
echo('Hello: ');
$hellofunc();

$helloadminfunc = authDecorator('sayHello', 'root');
echo('HelloAdmin: ');
$helloadminfunc();

$pagesfunc = authDecorator('showPages', 'admin');
echo('Pages: ');
$pagesfunc();

$adminfunc = authDecorator('showRoot', 'root');
echo('Root: ');
$adminfunc();
