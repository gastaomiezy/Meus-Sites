<?php
require_once __DIR__ . '/../../models/SolicitacaoCurso.php';
require_once __DIR__ . '/../../models/Adulto.php'; // Para obter as regiões
require_once __DIR__ . '/../../models/Curso.php';

$message = '';

$adulto_obj = new Adulto();
$curso_obj = new Curso();

$cursos = $curso_obj->read();

// Obter lista de regiões dos adultos existentes
$regioes_stmt = $adulto_obj->read();
$regioes = [];
while($row = $regioes_stmt->fetch_assoc()) {
    if (!in_array($row['regiao'], $regioes)) {
        $regioes[] = $row['regiao'];
    }
}
sort($regioes);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $solicitacao = new SolicitacaoCurso();
    $adulto = new Adulto();

    // Encontrar o adulto_id com base no email do utilizador logado
    $adulto_id = $adulto->findByEmail($_SESSION['user_email']);

    if (!$adulto_id) {
        $message = '<div class="alert alert-danger">Erro: Não foi possível encontrar um adulto associado a este utilizador.</div>';
    } else {
        $solicitacao->adulto_id = $adulto_id;
        $solicitacao->curso_id = $_POST['curso_id'];
        $solicitacao->numero_inscritos = $_POST['numero_inscritos'];
        $solicitacao->homens_inscritos = $_POST['homens_inscritos'];
        $solicitacao->mulheres_inscritas = $_POST['mulheres_inscritas'];
        $solicitacao->remetente_id = $_SESSION['user_id'];
        $solicitacao->regiao = $_POST['regiao'];

        // Lógica para upload de documentos
        $uploaded_files = [];
        if (isset($_FILES['documentos']) && !empty($_FILES['documentos']['name'][0])) {
            $target_dir = __DIR__ . "/../../public/uploads/";
            foreach ($_FILES['documentos']['name'] as $key => $name) {
                $file_name = basename($_FILES['documentos']['name'][$key]);
                $target_file = $target_dir . uniqid() . "_" . $file_name;
                $uploadOk = 1;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                // Check if file already exists
                if (file_exists($target_file)) {
                    $message = '<div class="alert alert-danger">Desculpe, o ficheiro já existe.</div>';
                    $uploadOk = 0;
                }

                // Check file size
                if ($_FILES['documentos']['size'][$key] > 5000000) { // 5MB
                    $message = '<div class="alert alert-danger">Desculpe, o seu ficheiro é muito grande.</div>';
                    $uploadOk = 0;
                }

                // Allow certain file formats
                if($imageFileType != "pdf" && $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif" ) {
                    $message = '<div class="alert alert-danger">Desculpe, apenas ficheiros PDF, JPG, JPEG, PNG & GIF são permitidos.</div>';
                    $uploadOk = 0;
                }

                // Check if $uploadOk is set to 0 by an error
                if ($uploadOk == 0) {
                    $message = '<div class="alert alert-danger">Desculpe, o seu ficheiro não foi carregado.</div>';
                } else {
                    if (move_uploaded_file($_FILES['documentos']['tmp_name'][$key], $target_file)) {
                        $uploaded_files[] = str_replace("C:\\xampp\\htdocs\\gestaoadulto\\public", "/gestaoadulto/public", $target_file); // Salva o caminho relativo para o BD
                    } else {
                        $message = '<div class="alert alert-danger">Desculpe, houve um erro ao carregar o seu ficheiro.</div>';
                    }
                }
            }
        }
        $solicitacao->documentos_paths = json_encode($uploaded_files);

        if ($solicitacao->create()) {
            $message = '<div class="alert alert-success">Solicitação de curso adicionada com sucesso!</div>';
        } else {
            $message = '<div class="alert alert-danger">Erro ao adicionar solicitação de curso.</div>';
        }
    }
}

?>

<div class="container">
    <h1>Nova Solicitação de Curso</h1>
    <?php echo $message; ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="regiao" class="form-label">Região</label>
            <select class="form-select" id="regiao" name="regiao" required>
                <option value="">Selecione uma Região</option>
                <?php
                require_once __DIR__ . '/../../models/Regiao.php';
                $regiao_model = new Regiao();
                $regioes = $regiao_model->read();
                while ($row = $regioes->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row['nome_regiao']) . "'>" . htmlspecialchars($row['nome_regiao']) . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="curso_id" class="form-label">Curso</label>
            <select class="form-select" id="curso_id" name="curso_id" required>
                <option value="">Selecione um Curso</option>
                <?php while ($row = $cursos->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nome_curso']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="numero_inscritos" class="form-label">Número Total de Inscritos</label>
            <input type="number" class="form-control" id="numero_inscritos" name="numero_inscritos" value="0" min="0" required>
        </div>
        <div class="mb-3">
            <label for="homens_inscritos" class="form-label">Homens Inscritos</label>
            <input type="number" class="form-control" id="homens_inscritos" name="homens_inscritos" value="0" min="0" required>
        </div>
        <div class="mb-3">
            <label for="mulheres_inscritas" class="form-label">Mulheres Inscritas</label>
            <input type="number" class="form-control" id="mulheres_inscritas" name="mulheres_inscritas" value="0" min="0" required>
        </div>
        <div class="mb-3">
            <label for="documentos" class="form-label">Documentos (PDF, JPG, PNG)</label>
            <input type="file" class="form-control" id="documentos" name="documentos[]" multiple>
        </div>
        <button type="submit" class="btn btn-primary">Enviar Solicitação</button>
        <a href="/gestaoadulto/public/solicitacoes" class="btn btn-secondary">Cancelar</a>
    </form>
</div>