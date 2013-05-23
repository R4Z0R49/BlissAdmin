BlissAdmin for the Reality private-hive & DayZ hive(soon)
=========

BlissAdmin is a DayZ Administration panel that was originally coded for the server pack Bliss. Bliss then got abandoned and Reality was born. We now use reality as a base but however are planning to add support for the regular DayZ hive structure.

Requirements
=========

*MySQL 5.4 or higher

*Apache 2.2 or higher

*PHP 5.3 

*Correctly installed and configured Battleye RCON

*Linux Support (Close pending path issues)


Features
=========

*Lists of players and vehicles.

*Player/vehicle inventory, states and position.

*Inventory and backpack string editors.

*Teleportation, skin changes, reset humanity, reviving, killing, healing and medical status options via the panel.

*Reset humanity, reviving, killing, healing and medical status options via the panel.

*Two access ranks to the panel, one with full access, one with a bit more restricted access.

*Google maps API based map with players, vehicles and deployables (optional tracking of players and vehicles).

*Inventory check for unknown items.

*Search for items, vehicles, players.

*Rcon-based online players list, kick-ban features and global messaging.

*Reset Players locations.


Installation
=========

*Import dayz.sql in the sql folder to your database.

*Rename config.php-dist to config.php.

*Edit config.php and set to the right values. This is highly important!

*The default login is: admin/123456

Updating
=========

*Check for any new files to run in the sql/updates folder.

*Check that your config.php is up to date with the new config.php-dist if it has been edited


