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

- Run your cron job on ``/home/folder/unrealircd-sql-stats/src/stats.php`` throwing it every 1, 3 or 5 minutes.


🔴 Delete your tables with every UnrealIRCd update
------------
🔴 If UnrealIRCd creates new keys and values in JSON logging, then delete your tables that start with "unrealircd_". Note that the tables are recreated automatically using the same columns as there are in the JSON logging.


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
SELECT num_users FROM `unrealircd_channels` WHERE name='#quizz'
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

##### Number of ASN duplicates sorted from largest to smallest, also displaying the asname :
```sql
SELECT country_code, asn, asname, COUNT(*) AS number_of_duplicates FROM unrealircd_users GROUP BY asn HAVING COUNT(*) > 1 ORDER BY number_of_duplicates DESC
```

and with the average of reputations:

```sql
SELECT country_code, asn, asname, COUNT(*) AS number_of_duplicates, AVG(reputation) AS average_reputation FROM unrealircd_users GROUP BY country_code, asn, asname HAVING COUNT(*) > 1 ORDER BY number_of_duplicates DESC
```

I am not 100% certain, but the higher the number of duplicates with a very low average reputation, the more likely it is that these could be "passoire" IP (French expression close to "not solid" or "which will soon break for nothing"), or that the users are not very engaged, or that the ISP frequently changes the user's IP addresses. It could also be an IPv6/64 address that remains the same, but the last four segments change constantly.
Additionally, if there is only one user with a single ASN or two from another country with a very low reputation, it is probably a new user (especially the IP). It might be worth filtering and scanning it, as it could be a VPN with a country very far from ours, or its ASN matches a hosting service provider.

##### Many other things