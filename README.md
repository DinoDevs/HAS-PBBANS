![Version](https://img.shields.io/badge/Version-1.6.0-green.svg?style=flat)
![Release](https://img.shields.io/badge/Release-29.06.2016-green.svg?style=flat)
![Game](https://img.shields.io/badge/Games-BF4/BF3/BFH-blue.svg?style=flat)

# HAS-PBBANS

## ☰ Before we start:

[Better Battlelog](https://getbblog.com/) is a blrowser addon for the webpage [Battlelog](http://battlelog.battlefield.com) which is a Web-UI for Battlefield games like Battlefield 3, Battlefield 4 and Battlefield Hardline. 
This project is a plugin that it is loaded within that addon (Better Battlelog) improving Battlefield 4 Web-UI.


## ☰ About :

This plug in will help you find a server with "PBBans", "GCC-Stream" or "Anticheat Inc" active, in order to play fair with no hackers to disturb you. 
By adding 3 icons (each for every service) on the server browser and on the server info page, it provide a fast way to know if the server is streaming on any of these 3 services.

Servers caches are deleted every week.
 

## ☰ Preview : 

Images
Server Browser (BF3 / BF4)
![Server Browser preview for bf3 and bf4](../master/images/server_browser_preview.PNG "Server Browser preview for bf3 and bf4")

Server Page (BF3 / BF4)
![Server Page preview for bf3 and bf4](../master/images/server_page_preview.PNG "Server Browser page for bf3 and bf4")

Server Page / Server Browser (BFH)
![Server Browser/Page preview for bfh](../master/images/server_preview_bfh.PNG "Server Browser/Page preview for bfh")


## ☰ Anticheat Services Links : 
 - [PBBans](http://www.pbbans.com)
 - [GCC-Stream](http://www.ggc-stream.net)
 - [Anticheat Inc](http://www.anticheatinc.net)
 

## ☰ Nerd Stuff : 

|Version | Plugin Changelog|
|:------:|:----------------|
|v 1.6.0 |BFH Support added|
|        |GGC server's link fixed (now points to GGC's Server Search page)|
|        |"Unknown" streamer's status tooltips position fixes|
|        |Displaying "on" when ACI & GGC status is unknown fix||
|v 1.5.3 |ACI LiveSecure option info added.|
|v 1.5.2 |Friends playing server info not showing streaming info fix.|
|v 1.5.1 |Displaying "error" fix|
|        |PBBans wrong ON fix|
|v 1.5   |New layout|
|        |Bugs pointed by BrainFooLong fixed|
|        |Data are pulled by server using ajax (faster)|
|v 1.4 Beta |added ACI (Anticheatinc) service status|
|v 1.3   |Code fixes|
|        |BF3 and BF4 support for both PBBans and GCC-Stream|
|v 1.2 Beta |GGC support added (BF4 only)|
|        |Server Cache added for faster GGC results [Server Side]|
|v 1.1   |BF3 Support added|
|v 1.0   |BF4 Support added|


|Date    | Server Side Updates|
|:------:|:----------------|
|20.06.2016|BFH Support added|
|19.06.2016|Server Owner API added|
|27.03.2016|PBBans server url changed|
| |GGC new way of getting servers||
| |ACI server block bypass |
|18.05.2015|Server response time limit set to 3 sec|
|18.02.2015|ACI is now fixed (ACI public server list was closed so we changed to get-each-server-status-and-cache)|
|12.02.2015|Problem getting streamers' pages, we changed to cUrl page-get|
|22.09.2014|Displaying "error" fix|
||PBBans wrong ON fix||
|09.09.2014|(on the beta version)|
| |Now supporting Anticheat Inc. for both BF3 and BF4|
| |GGC missing servers fix|
| |ACI remove "inactive servers" from database fix|
| |Added updating database image|
|08.09.2014|Now supporting both GGC-stream BF3 and BF4|
|07.09.2014|(on the beta version)|
| |GGC server IPs and PORTs are now daily cached = faster and less server resources|


## ☰ More Nerd Stuff : 

For the Server Owners, I implemented a small API (some commands)

Commands : 
- "refresh" : refreshes the cache, recheck the status of the server
- "noProxyPlease" : tell the server not to use a proxy server

You can now refresh the status of your server by visiting the link :
```
http://alites.tk/battlelog-plugins/has-pbbans/chupachups.php?ip=<server-ip>&port=<server-port>&bf=<3_or_4_or_h>&json&refresh

where <server-ip> is your server ip (ex 123.456.789.012),
the <server-port> is your server port (ex 123456)
and <3_or_4_or_h> is your game (3 for BF3, 4 for BF4 and h for BFH).
```

For example example you can refresh your the server "123.456.789.012:123456" by using the link:
```
http://alites.tk/battlelog-plugins/has-pbbans/chupachups.php?ip=123.456.789.012&port=123456&bf=4&json&refresh
```
and if you are still having problems with ACI use the "noProxyPlease" command too:
```
http://alites.tk/battlelog-plugins/has-pbbans/chupachups.php?ip=123.456.789.012&port=123456&bf=4&json&refresh&noProxyPlease
```
Sent me a message if you have any problem but I may not reply right away ... but I will reply :)


## ☰ PlugIn Links :

 - [Full Code with comments](../master/has-pbbans.latest.bblog.js)
 - [Minified Code](../master/has-pbbans.latest.bblog.min.js)
 
(The plugin is also available in the PlugIn Gallery. You are adviced to load it from the Gallery.)
