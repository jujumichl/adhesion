SELECT activites.act_ext_key, activites.act_libelle,sum(ins_montant), COUNT(*)FROM inscriptions
LEFT JOIN activites ON activites.act_id=inscriptions.act_id
 GROUP BY activites.act_libelle


SELECT typeactivite.tyac_famille,sum(ins_montant) FROM inscriptions
LEFT JOIN activites ON activites.act_id=inscriptions.act_id
LEFT JOIN typeactivite ON typeactivite.tyac_id=activites.tyac_id
 GROUP BY typeactivite.tyac_famille

-- Delete activités
DELETE  FROM inscriptions;
DELETE  FROM reglements;
DELETE FROM personnes

-- total des adh et act dans brouillon
SELECT sum(brouillon.brou_adh),SUM(brouillon.brou_act) FROM brouillon



-- Rapproche inscriptions et brouillon par activité
SELECT activites.act_ext_key, activites.act_libelle,sum(ins_montant) AS  act_montant,brou_act AS broui_montant , sum(ins_montant)-brou_act AS delta, COUNT(*) AS act_nb, nbbroui as broui_nb , brou_adh FROM inscriptions
LEFT JOIN activites ON activites.act_id=inscriptions.act_id
LEFT JOIN (select brou_code, sum(brou_adh) AS brou_adh,sum(brou_act) AS brou_act , COUNT(*) as nbbroui from brouillon 
                        LEFT JOIN modereglement ON modereglement.mreg_code = brouillon.brou_reglement 
                        LEFT JOIN activites ON activites.act_ext_key = brouillon.brou_code 
                        LEFT JOIN an_exercice ON an_exercice.ans_libelle = brouillon.brou_annee
                        GROUP BY  brou_code) AS brouQuery ON brouQuery.brou_code = activites.act_ext_key
 GROUP BY activites.act_libelle


-- Global adhésion et activité
 SELECT typeactivite.tyac_famille,sum(ins_montant) FROM inscriptions
LEFT JOIN activites ON activites.act_id=inscriptions.act_id
LEFT JOIN typeactivite ON typeactivite.tyac_id=activites.tyac_id
 GROUP BY typeactivite.tyac_famille

-- Check doublons
 SELECT inscriptions.per_id, per_email, ans_id,inscriptions.act_id , COUNT(*) AS numb FROM inscriptions
LEFT JOIN personnes ON personnes.per_id=inscriptions.per_id
GROUP BY per_id, ans_id,inscriptions.act_id 
having COUNT(*) > 1

-- Check doublons email
SELECT  brou_email, COUNT(*) from
(SELECT brou_email,brou_nom,brou_prenom  FROM brouillon
GROUP by brou_email,brou_nom,brou_prenom
ORDER BY brou_email) AS subq
GROUP BY brou_email

-- Check doublons activité par année
SELECT brou_email,brou_nom,brou_prenom , brou_annee, brou_code, COUNT(*) FROM brouillon
GROUP by brou_email,brou_nom,brou_prenom, brou_annee, brou_code
HAVING COUNT(*) >1
ORDER BY brou_email

