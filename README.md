# Livestats - Track your visitors in real-time !

## Goal: a lightweight solution to get a real-time snapshot of how many visitors are connected on your website.

The idea is to get a dashboard like this.

```
+--------+--------+--------+--------+
|   42   |   36   |    2   |   4    |
|  Total |  Read  |  Write |  Idle  |
+--------+--------|--------+--------|
```

## Motivation: build and share basic features of realtime tracking services.

I started this project after using 
[GoSquared](http://www.gosquared.com/) and [Chartbeat](http://www.chartbeat.com/).
These are really cool web applications which let you dive in the details of
who's doing what _right now_.

As one can understand, those great services are not free. And if you just
want a basic dashboard like the one described above, you may want to find
a free (and open-source maybe ? :)) solution !. Here Livestats come!

## License
This project is licensed under the [MIT](http://www.opensource.org/licenses/mit-license.php) 
or [GPL Version 2](http://www.opensource.org/licenses/gpl-2.0.php).

## Requirements

So what do you need to run this solution on this server ?

* Javascript turned on in the client browser
* At least PHP 5.1 on the server (needs [PDO](http://www.php.net/manual/ref.pdo-sqlite.php) with SQLite support).
* some _really basic_ setup

## Setup

Push to your server the following files, say in the ```/js/livestats``` repository:

```
/js/livestats
    +- backend
    |  +- db
    |  |  +- livestats.sqlite (Make sure it is writable !)
    |  |
    |  +- php
    |     +- livestats.php
    |     +- State.php
    |
    +- livestats.js
```

Then open your main page ```index.php``` and add at the very bottom (just before ```</body>```):

```html
<script type="text/javascript" src="js/livestats/livestats.js"></script>
<script type="text/javascript">
  var spy = new Livestats('js/livestats/backend/php/livestats.php');
  spy.start();
</script>
```

It will setup a ```Livestats``` javascript object and start hearbeating
to your server. The default ping interval is set to 15 seconds, but you
can tweak that giving a second parameter to the ```Livestats``` constructor.

```javascript
  // Starting a new spy with a ping interval of 30 seconds.
  var spy = new Livestats('js/livestats/backend/php/livestats.php', 30);
```

## Usage example

You can use the library ```backend/php/State.php``` to use the information
collected. 

```php
<?php
require_once('/your/path/to/backend/php/State.php');
$state = State::countStates();
// Do something with $state['total'], $state['reading'], $state['writing'] or $state['idle'];
?>
```

I setup a script as a cron job for [Sogedis](http://pieces-detachees.sogedis.fr), 
an French household appliance spare parts e-commerce, to feed a 
[Ducksboard](http://www.ducksboard.com) dashboard.

You can view this example on [Gist](https://gist.github.com/1430616).

## Information collected

For the first release, Livestats only collect:

* Number of visitors
* State of each visitor: Idle, Reading or Writing

What we may want to add ([Fork](https://github.com/ssaunier/livestats/fork)!) to future releases:

* URL visited
* Referer
* Keywords