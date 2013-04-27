/*
 * Livestats Javascript library
 * https://github.com/ssaunier/livestats
 *
 * Copyright 2013, Sébastien Saunier (@ssaunier)
 * MIT License
 *
 * Date: April 28th, 2013
 *
 * Compatible with IE 5.5+.
 */
function Livestats(pingUrl, pingInterval) 
{
  var that = this;  // Convention workaround of ECMAScript.
  
  /**
   * Start to track the current visitor by sending a heartbeat.
   * every _pingInterval to _pingUrl.
   */ 
   this.start = function() {    
      // Report we're reading on startup, non blocking call.
      setTimeout(function() { 
        _reportState();
      }, 1);
      
      // Set interval for future notification of state.
      that._timer = setInterval(function() {
        var state = _computeState();

        if (_state === State.IDLE && ((_getTimestamp() - _idleStart) > _idleTimeout)) {
          _sleeping = true;
        }
        else {
          _sleeping = false;
          _reportState();
        }
      }, _pingInterval * 1000);
    };

  /**
   * Stop to track current visitor activity.
   */ 
  this.stop = function() {
    clearInterval(that._timer);
  };
  
  // "Enum" of available states (this is kept in sync with backend).
  var State =
  {
    IDLE: 0,
    READING: 1,
    WRITING: 2
  };

  // Ping parameters
  var _pingInterval = pingInterval || 15;  // Default ping as 15 seconds.
  var _pingUrl = pingUrl;
  
  var _timer = null;
  var _state = State.READING;  // When loading the page, the user is reading.
  var _scrollPosition = 0;

  // Idling and Timeout
  var _idleTimeout = 5 * 60;  // After this number of seconds, stop tracking.
  var _sleeping = false;
  var _idleStart = 0;  // Timestamp when IDLE has started (used for idleTimeout and tracking pause)
  var _lastState = State.READING;
  
  // Events we want to capture to track visitor activity.
  var _events =
  {
    _mouseActivityDetected: false,
    _keyPressed: false,
    _mouseScrolled: false
  };
  
  if (document.captureEvents) {
    document.captureEvents(Event.MOUSEMOVE | Event.KEYPRESS | Event.CLICK);
  }
  
  /* Private methods below */

  function _getTimestamp() {
    return Math.round((new Date()).getTime() / 1000);
  }
  
  function _computeState() {
    _computeScrolling();
    if(_events._keyPressed)
      _state = State.WRITING;
    else if (_events._mouseActivityDetected || _events._mouseScrolled) 
      _state = State.READING;            
    else
      _state = State.IDLE;

    if (_lastState !== State.IDLE) {
      _idleStart = _getTimestamp();
    }
    _lastState = _state;
  }
  
  function _reportState() {
    _pingServerWithNewState();
    _rebindHandlers();
  }
  
  function _pingServerWithNewState() {
    xhr = _createXMLHttpRequest();
    if (xhr) {
      var params =
      "state=" + _state +
      "&url=" + encodeURIComponent(window.location.href) +
      "&title=" + encodeURIComponent(document.title);

      xhr.open("POST", _pingUrl, true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.send(params);
    }
  }
  
  function _rebindHandlers() {
    _events._mouseActivityDetected = false;
    _events._keyPressed = false;
    document.onmousemove = function() {
      _events._mouseActivityDetected = true;
    };
    document.onkeypress = function() {
      _events._keyPressed = true;
    };
  }
  
  function _computeScrolling() {
    var newScrollPosition = 
    (document.body && typeof document.body.scrollTop !== "undefined") ?
    document.body.scrollTop : 
    ((typeof document.documentElement !== "undefined") ?
      document.documentElement.scrollTop : 0);
    _events._mouseScrolled = newScrollPosition != _scrollPosition;
    _scrollPosition = newScrollPosition;
  }
  
  function _createXMLHttpRequest() {
    try { return new XMLHttpRequest(); } catch(e) {}
    try { return new ActiveXObject("Msxml2.XMLHTTP"); } catch (e) {}
    if (window.console && window.console.log) {
     console.log('Ajax not supported by your browser. Disabling the timer...');
    }
    that.stop();
    return null;
  }
}