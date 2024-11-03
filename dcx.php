<?php
session_start();
// session_abort();
// session_unset();
// session_destroy();
// session_register_shutdown();
// session_write_close();
if (session_unset() && session_destroy()){
    session_gc(); // nettoyer le cache
    session_register_shutdown(); // fermer la session
    header("Location: login.php");
    exit;
}
?>