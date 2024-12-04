<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Booknnection - Configurações</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f9;
        }

        .navbar {
            margin-bottom: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-weight: bold;
        }

        .card-footer {
            background-color: #f8f9fa;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            transition: background-color 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 15px 0;
            position: fixed;
            width: 100%;
            bottom: 0;
        }

        .hidden {
            display: none;
        }

        .toggle-header {
            cursor: pointer;
            user-select: none;
        }

        .toggle-header:hover {
            text-decoration: none;
        }
    </style>
</head>

<body>

    <header class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Booknnection</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Alterna navegação">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="pagina_principal.php">For You</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pagina_perfil.php">Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Manual</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pagina_home.html">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pagina_configuracoes.php">Configurações</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../controllers/User_Controller.php?acao=logout">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="card">
            <div class="card-header toggle-header" data-target="alterar-foto-perfil">
                <h2>Alterar Foto de Perfil</h2>
            </div>
            <div class="card-body hidden" id="alterar-foto-perfil">
                <form action="../controllers/user_controller.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="acao" value="alterar_foto_perfil">
                    <div class="form-group">
                        <label for="foto_perfil">Alterar Foto de Perfil:</label>
                        <input type="file" class="form-control" name="foto_perfil" id="foto_perfil">
                    </div>
                    <button type="submit" class="btn btn-primary">Alterar Foto</button>
                </form>
            </div>
            <hr>
            <div class="card-header toggle-header" data-target="trocar-senha">
                <h2>Trocar Senha</h2>
            </div>
            <div class="card-body hidden" id="trocar-senha">
                <form action="../controllers/user_controller.php" method="POST">
                    <input type="hidden" name="acao" value="trocar_senha">
                    <div class="form-group">
                        <label for="senha_atual">Senha Atual:</label>
                        <input type="password" class="form-control" name="senha_atual" id="senha_atual" required>
                    </div>
                    <div class="form-group">
                        <label for="nova_senha">Nova Senha:</label>
                        <input type="password" class="form-control" name="nova_senha" id="nova_senha" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Trocar Senha</button>
                </form>
            </div>
            <hr>
            <div class="card-header toggle-header" data-target="desativar-conta">
                <h2>Desativar Conta</h2>
            </div>
            <div class="card-body hidden" id="desativar-conta">
                <form action="../controllers/user_controller.php" method="POST" onsubmit="return confirmDesativarConta();">
                    <input type="hidden" name="acao" value="desativar_conta">
                    <button type="submit" class="btn btn-danger">Desativar Conta</button>
                </form>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>Desenvolvido Para TCC Senai</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha384-tsQFqpEReu7ZLhBV2VZlAu7zcOV+rXbYlF2cqB8txI/8aZajjp4Bqd+V6D5IgvKT"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
        crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            // Alternar visibilidade dos elementos com a classe toggle-header
            $('.toggle-header').click(function() {
                var target = $(this).data('target');
                $('#' + target).toggle();
            });
        });

        function confirmDesativarConta() {
            return confirm('Tem certeza de que deseja desativar sua conta? Esta ação irá apagar todas as informações da conta de forma permanente.');
        }
    </script>
</body>

</html>
