- [ ] Valider le modèle de connées avec YB
# Si une ligne :
- [x] faire fonction qui passe les donnée du csv a la table brouillon
- [x] creer adherent ou MAJ donnée adherent hormi email
- [ ] Garder le nom du fichier dans la box de récup de fichier.


# Si plusieurs ligne :
## Si payer le même jours
- [ ] Créer d'abord l'adhésion
- [ ] Puis les autres activités  ------ > pour 1 règlement
## Sinon :
-


- [ ] Cf fichier utils.php
- [ ] Renommer la table an_exercice par saison



# Fonction de Vérfication de la cohérence du fichier CSV
- [ ] Vérifier si on a tous les modes de règlements
- [ ] faire une condition qui ne récupère que les lignes complètes
- [ ] faire une fonction qui parcours la table brouillon et qui la vérifie (champ bool : (true)valide, (false)invalide)
- [ ] faire une fonction qui parcours tous les invalides qui détectant les personnes et les lignes associées a eux
- [ ] Pour chaque personne, ligne par année traitement des données (si 1 ligne do something, sinon plus d'année)
- [ ] Fonction de vérification et affichage des anomalies