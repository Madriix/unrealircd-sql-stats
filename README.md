UnrealIRCd SQL Stats
==============
[![Version](https://img.shields.io/badge/UnrealIRCd-6.1.7_or_later-darkgreen.svg)]()
[![Unreal](https://img.shields.io/badge/PHP-8.0_or_later-darkgreen.svg)](https://unrealircd.org)

This way, it would be possible to display the desired statistics on the websites.


Installation
------------
- Add this folder somewhere on the web server.

- Run 
``composer require unrealircd/unrealircd-rpc:dev-main``
- Edit the file src/config.php.

- Run your cron job on ``/home/folder/unrealircd-sql-stats/src/stats.php`` throwing it every 1 or 3 minutes.


<span style="color:red">Delete your tables with every UnrealIRCd update</span>
------------
<span style="color:red">If UnrealIRCd creates new keys and values in JSON logging, then delete your tables that start with "unrealircd_". Note that the tables are recreated automatically using the same columns as there are in the JSON logging.</span>


Useful select
------------
##### Display the list of server users : 
```sql
SELECT * FROM `unrealircd_users`
```

##### Display the number of users on the irc server : 
```sql
SELECT count(*) as number FROM `unrealircd_users`
```

##### Display the list of channels but not those that have +s mode : 
```sql
SELECT * FROM `unrealircd_channels` WHERE BINARY modes not like '%s%'
```

##### Display the number of users on a channel: 
```sql
SELECT num_users FROM `unrealircd_channels` WHERE name='#vintage'
```

##### Check if a nickname is blacklisted for example before a user registers to become a member : 
```sql
SELECT * FROM `unrealircd_name_bans` WHERE name='*snap*'
```

##### View a user's channels : 
```sql
SELECT channels FROM `unrealircd_users` WHERE name='Bruno23'
```

##### This query selects all usernames from the "unrealircd_users" table where channel #Channel2 is present in the comma separated "channels" column : 
```sql
SELECT name FROM `unrealircd_users` WHERE FIND_IN_SET('#Channel2', channels) > 0
```

The "FIND_IN_SET" function searches for the string '#Channel2' in the "channels" column and returns the position of the first occurrence in the string. If the string is not found, the function returns 0. The "FIND_IN_SET" function only works if the values are separated by commas, it will not work for other delimiters.

##### Here is an equivalent query :
```sql
SELECT name FROM `unrealircd_users` WHERE channels REGEXP '(^|,)#Channel2(,|$)'
```

##### Many other things