if (typeof(ss) === 'undefined' || typeof(ss.i18n) === 'undefined') {
    if (typeof(console) !== 'undefined') { // eslint-disable-line no-console
        console.error('Class ss.i18n not defined');  // eslint-disable-line no-console
    }
} else {
    ss.i18n.addDictionary('fr', {
        "Admin.NO_MATCHING_OPTIONS": "Aucune option correspondante",
        "AssetAdmin.UPLOADFIELD_UPLOAD_NEW": "Télécharger un nouveau fichier",
        "AssetAdmin.UPLOADFIELD_CHOOSE_EXISTING": "Choisir un fichier existant",
    });
}
