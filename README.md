# BanInfo
Information about ban and player's ban history

# Features
- Display information about player's or IP's active ban
- Display player's ban history since  plugin installation
- Multi-language support (currently English and Russian) individually for each player based on the device lang

# Commands
- /bi &lt;player&gt; – Display information about banned player
- /bi-ip &lt;IP&gt; – Display information about banned IP adress
- /pbans &lt;player&gt; [page] - Display player's ban history
- /clearhistory &lt;player&gt; [days] - Clear ban history older than ```[days]``` days or, if not specified, delete all
- /bans2history - Create a banned players' history based on banlist (only active bans)

# Permissions
- baninfo.commands.baninfo – Allow to use /bi
- baninfo.commands.baninfo.ip – Allow to use /bi-ip
- baninfo.commands.history - Allow to use /pbans
- baninfo.commands.clearhistory - Allow to use /clearhistory
- baninfo.commands.bans2history - Allow to use /bans2history
