waka
====

Waka is communcation tool, to keep a group of people up to date on a specific project.
A Waka instance functions much like a single forum thread, but without the over head of
signup. Instead it utilizes email adresses as authentication (much like doodle).
A Waka can send out email notifitcations when it was edited and highlights the
new posts and comments in the Waka. Other features include:

* no database required (data is stored in json-files)
* basic bbcode support
* live updating (without page reload)
* embedding files and images
* Latex support for formulas
* multi-user/collaborative posts
* two tiered user permissions (editor/subscriber)

###Security Issues

Because Waka's security builds on email and does not provide further username
and password authentication it can not be considered secure. One method of
adding security is to restrict access to the waka server my means
of the webserver (e.g. through .htaccess).

###Installation

Upload the files onto a php enabled websever. Browse to the that folder and
start using Waka.

###Demo Server

http://dasunwahrscheinliche.de/waka/
