- Restaurant
id (int unique)
name (varchar 50)
description (text)
photo (varchar 255)
address (varchar 255)
postalCode (varchar 10)
country (varchar 255)
createdAt (date)
updatedAt (date)
#idUser -> OneToOne

- Notice
id (int unique)
#idRestaurant -> OneToMany
#idUser -> OneToMany
note (int)
description (text)
createdAt (date)

- User
id (int unique)
email (varchar 50 unique)
pseudo (varchar 50)
password (varchar 50)
roles -> Client/Moderateur/Restaurateur (json)
createdAt (date)
updatedAt (date)


## Route
# Accueil
/

# Rechercher
/search

# Restaurant

Voir tous les restaurants
/restaurant

-- Les avis seront posté sur la même page que voir un restaurant
Voir le restaurant (id: Restaurant)
/restaurant/{id}

Créer son restaurant
/restaurant/create

-- Ici on vérificate que la personne est bien propriétaire du restaurant
Modifier son restaurant (id: Restaurant)
/restaurant/edit/{id}

# Avis

Voir les avis tous les avis d'un restaurant (id: Restaurant)
/restaurant/notice/{id}

Voir un avis un detail (id: Notice)
/notice/{id}

# User

Créer un nouveau compte user
/register

Se connecter
/login

Se déconnecter
/logout

# Modération
/moderate

Voir tous les restaurants
/moderate/restaurant
Créer un restaurant
/moderate/restaurant/create
Modifier un restaurant (id: Restaurant)
/moderate/restaurant/{id}
Supprimer un restaurant (id: Restaurant)
/moderate/restaurant/{id}

Voir tous les avis
/moderate/notice
Éditer un avis (id: Notice)
/modrate/notice/{id}
Supprimé un avis
/moderate/notice/{id}

Voir tous les utilisateurs
/moderate/user
Éditer un utilisateur (id: User)
/moderate/user/{id}
Supprimer un utilisateur (id: User)
/moderate/user/{id}