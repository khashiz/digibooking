ALTER TABLE `#__vikappointments_service` ADD COLUMN `metadata` text DEFAULT NULL;

UPDATE `#__vikappointments_config` SET `setting`='1.6.1' WHERE `param`='version';