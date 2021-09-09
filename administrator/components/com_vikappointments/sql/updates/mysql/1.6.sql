CREATE TABLE IF NOT EXISTS `#__vikappointments_order_status` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_order` int(10) unsigned NOT NULL,
  `status` varchar(16) NOT NULL,
  `comment` varchar(512),
  `client` tinyint(1) unsigned DEFAULT 0 COMMENT 'admin (1) or site (0)',
  `ip` varchar(32) NOT NULL,
  `createdby` int(10) unsigned DEFAULT 0,
  `createdon` datetime DEFAULT NULL,
  `type` varchar(48) DEFAULT '' COMMENT 'to which table it refers',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_conversion` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) DEFAULT '',
  `published` tinyint(1) DEFAULT 0,
  `statuses` varchar(512) DEFAULT '[]' COMMENT 'a JSON string containing all the supported statuses',
  `jsfile` varchar(256) DEFAULT '',
  `snippet` text DEFAULT NULL,
  `type` varchar(48) DEFAULT '' COMMENT 'to which table it refers',
  `page` varchar(48) DEFAULT '' COMMENT 'to which page it refers',
  `createdon` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_special_rates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `description` varchar(512) NOT NULL,
  `charge` decimal(10,2) NOT NULL COMMENT 'negative is supported for discounts',
  `published` tinyint(1) NOT NULL DEFAULT 1,
  `weekdays` varchar(16) DEFAULT '' COMMENT 'a list of accepted week days (comma separated)',
  `fromdate` date DEFAULT NULL COMMENT 'start publishing',
  `todate` date DEFAULT NULL COMMENT 'end publishing',
  `fromtime` int(4) DEFAULT 0 COMMENT 'from time: built as hour * 60 + minutes',
  `totime` int(4) DEFAULT 0 COMMENT 'end time: built as hour * 60 + minutes',
  `people` int(4) DEFAULT 0,
  `usergroups` varchar(32) DEFAULT '' COMMENT 'a list of accepted user groups (comma separated)',
  `createdon` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_ser_rates_assoc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_special_rate` int(10) unsigned NOT NULL,
  `id_service` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_coupon_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(48) NOT NULL,
  `description` varchar(2048) DEFAULT '',
  `ordering` int(10) unsigned DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_cf_service_assoc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_field` int(10) unsigned NOT NULL,
  `id_service` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_lang_subscr` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `id_subscr` int(10) unsigned NOT NULL,
  `tag` varchar(8) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_lang_customf` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `choose` text DEFAULT '',
  `poplink` varchar(256) DEFAULT '',
  `id_customf` int(10) unsigned NOT NULL,
  `tag` varchar(8) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

ALTER TABLE `#__vikappointments_reservation`
ADD COLUMN `closure` tinyint(1) DEFAULT 0,
ADD COLUMN `skip_deposit` tinyint(1) unsigned DEFAULT 0,
ADD COLUMN `cc_data` text DEFAULT NUll,
ADD COLUMN `payment_attempt` int(4) DEFAULT 1,
ADD COLUMN `conversion` varchar(64) DEFAULT '' COMMENT 'built as [PAGE].[ORDER_STATUS] (e.g. order.confirmed)';

ALTER TABLE `#__vikappointments_service`
ADD COLUMN `alias` varchar(128) NOT NULL AFTER `name`,
ADD COLUMN `app_per_slot` tinyint(1) NOT NULL DEFAULT 1 AFTER `priceperpeople`,
ADD COLUMN `checkout_selection` tinyint(1) DEFAULT 0 AFTER `has_own_cal`,
ADD COLUMN `display_seats` tinyint(1) DEFAULT 0 COMMENT 'when enabled, the timeline will display the remaining seats' AFTER `checkout_selection`,
ADD COLUMN `level` tinyint(1) DEFAULT 1 COMMENT 'the access view level' AFTER `id_group`,
ADD COLUMN `color` varchar(6) DEFAULT NULL COMMENT 'the hex color tag' AFTER `level`,
ADD COLUMN `createdby` int(10) unsigned DEFAULT 0 AFTER `color`;

ALTER TABLE `#__vikappointments_employee`
ADD COLUMN `alias` varchar(128) NOT NULL AFTER `nickname`;

ALTER TABLE `#__vikappointments_emp_worktime`
ADD COLUMN `parent` int(10) DEFAULT -1;

ALTER TABLE `#__vikappointments_custfields` 
DROP COLUMN `isnominative`, 
DROP COLUMN `isemail`, 
DROP COLUMN `isphone`, 
ADD COLUMN `rule` tinyint(2) NOT NULL DEFAULT 0 AFTER `required`,
ADD COLUMN `multiple` tinyint(1) DEFAULT 0 AFTER `choose`,
ADD COLUMN `formname` varchar(32) DEFAULT '',
ADD COLUMN `group` tinyint(1) DEFAULT 0 COMMENT '0 for shop, 1 for employees registration';

ALTER TABLE `#__vikappointments_option`
ADD COLUMN `published` tinyint(1) NOT NULL DEFAULT 1 AFTER `single`;

ALTER TABLE `#__vikappointments_coupon`
ADD COLUMN `id_group` int(10) unsigned DEFAULT 0;

ALTER TABLE `#__vikappointments_cust_mail`
ADD COLUMN `id_service` int(10) unsigned DEFAULT 0,
ADD COLUMN `id_employee` int(10) unsigned DEFAULT 0;

ALTER TABLE `#__vikappointments_package`
ADD COLUMN `level` tinyint(1) DEFAULT 1 COMMENT 'the access view level' AFTER `id_group`;

ALTER TABLE `#__vikappointments_package_order`
ADD COLUMN `cc_data` text DEFAULT NUll,
ADD COLUMN `payment_attempt` int(4) DEFAULT 1;

ALTER TABLE `#__vikappointments_subscr_order`
ADD COLUMN `cc_data` text DEFAULT NUll,
ADD COLUMN `payment_attempt` int(4) DEFAULT 1;

ALTER TABLE `#__vikappointments_users`
ADD COLUMN `credit` decimal(10,2) DEFAULT 0.0 COMMENT 'used to keep the credit in case of cancellation';

ALTER TABLE `#__vikappointments_gpayments` 
DROP COLUMN `shownotealw`, 
CHANGE `charge` `charge` decimal(8, 4) DEFAULT NULL, 
ADD COLUMN `appointments` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'allowed for appointments purchases' AFTER `published`,
ADD COLUMN `icontype` tinyint(1) DEFAULT 0 AFTER `setconfirmed`, 
ADD COLUMN `icon` varchar(128) DEFAULT '' AFTER `icontype`, 
ADD COLUMN `prenote` text DEFAULT NULL AFTER `note`,
ADD COLUMN `id_employee` int(10) unsigned DEFAULT 0,
ADD COLUMN `createdby` int(10) unsigned NOT NULL DEFAULT 0;

ALTER TABLE `#__vikappointments_lang_service`
ADD COLUMN `alias` varchar(128) DEFAULT NULL AFTER `name`;

ALTER TABLE `#__vikappointments_lang_employee`
ADD COLUMN `alias` varchar(128) DEFAULT NULL AFTER `nickname`;

ALTER TABLE `#__vikappointments_lang_payment`
ADD COLUMN `prenote` text DEFAULT '' AFTER `name`;

UPDATE `#__vikappointments_config` SET `setting`='1.6' WHERE `param`='version';

-- The table should be dropped manually as the existing
-- payments need to be merged to the global ones.
-- DROP TABLE `#__vikappointments_employee_payment`;

-- The router setting should be added manually in order
-- to rename the existing _router.php file (if disabled).
-- INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('router', 0);

INSERT INTO `#__vikappointments_config` (`param`, `setting`) VALUES('empresconfirm', 1);
INSERT INTO `#__vikappointments_config` (`param`, `setting`) VALUES('empattachser', 1);
INSERT INTO `#__vikappointments_config` (`param`, `setting`) VALUES('usedeposit', 1);
INSERT INTO `#__vikappointments_config` (`param`, `setting`) VALUES('googleapikey', '');
INSERT INTO `#__vikappointments_config` (`param`, `setting`) VALUES('listablecf', '');
INSERT INTO `#__vikappointments_config` (`param`, `setting`) VALUES('conversion_track', 0);
INSERT INTO `#__vikappointments_config` (`param`, `setting`) VALUES('gdpr', 0);
INSERT INTO `#__vikappointments_config` (`param`, `setting`) VALUES('policylink', '');
INSERT INTO `#__vikappointments_config` (`param`, `setting`) VALUES('usercredit', 0);
INSERT INTO `#__vikappointments_config` (`param`, `setting`) VALUES('empajaxsearch', 0);
INSERT INTO `#__vikappointments_config` (`param`, `setting`) VALUES('securehashkey', '');
INSERT INTO `#__vikappointments_config` (`param`, `setting`) VALUES('update_extra_fields', 0);
INSERT INTO `#__vikappointments_config` (`param`, `setting`) VALUES('calendarlayout', 'calendar');
INSERT INTO `#__vikappointments_config` (`param`, `setting`) VALUES('sitetheme', 'light');
