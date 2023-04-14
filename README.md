UnrealIRCd SQL Stats
==============

This way, it would be possible to display the desired statistics on the websites.


Installation
------------
- Add this folder somewhere on the web server.

- Run 
``composer require unrealircd/unrealircd-rpc:dev-main``
- Edit the file src/config.php.

- Run your cron job on /home/folder/unrealircd-rpc-sql/src/stats.php


Delete your tables with every UnrealIRCd update
------------
If UnrealIRCd creates new keys and values in JSON logging, then delete your tables that start with "unrealircd_". Note that the tables are recreated automatically using the same columns as there are in the JSON logging.


Useful select
------------
- Display the list of server users: ``SELECT * FROM `unrealircd_users` ``
- Display the number of users on the irc server: ``SELECT count(*) as number FROM `unrealircd_users` ``
- Display the list of channels but not those that have +s mode: ``SELECT * FROM `unrealircd_channels` WHERE modes not like '%s%'  ``
- Display the number of users on a channel: ``SELECT num_users FROM `unrealircd_channels` WHERE name='#vintage' ``
- Check if a nickname is blacklisted for example before a user registers to become a member: ``SELECT * FROM `unrealircd_name_bans` WHERE name='*snap*' ``
- View a user's channels: ``SELECT channels FROM `unrealircd_users` WHERE name='Bruno23' ``
- Many other things