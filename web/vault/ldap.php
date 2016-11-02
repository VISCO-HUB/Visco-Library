<?php

/*
// mydap version 4
// https://samjlevy.com/mydap-v4/
 
function mydap_start($username,$password,$host,$port=389) {
	global $mydap;
	if(isset($mydap)) die('Error, LDAP connection already established');
 
	// Connect to AD
	$mydap = ldap_connect($host,$port) or die('Error connecting to LDAP');
	
	ldap_set_option($mydap,LDAP_OPT_PROTOCOL_VERSION,3);
	@ldap_bind($mydap,$username,$password) or die('Error binding to LDAP: '.ldap_error($mydap));
 
	return true;
}
 
function mydap_end() {
	global $mydap;
	if(!isset($mydap)) die('Error, no LDAP connection established');
 
	// Close existing LDAP connection
	ldap_unbind($mydap);
}
 
function mydap_attributes($user_dn,$keep=false) {
	global $mydap;
	if(!isset($mydap)) die('Error, no LDAP connection established');
	if(empty($user_dn)) die('Error, no LDAP user specified');
 
	// Disable pagination setting, not needed for individual attribute queries
	ldap_control_paged_result($mydap,1);
 
	// Query user attributes
	$results = (($keep) ? ldap_search($mydap,$user_dn,'cn=*',$keep) : ldap_search($mydap,$user_dn,'cn=*'))
	or die('Error searching LDAP: '.ldap_error($mydap));
 
	$attributes = ldap_get_entries($mydap,$results);
 
	// Return attributes list
	if(isset($attributes[0])) return $attributes[0];
	else return array();
}
 
function mydap_members($object_dn,$object_class='g') {
	global $mydap;
	if(!isset($mydap)) die('Error, no LDAP connection established');
	if(empty($object_dn)) die('Error, no LDAP object specified');
 
	// Pagination to overcome 1000 LDAP SizeLimit
	$output = array();
	$pagesize = 1000;
	$counter = "";
	do {
		// Enable pagination
		ldap_control_paged_result($mydap,$pagesize,true,$counter);
 
		// Determine class of object we are dealing with
		if($object_class == 'g') {
			// Query Group members
			$results = ldap_search($mydap,$object_dn,'cn=*',array('member')) or die('Error searching LDAP: '.ldap_error($mydap));
			$members = ldap_get_entries($mydap,$results);
 
			// No group members found
			if(!isset($members[0]['member'])) return false;
 
			// Remove 'count' element from array
			array_shift($members[0]['member']);
 
			// Append to output
			$output = array_merge($output,$members[0]['member']);
		} elseif($object_class == 'c' || $object_class == "o") {
			// Query Container or Organizational Unit members
			$results = ldap_search($mydap,$object_dn,'objectClass=user',array('sn')) or die('Error searching LDAP: '.ldap_error($mydap));
			$members = ldap_get_entries($mydap, $results);
 
			// Remove 'count' element from array
			array_shift($members);
 
			// Pull the 'dn' from each result, append to output
			foreach($members as $e) $output[] = $e['dn'];
		} else die("Invalid mydap_member object_class, must be c, g, or o");
 
		// Retrieve pagination information/position
		ldap_control_paged_result_response($mydap,$results,$counter);
	} while($counter !== null && $counter != "");
 
	// Return alphabetized member list
	sort($output);
	return $output;
}
 
// ==================================================================================
// Example Usage
// ==================================================================================
 
// Establish connection
mydap_start(
	'v.lukyanenko@visco.no', // Active Directory search user
	'', // Active Directory search user password
	'192.168.1.18', // Active Directory server
	389 // Port (optional)
);
 
// Query users using mydap_members(object_dn,object_class)
// The object_dn parameter should be the distinguishedName of the object
// The object_class parameter should be 'c' for Container, 'g' for Group, or 'o' for Organizational Unit
// If left blank object_class will assume Group
// Ex: the default 'Users' object in AD is a Container
// The function returns an array of member distinguishedName's
$members = mydap_members('CN=Users,DC=ad,DC=local','c');
if(!$members) die('No members found, make sure you are specifying the correct object_class');
 
// Now collect attributes for each member pulled
// Specify user attributes we want to collect, to be used as the keep parameter of mydap_attributes
$keep = array('samaccountname','mail');
 
// Iterate each member to get attributes
$i = 1; // For counting our output
foreach($members as $m) {
	// Query a user's attributes using mydap_attributes(member_dn,keep)
	// The member_dn is the step $m of this foreach
	$attr = mydap_attributes($m,$keep);
 
	// Each attribute is returned as an array, the first key is [count], [0]+ will contain the actual value(s)
	// You will want to make sure the key exists to account for situations in which the attribute is not returned (has no value)
	$samaccountname = isset($attr['samaccountname'][0]) ? $attr['samaccountname'][0] : "[no account name]";
	$mail = isset($attr['mail'][0]) ? $attr['mail'][0] : "[no email]";
 
	// Do what you will, such as store or display member information
	echo "$i. $samaccountname, $mail<br>";
 
	$i++;
}
 
// Here you could run another mydap_members() if needed, merge with previous results, etc.
 
// Close connection
mydap_end();
 
// Here you can open a new connection with mydap_connect() if needed, such as to a different AD server
*/
?>


<?php
/*
function get_members($group=FALSE,$inclusive=FALSE) {
    // Active Directory server
    $ldap_host = "192.168.1.18";
 
    // Active Directory DN
    $ldap_dn = "CN=Users,DC=ad,DC=domain";
 
    // Domain, for purposes of constructing $user
    $ldap_usr_dom = "@".$ldap_host;
 
    // Active Directory user
    $user = "v.lukyanenko";
    $password = "fisart";
 
    // User attributes we want to keep
    // List of User Object properties:
    // http://www.dotnetactivedirectory.com/Understanding_LDAP_Active_Directory_User_Object_Properties.html
    $keep = array(
        "samaccountname",
        "distinguishedname"
    );
 
    // Connect to AD
    $ldap = ldap_connect($ldap_host) or die("Could not connect to LDAP");
    ldap_bind($ldap,$user.$ldap_usr_dom,$password) or die("Could not bind to LDAP");
 
 	// Begin building query
 	if($group) $query = "(&"; else $query = "";
 
 	$query .= "(&(objectClass=user)(objectCategory=person))";
 
    // Filter by memberOf, if group is set
    if(is_array($group)) {
    	// Looking for a members amongst multiple groups
    		if($inclusive) {
    			// Inclusive - get users that are in any of the groups
    			// Add OR operator
    			$query .= "(|";
    		} else {
				// Exclusive - only get users that are in all of the groups
				// Add AND operator
				$query .= "(&";
    		}
 
    		// Append each group
    		foreach($group as $g) $query .= "(memberOf=CN=$g,$ldap_dn)";
 
    		$query .= ")";
    } elseif($group) {
    	// Just looking for membership of one group
    	$query .= "(memberOf=CN=$group,$ldap_dn)";
    }
 
    // Close query
    if($group) $query .= ")"; else $query .= "";
 
	// Uncomment to output queries onto page for debugging
	// print_r($query);
 
    // Search AD
    $results = ldap_search($ldap,$ldap_dn,$query);
    $entries = ldap_get_entries($ldap, $results);
 
    // Remove first entry (it's always blank)
    array_shift($entries);
 
    $output = array(); // Declare the output array
 
    $i = 0; // Counter
    // Build output array
    foreach($entries as $u) {
        foreach($keep as $x) {
        	// Check for attribute
    		if(isset($u[$x][0])) $attrval = $u[$x][0]; else $attrval = NULL;
 
        	// Append attribute to output array
        	$output[$i][$x] = $attrval;
        }
        $i++;
    }
 
    return $output;
}
 
// Example Output
 
print_r(get_members()); // Gets all users in 'Users'
 
print_r(get_members("Test Group")); // Gets all members of 'Test Group'
 
print_r(get_members(
			array("Test Group","Test Group 2")
		)); // EXCLUSIVE: Gets only members that belong to BOTH 'Test Group' AND 'Test Group 2'
 
print_r(get_members(
			array("Test Group","Test Group 2"),TRUE
		)); // INCLUSIVE: Gets members that belong to EITHER 'Test Group' OR 'Test Group 2'
		
		*/
?>

<?php

	ini_set('display_errors', 1);
	$ldap = ldap_connect("192.168.0.10");
	$username="v.lukyanenko@visco.local";
	$password="fisart";
		
	
	if($bind = @ldap_bind($ldap, $username,$password))
	{
		echo "logged in";	
	}
	else
	{
		echo "fail";
	}
	
		
		
	
		
	

?>