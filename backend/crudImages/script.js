window.onload = function() {
    var selectAllButton = document.getElementById("selectAll");
    if (selectAllButton) {
        selectAllButton.addEventListener("click", function() {
            var checkboxes = document.querySelectorAll("input[name='selected_images[]']");
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = true;
            });
        });
    }

    var invertSelectionButton = document.getElementById("invertSelection");
    if (invertSelectionButton) {
        invertSelectionButton.addEventListener("click", function() {
            var checkboxes = document.querySelectorAll("input[name='selected_images[]']");
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = !checkbox.checked;
            });
        });
    }

    var updateForms = document.querySelectorAll(".update-form");
    Array.from(updateForms).forEach(function(form) {
        var deleteButton = form.querySelector("button[name='delete_id']");
        if (deleteButton) {
            deleteButton.addEventListener("click", function(event) {
                var confirmation = confirm("¿Seguro que deseas borrar?");
                if (!confirmation) {
                    event.preventDefault();
                }
            });
        }
    });

    var deleteSelectedButton = document.querySelector("input[name='delete_selected']");
    if (deleteSelectedButton) {
        deleteSelectedButton.addEventListener("click", function(event) {
            var confirmation = confirm("¿Seguro que deseas borrar?");
            if (!confirmation) {
                event.preventDefault();
            }
        });
    }
}
