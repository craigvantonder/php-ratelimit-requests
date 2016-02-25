PHP ratelimitRequests
=====================

A session based helper to enforce a cooldown period on repeated request attempts.

Example Usage:

    <?php
    # Include the class code
    require_once('ratelimitRequests.php');
    // Initialise / Construct the class
    $rate = new ratelimitRequests ();
    # Evaluate with 3 requests every 10 seconds and if it exceeds timeout for one minutes
    $eval = $rate->$evaluate(3, 10, 60);
    # The request passed enumeration
    if ($rate) {
      printf('This is a successful request!');
      exit;
    }
    // This attempted request is on a timeout
    else {
      printf('This is an unsuccessful request, please try again in 1 minute!');
      exit;
    }