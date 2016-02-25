<?php
# Include the class code
require_once('ratelimitRequests.php');
// Initialise / Construct the class
$rate = new ratelimitRequests ();
# Evaluate with 3 requests every 10 seconds and if it exceeds then timeout for one minutes
$ratelimit = $rate->evaluate(3, 10, 60);
# The request passed enumeration
if ($ratelimit) {
  printf('This is a successful request!');
  exit;
}
// This attempted request is on a timeout
else {
  printf('This is an unsuccessful request, please try again in 1 minute!');
  exit;
}