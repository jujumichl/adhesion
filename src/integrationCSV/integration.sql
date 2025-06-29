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


SELECT sum(brouillon.brou_adh),SUM(brouillon.brou_act) FROM brouillon


-- Rapproche inscriptions et brouillon par activité
SELECT activites.act_ext_key, activites.act_libelle,sum(ins_montant) act_montant,brou_act AS broui_montant ,  COUNT(*) AS act_nb, nbbroui as broui_nb , brou_adh FROM inscriptions
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

