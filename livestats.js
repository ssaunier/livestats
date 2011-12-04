/*
 * Livestats Javascript library
 * https://github.com/ssaunier/livestats
 *
 * Copyright 2011, Sébastien Saunier
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * Date: 12/01/2011
 */

var Livestats = {
    State: {
        IDLE: 0,
        READING: 1,
        WRITING: 2
    },

    _pingInterval: 15,  // Default value in seconds
    _pingUrl: 'backend/php/livestats.php',
    
    _timer: null,
    _state: 1,  // Default as reading
    _scrollPosition: 0,
    _sessionId: null,
    
    _events: {
        _mouseActivityDetected: false,
        _keyPressed: false,
        _mouseScrolled: false,
    },
    
    /**
     * Ping Interval in seconds.
     */   
    init: function(pingUrl, pingInterval) {
        if (pingUrl) {
            this._pingUrl = pingUrl;
        }
        if (pingInterval) {
            this._pingInterval = pingInterval;
        }
        if (document.captureEvents) {
            document.captureEvents(
                Event.MOUSEMOVE | Event.KEYPRESS | Event.CLICK);
        }
       
        this._reportState();
        return this;
    },
    
    stop: function() {
        clearTimeout(this._timer);
    },
    
    _computeState: function() {
        this._computeScrolling();
        if(this._events._keyPressed)
            this._state = this.State.WRITING;
        else if (this._events._mouseActivityDetected || this._events._mouseScrolled) 
            this._state = this.State.READING;            
        else
            this._state = this.State.IDLE;

        this._reportState();
    },
    
    _reportState: function() {
        this._pingServerWithNewState();
        this._rebindHandlers();
        this._setTimer();
    },
    
    _pingServerWithNewState: function() {
        xhr = this._createXMLHttpRequest();
        if (xhr) {
            var params = "state=" + this._state + "&sessionId=" + this._sessionId;
            xhr.open("POST", this._pingUrl, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send(params);
            var self = this;
            xhr.onreadystatechange = function() {
              if (xhr.readyState == 4)
                  self._sessionId  = xhr.responseText;
            }
        }
    },
    
    _rebindHandlers: function() {
        this._events._mouseActivityDetected = false;
        this._events._keyPressed = false;
        var self = this;
        document.onmousemove = function() {
            self._events._mouseActivityDetected = true;
        }
        document.onkeypress = function() {
            self._events._keyPressed = true;
        }
    },
    
    _computeScrolling: function() {
        var newScrollPosition = 
            (document.body && typeof document.body.scrollTop !== "undefined") ?
                document.body.scrollTop : 
                ((typeof document.documentElement !== "undefined") 
                ? document.documentElement.scrollTop : 0);
        this._events._mouseScrolled = newScrollPosition != this._scrollPosition;
        this._scrollPosition = newScrollPosition;
    },
    
    _createXMLHttpRequest: function() {
         try { return new XMLHttpRequest(); } catch(e) {}
         try { return new ActiveXObject("Msxml2.XMLHTTP"); } catch (e) {}
         if (window.console && window.console.log) {
             console.log('Ajax not supported by your browser. Disabling the timer...');
             this.stop();
         }
     },
    
     _setTimer: function() {
         var self = this;
         this._timer = setTimeout(function() {
             self._computeState();
         }, this._pingInterval * 1000);
     }
};