## php study note

### PHP Warning:  Module 'xxx' already loaded in Unknown on line 0
if you catch this warning.
I know two function:  
1. go to php.ini and comment the like:
```
;extension=memcache.so
```

2. if you cannot find xxx.so in php.ini. maybe it in
```
/etc/php/mods-available/
```
It is happend when you add other extension to already php.
