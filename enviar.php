<?php
// enviar.php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = strip_tags(trim($_POST["nome"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $mensagem = trim($_POST["mensagem"]);

    // E-mail de destino
    $para = getenv('CONTACT_TO_EMAIL');
    if (!$para || $para === '') {
        $para = 'contato@exemplo.com';
    }
    
    // Assunto
    $assunto = "Novo contato via Trilha da Regularização";

    // Conteúdo do e-mail
    $corpo = "Nome: $nome\n";
    $corpo .= "Email: $email\n\n";
    $corpo .= "Mensagem:\n$mensagem\n";

    // Cabeçalhos
    $headers = "From: no-reply@seusite.com\r\n"; // Troque "seusite.com" pelo seu domínio real na Hostinger
    $headers .= "Reply-To: $email\r\n";

    // Envia
    if (mail($para, $assunto, $corpo, $headers)) {
        // Redireciona de volta com sucesso (você pode criar uma página de obrigado)
        echo "<script>alert('Mensagem enviada com sucesso!'); window.location.href='index.html';</script>";
    } else {
        echo "<script>alert('Erro ao enviar mensagem.'); window.location.href='index.html';</script>";
    }
} else {
    header("Location: index.html");
}
?>