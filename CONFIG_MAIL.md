# Configuration email (Serinity)

Pour des raisons de sécurité, le code d'activation n'est **jamais affiché** sur le site : il est envoyé **uniquement par email**.

Configurez votre fichier **`.env`** avec :

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=ju177583@gmail.com
MAIL_PASSWORD=zlscngrdvarqeyzi
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=ju177583@gmail.com
MAIL_FROM_NAME="Serinity"
```

- **Important :** Avec Gmail et une authentification en 2 étapes, utilisez un **mot de passe d'application** pour `MAIL_PASSWORD`.
- Après modification du `.env` : `php artisan config:clear`
- Si vous ne recevez pas l'email : vérifiez les spams, puis utilisez **« Renvoyer le code par email »** sur la page d'activation.
