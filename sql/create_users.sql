CREATE TABLE utilisateurs (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    roles JSON NOT NULL,
    telephone VARCHAR(20) DEFAULT NULL,
    email_verifie BOOLEAN DEFAULT FALSE,
    photo_profil VARCHAR(255) DEFAULT NULL,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    est_actif BOOLEAN DEFAULT TRUE,
    derniere_connexion DATETIME DEFAULT NULL,
    agree_terms BOOLEAN DEFAULT FALSE,
    social_id VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
