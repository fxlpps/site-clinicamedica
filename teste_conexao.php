<?php
require __DIR__ . '/config/conexao.php';

echo "<pre>";
echo "Arquivo config/conexao.php incluído com sucesso.\n";
echo "Variável \$pdo existe? " . (isset($pdo) ? "SIM" : "NÃO") . "\n";
echo "Variável \$pdo é objeto PDO? " . (($pdo ?? null) instanceof PDO ? "SIM" : "NÃO") . "\n";
echo "</pre>";

if (!isset($pdo) || !($pdo instanceof PDO)) {
    die("Parando aqui: \$pdo não foi criado corretamente.");
}

$stmt = $pdo->query("SELECT COUNT(*) as total FROM usuario");
$resultado = $stmt->fetch();

echo "Conectou! Total de usuários: " . $resultado['total'];