document.addEventListener('DOMContentLoaded', () => {
    const alerts = document.querySelectorAll('.alert');
    const categorySelect = document.querySelector('#category-select');
    const customCategoryField = document.querySelector('#custom-category-field');

    alerts.forEach((alert) => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-6px)';
            alert.style.transition = 'all 0.3s ease';
        }, 3500);
    });

    if (categorySelect && customCategoryField) {
        const syncCategoryField = () => {
            customCategoryField.style.display = categorySelect.value === 'custom' ? 'grid' : 'none';
        };

        syncCategoryField();
        categorySelect.addEventListener('change', syncCategoryField);
    }
});
