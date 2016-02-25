PHP ratelimitRequest
====================

A session based helper to enforce a cooldown period on repeated request attempts.

Example Usage:

    <?php
    # Include the class code
    require_once('ratelimitRequests.php');
    // Initialise / Construct the class
    $rate = new ratelimitRequests ();
    # Evaluate with 50 requests every one minute and if it exceeds timeout for 5 minutes
    $eval = $rate->$evaluate(50, 60, 300);
    # The request passed enumeration
    if ($rate) {
      printf('Please try again in 5 minutes.');
      exit;
    }
    // This attempted request is on a timeout
    else {
      printf('Please try again in 5 minutes.');
      exit;
    }