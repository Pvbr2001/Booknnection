<aside class="sidebar-right">
    <div class="sidebar-section">
        <h2>Notificações Recentes</h2>
        <ul>
            <?php
            require_once '../config/database.php';

            // Conectar ao banco de dados
            $database = new Database();
            $conn = $database->getConnection();

            // Buscar notificações para o usuário atual
            $id_usuario = $_SESSION['user_id'];
            $sql = "SELECT u.nome AS nome_usuario, p.titulo AS titulo_post
                    FROM notificacoes n
                    JOIN posts p ON p.id = n.id_post
                    JOIN usuario u ON u.id = p.id_usuario
                    WHERE n.id_usuario = ? 
                    ORDER BY n.data_criacao DESC"; 
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();
            $result = $stmt->get_result();

            // Exibir notificações
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<li>";
                    echo "<strong>" . htmlspecialchars($row['nome_usuario']) . "</strong> solicitou uma troca para o livro: ";
                    echo "<a href='#'>" . htmlspecialchars($row['titulo_post']) . "</a>";
                    echo "</li>";
                }
            } else {
                echo "<li>Nenhuma notificação recente.</li>";
            }

            $stmt->close();
            $conn->close();
            ?>
        </ul>
    </div>
    <button class="sidebar-button">Botão Lateral A</button>
    <button class="sidebar-button">Botão Lateral B</button>
</aside>
