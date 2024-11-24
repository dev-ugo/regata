/**
 * Fonction pour vérifier la validité de l'email.
 * @param {string} email - L'adresse email à valider.
 * @returns {boolean} - True si l'email est valide, sinon false.
 */
function validateEmail(email) {
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailPattern.test(email);
}

/**
 * Fonction pour vérifier la longueur du mot de passe.
 * @param {string} password - Le mot de passe à vérifier.
 * @returns {boolean} - True si le mot de passe a une longueur d'au moins 6 caractères, sinon false.
 */
function validatePassword(password) {
    return password.length >= 6;
}

/**
 * Fonction pour activer ou désactiver le bouton de soumission en fonction de la validité des données saisies.
 */
function toggleSubmitButton() {
    var emailInput = document.getElementById('email');
    var passwordInput = document.getElementById('password');
    var submitButton = document.getElementById('submit-register');

    // Vérifier si l'email est valide et si le mot de passe a au moins 6 caractères
    if (validateEmail(emailInput.value) && validatePassword(passwordInput.value)) {
        submitButton.disabled = false; // Activer le bouton de soumission
    } else {
        submitButton.disabled = true; // Désactiver le bouton de soumission
    }
}

// Écouter les événements 'input' sur les champs de l'email et du mot de passe
document.getElementById('email').addEventListener('input', function () {
    var emailInput = document.getElementById('email');
    var emailFeedback = document.getElementById('email-feedback');

    if (!validateEmail(emailInput.value)) {
        emailInput.classList.add('is-invalid');
        emailFeedback.style.display = 'block';
    } else {
        emailInput.classList.remove('is-invalid');
        emailFeedback.style.display = 'none';
    }

    toggleSubmitButton(); // Appeler la fonction pour activer ou désactiver le bouton de soumission
});

document.getElementById('password').addEventListener('input', function () {
    var passwordInput = document.getElementById('password');
    var passwordFeedback = document.getElementById('password-feedback');

    if (!validatePassword(passwordInput.value)) {
        passwordInput.classList.add('is-invalid');
        passwordFeedback.style.display = 'block';
    } else {
        passwordInput.classList.remove('is-invalid');
        passwordFeedback.style.display = 'none';
    }

    toggleSubmitButton(); // Appeler la fonction pour activer ou désactiver le bouton de soumission
});
