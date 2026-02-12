<?php
require_once '../config.php';
requireLogin();
if (getUserRole() !== 'admin') {
    redirect('/dashboard/' . getUserRole() . '.php');
}
redirect('/dashboard/admin/index.php');
