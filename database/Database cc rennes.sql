CREATE TABLE `activites` (
  `act_id` int,
  `act_ext_key` varchar(255) COMMENT 'clé externe type D02',
  `act_libelle` varchar(255) NOT NULL,
  `act_mode_rem` varchar(255) COMMENT 'Mode de rémunération du prof a l''h ou au 1/4/ h/élève',
  `dom_id` int,
  `tyac_id` int
);

CREATE TABLE `domaines` (
  `dom_id` int,
  `dom_libelle` varchar(255) COMMENT 'Danse, '
);

CREATE TABLE `typeactivite` (
  `tyac_id` int,
  `tyac_libelle` varchar(255) COMMENT 'cours, événement,adhésion, stage, don, Crowdfunding',
  `tyac_famille` varchar(255)
);

CREATE TABLE `personnes` (
  `per_id` int ,
  `per_nom` varchar(255) NOT NULL,
  `civ_id` int,
  `per_prenom` varchar(255) NOT NULL,
  `per_tel` varchar(255) UNIQUE,
  `per_email` varchar(255) NOT NULL,
  `per_adresse` varchar(255),
  `per_code_postal` varchar(255),
  `per_ville` varchar(255),
  `per_dat_naissance` datetime
);

CREATE TABLE `civilities` (
  `civ_id` int,
  `civ_libelle` varchar(255)
);

CREATE TABLE `role` (
  `rol_id` int ,
  `tyro_id` int,
  `pers_id` int,
  `act_id` int
);

CREATE TABLE `reglements` (
  `reg_id` int ,
  `reg_montant` float,
  `mreg_id` int,
  `reg_date` varchar(255)
);

CREATE TABLE `modereglement` (
  `mreg_id` int,
  `mreg_code` varchar(255) COMMENT 'ESP, CHE, HEL, DIS, TPE',
  `mreg_Libelle` varchar(255)
);

CREATE TABLE `tarifs` (
  `tar_id` int ,
  `ans_id` int,
  `act_id` int,
  `tar_montant` float,
  `tar_libelle` varchar(255)
);

CREATE TABLE `an_exercice` (
  `ans_id` int,
  `ans_libelle` varchar(255) COMMENT '2019-2020, 2020-2021,...',
  `ans_date_debut` datetime,
  `ans_date_fin` datetime
);

CREATE TABLE `inscriptions` (
  `ins_id` int ,
  `per_id` int,
  `act_id` int,
  `ins_date_inscription` date NOT NULL,
  `id_reg` int,
  `ins_debut` datetime COMMENT 'début d''activité de la ligne',
  `ins_fin` datetime COMMENT 'fin''activité de la ligne'
);

CREATE TABLE `typerole` (
  `tyro_id` int,
  `tyro_libelle` varchar(255) COMMENT 'animateur, référent, etc'
);

ALTER TABLE `civilities` ADD FOREIGN KEY (`civ_id`) REFERENCES `personnes` (`civ_id`);

ALTER TABLE `inscriptions` ADD FOREIGN KEY (`act_id`) REFERENCES `activites` (`act_id`);

ALTER TABLE `personnes` ADD FOREIGN KEY (`per_id`) REFERENCES `inscriptions` (`per_id`);

ALTER TABLE `inscriptions` ADD FOREIGN KEY (`id_reg`) REFERENCES `reglements` (`reg_id`);

ALTER TABLE `domaines` ADD FOREIGN KEY (`dom_id`) REFERENCES `activites` (`dom_id`);

ALTER TABLE `tarifs` ADD FOREIGN KEY (`ans_id`) REFERENCES `an_exercice` (`ans_id`);

ALTER TABLE `activites` ADD FOREIGN KEY (`act_id`) REFERENCES `tarifs` (`act_id`);

ALTER TABLE `activites` ADD FOREIGN KEY (`tyac_id`) REFERENCES `typeactivite` (`tyac_id`);

ALTER TABLE `personnes` ADD FOREIGN KEY (`per_id`) REFERENCES `role` (`pers_id`);

ALTER TABLE `role` ADD FOREIGN KEY (`act_id`) REFERENCES `activites` (`act_id`);

ALTER TABLE `reglements` ADD FOREIGN KEY (`mreg_id`) REFERENCES `modereglement` (`mreg_id`);

ALTER TABLE `role` ADD FOREIGN KEY (`tyro_id`) REFERENCES `typerole` (`tyro_id`);
