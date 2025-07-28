<?php
require_once __DIR__ . '/../../models/Utilizador.php';

if ($_SESSION['user_role'] !== 'Administrador') {
    redirect('/gestaoadulto/public/dashboard');
}

$utilizador = new Utilizador();
$message = '';

// Lógica para aprovar/rejeitar/apagar utilizador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $utilizador->id = $_POST['user_id'];
    if ($utilizador->readOne()) { // Carrega os dados atuais do utilizador
        if ($_POST['action'] === 'approve') {
            $utilizador->status = 'Aprovado';
            $utilizador->papel = $_POST['papel']; // Permite definir o papel na aprovação
            if ($utilizador->updateStatusAndRole()) {
                $message = '<div class="alert alert-success">Utilizador aprovado e papel definido com sucesso!</div>';
            } else {
                $message = '<div class="alert alert-danger">Erro ao aprovar utilizador.</div>';
            }
        } elseif ($_POST['action'] === 'reject') {
            $utilizador->status = 'Rejeitado';
            // Mantém o papel atual, ou pode definir para um padrão se preferir
            if ($utilizador->updateStatusAndRole()) {
                $message = '<div class="alert alert-warning">Utilizador rejeitado.</div>';
            } else {
                $message = '<div class="alert alert-danger">Erro ao rejeitar utilizador.</div>';
            }
        } elseif ($_POST['action'] === 'delete') {
            if ($utilizador->delete()) {
                $message = '<div class="alert alert-success">Utilizador apagado com sucesso!</div>';
            } else {
                $message = '<div class="alert alert-danger">Erro ao apagar utilizador.</div>';
            }
        }
    } else {
        $message = '<div class="alert alert-danger">Utilizador não encontrado.</div>';
    }
}

$stmt = $utilizador->read();
$num = $stmt->num_rows;

?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestão de Utilizadores</h1>
    </div>

    <?php echo $message; ?>

    <?php if ($num > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Papel</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $stmt->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['nome']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['telefone']); ?></td>
                            <td><?php echo htmlspecialchars($row['papel']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td>
                                <?php if ($row['status'] === 'Pendente'): ?>
                                    <form action="" method="POST" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <select name="papel" class="form-select form-select-sm d-inline w-auto me-1">
                                            <option value="Utilizador">Utilizador</option>
                                            <option value="Administrador">Administrador</option>
                                        </select>
                                        <button type="submit" class="btn btn-success btn-sm me-1">Aprovar</button>
                                    </form>
                                    <form action="" method="POST" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn btn-warning btn-sm me-1">Rejeitar</button>
                                    </form>
                                <?php endif; ?>
                                <?php if ($row['id'] !== $_SESSION['user_id']): // Não permite apagar a própria conta ?>
                                    <form action="" method="POST" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja apagar este utilizador?');">Apagar</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            Nenhum utilizador encontrado.
        </div>
    <?php endif; ?>
</div>
