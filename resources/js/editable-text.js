document.addEventListener('DOMContentLoaded', function () {
    const table = document.querySelector('.datatables-table'); // Update with the actual selector for your table

    table.addEventListener('click', function (event) {
        const target = event.target;

        if (target.tagName === 'TD' && target.classList.contains('editable')) {
            const isEditing = target.classList.contains('editing');

            if (!isEditing) {
                const originalValue = target.textContent.trim();

                // Replace the content with an input field
                const input = document.createElement('input');
                input.value = originalValue;
                input.classList.add('editable-input');

                target.innerHTML = '';
                target.appendChild(input);

                input.focus();

                // Add an event listener to handle the input blur event (when clicking outside the input)
                input.addEventListener('blur', function () {
                    const newValue = input.value;
                    target.innerHTML = newValue;
                    target.classList.remove('editing');

                    // Add logic to save changes to your backend, if needed
                });

                target.classList.add('editing');
            }
        }
    });
});