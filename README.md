# Livestats - Track your visitors in real-time !

## Goal: a lightweight solution to get a real-time snapshot of your visitor activity.

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

## Restrictions
This solution has not been tested on heavy traffic websites. It uses your server resources to work.
If you have a lot of visitors, you may want to consider professional solutions
in the cloud like [GoSquared](http://www.gosquared.com/) and [Chartbeat](http://www.chartbeat.com/).

## License
This project is licensed under the [MIT License](http://www.opensource.org/licenses/mit-license.php).

## Requirements

So what do you need to run this solution ?

* Javascript turned on in the client browser. This is pure JS, no library (like jQuery) required.
* At least PHP 5.1 (requires [PDO](http://www.php.net/manual/intro.pdo.php)) on the server.
* some _really basic_ setup

## Test Setup (by default with SQLite)


As you can't wait to see ```livestats``` in action, go to your __test__ server 
and push the following files, say in the ```/js/livestats``` repository:

```
/js/livestats
    +- backend
    |  +- db
    |  |  +- livestats.sqlite (Make sure it is writable !)
    |  |
    |  +- php
    |     +- config.inc.php
    |     +- DBConnector.php
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
to your server. The default ping interval is set to 30 seconds, but you
can tweak that giving a second parameter to the ```Livestats``` constructor.

```javascript
  // Starting a new spy with a ping interval of 15 seconds.
  var spy = new Livestats('js/livestats/backend/php/livestats.php', 15);
```

## Production Setup (with MySQL or other)

I've seen that with a heartbeat interval of __30__ seconds, SQLite begins to show
its limits when you get more than __100__ connected visitors at the same time.

Using MySQL (or any other DMS) is very simple with PDO:

* Make sure the driver for MySQL is available on your PHP setup (use [phpinfo](http://php.net/manual/function.phpinfo.php))
* Create a new schema on your DB server and create the livestats table with ```livestats/backend/db/livestats.sql```
* Open ```livestats/backend/php/config.inc.php``` and update the ```$livestats_db_config``` setup.

And you're done! Your livestats instance should now use MySQL.

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

You can also setup a cron job to feed a [Ducksboard](http://www.ducksboard.com) dashboard.
You can view this example on [Gist](https://gist.github.com/1430616).

## Information collected

For the first release, Livestats only collect:

* Number of visitors
* State of each visitor: Idle, Reading or Writing

What we may want to add ([Fork](https://github.com/ssaunier/livestats/fork)!) to future releases:

* URL visited
* Referer
* Keywords