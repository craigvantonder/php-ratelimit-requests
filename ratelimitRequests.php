<?php
/**
 * @ratelimitRequests A session based helper to enforce a cooldown period on repeated
 *                   request attempts.
 * @author Craig van Tonder
 * @version 0.0.4
 */

class ratelimitRequests
{
  # The key/name of the storage session
  private $sessionName;
  # Request limit at x requests
  private $ratelimit;
  # Request limit reset after x seconds
  private $ratelimitPeriod;
  # Timeout period lasts is x seconds
  private $timeoutDuration;

  /**
    * On class construction
    */
  public function __construct()
  {
    # Define the index of the request session
    $this->sessionName = 'RATELIMIT_REQUESTS_EXAMPLE_COM';
    # Initalise sessions if they have not already been started
    $this->initialiseSessions ();
  }

  /**
    * Calculate if this request should be processed based on the limit configuration
    * @param $ratelimit int Limit is x requests - 120
    * @param $ratelimitPeriod int Every x seconds - 60
    * @param $timeoutDuration int Timeout lasts x seconds - 360
    */
  public function evaluate ($ratelimit, $ratelimitPeriod, $timeoutDuration) {

    # Rate limit and timeout configuration
    $this->ratelimit = $ratelimit;
    $this->ratelimitPeriod = $ratelimitPeriod;
    $this->timeoutDuration = $timeoutDuration;

    # If no request session has been started
    if (!$this->issetSession('startTime')) {
      // Refresh the session
      $this->refresh();
    }

    # If a timeout on the request session has been set
    elseif ($this->getSession('timeout')) {
      // If the timeout has not expired yet
      if (time() - $this->getSession('timeoutDuration') < $this->timeoutDuration) {
        // Timeout requests
        $this->timeout();
      }
      // If the timeout has expired
      else {
        // Refresh the session
        $this->refresh();
      }
    }

    # The request session has started more than $ratelimitPeriod seconds ago
    elseif ($this->issetSession('startTime') && (time() - $this->getSession('startTime') > $this->ratelimitPeriod)) {
      // Refresh the session
      $this->refresh();
    }

    # The request session has started but is less than $ratelimit seconds ago
    else {
      // Reset the request sessions creation time
      $this->setSession($this->sessionName, 'startTime', time());
      // Get the request sessions current count
      $count = $this->getSession('reqCount');
      // Rate limit the requests to $ratelimit per $ratelimitPeriod seconds
      if ($count == $this->ratelimit) {
        // Timeout requests
        $this->timeout();
      }
      // No rate limit needs to be enforced so update the count
      else {
        // Increment the inital count of requests
        $count++;
        // Set the new count of requests
        $this->setSession('reqCount', $count);
        // Set the timeout status
        $this->setSession('timeoutStatus', FALSE);
      }
    }

    # Return the request sessions timeout status
    return $this->getSession('timeoutStatus');
  }

  /**
   * Initialise sessions if they have not already been started
   */
  private function initialiseSessions () {
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }
  }

  /**
    * Helper to timeout/halt/cooldown requests via the request session
    */
  private function timeout () {
    // Set the timeout state
    $this->setSession('timeout', TRUE);
    // Set the timeout time
    $this->setSession('timeoutDuration', time());
    // Set the timeout status
    $this->setSession('timeoutStatus', TRUE);
  }

  /**
    * Helper to refresh the request session values
    */
  private function refresh () {
    // Set the creation time
    $this->setSession('startTime', time());
    // Set the inital count
    $this->setSession('reqCount', 1);
    // Set the timeout state
    $this->setSession('timeout', FALSE);
    // Set the timeout status
    $this->setSession('timeoutStatus', FALSE);
  }

  /**
    * Checks if the given session key is set
    *
    * @param $sessionKey string Array key that we are checking the existance of
    */
  private function issetSession($sessionKey)
  {
    // If we have an array
    if (strlen($sessionKey) > 0) {
      // If the array key is set
      if (isset($_SESSION[$this->sessionName][$sessionKey])) {
        return TRUE;
      } else {
        return FALSE;
      }
    } else {
      // If the array key is set
      if (isset($_SESSION[$this->sessionName])) {
        return TRUE;
      } else {
        return FALSE;
      }
    }
    // Else otherwise?
    return FALSE;
  }

  /**
    * Sets a given session keys value
    *
    * @param $sessionKey Array key of the session value to be stored
    * @param $sessionValue string Value to be stored in this array keys value
    */
  private function setSession($sessionKey, $sessionValue)
  {
    // If we have an array
    if (strlen($sessionKey) > 0) {
      $_SESSION[$this->sessionName][$sessionKey] = $sessionValue;
    } else {
      $_SESSION[$this->sessionName] = $sessionValue;
    }
  }

  /**
    * Gets a given session keys value
    *
    * @param $sessionKey string Array key of the session value to be fetched
    */
  private function getSession($sessionKey)
  {
    // If we have an array
    if (strlen($sessionKey) > 0) {
      return $_SESSION[$this->sessionName][$sessionKey];
    } else {
      return $_SESSION[$this->sessionName];
    }
  }
}
