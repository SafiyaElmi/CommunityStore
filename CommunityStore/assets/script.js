document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.confirm-delete').forEach(el => {

        el.addEventListener('click', e => {

            const confirmed = confirm(
                'Are you sure you want to delete your account? This cannot be undone.'
            );

            if (!confirmed) {
                e.preventDefault();
            }

        });

    });

    document.querySelectorAll('.auto-dismiss').forEach(a => {

        setTimeout(() => {
            a.remove();
        }, 4000);

    });

    document.querySelectorAll('.qty').forEach(box => {

        const input = box.querySelector('input');

        box.querySelectorAll('button').forEach(btn => {

            btn.addEventListener('click', () => {

                input.value = Math.max(
                    1,
                    parseInt(input.value) +
                    (btn.dataset.step === 'up' ? 1 : -1)
                );

                input.closest('form')?.submit();

            });

        });

    });

    const editNameBtn = document.querySelector('.edit-name-btn');

    if (editNameBtn) {

        editNameBtn.addEventListener('click', () => {

            const nameDisplay =
                document.querySelector('#nameDisplay');

            if (!nameDisplay) {
                return;
            }

            const firstName =
                nameDisplay.dataset.firstname || '';

            const lastName =
                nameDisplay.dataset.lastname || '';

            nameDisplay.innerHTML = `

                <form
                    method="POST"
                    action="profile.php"
                    class="inline-edit-form"
                >

                    <input
                        type="hidden"
                        name="action"
                        value="update_name"
                    >

                    <div class="name-edit-fields">

                        <input
                            type="text"
                            name="firstName"
                            value="${escapeHtml(firstName)}"
                            class="profile-edit-input"
                            placeholder="First name"
                            required
                        >

                        <input
                            type="text"
                            name="lastName"
                            value="${escapeHtml(lastName)}"
                            class="profile-edit-input"
                            placeholder="Last name"
                            required
                        >

                    </div>


                    <button
                        type="submit"
                        class="profile-save-btn"
                    >
                        Save
                    </button>


                    <button
                        type="button"
                        class="profile-cancel-btn cancel-name-edit"
                    >
                        Cancel
                    </button>

                </form>

            `;

            const cancelButton =
                nameDisplay.querySelector('.cancel-name-edit');


            cancelButton.addEventListener('click', () => {

                nameDisplay.innerHTML = `

                    <h2>
                        ${escapeHtml(firstName)}
                        ${escapeHtml(lastName)}
                    </h2>

                    <button
                        type="button"
                        class="profile-edit-btn edit-name-btn"
                        title="Edit name"
                    >
                        <i class="bi bi-pencil"></i>
                    </button>

                `;

                attachNameEditButton();

            });

        });

    }

    const editEmailBtn = document.querySelector('.edit-email-btn');

    if (editEmailBtn) {

        editEmailBtn.addEventListener('click', () => {

            const emailDisplay =
                document.querySelector('#emailDisplay');

            if (!emailDisplay) {
                return;
            }

            const email =
                emailDisplay.dataset.email || '';

            emailDisplay.innerHTML = `

                <form
                    method="POST"
                    action="profile.php"
                    class="inline-edit-form"
                >

                    <input
                        type="hidden"
                        name="action"
                        value="update_email"
                    >

                    <input
                        type="email"
                        name="email"
                        value="${escapeHtml(email)}"
                        class="profile-edit-input"
                        placeholder="Email address"
                        required
                    >

                    <button
                        type="submit"
                        class="profile-save-btn"
                    >
                        Save
                    </button>

                    <button
                        type="button"
                        class="profile-cancel-btn cancel-email-edit"
                    >
                        Cancel
                    </button>

                </form>

            `;

            const cancelButton =
                emailDisplay.querySelector('.cancel-email-edit');

            cancelButton.addEventListener('click', () => {

                emailDisplay.innerHTML = `

                    <p>
                        ${escapeHtml(email)}
                    </p>

                    <button
                        type="button"
                        class="profile-edit-btn edit-email-btn"
                        title="Edit email"
                    >
                        <i class="bi bi-pencil"></i>
                    </button>

                `;

                attachEmailEditButton();

            });

        });

    }

    const userType =
        document.body.dataset.userType || '';

    document.querySelectorAll('.role-function').forEach(element => {

        const allowedRoles =
            (element.dataset.roles || '')
                .split(',')
                .map(role => role.trim())
                .filter(Boolean);


        if (
            allowedRoles.length &&
            !allowedRoles.includes(userType)
        ) {

            element.style.display = 'none';

        }

    });

});

function attachNameEditButton() {

    const button =
        document.querySelector('.edit-name-btn');

    if (!button) {
        return;
    }

    button.addEventListener('click', () => {

        const nameDisplay =
            document.querySelector('#nameDisplay');

        if (!nameDisplay) {
            return;
        }

        const firstName =
            nameDisplay.dataset.firstname || '';

        const lastName =
            nameDisplay.dataset.lastname || '';

        nameDisplay.innerHTML = `

            <form
                method="POST"
                action="profile.php"
                class="inline-edit-form"
            >

                <input
                    type="hidden"
                    name="action"
                    value="update_name"
                >

                <div class="name-edit-fields">

                    <input
                        type="text"
                        name="firstName"
                        value="${escapeHtml(firstName)}"
                        class="profile-edit-input"
                        placeholder="First name"
                        required
                    >

                    <input
                        type="text"
                        name="lastName"
                        value="${escapeHtml(lastName)}"
                        class="profile-edit-input"
                        placeholder="Last name"
                        required
                    >

                </div>

                <button
                    type="submit"
                    class="profile-save-btn"
                >
                    Save
                </button>

                <button
                    type="button"
                    class="profile-cancel-btn cancel-name-edit"
                >
                    Cancel
                </button>

            </form>

        `;

        nameDisplay
            .querySelector('.cancel-name-edit')
            .addEventListener('click', () => {

                nameDisplay.innerHTML = `

                    <h2>
                        ${escapeHtml(firstName)}
                        ${escapeHtml(lastName)}
                    </h2>

                    <button
                        type="button"
                        class="profile-edit-btn edit-name-btn"
                        title="Edit name"
                    >
                        <i class="bi bi-pencil"></i>
                    </button>

                `;

                attachNameEditButton();

            });

    });

}

function attachEmailEditButton() {

    const button =
        document.querySelector('.edit-email-btn');

    if (!button) {
        return;
    }

    button.addEventListener('click', () => {

        const emailDisplay =
            document.querySelector('#emailDisplay');

        if (!emailDisplay) {
            return;
        }

        const email =
            emailDisplay.dataset.email || '';

        emailDisplay.innerHTML = `

            <form
                method="POST"
                action="profile.php"
                class="inline-edit-form"
            >

                <input
                    type="hidden"
                    name="action"
                    value="update_email"
                >

                <input
                    type="email"
                    name="email"
                    value="${escapeHtml(email)}"
                    class="profile-edit-input"
                    placeholder="Email address"
                    required
                >

                <button
                    type="submit"
                    class="profile-save-btn"
                >
                    Save
                </button>

                <button
                    type="button"
                    class="profile-cancel-btn cancel-email-edit"
                >
                    Cancel
                </button>

            </form>

        `;

        emailDisplay
            .querySelector('.cancel-email-edit')
            .addEventListener('click', () => {

                emailDisplay.innerHTML = `

                    <p>
                        ${escapeHtml(email)}
                    </p>

                    <button
                        type="button"
                        class="profile-edit-btn edit-email-btn"
                        title="Edit email"
                    >
                        <i class="bi bi-pencil"></i>
                    </button>

                `;

                attachEmailEditButton();

            });

    });

}

function escapeHtml(value) {

    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

}