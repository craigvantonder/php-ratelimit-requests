PHP ratelimitRequests
=====================

A session based helper to enforce a cooldown period on repeated request attempts.

Example Usage:

    <?php
    # Include the class code
    require_once('ratelimitRequests.php');
    # Initialise / Construct the class
    $rate = new ratelimitRequests ();
    # Evaluate with 3 requests every 10 seconds and if it exceeds then timeout for one minutes
    $ratelimited = $rate->evaluate(3, 10, 60);
    # This attempted request is on a timeout
    if ($ratelimited) {
      printf('This is an unsuccessful request, please try again in 1 minute!');
      exit;
    }
    # The request passed enumeration
    else {
      printf('This is a successful request!');
      exit;
    }
    
Licence: MIT
