var eyeOpen = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>';
var eyeClosed = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 19c-7 0-11-7-11-7a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 7 11 7a18.5 18.5 0 0 1-2.16 3.19"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';

document.querySelectorAll('.toggle-password').forEach(function (button) {
    var input = button.parentElement.querySelector('input');
    button.innerHTML = eyeOpen;

    function refresh() {
        if (input.value.length > 0) {
            button.classList.add('visible');
        } else {
            button.classList.remove('visible');
            input.type = 'password';
            button.innerHTML = eyeOpen;
        }
    }

    input.addEventListener('input', refresh);
    refresh();

    button.addEventListener('click', function () {
        if (input.type === 'password') {
            input.type = 'text';
            button.innerHTML = eyeClosed;
        } else {
            input.type = 'password';
            button.innerHTML = eyeOpen;
        }
    });
});

var modal = document.getElementById('delete-modal');
if (modal) {
    var modalTitle = document.getElementById('delete-title');
    var modalId = document.getElementById('delete-id');

    document.querySelectorAll('.delete-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            modalTitle.textContent = button.getAttribute('data-title');
            modalId.value = button.getAttribute('data-id');
            modal.classList.add('open');
        });
    });

    document.getElementById('delete-cancel').addEventListener('click', function () {
        modal.classList.remove('open');
    });

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            modal.classList.remove('open');
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            modal.classList.remove('open');
        }
    });
}
