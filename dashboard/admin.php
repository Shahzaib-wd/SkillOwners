<?php
require_once '../config.php';
requireLogin();
if (getUserRole() !== 'admin') {
    redirect('/dashboard/' . getUserRole());
}
redirect('/dashboard/admin/index');
