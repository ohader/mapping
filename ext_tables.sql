#
# Table structure for table 'tx_mapping_domain_model_structure'
#
CREATE TABLE tx_mapping_domain_model_structure (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	title varchar(255) NOT NULL default '',
	template varchar(255) NOT NULL default '',
	heads mediumtext,
	elements mediumtext,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);
