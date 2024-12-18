document.addEventListener('DOMContentLoaded', function () {
    // Função para exibir alertas
    window.showAlert = function (message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            <strong>${type.charAt(0).toUpperCase() + type.slice(1)}:</strong> ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        `;
        document.body.appendChild(alertDiv);

        // Remover o alerta após 5 segundos
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    };

    // Interceptar todas as requisições AJAX
    $(document).ajaxComplete(function (event, xhr, settings) {
        try {
            const response = JSON.parse(xhr.responseText);
            if (response.status === 'success') {
                window.showAlert(response.message, 'success');
            } else if (response.status === 'error') {
                window.showAlert(response.message, 'danger');
            }
        } catch (error) {
            console.error('Erro ao processar a resposta:', error);
        }
    });

    // Adicionar evento de clique para fechar o alerta
    $(document).on('click', '.alert .close', function () {
        $(this).parent().remove();
    });

    // Lógica para adicionar livros
    const addBookForm = document.getElementById('add-book-form');
    if (addBookForm) {
        addBookForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(addBookForm);
            fetch('../controllers/user_controller.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.showAlert(data.message, 'success');
                        addBookForm.reset();
                    } else {
                        window.showAlert(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                });
        });
    }

    // Lógica para enviar solicitações de troca
    const sendRequestForm = document.getElementById('send-request-form');
    if (sendRequestForm) {
        sendRequestForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(sendRequestForm);
            fetch('../controllers/post_actions.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.showAlert(data.message, 'success');
                        sendRequestForm.reset();
                    } else {
                        window.showAlert(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                });
        });
    }
});
