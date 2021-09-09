ALTER TABLE `#__vikappointments_special_rates`
ADD COLUMN `params` text DEFAULT NULL;

ALTER TABLE `#__vikappointments_gpayments`
ADD COLUMN `level` tinyint(1) DEFAULT 1 COMMENT 'the access view level' AFTER `position`;

ALTER TABLE `#__vikappointments_ser_emp_assoc`
ADD COLUMN `description` text DEFAULT NULL;

INSERT INTO `#__vikappointments_config` (`param`, `setting`) VALUES ('showcheckout', 0);

UPDATE `#__vikappointments_config` SET `setting`='1.6.2' WHERE `param`='version';