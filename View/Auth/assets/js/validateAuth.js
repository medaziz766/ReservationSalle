document.addEventListener('DOMContentLoaded', function () {
    function setMsg(field, message, isValid) {
        let msg = field.parentElement.querySelector('.field-msg');
        if (!msg) {
            msg = document.createElement('div');
            msg.className = 'field-msg';
            field.parentElement.appendChild(msg);
        }
        msg.textContent = message;
        msg.className = 'field-msg ' + (isValid ? 'msg-success' : 'msg-error');
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // --- Login form ---
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        const email = document.getElementById('email');
        const password = document.getElementById('password');

        function checkEmail() {
            const valid = emailRegex.test(email.value.trim());
            setMsg(email, valid ? 'Correct' : "L'email n'est pas valide.", valid);
            return valid;
        }
        function checkPassword() {
            const valid = password.value.length >= 6;
            setMsg(password, valid ? 'Correct' : 'Le mot de passe doit contenir au moins 6 caractères.', valid);
            return valid;
        }

        email.addEventListener('blur', checkEmail);
        password.addEventListener('blur', checkPassword);

        loginForm.addEventListener('submit', function (e) {
            const ok = [checkEmail(), checkPassword()].every(Boolean);
            if (!ok) { e.preventDefault(); alert('Merci de corriger les erreurs du formulaire.'); }
        });
    }

    // --- Register form ---
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        const nom = document.getElementById('nom');
        const prenom = document.getElementById('prenom');
        const email = document.getElementById('email');
        const password = document.getElementById('password');

        function checkNom() {
            const valid = /^[A-Za-zÀ-ÿ\s]{2,}$/.test(nom.value.trim());
            setMsg(nom, valid ? 'Correct' : 'Le nom doit contenir uniquement des lettres (min 2 caractères).', valid);
            return valid;
        }
        function checkPrenom() {
            const valid = /^[A-Za-zÀ-ÿ\s]{2,}$/.test(prenom.value.trim());
            setMsg(prenom, valid ? 'Correct' : 'Le prénom doit contenir uniquement des lettres (min 2 caractères).', valid);
            return valid;
        }
        function checkEmail() {
            const valid = emailRegex.test(email.value.trim());
            setMsg(email, valid ? 'Correct' : "L'email n'est pas valide.", valid);
            return valid;
        }
        function checkPassword() {
            const valid = password.value.length >= 6;
            setMsg(password, valid ? 'Correct' : 'Le mot de passe doit contenir au moins 6 caractères.', valid);
            return valid;
        }

        nom.addEventListener('keyup', checkNom);
        prenom.addEventListener('keyup', checkPrenom);
        email.addEventListener('blur', checkEmail);
        password.addEventListener('blur', checkPassword);

        registerForm.addEventListener('submit', function (e) {
            const ok = [checkNom(), checkPrenom(), checkEmail(), checkPassword()].every(Boolean);
            if (!ok) { e.preventDefault(); alert('Merci de corriger les erreurs du formulaire.'); }
        });
    }
});
