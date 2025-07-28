<?php
require_once __DIR__ . '/../../models/SolicitacaoCurso.php';

$solicitacao = new SolicitacaoCurso();
$message = '';

if (isset($_GET['id'])) {
    $solicitacao->id = $_GET['id'];
    if (!$solicitacao->readOne()) {
        $message = '<div class="alert alert-danger">Solicitação não encontrada.</div>';
    }
} else {
    redirect('/gestaoadulto/public/solicitacoes');
}

?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Detalhes da Solicitação de Curso</h1>
        <a href="/gestaoadulto/public/solicitacoes/edit?id=<?php echo $solicitacao->id; ?>" class="btn btn-warning">Editar</a>
    </div>

    <?php echo $message; ?>

    <?php if (empty($message)): ?>
        <div class="card mb-3">
            <div class="card-header">Informações da Solicitação</div>
            <div class="card-body">
                <p><strong>Região:</strong> <?php echo htmlspecialchars($solicitacao->regiao); ?></p>
                <p><strong>Curso:</strong> <?php echo htmlspecialchars($solicitacao->curso_nome); ?></p>
                <p><strong>Número Total de Inscritos:</strong> <?php echo htmlspecialchars($solicitacao->numero_inscritos); ?></p>
                <p><strong>Homens Inscritos:</strong> <?php echo htmlspecialchars($solicitacao->homens_inscritos); ?></p>
                <p><strong>Mulheres Inscritas:</strong> <?php echo htmlspecialchars($solicitacao->mulheres_inscritas); ?></p>
                <p><strong>Data da Solicitação:</strong> <?php echo htmlspecialchars($solicitacao->data_solicitacao); ?></p>
                <p><strong>Remetente:</strong> <?php echo htmlspecialchars($solicitacao->remetente_nome); ?></p>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Documentos Anexados</div>
            <div class="card-body">
                <?php
                $documentos = json_decode($solicitacao->documentos_paths, true);
                if (!empty($documentos)) {
                    echo '<ul>';
                    foreach ($documentos as $doc_path) {
                        echo '<li><a href="' . htmlspecialchars($doc_path) . '" target="_blank">' . basename($doc_path) . '</a></li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p>Nenhum documento anexado.</p>';
                }
                ?>
            </div>
        </div>

        <a href="/gestaoadulto/public/solicitacoes" class="btn btn-secondary">Voltar para a Lista</a>
    <?php endif; ?>
</div>