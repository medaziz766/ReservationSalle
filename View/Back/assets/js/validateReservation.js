document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('reservationForm');
    if (!form) return;

    const salleId = document.getElementById('salleId');
    const utilisateurId = document.getElementById('utilisateurId');
    const objet = document.getElementById('objet');
    const dateDebut = document.getElementById('dateDebut');
    const dateFin = document.getElementById('dateFin');

    function setMsg(field, message, isValid) {
        let msg = field.parentElement.querySelector('.field-msg');
        if (!msg) { msg = document.createElement('div'); msg.className = 'field-msg'; field.parentElement.appendChild(msg); }
        msg.textContent = message;
        msg.className = 'field-msg ' + (isValid ? 'msg-success' : 'msg-error');
    }

    function checkSalle() {
        const valid = salleId.value !== '';
        setMsg(salleId, valid ? 'Correct' : 'Choisissez une salle.', valid);
        return valid;
    }
    function checkUtilisateur() {
        if (!utilisateurId) return true;
        const valid = utilisateurId.value !== '';
        setMsg(utilisateurId, valid ? 'Correct' : 'Choisissez un utilisateur.', valid);
        return valid;
    }
    function checkObjet() {
        if (!objet) return true;
        const valid = objet.value.trim().length >= 3;
        setMsg(objet, valid ? 'Correct' : "L'objet doit contenir au moins 3 caractères.", valid);
        return valid;
    }
    function checkDates() {
        if (!dateDebut.value || !dateFin.value) {
            setMsg(dateFin, 'Les dates de début et de fin sont obligatoires.', false);
            return false;
        }
        const valid = new Date(dateFin.value) > new Date(dateDebut.value);
        setMsg(dateFin, valid ? 'Correct' : 'La date de fin doit être après la date de début.', valid);
        return valid;
    }

    salleId.addEventListener('change', checkSalle);
    if (utilisateurId) utilisateurId.addEventListener('change', checkUtilisateur);
    objet.addEventListener('keyup', checkObjet);
    dateDebut.addEventListener('change', checkDates);
    dateFin.addEventListener('change', checkDates);

    form.addEventListener('submit', function (e) {
        const ok = [checkSalle(), checkUtilisateur(), checkObjet(), checkDates()].every(Boolean);
        if (!ok) { e.preventDefault(); alert('Merci de corriger les erreurs du formulaire.'); }
    });
});
