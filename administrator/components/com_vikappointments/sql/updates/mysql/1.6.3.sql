ALTER TABLE `#__vikappointments_coupon`
ADD COLUMN `pubmode` tinyint(1) DEFAULT 1 COMMENT '1 to consider publishing on current date, 2 for checkin date' AFTER `mincost`;

UPDATE `#__vikappointments_config` SET `setting`='1.6.3' WHERE `param`='version';