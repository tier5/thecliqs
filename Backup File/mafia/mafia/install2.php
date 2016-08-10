<?
include("setup.php");

mysql_query("CREATE TABLE `r$round[0]_board` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `time` int(11) NOT NULL default '0',
  `who` mediumint(8) unsigned NOT NULL default '0',
  `msg` text NOT NULL,
  `del` varchar(12) NOT NULL default '',
  `board` varchar(32) NOT NULL default '',
  `adminpost` varchar(6) NOT NULL default 'no',
  `status` varchar(12) NOT NULL default 'new',
  `paused` varchar(12) NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  KEY `time` (`time`)
) TYPE=MyISAM;");

mysql_query("CREATE TABLE `r$round[0]_blackmarket` (
  `id` int(11) NOT NULL auto_increment,
  `item` varchar(20) NOT NULL default '',
  `amount` int(11) NOT NULL default '0',
  `cost` int(11) NOT NULL default '0',
  `owner` varchar(200) NOT NULL default '',
  `expires` bigint(20) NOT NULL default '0',
  `sellerid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;");

mysql_query("CREATE TABLE `r$round[0]_city` (
  `id` tinyint(1) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `country` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;");

mysql_query("CREATE TABLE `r$round[0]_contracts` (
  `id` tinyint(1) NOT NULL auto_increment,
  `date` varchar(255) NOT NULL default '',
  `contractor` varchar(255) NOT NULL default '',
  `target` varchar(255) NOT NULL default '',
  `amount` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;");

mysql_query("CREATE TABLE `r$round[0]_contacts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pimp` int(11) NOT NULL default '0',
  `contact` int(11) NOT NULL default '0',
  `type` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;");

mysql_query("CREATE TABLE `r$round[0]_crew` (
  `id` int(6) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `founder` varchar(255) NOT NULL default '',
  `members` smallint(6) NOT NULL default '1',
  `profile` varchar(255) NOT NULL default '',
  `icon` varchar(255) NOT NULL default '',
  `city` tinyint(3) unsigned NOT NULL default '1',
  `rank` decimal(55,0) unsigned NOT NULL default '99999',
  `networth` decimal(55,0) unsigned NOT NULL default '0',
  `cofounder` varchar(32) NOT NULL default '',
  `money` decimal(55,0) unsigned NOT NULL default '50000',
  `condom` decimal(55,0) unsigned NOT NULL default '500',
  `crack` decimal(55,0) unsigned NOT NULL default '500',
  `medicine` decimal(55,0) unsigned NOT NULL default '500',
  `thug` decimal(55,0) unsigned NOT NULL default '500',
  `beer` decimal(55,0) unsigned NOT NULL default '0',
  `weed` decimal(55,0) unsigned NOT NULL default '0',
  `glock` decimal(55,0) unsigned NOT NULL default '0',
  `shotgun` decimal(55,0) unsigned NOT NULL default '0',
  `uzi` decimal(55,0) unsigned NOT NULL default '0',
  `ak47` decimal(55,0) unsigned NOT NULL default '0',
  `bank` decimal(55,0) unsigned NOT NULL default '50000',
  `lowrider` decimal(55,0) unsigned NOT NULL default '0',
  `cbank` decimal(55,0) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;");

mysql_query("CREATE TABLE `r$round[0]_cronjobs` (
  `cronjob` varchar(32) NOT NULL default '0',
  `lastran` int(12) NOT NULL default '0'
) TYPE=MyISAM;");

mysql_query("CREATE TABLE `r$round[0]_invites` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `crew` bigint(20) NOT NULL default '0',
  `pimp` bigint(20) NOT NULL default '0',
  `cancelled` char(3) NOT NULL default 'no',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;");

mysql_query("CREATE TABLE `r$round[0]_mailbox` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `src` int(11) NOT NULL default '0',
  `dest` int(11) NOT NULL default '0',
  `msg` text NOT NULL,
  `time` int(12) NOT NULL default '0',
  `inbox` varchar(32) NOT NULL default '',
  `del` char(3) NOT NULL default 'no',
  `crew` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;");

mysql_query("CREATE TABLE `r$round[0]_pimp` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `pimp` varchar(18) NOT NULL default '',
  `user` varchar(255) NOT NULL default '',
  `pass` varchar(32) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `trn` int(10) unsigned NOT NULL default '1000',
  `res` int(10) unsigned NOT NULL default '2000',
  `money` decimal(55,0) unsigned NOT NULL default '750000000',
  `payout` tinyint(3) unsigned NOT NULL default '10',
  `whore` decimal(55,0) unsigned NOT NULL default '10000',
  `dealers` decimal(55,0) unsigned NOT NULL default '10',
  `bootleggers` decimal(55,0) unsigned NOT NULL default '10',
  `hustlers` decimal(55,0) unsigned NOT NULL default '10',
  `punks` decimal(55,0) unsigned NOT NULL default '10',
  `hitmen` decimal(55,0) unsigned NOT NULL default '10000',
  `bodyguards` decimal(55,0) unsigned NOT NULL default '10',
  `thug` decimal(55,0) unsigned NOT NULL default '10000',
  `informant` decimal(55,0) unsigned NOT NULL default '10',
  `whappy` tinyint(3) unsigned NOT NULL default '100',
  `ihappy` tinyint(3) unsigned NOT NULL default '100',
  `condom` decimal(55,0) unsigned NOT NULL default '250000',
  `crack` decimal(55,0) unsigned NOT NULL default '250000',
  `medicine` decimal(55,0) unsigned NOT NULL default '250000',
  `punk` decimal(55,0) unsigned NOT NULL default '10',
  `thappy` tinyint(3) unsigned NOT NULL default '100',
  `phappy` tinyint(3) unsigned NOT NULL default '100',
  `weed` decimal(55,0) unsigned NOT NULL default '250000',
  `beer` decimal(55,0) unsigned NOT NULL default '250000',
  `lowrider` decimal(55,0) unsigned NOT NULL default '0',
  `hummer` decimal(55,0) unsigned NOT NULL default '0',
  `viper` decimal(55,0) unsigned NOT NULL default '0',
  `bmxbike` decimal(55,0) unsigned NOT NULL default '0',
  `plane` decimal(55,0) unsigned NOT NULL default '0',
  `glock` decimal(55,0) unsigned NOT NULL default '10',
  `shotgun` decimal(55,0) unsigned NOT NULL default '0',
  `uzi` decimal(55,0) unsigned NOT NULL default '0',
  `ak47` decimal(55,0) unsigned NOT NULL default '0',
  `explosive` decimal(55,0) unsigned NOT NULL default '10',
  `pepperspray` decimal(55,0) unsigned NOT NULL default '0',
  `dildo` decimal(55,0) unsigned NOT NULL default '0',
  `gernade` decimal(55,0) unsigned NOT NULL default '0',
  `attin` int(10) unsigned NOT NULL default '0',
  `attout` int(10) unsigned NOT NULL default '0',
  `attackout` decimal(55,0) NOT NULL default '0',
  `attackin` decimal(55,0) NOT NULL default '0',
  `city` tinyint(3) unsigned NOT NULL default '1',
  `crew` bigint(20) unsigned NOT NULL default '0',
  `networth` decimal(55,0) unsigned NOT NULL default '0',
  `bank` decimal(55,0) unsigned NOT NULL default '50000',
  `tbank` decimal(55,0) unsigned NOT NULL default '0',
  `maxadd` decimal(55,0) unsigned NOT NULL default '0',
  `rank` decimal(55,0) unsigned NOT NULL default '99999',
  `nrank` decimal(55,0) unsigned NOT NULL default '99999',
  `online` int(12) unsigned NOT NULL default '0',
  `lastattack` int(12) unsigned NOT NULL default '0',
  `lastattackby` int(11) unsigned NOT NULL default '0',
  `profile` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `msg` tinyint(3) unsigned NOT NULL default '0',
  `msgsent` bigint(20) NOT NULL default '0',
  `atk` tinyint(3) unsigned NOT NULL default '0',
  `ivt` tinyint(3) unsigned NOT NULL default '0',
  `cmsg` tinyint(3) unsigned NOT NULL default '0',
  `whorek` decimal(55,0) NOT NULL default '0',
  `thugk` decimal(55,0) NOT NULL default '0',
  `infok` decimal(55,0) NOT NULL default '0',
  `punkk` decimal(55,0) NOT NULL default '0',
  `newalert` tinyint(1) NOT NULL default '0',
  `alert` text NOT NULL,
  `ip` varchar(15) NOT NULL default '0.0.0.0',
  `host` varchar(255) NOT NULL default '',
  `code` varchar(43) NOT NULL default '',
  `pin` varchar(32) NOT NULL default '',
  `status` varchar(12) NOT NULL default 'normal',
  `subscribe` varchar(12) NOT NULL default '0',
  `workstatus` varchar(12) NOT NULL default 'normal',
  `protection` int(11) NOT NULL default '0',
  `protectstarted` int(12) NOT NULL default '0',
  `postpriv` varchar(12) NOT NULL default 'enabled',
  `defaultturns` int(12) NOT NULL default '5',
  `sounds` varchar(18) NOT NULL default 'enabled',
  `page` varchar(200) NOT NULL default '',
  `lottonum` varchar(100) NOT NULL default '',
  `posts` int(11) NOT NULL default '0',
  `cashstolen` decimal(55,0) NOT NULL default '0',
  `hitmenk` decimal(55,0) NOT NULL default '0',
  `bodyguardsk` decimal(55,0) NOT NULL default '0',
  `transfered` decimal(55,0) NOT NULL default '0',
  `ctitle` varchar(100) NOT NULL default '',
  `lvl` varchar(5) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `rank` (`rank`),
  KEY `nrank` (`nrank`)
) TYPE=MyISAM ;");

mysql_query("CREATE TABLE `r$round[0]_money_transfer` (
  `id` int(11) NOT NULL auto_increment,
  `sender_id` int(11) default NULL,
  `receiver_id` int(11) default NULL,
  `amount` double default NULL,
  `status` enum('Pending','Canceled','Completed') default 'Pending',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;");
?>