<?php
/**
 * @ratelimitRequest A session based helper to enforce a cooldown period on repeated
 *                   request attempts.
 * @author Craig van Tonder
 * @version 0.0.1
 */

class ratelimitRequest
{
  private $ratelimit;
  private $ratelimitPeriod;
  private $ratelimitPeriod;

  /**
    * Create router in one call from config.
    *
    * @param $ratelimit int Limit is x requests - 120
    * @param $ratelimitPeriod int Every x seconds - 60
    * @param $cooldownPeriod int Cooldown lasts x seconds - 360
    */
  public function __construct($ratelimit, $ratelimitPeriod, $cooldownPeriod)
  {
    # Initalise sessions if they have not already been started
    $this->initialiseSessions ();
    # Define the index of the request session
    $this->sessionName = 'RATELIMITE_REQUEST_EXAMPLE_COM';
    # Rate limit and cooldown configuration
    $this->ratelimit = $ratelimit; // Request limit at x requests
    $this->ratelimitPeriod = $ratelimitPeriod; // Request limit reset after x seconds
    $this->cooldownPeriod = $cooldownPeriod; // Cooldown period lasts is x seconds
  }

  /**
    * Calculate if this request should be processed based on the limit configuration
    */
  public function ratelimitRequest () {

    # If no request session has been started
    if (!$this->issetSession('time')) {
      // Refresh the session
      $this->refresh();
    }

    # If a lock on the request session has been set
    elseif ($this->getSession('lock')) {
      // If the locks timeout has not expired
      if (time() - $this->getSession('lockTime') < $this->cooldownPeriod) {
        // Lock requests
        $this->lock();
      }
      // If the locks timeout has expired
      else {
        // Refresh the session
        $this->refresh();
      }
    }

    # The request session has started more than 1 minute ago
    elseif ($this->issetSession('time') && (time() - $this->getSession('time') > $this->ratelimitPeriod)) {
      // Refresh the session
      $this->refresh();
    }

    # The request session has started but is less than 1 minute ago
    else {
      // Reset creation time
      setSession($this->sessionName, 'time', time());
      // Get the enumeration sessions current count
      $count = $this->getSession('count');
      // Rate limit the enumeration to 255 per minute
      if ($count == $this->ratelimit) {
        // Lock requests
        $this->lock();
      }
      // No rate limit needs to be enforced so update the count
      else {
        // Increment the inital count
        $count++;
        // Set the new count
        $this->setSession('count', $count);
        // Set the lock status
        $this->setSession('status', TRUE);
      }
    }

    # Return the request sessions lock status
    return $this->getSession('status');
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
    * Helper to lock/halt/cooldown requests via the request session
    *
    * @param $this->sessionName string Limit is x requests - 120
    */
  private function lock () {
    // Set the lock state
    $this->setSession('lock', TRUE);
    // Set the lock time
    $this->setSession('lockTime', time());
    // Set the lock status
    $this->setSession('status', FALSE);
  }

  /**
    * Helper function to refresh the request session
    */
  private function refresh () {
    // Set the creation time
    $this->setSession('time', time());
    // Set the inital count
    $this->setSession('count', 1);
    // Set the lock state
    $this->setSession('lock', FALSE);
    // Set the lock status
    $this->setSession('status', TRUE);
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
      if (isset($_SESSION[$this->sessionName . '_' . sessName][$sessionKey])) {
        return TRUE;
      } else {
        return FALSE;
      }
    } else {
      // If the array key is set
      if (isset($_SESSION[$this->sessionName . '_' . sessName])) {
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
      $_SESSION[$this->sessionName . '_' . sessName][$sessionKey] = $sessionValue;
    } else {
      $_SESSION[$this->sessionName . '_' . sessName] = $sessionValue;
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
      return $_SESSION[$sessionName . '_' . sessName][$sessionKey];
    } else {
      return $_SESSION[$sessionName . '_' . sessName];
    }
  }
}