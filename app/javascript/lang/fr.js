if (typeof(ss) === 'undefined' || typeof(ss.i18n) === 'undefined') {
    if (typeof(console) !== 'undefined') { // eslint-disable-line no-console
        console.error('Class ss.i18n not defined');  // eslint-disable-line no-console
    }
} else {
    ss.i18n.addDictionary('fr', {
        "Admin.NO_MATCHING_OPTIONS": "Aucune option correspondante",
        "AssetAdmin.UPLOADFIELD_UPLOAD_NEW": "Télécharger un nouveau fichier",
        "AssetAdmin.UPLOADFIELD_CHOOSE_EXISTING": "Choisir un fichier existant",
        "Admin.SEARCH_OPTIONS": "Options de recherche",
        "Admin.ENTER": "Entrer",
        "Admin.SEARCH": "Rechercher",
        "LinkField.ADD_LINK": "Ajouter un lien",
        "LinkField.ARCHIVE": "Archiver",
        "LinkField.ARCHIVE_CONFIRM": "Êtes-vous sûr de vouloir archiver ce lien ?",
        "LinkField.ARCHIVE_ERROR": "Échec de l'archivage du lien",
        "LinkField.ARCHIVE_SUCCESS": "Lien archivé",
        "LinkField.CANNOT_CREATE_LINK": "Impossible de créer le lien",
        "LinkField.DELETE": "Supprimer",
        "LinkField.DELETE_CONFIRM": "Êtes-vous sûr de vouloir supprimer ce lien ?",
        "LinkField.DELETE_ERROR": "Échec de la suppression du lien",
        "LinkField.DELETE_SUCCESS": "Lien supprimé",
        "LinkField.EDIT_LINK": "Modifier le lien",
        "LinkField.FAILED_TO_LOAD_LINKS": "Échec du chargement du (des) lien(s)",
        "LinkField.FAILED_TO_SAVE_LINK": "Échec de l'enregistrement du lien",
        "LinkField.LINK_DRAFT_LABEL": "Brouillon",
        "LinkField.LINK_DRAFT_TITLE": "Le lien a des modifications en brouillon",
        "LinkField.LINK_MODIFIED_LABEL": "Modifié",
        "LinkField.LINK_MODIFIED_TITLE": "Le lien a des modifications non publiées",
        "LinkField.SAVE_RECORD_FIRST": "Impossible d'ajouter des liens tant que l'enregistrement n'a pas été sauvegardé",
        "LinkField.SAVE_SUCCESS": "Lien sauvegardé",
        "LinkField.SORT_ERROR": "Impossible de trier les liens",
        "LinkField.SORT_SUCCESS": "Ordre de tri des liens mis à jour"
    });
}
