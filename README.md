# api-siren
API permettant l'import d'un fichier SIREN et la recherche d'un numéro SIREN

GET /company/{siren}
Permet de vérifier l'existence d'un établiseement et de récupérer ses informations le cas échéant.

POST /company/update
Permet d'importer le fichier CSV de mise à jour des établissements.
