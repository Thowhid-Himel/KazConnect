document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('.validate-form');

    forms.forEach((form) => {
        form.addEventListener('submit', (event) => {
            const requiredFields = form.querySelectorAll('[required]');
            let hasError = false;

            requiredFields.forEach((field) => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#c2410c';
                    hasError = true;
                } else {
                    field.style.borderColor = '';
                }
            });

            if (hasError) {
                event.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });
});
