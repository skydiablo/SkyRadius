# SkyRadius

The first (known to me) RADIUS server which was implemented natively in PHP! Based on the incredible
possibilities of ReachtPHP I was able to write this library. Currently RFC2865 + RFC2868 are implemented,
should follow RFC2866 + RFC2867 (I'm always happy about PRs ;) ).

## Example

Enter `./Example/` directory:

```
cd ./Example/
```

Load vendor Libs:

```
composer install
```

Install `radclient` from `freeRADIUS`-Project:

```
sudo apt install freeradius-utils
```

Run `SkyDiablo/SkyRadius` Example-Server:

```
php radius.php
```

Run in an separated console session the `radclient`

```
echo "User-Name=test,User-Password=mypass,Framed-Protocol=PPP" | radclient -x 127.0.0.1:3500 auth test

Sent Access-Request Id 31 from 0.0.0.0:52235 to 127.0.0.1:3500 length 50
        User-Name = "test"
        User-Password = "mypass"
        Framed-Protocol = PPP
        Cleartext-Password = "mypass"
Received Access-Accept Id 31 from 127.0.0.1:3500 to 0.0.0.0:0 length 110
        Reply-Message = "Echo Test-Radius-Server"
        User-Name = "test"
        User-Password = "ѣ\3332a\274\016({\312A\257P\3623\214\273-\342\331Z\035\024:\267\254i#h'\200\262\021f˷c\305y2*\201qlNh\234\236u\377\207"
        Framed-Protocol = PPP 
```

* As you can see, the echo is missing the attribute `Cleartext-Password` -> it isn't implemented by default, yet ;)
* Also ignore the cryptic looking `User-Password` attribute, so server-side encrypting for this attribute is also missing. But given by RFC, the server should never doing this! 

## Benchmark

Extended to the given example above, you can stress-test your `SkyDiablo/SkyRadius` instance by improve the requests:

```
echo "User-Name=test,User-Password=mypass,Framed-Protocol=PPP" | radclient -n 1000 -c 99999999999 127.0.0.1:3500 auth test
```

So far I could not do any real tests, but first tests have shown that it is possible to more than 500 requests/sec.
I couldn't create more requests because my CPU was down. The SkyRadius-Server ran with 15% CPU and 10MB RAM. Here I 
would be pleased about experience values of users.

## TODOs

- Attribute Dictionary Loader
  - YAML
  - JSON
- UnitTest