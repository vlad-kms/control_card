CREATE TABLE IF NOT EXISTS `#__controlcard_cards` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date and time creation',
  `date_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date and time last change',
  `person_id_create` int(12) UNSIGNED NOT NULL COMMENT 'ID person creation',
  `person_id`        INT(12) UNSIGNED NOT NULL COMMENT 'ID person кто исполнитель',
  `num_controlcard` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `note` VARCHAR(512) NOT NULL DEFAULT '',
  `performed` TINYINT(1) NOT NULL DEFAULT 0,
  `note_big` VARCHAR(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `ind_user_id` (`person_id_create`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

ALTER TABLE `#__controlcard_cards` ADD COLUMN `performed_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата на которую требуется исполнить';
ALTER TABLE `#__controlcard_cards` ADD COLUMN `performed_type` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Флаг исполнения (дата): 1-однократно; 2-еженедельно; 3-ежемесячно; 4-ежегодно; 5-каждые N дней(N в поле performed_ext_int';
ALTER TABLE `#__controlcard_cards` ADD COLUMN `performed_ext_int` INT(11) NOT NULL DEFAULT 0 Comment 'Доп. поле типа INT. Значение зависит от performed_type. Если performed_type=5, тогда здесь кол-во дней между испонениями';
ALTER TABLE `#__controlcard_cards` ADD COLUMN `reason` VARCHAR(255) DEFAULT '' COMMENT 'Основание для карточки';
ALTER TABLE `#__controlcard_cards` ADD COLUMN `report` VARCHAR(255) COMMENT 'Рапорт';
ALTER TABLE `#__controlcard_cards` ADD COLUMN `num_document_int` VARCHAR(50) COMMENT 'Номер документа';
ALTER TABLE `#__controlcard_cards` ADD COLUMN `date_document_int` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата документа';
ALTER TABLE `#__controlcard_cards` ADD COLUMN `num_document` VARCHAR(50) COMMENT 'Номер внутреннего докуиента';
ALTER TABLE `#__controlcard_cards` ADD COLUMN `date_document` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата внутреннего документа';

  # controlcard_persons
CREATE TABLE IF NOT EXISTS `#__controlcard_persons` (
  `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fio` varchar(255) NOT NULL,
  `dismiss` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` INT(11),
  PRIMARY KEY (`id`),
  KEY `idx_fio` (`fio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  # controlcard_persons
ALTER TABLE `#__controlcard_persons` ADD UNIQUE KEY `idx_user_id` (`user_id`);
ALTER TABLE `#__controlcard_persons` ADD COLUMN `person_post` VARCHAR(255) DEFAULT '' COMMENT 'Должность сотрудника (отв. лица)';

  # Триггеры controlcard_cards
DROP TRIGGER IF EXISTS `ccards_BEFORE_UPDATE`;
DROP TRIGGER IF EXISTS `#__CONTROLCARDS_BEFORE_UPDATE`;
DROP TRIGGER IF EXISTS `#__CARDS_BEFORE_UPDATE`;
CREATE TRIGGER `#__CARDS_BEFORE_UPDATE` BEFORE UPDATE ON `#__controlcard_cards` FOR EACH ROW SET NEW.`date_change` = CURRENT_TIMESTAMP;

ALTER TABLE `#__controlcard_cards` ADD CONSTRAINT `fk_person_create`
  FOREIGN KEY (`person_id_create`) REFERENCES `#__controlcard_persons`(`id`)
  ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `#__controlcard_cards` ADD CONSTRAINT `fk_person_executer`
  FOREIGN KEY (`person_id`) REFERENCES `#__controlcard_persons`(`id`)
  ON DELETE RESTRICT ON UPDATE CASCADE;

CREATE TABLE `#__controlcard_reasons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(512) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `#__controlcard_cards` MODIFY `reason` varchar(512) NOT NULL default '';

ALTER TABLE `#__controlcard_persons` ADD COLUMN `email` VARCHAR(100) DEFAULT '' COMMENT 'EMail сотрудника (отв. лица)';