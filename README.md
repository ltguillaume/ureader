<a href="https://buymeacoff.ee/ltGuillaume"><img title="Donate using Buy Me a Coffee" src="https://raw.githubusercontent.com/ltGuillaume/Resources/master/buybeer.svg"></a> <a href="https://liberapay.com/ltGuillaume/donate"><img title="Donate using Liberapay" src="https://raw.githubusercontent.com/ltGuillaume/Resources/master/liberapay.svg"></a>

# uReader
Minimal code to present a preformatted plain text document for comfortable reading on mobile and desktop.

## Overview
- Pagination
- Font scaling
- Keyboard, touch and mouse wheel navigation
- 3 themes: reading mode (blue background), dark and light
- NoScript support (without pagination, font scaling and theme switching)
- Optional protection with a watchword, passed on directly as URL parameter or entered via a prompt

## Getting started
1. Copy the files to a server with PHP
1. Copy `config.php.template` to `config.php` and set the variable `$wwd` if you would like watchword protection
	- For Apache, use `.htaccess` to prevent access to the contents directly
	- For nginx, add something like this to do the same:
	```
    location / {
      rewrite ^ /index.php;
    }
	```
1. Put the preformatted text in `contents.txt`
1. Alternatively, move `contents.txt` into a subfolder (together with `config.php` for per-book settings)

## Credits
* The [Fanwood Text](https://www.theleagueofmoveabletype.com/fanwood) font by Barry Schwartz