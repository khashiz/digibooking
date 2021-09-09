CREATE TABLE IF NOT EXISTS `#__vikappointments_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `description` text DEFAULT '',
  `ordering` int(10) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_employee_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `description` text DEFAULT '',
  `ordering` int(10) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_reservation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_payment` int(10) NOT NULL,
  `id_employee` int(10) NOT NULL,
  `id_service` int(10) NOT NULL,
  `checkin_ts` int(12) NOT NULL,
  `people` int(4) NOT NULL DEFAULT 1,
  `duration` int(10) NOT NULL,
  `sleep` int(4) DEFAULT 0 NOT NULL,
  `total_cost` decimal(10,2) DEFAULT 0.0,
  `tot_paid` decimal(10,2) DEFAULT 0.0,
  `paid` tinyint(1) NOT NULL DEFAULT 0,
  `purchaser_nominative` varchar(128) DEFAULT '',
  `purchaser_mail` varchar(64) NOT NULL DEFAULT '',
  `purchaser_phone` varchar(32) DEFAULT '',
  `purchaser_prefix` varchar(10) DEFAULT '',
  `purchaser_country` varchar(2) DEFAULT '',
  `langtag` varchar(8) DEFAULT '',
  `custom_f` text DEFAULT '',
  `coupon_str` varchar(128) DEFAULT '',
  `status` varchar(16) DEFAULT 'PENDING',
  `locked_until` int(12) DEFAULT 0,
  `sid` varchar(16) NOT NULL DEFAULT '000000000000',
  `conf_key` varchar(12) DEFAULT '',
  `view_emp` tinyint(1) DEFAULT 0,
  `notes` text DEFAULT '',
  `uploads` varchar(256) DEFAULT '',
  `id_parent` int(10) NOT NULL DEFAULT -1,
  `createdon` int(12) DEFAULT -1,
  `createdby` int(10) DEFAULT -1,
  `id_user` int(10) DEFAULT -1,
  `skip_deposit` tinyint(1) unsigned DEFAULT 0,
  `log` text DEFAULT '',
  `closure` tinyint(1) DEFAULT 0,
  `cc_data` text DEFAULT NUll,
  `payment_attempt` int(4) DEFAULT 1,
  `conversion` varchar(64) DEFAULT '' COMMENT 'built as [PAGE].[ORDER_STATUS] (e.g. order.confirmed)',
  PRIMARY KEY (`id`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_service` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `alias` varchar(128) NOT NULL,
  `description` text DEFAULT '',
  `duration` int(6) NOT NULL,
  `sleep` int(4) DEFAULT 0 NOT NULL,
  `interval` tinyint(1) DEFAULT 1 NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `max_capacity` int(5) NOT NULL DEFAULT 1,
  `min_per_res` int(5) NOT NULL DEFAULT 1,
  `max_per_res` int(5) NOT NULL DEFAULT 1,
  `priceperpeople` tinyint(1) NOT NULL DEFAULT 1,
  `app_per_slot` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'when disabled, the maximum capacity is ignored',
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `quick_contact` tinyint(1) NOT NULL DEFAULT 0,
  `choose_emp` tinyint(1) NOT NULL DEFAULT 1,
  `enablezip` tinyint(1) NOT NULL DEFAULT 0,
  `use_recurrence` tinyint(1) NOT NULL DEFAULT 0,
  `image` varchar(128) DEFAULT '',
  `start_publishing` int(11) DEFAULT -1,
  `end_publishing` int(11) DEFAULT -1,
  `has_own_cal` tinyint(1) DEFAULT 0,
  `checkout_selection` tinyint(1) DEFAULT 0,
  `display_seats` tinyint(1) DEFAULT 0 COMMENT 'when enabled, the timeline will display the remaining seats', 
  `id_group` int(10) DEFAULT -1,
  `level` tinyint(1) DEFAULT 1 COMMENT 'the access view level',
  `color` varchar(6) DEFAULT NULL COMMENT 'the hex color tag',
  `createdby` int(10) unsigned DEFAULT 0,
  `ordering` int(10) NOT NULL DEFAULT 1,
  `metadata` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_employee` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(128) NOT NULL,
  `lastname` varchar(128) NOT NULL,
  `nickname` varchar(128) NOT NULL,
  `alias` varchar(128) NOT NULL,
  `email` varchar(128) DEFAULT '',
  `notify` tinyint(1) NOT NULL DEFAULT 0,
  `showphone` tinyint(1) NOT NULL DEFAULT 0,
  `quick_contact` tinyint(1) NOT NULL DEFAULT 0,
  `listable` tinyint(1) NOT NULL DEFAULT 1,
  `phone` varchar(32) DEFAULT '',
  `image` varchar(128) DEFAULT '',
  `note` text DEFAULT '',
  `synckey` varchar(32) DEFAULT 'secret',
  `jid` int(10) DEFAULT -1,
  `id_group` int(10) DEFAULT -1,
  `active_to` int(12) DEFAULT -1,
  `active_since` int(12) DEFAULT -1,
  `timezone` varchar(32) DEFAULT '',
  `billing_json` varchar(2048) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_option` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `description` varchar(1024) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 1,
  `single` tinyint(1) NOT NULL DEFAULT 1,
  `maxq` int(6) NOT NULL DEFAULT 1,
  `required` tinyint(1) DEFAULT 0,
  `image` varchar(128) DEFAULT '',
  `displaymode` tinyint(1) DEFAULT 1,
  `ordering` int(10) unsigned DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_option_value` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_option` int(10) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `inc_price` decimal(10,2) NOT NULL,
  `ordering` int(10) unsigned DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_ser_emp_assoc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_service` int(10) NOT NULL,
  `id_employee` int(10) NOT NULL,
  `rate` decimal(8,2) DEFAULT 0.0,
  `duration` int(6) NOT NULL,
  `sleep` int(4) DEFAULT 0 NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_ser_opt_assoc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_service` int(10) NOT NULL,
  `id_option` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_res_opt_assoc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_reservation` int(10) NOT NULL,
  `id_option` int(10) NOT NULL,
  `id_variation` int(10) DEFAULT -1,
  `inc_price` decimal(10,2) NOT NULL,
  `quantity` int(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_emp_worktime` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_employee` int(10) NOT NULL,
  `id_service` int(10) DEFAULT -1,
  `day` int(2) NOT NULL,
  `fromts` int(6) NOT NULL,
  `endts` int(6) NOT NULL,
  `ts` int(12) DEFAULT -1,
  `closed` tinyint(1) NOT NULL DEFAULT 0,
  `id_location` int(10) DEFAULT -1,
  `parent` int(10) DEFAULT -1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_waitinglist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_service` int(10) unsigned NOT NULL,
  `id_employee` int(10) DEFAULT -1,
  `email` varchar(64) DEFAULT '',
  `phone_number` varchar(32) DEFAULT '',
  `phone_prefix` varchar(10) DEFAULT '',
  `timestamp` int(12) NOT NULL,
  `jid` int(10) DEFAULT -1,
  `created_on` int(12) NOT NULL,
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
  `params` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_ser_rates_assoc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_special_rate` int(10) unsigned NOT NULL,
  `id_service` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_coupon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(32) NOT NULL,
  `type` tinyint(2) NOT NULL DEFAULT 1,
  `percentot` tinyint(2) NOT NULL DEFAULT 2,
  `value` decimal(10,2) NOT NULL DEFAULT 0.0,
  `mincost` decimal(10,2) NOT NULL DEFAULT 0.0,
  `pubmode` tinyint(1) DEFAULT 1 COMMENT '1 to consider publishing on current date, 2 for checkin date',
  `dstart` int(12) DEFAULT -1,
  `dend` int(12) DEFAULT -1,
  `lastminute` int(4) DEFAULT 0,
  `max_quantity` int(8) DEFAULT 1,
  `used_quantity` int(8) DEFAULT 0,
  `remove_gift` tinyint(1) DEFAULT 0,
  `notes` varchar(512) DEFAULT '',
  `id_group` int(10) unsigned DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_coupon_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(48) NOT NULL,
  `description` varchar(2048) DEFAULT '',
  `ordering` int(10) unsigned DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_coupon_service_assoc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_service` int(10) unsigned NOT NULL,
  `id_coupon` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_coupon_employee_assoc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_employee` int(10) unsigned NOT NULL,
  `id_coupon` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_gpayments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `file` varchar(64) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `appointments` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'allowed for appointments purchases',
  `subscr` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'allowed for packages and subscriptions',
  `prenote` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `charge` decimal(8,4) DEFAULT NULL,
  `setconfirmed` tinyint(1) NOT NULL DEFAULT 0,
  `icontype` tinyint(1) DEFAULT 0,
  `icon` varchar(128) DEFAULT '',
  `position` varchar(64) DEFAULT '',
  `level` tinyint(1) DEFAULT 1 COMMENT 'the access view level',
  `id_employee` int(10) unsigned DEFAULT 0,
  `createdby` int(10) unsigned NOT NULL DEFAULT 0,
  `params` varchar(512) DEFAULT NULL,
  `ordering` int(10) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_custfields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `formname` varchar(32) DEFAULT '',
  `type` varchar(64) NOT NULL DEFAULT 'text',
  `choose` text DEFAULT NULL,
  `multiple` tinyint(1) DEFAULT 0,
  `required` tinyint(1) NOT NULL DEFAULT 0,
  `ordering` int(10) NOT NULL DEFAULT 1,
  `rule` tinyint(2) NOT NULL DEFAULT 0,
  `poplink` varchar(256) DEFAULT NULL,
  `id_employee` int(10) DEFAULT -1,
  `group` tinyint(1) DEFAULT 0 COMMENT '0 for shop, 1 for employees registration',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_cf_service_assoc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_field` int(10) unsigned NOT NULL,
  `id_service` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `param` varchar(32) NOT NULL DEFAULT 'false',
  `setting` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `param` (`param`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_reviews` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `jid` int(10) NOT NULL,
  `timestamp` int(12) NOT NULL,
  `name` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `title` varchar(64) DEFAULT '',
  `comment` text DEFAULT '',
  `rating` int(1) unsigned DEFAULT 0, 
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `langtag` varchar(8) DEFAULT '',
  `id_employee` int(10) DEFAULT -1,
  `id_service` int(10) DEFAULT -1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_subscription` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `amount` int(10) unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.0,
  `published` tinyint(1) NOT NULL DEFAULT 1,
  `trial` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_subscr_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sid` varchar(16) NOT NULL,
  `id_employee` int(10) unsigned NOT NULL,
  `id_subscr` int(10) unsigned NOT NULL,
  `id_payment` int(10) DEFAULT -1,
  `total_cost` decimal(6, 2) DEFAULT 0.0,
  `tot_paid` decimal(6, 2) DEFAULT 0.0,
  `status` varchar(32) DEFAULT 'PENDING',
  `createdon` int(12) NOT NULL,
  `cc_data` text DEFAULT NUll,
  `payment_attempt` int(4) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_package_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(512) NOT NULL,
  `description` text DEFAULT '',
  `ordering` int(10) unsigned DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_package` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `description` text DEFAULT '',
  `price` decimal(10,2) NOT NULL,
  `num_app` int(4) unsigned NOT NULL,
  `published` tinyint(1) unsigned DEFAULT 0,
  `start_ts` int(12) DEFAULT -1,
  `end_ts` int(12) DEFAULT -1,
  `id_group` int(10) DEFAULT -1,
  `level` tinyint(1) DEFAULT 1 COMMENT 'the access view level',
  `ordering` int(10) unsigned DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_package_service` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_package` int(10) unsigned NOT NULL,
  `id_service` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_package_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sid` varchar(16) NOT NULL,
  `id_payment` int(10) DEFAULT -1,
  `status` varchar(32) DEFAULT 'PENDING',
  `total_cost` decimal(10,2) DEFAULT 0.0,
  `tot_paid` decimal(10, 2) DEFAULT 0.0,
  `purchaser_nominative` varchar(128) DEFAULT '',
  `purchaser_mail` varchar(64) NOT NULL,
  `purchaser_phone` varchar(32) DEFAULT '',
  `purchaser_prefix` varchar(10) DEFAULT '',
  `purchaser_country` varchar(2) DEFAULT '',
  `langtag` varchar(8) DEFAULT '',
  `custom_f` text DEFAULT '',
  `createdon` int(12) DEFAULT -1,
  `createdby` int(10) DEFAULT -1,
  `id_user` int(10) DEFAULT -1,
  `log` text DEFAULT '',
  `cc_data` text DEFAULT NUll,
  `payment_attempt` int(4) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_package_order_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_order` int(10) NOT NULL,
  `id_package` int(10) NOT NULL,
  `price` decimal(10,2) DEFAULT 0.0,
  `quantity` int(4) unsigned DEFAULT 1,
  `num_app` int(4) unsigned DEFAULT 1,
  `used_app` int(4) unsigned DEFAULT 0,
  `modifiedon` int(12) DEFAULT -1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `jid` int(10) NOT NULL,
  `country_code` varchar(2) DEFAULT '',
  `fields` text DEFAULT '',
  `billing_name` varchar(64) DEFAULT '',
  `billing_mail` varchar(64) DEFAULT '',
  `billing_phone` varchar(64) DEFAULT '',
  `billing_state` varchar(64) DEFAULT '',
  `billing_city` varchar(64) DEFAULT '',
  `billing_address` varchar(64) DEFAULT '',
  `billing_address_2` varchar(64) DEFAULT '',
  `billing_zip` varchar(12) DEFAULT '',
  `company` varchar(64) DEFAULT '',
  `vatnum` varchar(24) DEFAULT '',
  `ssn` varchar(32) DEFAULT '',
  `notes` varchar(2048) DEFAULT '',
  `image` varchar(128) DEFAULT '',
  `credit` decimal(10,2) DEFAULT 0.0 COMMENT 'used to keep the credit in case of cancellation',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

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
  `page` varchar(48) DEFAULT '' COMMENT 'to which page it refers',
  `type` varchar(48) DEFAULT '' COMMENT 'to which table it refers',
  `createdon` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_lang_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `description` text DEFAULT '',
  `tag` varchar(8) NOT NULL,
  `id_group` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_lang_empgroup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `description` text DEFAULT '',
  `tag` varchar(8) NOT NULL,
  `id_empgroup` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_lang_service` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `alias` varchar(128) DEFAULT NULL,
  `description` text DEFAULT '',
  `tag` varchar(8) NOT NULL,
  `id_service` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_lang_employee` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nickname` varchar(128) NOT NULL,
  `alias` varchar(128) DEFAULT NULL,
  `note` text DEFAULT '',
  `tag` varchar(8) NOT NULL,
  `id_employee` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_lang_option` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `description` varchar(1024) DEFAULT '',
  `vars_json` varchar(1024) DEFAULT '',
  `tag` varchar(8) NOT NULL,
  `id_option` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_lang_package` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `description` text DEFAULT '',
  `tag` varchar(8) NOT NULL,
  `id_package` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_lang_package_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `description` text DEFAULT '',
  `tag` varchar(8) NOT NULL,
  `id_package_group` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_lang_payment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `prenote` text DEFAULT '',
  `note` text DEFAULT '',
  `tag` varchar(8) NOT NULL,
  `id_payment` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_lang_customf` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `choose` text DEFAULT '',
  `poplink` varchar(256) DEFAULT '',
  `id_customf` int(10) unsigned NOT NULL,
  `tag` varchar(8) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_lang_subscr` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `id_subscr` int(10) unsigned NOT NULL,
  `tag` varchar(8) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_cust_mail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `position` varchar(128) DEFAULT '',
  `status` varchar(16) DEFAULT '',
  `tag` varchar(8) NOT NULL,
  `content` text DEFAULT '',
  `file` varchar(128) DEFAULT '',
  `id_service` int(10) unsigned DEFAULT 0,
  `id_employee` int(10) unsigned DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_employee_location` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT '',
  `id_employee` int(10) DEFAULT -1,
  `id_country` int(10) DEFAULT -1,
  `id_state` int(10) DEFAULT -1,
  `id_city` int(10) DEFAULT -1,
  `address` varchar(64) DEFAULT '',
  `zip` varchar(8) DEFAULT '',
  `longitude` decimal(9,6) DEFAULT NULL,
  `latitude` decimal(9,6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_employee_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_employee` int(10) unsigned NOT NULL,
  `listlimit` int(3) unsigned DEFAULT 5,
  `listposition` tinyint(1) unsigned DEFAULT 1,
  `listordering` varchar(4) DEFAULT 'ASC',
  `numcals` int(2) unsigned DEFAULT 6,
  `firstmonth` int(2) DEFAULT -1,
  `zipcodes` varchar(2048) DEFAULT '',
  `zip_field_id` int(10) DEFAULT -1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country_name` varchar(48) NOT NULL,
  `country_2_code` varchar(2) NOT NULL,
  `country_3_code` varchar(3) NOT NULL,
  `phone_prefix` varchar(8) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `country_2_code` (`country_2_code`),
  UNIQUE KEY `country_3_code` (`country_3_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_states` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_country` int(10) unsigned NOT NULL DEFAULT 0,
  `state_name` varchar(64) NOT NULL,
  `state_2_code` char(2) NOT NULL,
  `state_3_code` char(3) DEFAULT '',
  `published` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_state_2_code` (`id_country`,`state_2_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__vikappointments_cities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_state` int(10) unsigned NOT NULL DEFAULT 0,
  `city_name` varchar(64) NOT NULL,
  `city_2_code` char(2) NOT NULL,
  `city_3_code` char(3) DEFAULT '',
  `longitude` decimal(6,3) DEFAULT NULL,
  `latitude` decimal(6,3) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_city_2_code` (`id_state`,`city_2_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__vikappointments_cronjob` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `class` varchar(128) NOT NULL,
  `published` tinyint(1) DEFAULT 0,
  `params` text DEFAULT '',
  `createdon` int(12) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__vikappointments_cronjob_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `status` tinyint(1) DEFAULT 1,
  `mailed` tinyint(1) DEFAULT 0,
  `createdon` int(12) DEFAULT 0,
  `id_cronjob` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `#__vikappointments_subscription` (`name`,`amount`,`type`,`price`,`published`,`trial`) VALUES('One Week', 1, 2, 0.0, 0, 1);
INSERT INTO `#__vikappointments_subscription` (`name`,`amount`,`type`,`price`,`published`,`trial`) VALUES('One Month', 1, 3, 30.0, 0, 0);
INSERT INTO `#__vikappointments_subscription` (`name`,`amount`,`type`,`price`,`published`,`trial`) VALUES('Six Months', 6, 3, 162.0, 0, 0);
INSERT INTO `#__vikappointments_subscription` (`name`,`amount`,`type`,`price`,`published`,`trial`) VALUES('One Year', 1, 4, 292.0, 0, 0);

INSERT INTO `#__vikappointments_custfields` (`name`,`type`,`required`,`ordering`,`rule`) VALUES ('CUSTOMF_NAME','text',1,1,1);
INSERT INTO `#__vikappointments_custfields` (`name`,`type`,`required`,`ordering`,`rule`) VALUES ('CUSTOMF_LNAME','text',1,2,1);
INSERT INTO `#__vikappointments_custfields` (`name`,`type`,`required`,`ordering`,`rule`) VALUES ('CUSTOMF_EMAIL','text',1,3,2);
INSERT INTO `#__vikappointments_custfields` (`name`,`type`,`required`,`ordering`,`rule`) VALUES ('CUSTOMF_PHONE','text',0,4,3);

INSERT INTO `#__vikappointments_countries` (`country_name`, `country_2_code`, `country_3_code`, `phone_prefix`, `published`) VALUES
('Afghanistan', 'AF', 'AFG', '+93', 1),
('Aland', 'AX', 'ALA', '+358 18', 1),
('Albania', 'AL', 'ALB', '+355', 1),
('Algeria', 'DZ', 'DZA', '+213', 1),
('American Samoa', 'AS', 'ASM', '+1 684', 1),
('Andorra', 'AD', 'AND', '+376', 1),
('Angola', 'AO', 'AGO', '+244', 1),
('Anguilla', 'AI', 'AIA', '+1 264', 1),
('Antarctica', 'AQ', 'ATA', '+6721', 1),
('Antigua and Barbuda', 'AG', 'ATG', '+1 268', 1),
('Argentina', 'AR', 'ARG', '+54', 1),
('Armenia', 'AM', 'ARM', '+374', 1),
('Aruba', 'AW', 'ABW', '+297', 1),
('Ascension Island', 'AC', 'ASC', '+247', 1),
('Australia', 'AU', 'AUS', '+61', 1),
('Austria', 'AT', 'AUT', '+43', 1),
('Azerbaijan', 'AZ', 'AZE', '+994', 1),
('Bahamas', 'BS', 'BHS', '+1 242', 1),
('Bahrain', 'BH', 'BHR', '+973', 1),
('Bangladesh', 'BD', 'BGD', '+880', 1),
('Barbados', 'BB', 'BRB', '+1 246', 1),
('Belarus', 'BY', 'BLR', '+375', 1),
('Belgium', 'BE', 'BEL', '+32', 1),
('Belize', 'BZ', 'BLZ', '+501', 1),
('Benin', 'BJ', 'BEN', '+229', 1),
('Bermuda', 'BM', 'BMU', '+1 441', 1),
('Bhutan', 'BT', 'BTN', '+975', 1),
('Bolivia', 'BO', 'BOL', '+591', 1),
('Bosnia and Herzegovina', 'BA', 'BIH', '+387', 1),
('Botswana', 'BW', 'BWA', '+267', 1),
('Bouvet Island', 'BV', 'BVT', '+47', 0),
('Brazil', 'BR', 'BRA', '+55', 1),
('British Indian Ocean Territory', 'IO', 'IOT', '+246', 1),
('British Virgin Islands', 'VG', 'VGB', '+1 284', 1),
('Brunei', 'BN', 'BRN', '+673', 1),
('Bulgaria', 'BG', 'BGR', '+359', 1),
('Burkina Faso', 'BF', 'BFA', '+226', 1),
('Burundi', 'BI', 'BDI', '+257', 1),
('Cambodia', 'KH', 'KHM', '+855', 1),
('Cameroon', 'CM', 'CMR', '+237', 1),
('Canada', 'CA', 'CAN', '+1', 1),
('Cape Verde', 'CV', 'CPV', '+238', 1),
('Cayman Islands', 'KY', 'CYM', '+1 345', 1),
('Central African Republic', 'CF', 'CAF', '+236', 1),
('Chad', 'TD', 'TCD', '+235', 1),
('Chile', 'CL', 'CHL', '+56', 1),
('China', 'CN', 'CHN', '+86', 1),
('Christmas Island', 'CX', 'CXR', '+61 8964', 1),
('Cocos Islands', 'CC', 'CCK', '+61 8962', 1),
('Colombia', 'CO', 'COL', '+57', 1),
('Comoros', 'KM', 'COM', '+269', 1),
('Cook Islands', 'CK', 'COK', '+682', 1),
('Costa Rica', 'CR', 'CRI', '+506', 1),
('Cote d''Ivoire', 'CI', 'CIV', '+225', 1),
('Croatia', 'HR', 'HRV', '+385', 1),
('Cuba', 'CU', 'CUB', '+53', 1),
('Cyprus', 'CY', 'CYP', '+357', 1),
('Czech Republic', 'CZ', 'CZE', '+420', 1),
('Democratic Republic of the Congo', 'CD', 'COD', '+243', 1),
('Denmark', 'DK', 'DNK', '+45', 1),
('Djibouti', 'DJ', 'DJI', '+253', 1),
('Dominica', 'DM', 'DMA', '+1 767', 1),
('Dominican Republic', 'DO', 'DOM', '+1 809', 1),
('East Timor', 'TL', 'TLS', '+670', 1),
('Ecuador', 'EC', 'ECU', '+593', 1),
('Egypt', 'EG', 'EGY', '+20', 1),
('El Salvador', 'SV', 'SLV', '+503', 1),
('Equatorial Guinea', 'GQ', 'GNQ', '+240', 1),
('Eritrea', 'ER', 'ERI', '+291', 1),
('Estonia', 'EE', 'EST', '+372', 1),
('Ethiopia', 'ET', 'ETH', '+251', 1),
('Falkland Islands', 'FK', 'FLK', '+500', 1),
('Faroe Islands', 'FO', 'FRO', '+298', 1),
('Fiji', 'FJ', 'FJI', '+679', 1),
('Finland', 'FI', 'FIN', '+358', 1),
('France', 'FR', 'FRA', '+33', 1),
('French Austral and Antarctic Territories', 'TF', 'ATF', '+33', 1),
('French Guiana', 'GF', 'GUF', '+594', 1),
('French Polynesia', 'PF', 'PYF', '+689', 1),
('Gabon', 'GA', 'GAB', '+241', 1),
('Gambia', 'GM', 'GMB', '+220', 1),
('Georgia', 'GE', 'GEO', '+995', 1),
('Germany', 'DE', 'DEU', '+49', 1),
('Ghana', 'GH', 'GHA', '+233', 1),
('Gibraltar', 'GI', 'GIB', '+350', 1),
('Greece', 'GR', 'GRC', '+30', 1),
('Greenland', 'GL', 'GRL', '+299', 1),
('Grenada', 'GD', 'GRD', '+1 473', 1),
('Guadeloupe', 'GP', 'GLP', '+590', 1),
('Guam', 'GU', 'GUM', '+1 671', 1),
('Guatemala', 'GT', 'GTM', '+502', 1),
('Guernsey', 'GG', 'GGY', '+44 1481', 1),
('Guinea', 'GN', 'GIN', '+224', 1),
('Guinea-Bissau', 'GW', 'GNB', '+245', 1),
('Guyana', 'GY', 'GUY', '+592', 1),
('Haiti', 'HT', 'HTI', '+509', 1),
('Heard and McDonald Islands', 'HM', 'HMD', '+61', 0),
('Honduras', 'HN', 'HND', '+504', 1),
('Hong Kong', 'HK', 'HKG', '+852', 1),
('Hungary', 'HU', 'HUN', '+36', 1),
('Iceland', 'IS', 'ISL', '+354', 1),
('India', 'IN', 'IND', '+91', 1),
('Indonesia', 'ID', 'IDN', '+62', 1),
('Iran', 'IR', 'IRN', '+98', 1),
('Iraq', 'IQ', 'IRQ', '+964', 1),
('Ireland', 'IE', 'IRL', '+353', 1),
('Isle of Man', 'IM', 'IMN', '+44 1624', 1),
('Israel', 'IL', 'ISR', '+972', 1),
('Italy', 'IT', 'ITA', '+39', 1),
('Jamaica', 'JM', 'JAM', '+1 876', 1),
('Japan', 'JP', 'JPN', '+81', 1),
('Jersey', 'JE', 'JEY', '+44 1534', 1),
('Jordan', 'JO', 'JOR', '+962', 1),
('Kazakhstan', 'KZ', 'KAZ', '+7', 1),
('Kenya', 'KE', 'KEN', '+254', 1),
('Kiribati', 'KI', 'KIR', '+686', 1),
('Kosovo', 'KV', 'KV', '+381', 1),
('Kuwait', 'KW', 'KWT', '+965', 1),
('Kyrgyzstan', 'KG', 'KGZ', '+996', 1),
('Laos', 'LA', 'LAO', '+856', 1),
('Latvia', 'LV', 'LVA', '+371', 1),
('Lebanon', 'LB', 'LBN', '+961', 1),
('Lesotho', 'LS', 'LSO', '+266', 1),
('Liberia', 'LR', 'LBR', '+231', 1),
('Libya', 'LY', 'LBY', '+218', 1),
('Liechtenstein', 'LI', 'LIE', '+423', 1),
('Lithuania', 'LT', 'LTU', '+370', 1),
('Luxembourg', 'LU', 'LUX', '+352', 1),
('Macau', 'MO', 'MAC', '+853', 1),
('Macedonia', 'MK', 'MKD', '+389', 1),
('Madagascar', 'MG', 'MDG', '+261', 1),
('Malawi', 'MW', 'MWI', '+265', 1),
('Malaysia', 'MY', 'MYS', '+60', 1),
('Maldives', 'MV', 'MDV', '+960', 1),
('Mali', 'ML', 'MLI', '+223', 1),
('Malta', 'MT', 'MLT', '+356', 1),
('Marshall Islands', 'MH', 'MHL', '+692', 1),
('Martinique', 'MQ', 'MTQ', '+596', 1),
('Mauritania', 'MR', 'MRT', '+222', 1),
('Mauritius', 'MU', 'MUS', '+230', 1),
('Mayotte', 'YT', 'MYT', '+262', 1),
('Mexico', 'MX', 'MEX', '+52', 1),
('Micronesia', 'FM', 'FSM', '+691', 1),
('Moldova', 'MD', 'MDA', '+373', 1),
('Monaco', 'MC', 'MCO', '+377', 1),
('Mongolia', 'MN', 'MNG', '+976', 1),
('Montenegro', 'ME', 'MNE', '+382', 1),
('Montserrat', 'MS', 'MSR', '+1 664', 1),
('Morocco', 'MA', 'MAR', '+212', 1),
('Mozambique', 'MZ', 'MOZ', '+258', 1),
('Myanmar', 'MM', 'MMR', '+95', 1),
('Namibia', 'NA', 'NAM', '+264', 1),
('Nauru', 'NR', 'NRU', '+674', 1),
('Nepal', 'NP', 'NPL', '+977', 1),
('Netherlands', 'NL', 'NLD', '+31', 1),
('Netherlands Antilles', 'AN', 'ANT', '+599', 1),
('New Caledonia', 'NC', 'NCL', '+687', 1),
('New Zealand', 'NZ', 'NZL', '+64', 1),
('Nicaragua', 'NI', 'NIC', '+505', 1),
('Niger', 'NE', 'NER', '+227', 1),
('Nigeria', 'NG', 'NGA', '+234', 1),
('Niue', 'NU', 'NIU', '+683', 1),
('Norfolk Island', 'NF', 'NFK', '+6723', 1),
('North Korea', 'KP', 'PRK', '+850', 1),
('Northern Mariana Islands', 'MP', 'MNP', '+1 670', 1),
('Norway', 'NO', 'NOR', '+47', 1),
('Oman', 'OM', 'OMN', '+968', 1),
('Pakistan', 'PK', 'PAK', '+92', 1),
('Palau', 'PW', 'PLW', '+680', 1),
('Palestine', 'PS', 'PSE', '+970', 1),
('Panama', 'PA', 'PAN', '+507', 1),
('Papua New Guinea', 'PG', 'PNG', '+675', 1),
('Paraguay', 'PY', 'PRY', '+595', 1),
('Peru', 'PE', 'PER', '+51', 1),
('Philippines', 'PH', 'PHL', '+63', 1),
('Pitcairn Islands', 'PN', 'PCN', '+649', 1),
('Poland', 'PL', 'POL', '+48', 1),
('Portugal', 'PT', 'PRT', '+351', 1),
('Puerto Rico', 'PR', 'PRI', '+1 787', 1),
('Qatar', 'QA', 'QAT', '+974', 1),
('Republic of the Congo', 'CG', 'COG', '+242', 1),
('Reunion', 'RE', 'REU', '+262', 1),
('Romania', 'RO', 'ROM', '+40', 1),
('Russia', 'RU', 'RUS', '+7', 1),
('Rwanda', 'RW', 'RWA', '+250', 1),
('Saint Helena', 'SH', 'SHN', '+290', 1),
('Saint Kitts and Nevis', 'KN', 'KNA', '+1 869', 1),
('Saint Lucia', 'LC', 'LCA', '+1 758', 1),
('Saint Pierre and Miquelon', 'PM', 'SPM', '+508', 1),
('Saint Vincent and the Grenadines', 'VC', 'VCT', '+1 784', 1),
('Samoa', 'WS', 'WSM', '+685', 1),
('San Marino', 'SM', 'SMR', '+378', 1),
('Sao Tome and Principe', 'ST', 'STP', '+239', 1),
('Saudi Arabia', 'SA', 'SAU', '+966', 1),
('Senegal', 'SN', 'SEN', '+221', 1),
('Serbia', 'RS', 'SRB', '+381', 1),
('Seychelles', 'SC', 'SYC', '+248', 1),
('Sierra Leone', 'SL', 'SLE', '+232', 1),
('Singapore', 'SG', 'SGP', '+65', 1),
('Sint Maarten', 'SX', 'SXM', '+1 721', 1),
('Slovakia', 'SK', 'SVK', '+421', 1),
('Slovenia', 'SI', 'SVN', '+386', 1),
('Solomon Islands', 'SB', 'SLB', '+677', 1),
('Somalia', 'SO', 'SOM', '+252', 1),
('South Africa', 'ZA', 'ZAF', '+27', 1),
('South Georgia and the South Sandwich Islands', 'GS', 'SGS', '+44', 1),
('South Korea', 'KR', 'KOR', '+82', 1),
('South Sudan', 'SS', 'SSD', '+211', 1),
('Spain', 'ES', 'ESP', '+34', 1),
('Sri Lanka', 'LK', 'LKA', '+94', 1),
('Sudan', 'SD', 'SDN', '+249', 1),
('Suriname', 'SR', 'SUR', '+597', 1),
('Svalbard and Jan Mayen Islands', 'SJ', 'SJM', '+47', 0),
('Swaziland', 'SZ', 'SWZ', '+268', 1),
('Sweden', 'SE', 'SWE', '+46', 1),
('Switzerland', 'CH', 'CHE', '+41', 1),
('Syria', 'SY', 'SYR', '+963', 1),
('Taiwan', 'TW', 'TWN', '+886', 1),
('Tajikistan', 'TJ', 'TJK', '+992', 1),
('Tanzania', 'TZ', 'TZA', '+255', 1),
('Thailand', 'TH', 'THA', '+66', 1),
('Togo', 'TG', 'TGO', '+228', 1),
('Tokelau', 'TK', 'TKL', '+690', 1),
('Tonga', 'TO', 'TON', '+676', 1),
('Trinidad and Tobago', 'TT', 'TTO', '+1 868', 1),
('Tunisia', 'TN', 'TUN', '+216', 1),
('Turkey', 'TR', 'TUR', '+90', 1),
('Turkmenistan', 'TM', 'TKM', '+993', 1),
('Turks and Caicos Islands', 'TC', 'TCA', '+1 649', 1),
('Tuvalu', 'TV', 'TUV', '+688', 1),
('U.S. Virgin Islands', 'VI', 'VIR', '+1 340', 1),
('Uganda', 'UG', 'UGA', '+256', 1),
('Ukraine', 'UA', 'UKR', '+380', 1),
('United Arab Emirates', 'AE', 'ARE', '+971', 1),
('United Kingdom', 'GB', 'GBR', '+44', 1),
('United States', 'US', 'USA', '+1', 1),
('Uruguay', 'UY', 'URY', '+598', 1),
('Uzbekistan', 'UZ', 'UZB', '+998', 1),
('Vanuatu', 'VU', 'VUT', '+678', 1),
('Vatican City', 'VA', 'VAT', '+379', 1),
('Venezuela', 'VE', 'VEN', '+58', 1),
('Vietnam', 'VN', 'VNM', '+84', 1),
('Wallis and Futuna', 'WF', 'WLF', '+681', 1),
('Western Sahara', 'EH', 'ESH', '+212 28', 1),
('Yemen', 'YE', 'YEM', '+967', 1),
('Zambia', 'ZM', 'ZMB', '+260', 1),
('Zimbabwe', 'ZW', 'ZWE', '+263', 1);

INSERT INTO `#__vikappointments_states` (`id_country`, `state_name`, `state_2_code`, `state_3_code`, `published`) VALUES
(236, 'Alabama', 'AL', 'ALA', 1),
(236, 'Alaska', 'AK', 'ALK', 1),
(236, 'Arizona', 'AZ', 'ARZ', 1),
(236, 'Arkansas', 'AR', 'ARK', 1),
(236, 'California', 'CA', 'CAL', 1),
(236, 'Colorado', 'CO', 'COL', 1),
(236, 'Connecticut', 'CT', 'CCT', 1),
(236, 'Delaware', 'DE', 'DEL', 1),
(236, 'District Of Columbia', 'DC', 'DOC', 1),
(236, 'Florida', 'FL', 'FLO', 1),
(236, 'Georgia', 'GA', 'GEA', 1),
(236, 'Hawaii', 'HI', 'HWI', 1),
(236, 'Idaho', 'ID', 'IDA', 1),
(236, 'Illinois', 'IL', 'ILL', 1),
(236, 'Indiana', 'IN', 'IND', 1),
(236, 'Iowa', 'IA', 'IOA', 1),
(236, 'Kansas', 'KS', 'KAS', 1),
(236, 'Kentucky', 'KY', 'KTY', 1),
(236, 'Louisiana', 'LA', 'LOA', 1),
(236, 'Maine', 'ME', 'MAI', 1),
(236, 'Maryland', 'MD', 'MLD', 1),
(236, 'Massachusetts', 'MA', 'MSA', 1),
(236, 'Michigan', 'MI', 'MIC', 1),
(236, 'Minnesota', 'MN', 'MIN', 1),
(236, 'Mississippi', 'MS', 'MIS', 1),
(236, 'Missouri', 'MO', 'MIO', 1),
(236, 'Montana', 'MT', 'MOT', 1),
(236, 'Nebraska', 'NE', 'NEB', 1),
(236, 'Nevada', 'NV', 'NEV', 1),
(236, 'New Hampshire', 'NH', 'NEH', 1),
(236, 'New Jersey', 'NJ', 'NEJ', 1),
(236, 'New Mexico', 'NM', 'NEM', 1),
(236, 'New York', 'NY', 'NEY', 1),
(236, 'North Carolina', 'NC', 'NOC', 1),
(236, 'North Dakota', 'ND', 'NOD', 1),
(236, 'Ohio', 'OH', 'OHI', 1),
(236, 'Oklahoma', 'OK', 'OKL', 1),
(236, 'Oregon', 'OR', 'ORN', 1),
(236, 'Pennsylvania', 'PA', 'PEA', 1),
(236, 'Rhode Island', 'RI', 'RHI', 1),
(236, 'South Carolina', 'SC', 'SOC', 1),
(236, 'South Dakota', 'SD', 'SOD', 1),
(236, 'Tennessee', 'TN', 'TEN', 1),
(236, 'Texas', 'TX', 'TXS', 1),
(236, 'Utah', 'UT', 'UTA', 1),
(236, 'Vermont', 'VT', 'VMT', 1),
(236, 'Virginia', 'VA', 'VIA', 1),
(236, 'Washington', 'WA', 'WAS', 1),
(236, 'West Virginia', 'WV', 'WEV', 1),
(236, 'Wisconsin', 'WI', 'WIS', 1),
(236, 'Wyoming', 'WY', 'WYO', 1),
(41, 'Alberta', 'AB', 'ALB', 1),
(41, 'British Columbia', 'BC', 'BRC', 1),
(41, 'Manitoba', 'MB', 'MAB', 1),
(41, 'New Brunswick', 'NB', 'NEB', 1),
(41, 'Newfoundland and Labrador', 'NL', 'NFL', 1),
(41, 'Northwest Territories', 'NT', 'NWT', 1),
(41, 'Nova Scotia', 'NS', 'NOS', 1),
(41, 'Nunavut', 'NU', 'NUT', 1),
(41, 'Ontario', 'ON', 'ONT', 1),
(41, 'Prince Edward Island', 'PE', 'PEI', 1),
(41, 'Quebec', 'QC', 'QEC', 1),
(41, 'Saskatchewan', 'SK', 'SAK', 1),
(41, 'Yukon', 'YT', 'YUT', 1),
(235, 'England', 'EN', 'ENG', 1),
(235, 'Northern Ireland', 'NI', 'NOI', 1),
(235, 'Scotland', 'SD', 'SCO', 1),
(235, 'Wales', 'WS', 'WLS', 1),
(15, 'Australian Capital Territory', 'AC', 'ACT', 1),
(15, 'New South Wales', 'NS', 'NSW', 1),
(15, 'Northern Territory', 'NT', 'NOT', 1),
(15, 'Queensland', 'QL', 'QLD', 1),
(15, 'South Australia', 'SA', 'SOA', 1),
(15, 'Tasmania', 'TS', 'TAS', 1),
(15, 'Victoria', 'VI', 'VIC', 1),
(15, 'Western Australia', 'WA', 'WEA', 1),
(142, 'Aguascalientes', 'AG', 'AGS', 1),
(142, 'Baja California Norte', 'BN', 'BCN', 1),
(142, 'Baja California Sur', 'BS', 'BCS', 1),
(142, 'Campeche', 'CA', 'CAM', 1),
(142, 'Chiapas', 'CS', 'CHI', 1),
(142, 'Chihuahua', 'CH', 'CHA', 1),
(142, 'Coahuila', 'CO', 'COA', 1),
(142, 'Colima', 'CM', 'COL', 1),
(142, 'Distrito Federal', 'DF', 'DFM', 1),
(142, 'Durango', 'DO', 'DGO', 1),
(142, 'Guanajuato', 'GO', 'GTO', 1),
(142, 'Guerrero', 'GU', 'GRO', 1),
(142, 'Hidalgo', 'HI', 'HGO', 1),
(142, 'Jalisco', 'JA', 'JAL', 1),
(142, 'M', 'EM', 'EDM', 1),
(142, 'Michoac', 'MI', 'MCN', 1),
(142, 'Morelos', 'MO', 'MOR', 1),
(142, 'Nayarit', 'NY', 'NAY', 1),
(142, 'Nuevo Le', 'NL', 'NUL', 1),
(142, 'Oaxaca', 'OA', 'OAX', 1),
(142, 'Puebla', 'PU', 'PUE', 1),
(142, 'Quer', 'QU', 'QRO', 1),
(142, 'Quintana Roo', 'QR', 'QUR', 1),
(142, 'San Luis Potos', 'SP', 'SLP', 1),
(142, 'Sinaloa', 'SI', 'SIN', 1),
(142, 'Sonora', 'SO', 'SON', 1),
(142, 'Tabasco', 'TA', 'TAB', 1),
(142, 'Tamaulipas', 'TM', 'TAM', 1),
(142, 'Tlaxcala', 'TX', 'TLX', 1),
(142, 'Veracruz', 'VZ', 'VER', 1),
(142, 'Yucat', 'YU', 'YUC', 1),
(142, 'Zacatecas', 'ZA', 'ZAC', 1),
(32, 'Acre', 'AC', 'ACR', 1),
(32, 'Alagoas', 'AL', 'ALG', 1),
(32, 'Amap', 'AP', 'AMP', 1),
(32, 'Amazonas', 'AM', 'AMZ', 1),
(32, 'Bah', 'BA', 'BAH', 1),
(32, 'Cear', 'CE', 'CEA', 1),
(32, 'Distrito Federal', 'DF', 'DFB', 1),
(32, 'Espirito Santo', 'ES', 'ESS', 1),
(32, 'Goi', 'GO', 'GOI', 1),
(32, 'Maranh', 'MA', 'MAR', 1),
(32, 'Mato Grosso', 'MT', 'MAT', 1),
(32, 'Mato Grosso do Sul', 'MS', 'MGS', 1),
(32, 'Minas Gera', 'MG', 'MIG', 1),
(32, 'Paran', 'PR', 'PAR', 1),
(32, 'Para', 'PB', 'PRB', 1),
(32, 'Par', 'PA', 'PAB', 1),
(32, 'Pernambuco', 'PE', 'PER', 1),
(32, 'Piau', 'PI', 'PIA', 1),
(32, 'Rio Grande do Norte', 'RN', 'RGN', 1),
(32, 'Rio Grande do Sul', 'RS', 'RGS', 1),
(32, 'Rio de Janeiro', 'RJ', 'RDJ', 1),
(32, 'Rond', 'RO', 'RON', 1),
(32, 'Roraima', 'RR', 'ROR', 1),
(32, 'Santa Catarina', 'SC', 'SAC', 1),
(32, 'Sergipe', 'SE', 'SER', 1),
(32, 'S', 'SP', 'SAP', 1),
(32, 'Tocantins', 'TO', 'TOC', 1),
(47, 'Anhui', '34', 'ANH', 1),
(47, 'Beijing', '11', 'BEI', 1),
(47, 'Chongqing', '50', 'CHO', 1),
(47, 'Fujian', '35', 'FUJ', 1),
(47, 'Gansu', '62', 'GAN', 1),
(47, 'Guangdong', '44', 'GUA', 1),
(47, 'Guangxi Zhuang', '45', 'GUZ', 1),
(47, 'Guizhou', '52', 'GUI', 1),
(47, 'Hainan', '46', 'HAI', 1),
(47, 'Hebei', '13', 'HEB', 1),
(47, 'Heilongjiang', '23', 'HEI', 1),
(47, 'Henan', '41', 'HEN', 1),
(47, 'Hubei', '42', 'HUB', 1),
(47, 'Hunan', '43', 'HUN', 1),
(47, 'Jiangsu', '32', 'JIA', 1),
(47, 'Jiangxi', '36', 'JIX', 1),
(47, 'Jilin', '22', 'JIL', 1),
(47, 'Liaoning', '21', 'LIA', 1),
(47, 'Nei Mongol', '15', 'NML', 1),
(47, 'Ningxia Hui', '64', 'NIH', 1),
(47, 'Qinghai', '63', 'QIN', 1),
(47, 'Shandong', '37', 'SNG', 1),
(47, 'Shanghai', '31', 'SHH', 1),
(47, 'Shaanxi', '61', 'SHX', 1),
(47, 'Sichuan', '51', 'SIC', 1),
(47, 'Tianjin', '12', 'TIA', 1),
(47, 'Xinjiang Uygur', '65', 'XIU', 1),
(47, 'Xizang', '54', 'XIZ', 1),
(47, 'Yunnan', '53', 'YUN', 1),
(47, 'Zhejiang', '33', 'ZHE', 1),
(108, 'Israel', 'IL', 'ISL', 1),
(108, 'Gaza Strip', 'GZ', 'GZS', 1),
(108, 'West Bank', 'WB', 'WBK', 1),
(156, 'St. Maarten', 'SM', 'STM', 1),
(156, 'Bonaire', 'BN', 'BNR', 1),
(156, 'Curacao', 'CR', 'CUR', 1),
(183, 'Alba', 'AB', 'ABA', 1),
(183, 'Arad', 'AR', 'ARD', 1),
(183, 'Arges', 'AG', 'ARG', 1),
(183, 'Bacau', 'BC', 'BAC', 1),
(183, 'Bihor', 'BH', 'BIH', 1),
(183, 'Bistrita-Nasaud', 'BN', 'BIS', 1),
(183, 'Botosani', 'BT', 'BOT', 1),
(183, 'Braila', 'BR', 'BRL', 1),
(183, 'Brasov', 'BV', 'BRA', 1),
(183, 'Bucuresti', 'B', 'BUC', 1),
(183, 'Buzau', 'BZ', 'BUZ', 1),
(183, 'Calarasi', 'CL', 'CAL', 1),
(183, 'Caras Severin', 'CS', 'CRS', 1),
(183, 'Cluj', 'CJ', 'CLJ', 1),
(183, 'Constanta', 'CT', 'CST', 1),
(183, 'Covasna', 'CV', 'COV', 1),
(183, 'Dambovita', 'DB', 'DAM', 1),
(183, 'Dolj', 'DJ', 'DLJ', 1),
(183, 'Galati', 'GL', 'GAL', 1),
(183, 'Giurgiu', 'GR', 'GIU', 1),
(183, 'Gorj', 'GJ', 'GOR', 1),
(183, 'Hargita', 'HR', 'HRG', 1),
(183, 'Hunedoara', 'HD', 'HUN', 1),
(183, 'Ialomita', 'IL', 'IAL', 1),
(183, 'Iasi', 'IS', 'IAS', 1),
(183, 'Ilfov', 'IF', 'ILF', 1),
(183, 'Maramures', 'MM', 'MAR', 1),
(183, 'Mehedinti', 'MH', 'MEH', 1),
(183, 'Mures', 'MS', 'MUR', 1),
(183, 'Neamt', 'NT', 'NEM', 1),
(183, 'Olt', 'OT', 'OLT', 1),
(183, 'Prahova', 'PH', 'PRA', 1),
(183, 'Salaj', 'SJ', 'SAL', 1),
(183, 'Satu Mare', 'SM', 'SAT', 1),
(183, 'Sibiu', 'SB', 'SIB', 1),
(183, 'Suceava', 'SV', 'SUC', 1),
(183, 'Teleorman', 'TR', 'TEL', 1),
(183, 'Timis', 'TM', 'TIM', 1),
(183, 'Tulcea', 'TL', 'TUL', 1),
(183, 'Valcea', 'VL', 'VAL', 1),
(183, 'Vaslui', 'VS', 'VAS', 1),
(183, 'Vrancea', 'VN', 'VRA', 1),
(109, 'Agrigento', 'AG', 'AGR', 1),
(109, 'Alessandria', 'AL', 'ALE', 1),
(109, 'Ancona', 'AN', 'ANC', 1),
(109, 'Aosta', 'AO', 'AOS', 1),
(109, 'Arezzo', 'AR', 'ARE', 1),
(109, 'Ascoli Piceno', 'AP', 'API', 1),
(109, 'Asti', 'AT', 'AST', 1),
(109, 'Avellino', 'AV', 'AVE', 1),
(109, 'Bari', 'BA', 'BAR', 1),
(109, 'Belluno', 'BL', 'BEL', 1),
(109, 'Benevento', 'BN', 'BEN', 1),
(109, 'Bergamo', 'BG', 'BEG', 1),
(109, 'Biella', 'BI', 'BIE', 1),
(109, 'Bologna', 'BO', 'BOL', 1),
(109, 'Bolzano', 'BZ', 'BOZ', 1),
(109, 'Brescia', 'BS', 'BRE', 1),
(109, 'Brindisi', 'BR', 'BRI', 1),
(109, 'Cagliari', 'CA', 'CAG', 1),
(109, 'Caltanissetta', 'CL', 'CAL', 1),
(109, 'Campobasso', 'CB', 'CBO', 1),
(109, 'Carbonia-Iglesias', 'CI', 'CAR', 1),
(109, 'Caserta', 'CE', 'CAS', 1),
(109, 'Catania', 'CT', 'CAT', 1),
(109, 'Catanzaro', 'CZ', 'CTZ', 1),
(109, 'Chieti', 'CH', 'CHI', 1),
(109, 'Como', 'CO', 'COM', 1),
(109, 'Cosenza', 'CS', 'COS', 1),
(109, 'Cremona', 'CR', 'CRE', 1),
(109, 'Crotone', 'KR', 'CRO', 1),
(109, 'Cuneo', 'CN', 'CUN', 1),
(109, 'Enna', 'EN', 'ENN', 1),
(109, 'Ferrara', 'FE', 'FER', 1),
(109, 'Firenze', 'FI', 'FIR', 1),
(109, 'Foggia', 'FG', 'FOG', 1),
(109, 'Forli-Cesena', 'FC', 'FOC', 1),
(109, 'Frosinone', 'FR', 'FRO', 1),
(109, 'Genova', 'GE', 'GEN', 1),
(109, 'Gorizia', 'GO', 'GOR', 1),
(109, 'Grosseto', 'GR', 'GRO', 1),
(109, 'Imperia', 'IM', 'IMP', 1),
(109, 'Isernia', 'IS', 'ISE', 1),
(109, 'L\'Aquila', 'AQ', 'AQU', 1),
(109, 'La Spezia', 'SP', 'LAS', 1),
(109, 'Latina', 'LT', 'LAT', 1),
(109, 'Lecce', 'LE', 'LEC', 1),
(109, 'Lecco', 'LC', 'LCC', 1),
(109, 'Livorno', 'LI', 'LIV', 1),
(109, 'Lodi', 'LO', 'LOD', 1),
(109, 'Lucca', 'LU', 'LUC', 1),
(109, 'Macerata', 'MC', 'MAC', 1),
(109, 'Mantova', 'MN', 'MAN', 1),
(109, 'Massa-Carrara', 'MS', 'MAS', 1),
(109, 'Matera', 'MT', 'MAA', 1),
(109, 'Medio Campidano', 'VS', 'MED', 1),
(109, 'Messina', 'ME', 'MES', 1),
(109, 'Milano', 'MI', 'MIL', 1),
(109, 'Modena', 'MO', 'MOD', 1),
(109, 'Napoli', 'NA', 'NAP', 1),
(109, 'Novara', 'NO', 'NOV', 1),
(109, 'Nuoro', 'NU', 'NUR', 1),
(109, 'Ogliastra', 'OG', 'OGL', 1),
(109, 'Olbia-Tempio', 'OT', 'OLB', 1),
(109, 'Oristano', 'OR', 'ORI', 1),
(109, 'Padova', 'PD', 'PDA', 1),
(109, 'Palermo', 'PA', 'PAL', 1),
(109, 'Parma', 'PR', 'PAA', 1),
(109, 'Pavia', 'PV', 'PAV', 1),
(109, 'Perugia', 'PG', 'PER', 1),
(109, 'Pesaro e Urbino', 'PU', 'PES', 1),
(109, 'Pescara', 'PE', 'PSC', 1),
(109, 'Piacenza', 'PC', 'PIA', 1),
(109, 'Pisa', 'PI', 'PIS', 1),
(109, 'Pistoia', 'PT', 'PIT', 1),
(109, 'Pordenone', 'PN', 'POR', 1),
(109, 'Potenza', 'PZ', 'PTZ', 1),
(109, 'Prato', 'PO', 'PRA', 1),
(109, 'Ragusa', 'RG', 'RAG', 1),
(109, 'Ravenna', 'RA', 'RAV', 1),
(109, 'Reggio Calabria', 'RC', 'REG', 1),
(109, 'Reggio Emilia', 'RE', 'REE', 1),
(109, 'Rieti', 'RI', 'RIE', 1),
(109, 'Rimini', 'RN', 'RIM', 1),
(109, 'Roma', 'RM', 'ROM', 1),
(109, 'Rovigo', 'RO', 'ROV', 1),
(109, 'Salerno', 'SA', 'SAL', 1),
(109, 'Sassari', 'SS', 'SAS', 1),
(109, 'Savona', 'SV', 'SAV', 1),
(109, 'Siena', 'SI', 'SIE', 1),
(109, 'Siracusa', 'SR', 'SIR', 1),
(109, 'Sondrio', 'SO', 'SOO', 1),
(109, 'Taranto', 'TA', 'TAR', 1),
(109, 'Teramo', 'TE', 'TER', 1),
(109, 'Terni', 'TR', 'TRN', 1),
(109, 'Torino', 'TO', 'TOR', 1),
(109, 'Trapani', 'TP', 'TRA', 1),
(109, 'Trento', 'TN', 'TRE', 1),
(109, 'Treviso', 'TV', 'TRV', 1),
(109, 'Trieste', 'TS', 'TRI', 1),
(109, 'Udine', 'UD', 'UDI', 1),
(109, 'Varese', 'VA', 'VAR', 1),
(109, 'Venezia', 'VE', 'VEN', 1),
(109, 'Verbano Cusio Ossola', 'VB', 'VCO', 1),
(109, 'Vercelli', 'VC', 'VER', 1),
(109, 'Verona', 'VR', 'VRN', 1),
(109, 'Vibo Valenzia', 'VV', 'VIV', 1),
(109, 'Vicenza', 'VI', 'VII', 1),
(109, 'Viterbo', 'VT', 'VIT', 1),
(209, 'A Coru', '15', 'ACO', 1),
(209, 'Alava', '01', 'ALA', 1),
(209, 'Albacete', '02', 'ALB', 1),
(209, 'Alicante', '03', 'ALI', 1),
(209, 'Almeria', '04', 'ALM', 1),
(209, 'Asturias', '33', 'AST', 1),
(209, 'Avila', '05', 'AVI', 1),
(209, 'Badajoz', '06', 'BAD', 1),
(209, 'Baleares', '07', 'BAL', 1),
(209, 'Barcelona', '08', 'BAR', 1),
(209, 'Burgos', '09', 'BUR', 1),
(209, 'Caceres', '10', 'CAC', 1),
(209, 'Cadiz', '11', 'CAD', 1),
(209, 'Cantabria', '39', 'CAN', 1),
(209, 'Castellon', '12', 'CAS', 1),
(209, 'Ceuta', '51', 'CEU', 1),
(209, 'Ciudad Real', '13', 'CIU', 1),
(209, 'Cordoba', '14', 'COR', 1),
(209, 'Cuenca', '16', 'CUE', 1),
(209, 'Girona', '17', 'GIR', 1),
(209, 'Granada', '18', 'GRA', 1),
(209, 'Guadalajara', '19', 'GUA', 1),
(209, 'Guipuzcoa', '20', 'GUI', 1),
(209, 'Huelva', '21', 'HUL', 1),
(209, 'Huesca', '22', 'HUS', 1),
(209, 'Jaen', '23', 'JAE', 1),
(209, 'La Rioja', '26', 'LRI', 1),
(209, 'Las Palmas', '35', 'LPA', 1),
(209, 'Leon', '24', 'LEO', 1),
(209, 'Lleida', '25', 'LLE', 1),
(209, 'Lugo', '27', 'LUG', 1),
(209, 'Madrid', '28', 'MAD', 1),
(209, 'Malaga', '29', 'MAL', 1),
(209, 'Melilla', '52', 'MEL', 1),
(209, 'Murcia', '30', 'MUR', 1),
(209, 'Navarra', '31', 'NAV', 1),
(209, 'Ourense', '32', 'OUR', 1),
(209, 'Palencia', '34', 'PAL', 1),
(209, 'Pontevedra', '36', 'PON', 1),
(209, 'Salamanca', '37', 'SAL', 1),
(209, 'Santa Cruz de Tenerife', '38', 'SCT', 1),
(209, 'Segovia', '40', 'SEG', 1),
(209, 'Sevilla', '41', 'SEV', 1),
(209, 'Soria', '42', 'SOR', 1),
(209, 'Tarragona', '43', 'TAR', 1),
(209, 'Teruel', '44', 'TER', 1),
(209, 'Toledo', '45', 'TOL', 1),
(209, 'Valencia', '46', 'VAL', 1),
(209, 'Valladolid', '47', 'VLL', 1),
(209, 'Vizcaya', '48', 'VIZ', 1),
(209, 'Zamora', '49', 'ZAM', 1),
(209, 'Zaragoza', '50', 'ZAR', 1),
(11, 'Buenos Aires', 'BA', 'BAS', 1),
(11, 'Ciudad Autonoma De Buenos Aires', 'CB', 'CBA', 1),
(11, 'Catamarca', 'CA', 'CAT', 1),
(11, 'Chaco', 'CH', 'CHO', 1),
(11, 'Chubut', 'CT', 'CTT', 1),
(11, 'Cordoba', 'CO', 'COD', 1),
(11, 'Corrientes', 'CR', 'CRI', 1),
(11, 'Entre Rios', 'ER', 'ERS', 1),
(11, 'Formosa', 'FR', 'FRM', 1),
(11, 'Jujuy', 'JU', 'JUJ', 1),
(11, 'La Pampa', 'LP', 'LPM', 1),
(11, 'La Rioja', 'LR', 'LRI', 1),
(11, 'Mendoza', 'ME', 'MED', 1),
(11, 'Misiones', 'MI', 'MIS', 1),
(11, 'Neuquen', 'NQ', 'NQU', 1),
(11, 'Rio Negro', 'RN', 'RNG', 1),
(11, 'Salta', 'SA', 'SAL', 1),
(11, 'San Juan', 'SJ', 'SJN', 1),
(11, 'San Luis', 'SL', 'SLU', 1),
(11, 'Santa Cruz', 'SC', 'SCZ', 1),
(11, 'Santa Fe', 'SF', 'SFE', 1),
(11, 'Santiago Del Estero', 'SE', 'SEN', 1),
(11, 'Tierra Del Fuego', 'TF', 'TFE', 1),
(11, 'Tucuman', 'TU', 'TUC', 1),
(12, 'Aragatsotn', 'AG', 'ARG', 1),
(12, 'Ararat', 'AR', 'ARR', 1),
(12, 'Armavir', 'AV', 'ARM', 1),
(12, 'Gegharkunik', 'GR', 'GEG', 1),
(12, 'Kotayk', 'KT', 'KOT', 1),
(12, 'Lori', 'LO', 'LOR', 1),
(12, 'Shirak', 'SH', 'SHI', 1),
(12, 'Syunik', 'SU', 'SYU', 1),
(12, 'Tavush', 'TV', 'TAV', 1),
(12, 'Vayots-Dzor', 'VD', 'VAD', 1),
(12, 'Yerevan', 'ER', 'YER', 1),
(102, 'Andaman & Nicobar Islands', 'AI', 'ANI', 1),
(102, 'Andhra Pradesh', 'AN', 'AND', 1),
(102, 'Arunachal Pradesh', 'AR', 'ARU', 1),
(102, 'Assam', 'AS', 'ASS', 1),
(102, 'Bihar', 'BI', 'BIH', 1),
(102, 'Chandigarh', 'CA', 'CHA', 1),
(102, 'Chhatisgarh', 'CH', 'CHH', 1),
(102, 'Dadra & Nagar Haveli', 'DD', 'DAD', 1),
(102, 'Daman & Diu', 'DA', 'DAM', 1),
(102, 'Delhi', 'DE', 'DEL', 1),
(102, 'Goa', 'GO', 'GOA', 1),
(102, 'Gujarat', 'GU', 'GUJ', 1),
(102, 'Haryana', 'HA', 'HAR', 1),
(102, 'Himachal Pradesh', 'HI', 'HIM', 1),
(102, 'Jammu & Kashmir', 'JA', 'JAM', 1),
(102, 'Jharkhand', 'JH', 'JHA', 1),
(102, 'Karnataka', 'KA', 'KAR', 1),
(102, 'Kerala', 'KE', 'KER', 1),
(102, 'Lakshadweep', 'LA', 'LAK', 1),
(102, 'Madhya Pradesh', 'MD', 'MAD', 1),
(102, 'Maharashtra', 'MH', 'MAH', 1),
(102, 'Manipur', 'MN', 'MAN', 1),
(102, 'Meghalaya', 'ME', 'MEG', 1),
(102, 'Mizoram', 'MI', 'MIZ', 1),
(102, 'Nagaland', 'NA', 'NAG', 1),
(102, 'Orissa', 'OR', 'ORI', 1),
(102, 'Pondicherry', 'PO', 'PON', 1),
(102, 'Punjab', 'PU', 'PUN', 1),
(102, 'Rajasthan', 'RA', 'RAJ', 1),
(102, 'Sikkim', 'SI', 'SIK', 1),
(102, 'Tamil Nadu', 'TA', 'TAM', 1),
(102, 'Tripura', 'TR', 'TRI', 1),
(102, 'Uttaranchal', 'UA', 'UAR', 1),
(102, 'Uttar Pradesh', 'UT', 'UTT', 1),
(102, 'West Bengal', 'WE', 'WES', 1),
(104, 'Ahmadi va Kohkiluyeh', 'BO', 'BOK', 1),
(104, 'Ardabil', 'AR', 'ARD', 1),
(104, 'Azarbayjan-e Gharbi', 'AG', 'AZG', 1),
(104, 'Azarbayjan-e Sharqi', 'AS', 'AZS', 1),
(104, 'Bushehr', 'BU', 'BUS', 1),
(104, 'Chaharmahal va Bakhtiari', 'CM', 'CMB', 1),
(104, 'Esfahan', 'ES', 'ESF', 1),
(104, 'Fars', 'FA', 'FAR', 1),
(104, 'Gilan', 'GI', 'GIL', 1),
(104, 'Gorgan', 'GO', 'GOR', 1),
(104, 'Hamadan', 'HA', 'HAM', 1),
(104, 'Hormozgan', 'HO', 'HOR', 1),
(104, 'Ilam', 'IL', 'ILA', 1),
(104, 'Kerman', 'KE', 'KER', 1),
(104, 'Kermanshah', 'BA', 'BAK', 1),
(104, 'Khorasan-e Junoubi', 'KJ', 'KHJ', 1),
(104, 'Khorasan-e Razavi', 'KR', 'KHR', 1),
(104, 'Khorasan-e Shomali', 'KS', 'KHS', 1),
(104, 'Khuzestan', 'KH', 'KHU', 1),
(104, 'Kordestan', 'KO', 'KOR', 1),
(104, 'Lorestan', 'LO', 'LOR', 1),
(104, 'Markazi', 'MR', 'MAR', 1),
(104, 'Mazandaran', 'MZ', 'MAZ', 1),
(104, 'Qazvin', 'QA', 'QAS', 1),
(104, 'Qom', 'QO', 'QOM', 1),
(104, 'Semnan', 'SE', 'SEM', 1),
(104, 'Sistan va Baluchestan', 'SB', 'SBA', 1),
(104, 'Tehran', 'TE', 'TEH', 1),
(104, 'Yazd', 'YA', 'YAZ', 1),
(104, 'Zanjan', 'ZA', 'ZAN', 1),
(86, 'ΛΕΥΚΑΔΑΣ', 'ΛΕ', 'ΛΕΥ', 1),
(86, 'ΛΑΡΙΣΑΣ', 'ΛΡ', 'ΛΑΡ', 1),
(86, 'ΑΡΚΑΔΙΑΣ', 'ΑΚ', 'ΑΡΚ', 1),
(86, 'ΑΡΓΟΛΙΔΑΣ', 'ΑΡ', 'ΑΡΓ', 1),
(86, 'ΛΑΣΙΘΙΟΥ', 'ΛΑ', 'ΛΑΣ', 1),
(86, 'ΛΕΣΒΟΥ', 'ΛΣ', 'ΛΕΣ', 1),
(86, 'ΚΥΚΛΑΔΩΝ', 'ΚΥ', 'ΚΥΚ', 1),
(86, 'ΑΙΤΩΛΟΑΚΑΡΝΑΝΙΑΣ', 'ΑΙ', 'ΑΙΤ', 1),
(86, 'ΚΟΡΙΝΘΙΑΣ', 'ΚΟ', 'ΚΟΡ', 1),
(86, 'ΛΑΚΩΝΙΑΣ', 'ΛK', 'ΛΑΚ', 1),
(86, 'ΗΜΑΘΙΑΣ', 'ΗΜ', 'ΗΜΑ', 1),
(86, 'ΗΡΑΚΛΕΙΟΥ', 'ΗΡ', 'ΗΡΑ', 1),
(86, 'ΘΕΣΠΡΩΤΙΑΣ', 'ΘΠ', 'ΘΕΠ', 1),
(86, 'ΘΕΣΣΑΛΟΝΙΚΗΣ', 'ΘΕ', 'ΘΕΣ', 1),
(86, 'ΙΩΑΝΝΙΝΩΝ', 'ΙΩ', 'ΙΩΑ', 1),
(86, 'ΚΑΒΑΛΑΣ', 'ΚΒ', 'ΚΑΒ', 1),
(86, 'ΚΑΡΔΙΤΣΑΣ', 'ΚΡ', 'ΚΑΡ', 1),
(86, 'ΚΑΣΤΟΡΙΑΣ', 'ΚΣ', 'ΚΑΣ', 1),
(86, 'ΚΕΡΚΥΡΑΣ', 'ΚΕ', 'ΚΕΡ', 1),
(86, 'ΚΕΦΑΛΛΗΝΙΑΣ', 'ΚΦ', 'ΚΕΦ', 1),
(86, 'ΚΙΛΚΙΣ', 'ΚΙ', 'ΚΙΛ', 1),
(86, 'ΚΟΖΑΝΗΣ', 'ΚZ', 'ΚΟΖ', 1),
(86, 'ΑΧΑΪΑΣ', 'ΑΧ', 'ΑΧΑ', 1),
(86, 'ΒΟΙΩΤΙΑΣ', 'ΒΟ', 'ΒΟΙ', 1),
(86, 'ΓΡΕΒΕΝΩΝ', 'ΓΡ', 'ΓΡΕ', 1),
(86, 'ΔΡΑΜΑΣ', 'ΔΡ', 'ΔΡΑ', 1),
(86, 'ΔΩΔΕΚΑΝΗΣΟΥ', 'ΔΩ', 'ΔΩΔ', 1),
(86, 'ΕΒΡΟΥ', 'ΕΒ', 'ΕΒΡ', 1),
(86, 'ΕΥΒΟΙΑΣ', 'ΕΥ', 'ΕΥΒ', 1),
(86, 'ΕΥΡΥΤΑΝΙΑΣ', 'ΕΡ', 'ΕΥΡ', 1),
(86, 'ΖΑΚΥΝΘΟΥ', 'ΖΑ', 'ΖΑΚ', 1),
(86, 'ΗΛΕΙΑΣ', 'ΗΛ', 'ΗΛΕ', 1),
(86, 'ΑΡΤΑΣ', 'ΑΑ', 'ΑΡΤ', 1),
(86, 'ΑΤΤΙΚΗΣ', 'ΑΤ', 'ΑΤΤ', 1),
(86, 'ΜΑΓΝΗΣΙΑΣ', 'ΜΑ', 'ΜΑΓ', 1),
(86, 'ΜΕΣΣΗΝΙΑΣ', 'ΜΕ', 'ΜΕΣ', 1),
(86, 'ΞΑΝΘΗΣ', 'ΞΑ', 'ΞΑΝ', 1),
(86, 'ΠΕΛΛΗΣ', 'ΠΕ', 'ΠΕΛ', 1),
(86, 'ΠΙΕΡΙΑΣ', 'ΠΙ', 'ΠΙΕ', 1),
(86, 'ΠΡΕΒΕΖΑΣ', 'ΠΡ', 'ΠΡΕ', 1),
(86, 'ΡΕΘΥΜΝΗΣ', 'ΡΕ', 'ΡΕΘ', 1),
(86, 'ΡΟΔΟΠΗΣ', 'ΡΟ', 'ΡΟΔ', 1),
(86, 'ΣΑΜΟΥ', 'ΣΑ', 'ΣΑΜ', 1),
(86, 'ΣΕΡΡΩΝ', 'ΣΕ', 'ΣΕΡ', 1),
(86, 'ΤΡΙΚΑΛΩΝ', 'ΤΡ', 'ΤΡΙ', 1),
(86, 'ΦΘΙΩΤΙΔΑΣ', 'ΦΘ', 'ΦΘΙ', 1),
(86, 'ΦΛΩΡΙΝΑΣ', 'ΦΛ', 'ΦΛΩ', 1),
(86, 'ΦΩΚΙΔΑΣ', 'ΦΩ', 'ΦΩΚ', 1),
(86, 'ΧΑΛΚΙΔΙΚΗΣ', 'ΧΑ', 'ΧΑΛ', 1),
(86, 'ΧΑΝΙΩΝ', 'ΧΝ', 'ΧΑΝ', 1),
(86, 'ΧΙΟΥ', 'ΧΙ', 'ΧΙΟ', 1);

INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('agencyname', '');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('adminemail', '');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('senderemail', '');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('companylogo', '');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('dateformat', 'm/d/Y');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('timeformat', 'h:i A');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('currencysymb', '&euro;');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('currencyname', 'EUR');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('currsymbpos', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('currdecimalsep', '.');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('currthousandssep', ',');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('currdecimaldig', 2);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('minuteintervals', 30);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('loadjquery', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('closingdays', '');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('openingtime', '8:30');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('closingtime', '17:0');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('showfooter', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('numcals', 3);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('emplistlim', 10);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('emplistmode', '{"1":1,"2":1,"3":1,"4":1,"5":1,"6":1,"7":1,"8":1}');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('emplisttype', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('keepapplock', 15);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('deftask', 'vikappointments');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('calendarlayout', 'calendar');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('minrestr', 30);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('nummonths', 6);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('defstatus', 'CONFIRMED');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('closingperiods', '');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('enablecanc', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('canctime', 2);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('usercredit', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('loginreq', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('enablecart', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('maxcartsize', -1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('shoplink', -1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('shoplinkcustom', '');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('refreshtime', 30);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('mailattach', '');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('showphprefix', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('usedeposit', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('depositafter', 300);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('depositvalue', 40);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('deposittype', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('formatduration', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('calsfrom', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('calsfromyear', '');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('printorders', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('invoiceorders', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('mailcustwhen', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('mailempwhen', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('mailadminwhen', 2);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('mailtmpl', 'customer_email_tmpl.php');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('adminmailtmpl', 'admin_email_tmpl.php');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('empmailtmpl', 'employee_email_tmpl.php');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('cancmailtmpl', 'cancellation_email_tmpl.php');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('synckey', 'secret');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('icsattach', '0;0;0');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('csvattach', '0;0;0');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('ismultilang', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('legendcal', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('serdesclength', 256);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('serlinkhref', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('empdesclength', 1024);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('emplinkhref', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('empgroupfilter', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('empordfilter', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('empajaxsearch', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('pagegroup', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('askconfirm', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('findresmode', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('confcartdisplay', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('multitimezone', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('firstday', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('tswayreport', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('cartallowsync', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('mainmenustatus', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('googleapikey', '');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('sitetheme', 'light');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('router', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('showcheckout', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('update_extra_fields', 0);

INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('pdfparams', '');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('pdfconstraints', '');

INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('enablerecur', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('repeatbyrecur', '1;1;1');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('minamountrecur', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('maxamountrecur', 12);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('fornextrecur', '0;1;1');

INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('enablereviews', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('revservices', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('revemployees', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('revcommentreq', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('revminlength', 48);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('revmaxlength', 512);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('revlimlist', 5);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('revlangfilter', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('revautopublished', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('revloadmode', 1);

INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('enablewaitlist', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('waitlistsmscont', '');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('waitlistmailtmpl', 'waitlist_email_tmpl.php');

INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('enablepackages', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('packsperrow', 3);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('maxpackscart', -1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('packsreguser', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('packmailtmpl', 'packages_email_tmpl.php');

INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('zipcfid', -1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('zipcodes', '');

INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('listablecols', 'id,sid,checkin_ts,checkout,employee,service,info,nominative,mail,phone,total,status');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('listablecf', '');

INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('empsignup', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('empsignstatus', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('empsignrule', 3);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('empassignser', '');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('empcreate', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('empattachser', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('empmanage', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('empmanageser', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('empmanagerate', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('empmanagepay', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('empmanagewd', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('empmanageloc', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('empmanagecoupon', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('empmanagecustfield', 0);

INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('empremove', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('empmaxser', 5);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('emprescreate', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('empresmanage', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('empresremove', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('empresnotify', 1);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('empresconfirm', 1);

INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('oriwres', 256);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('orihres', 256);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('smallwres', 128);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('smallhres', 128);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('isresize', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('isconfig', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('is_stat', 1);

INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('smsapi', '');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('smsenabled', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('smsapito', '1,1,0');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('smsapiadminphone', '');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('smsapifields', '');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('smstextcust', '');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('smstmplcust', '');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('smstmplcustmulti', '');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('smstmpladmin', '');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('smstmpladminmulti', '');

INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('cron_secure_key', '');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('cron_log_mode', 1);

INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('gdpr', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('policylink', '');

INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('conversion_track', 0);
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('securehashkey', '');

INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('version', '1.6.3');
INSERT INTO `#__vikappointments_config`(`param`, `setting`) VALUES('subversion', '0');

INSERT INTO `#__vikappointments_gpayments` (`name`,`file`,`published`,`note`,`charge`,`setconfirmed`,`ordering`) VALUES ('PayPal','paypal.php','0','<p></p>','0.00','0','1');
INSERT INTO `#__vikappointments_gpayments` (`name`,`file`,`published`,`note`,`charge`,`setconfirmed`,`ordering`) VALUES ('Pay on Arrival','bank_transfer.php','0','<p>Extra Info...</p>','0.00','1','2');
INSERT INTO `#__vikappointments_gpayments` (`name`,`file`,`published`,`note`,`charge`,`setconfirmed`,`ordering`) VALUES ('Offline Credit Card','offline_credit_card.php','0','<p></p>','0.00','0','3');
